<?php

namespace app\home\controller;

use app\home\model\Address;
use app\home\model\Cart;
use app\home\model\Goods;
use app\home\model\GoodsAttr;
use app\home\model\OrderGoods;
use think\Controller;
use think\Request;

class Order extends Base
{

    /**
     * 结算页面
     *
     * @return \think\Response
     */
    public function create()
    {
        //登录判断
        if (!session('?user_info')){
            //没有登录，跳转到登录页面
            //设置 登录成功后的跳转地址
            session('back_url', 'home/cart/index');
            $this->redirect('home/login/login');
        }
        //接收参数
        $cart_ids = request()->param('cart_ids');
        //查询收货地址信息
        $user_id = session('user_info.id');
        $address = Address::where('user_id', $user_id)->select();
        //查询购物记录信息
        $cart_data = Cart::where('id', 'in', $cart_ids)->select();
        //计算总金额
        $total_price = 0;
        $total_number = 0;
        foreach ($cart_data as &$v) {
            //商品基本信息
            $v['goods'] = Goods::find($v['goods_id']);
            //查询选中的商品属性值、属性名称  连表查询 tpshop_goods_attr   tpshop_attribute
            //SELECT t1.*, t2.attr_name FROM `tpshop_goods_attr` t1 left join tpshop_attribute t2 on t1.attr_id = t2.id where t1.id in (17,22);
            $v['goodsattr'] = GoodsAttr::alias('t1')
                ->join('tpshop_attribute t2', 't1.attr_id = t2.id', 'left')
                ->field('t1.*, t2.attr_name')
                ->where('t1.id', 'in', $v['goods_attr_ids'])
                ->select();
            //累加总金额
            $total_price += $v['goods']['goods_price'] * $v['number'];
            $total_number += $v['number'];
        }
        unset($v);
        return view('create', ['address' => $address, 'cart_data' => $cart_data, 'total_price' => $total_price, 'total_number' => $total_number]);
    }


    /**
     * 创建订单
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        //接收参数
        $data = request()->param();
//        dump($data);die;
        //订单表数据添加
        //订单编号 系统唯一
        $order_sn = time() . mt_rand(100000, 999999);
        $user_id = session('user_info.id');
        //计算订单总金额
        //SELECT t1.*, t2.goods_price, t2.goods_name, t2.goods_logo FROM `tpshop_cart` as t1 left join tpshop_goods t2 on t1.goods_id = t2.id where t1.id in (3,4,6);
        $cart_data = Cart::alias('t1')
            ->join('tpshop_goods t2', 't1.goods_id = t2.id', 'left')
            ->field('t1.*, t2.goods_name, t2.goods_logo, t2.goods_price')
            ->where('t1.id', 'in', $data['cart_ids'])
            ->select();
        $order_amount = 0;
        foreach ($cart_data as $v) {
            $order_amount += $v['number'] * $v['goods_price'];
        }
        //查询收货地址信息
        $address = Address::find($data['address_id']);
        //组装订单表一条数据
        $order_data = [
            'order_sn' => $order_sn,
            'user_id' => $user_id,
            'order_amount' => $order_amount,
            'consignee_name' => $address['consignee'],
            'consignee_phone' => $address['phone'],
            'consignee_address' => $address['address'],
            'pay_type' => $data['pay_type'],
        ];
        //添加到订单表
        $order = \app\home\model\Order::create($order_data);
        //订单商品表添加多条数据
        $goods_data = [];
        foreach ($cart_data as $v){
            $row = [
                'order_id' => $order->id,
                'goods_id' => $v['goods_id'],
                'goods_name' => $v['goods_name'],
                'goods_logo' => $v['goods_logo'],
                'goods_price' => $v['goods_price'],
                'number' => $v['number'],
                'goods_attr_ids' => $v['goods_attr_ids']
            ];
            $goods_data[] = $row;
        }
        //批量添加
        $ordergoods = new OrderGoods();
        $ordergoods->saveAll($goods_data);
        //删除购物车中对应的记录
//        Cart::destroy($data['cart_ids']);
        //接下来就是支付了
        switch ($data['pay_type']) {
            case 'card':
                //银行卡
                echo '您已下单成功!';
                break;
            case 'wechat':
                //微信支付
                echo '您已下单成功!';
//                return view('weixinpay');
                break;
            case 'alipay':
                //支付宝支付
                //组装一个表单 html代码
                $html = "<form id='alipayment' action='/plugins/alipay/pagepay/pagepay.php' method='post' style='display: none' >
            <input id='WIDout_trade_no' name='WIDout_trade_no' value='{$order_sn}' />
            <input id='WIDsubject' name='WIDsubject' value='tpshop商城订单'/>
            <input id='WIDtotal_amount' name='WIDtotal_amount' value='{$order_amount}'/>
            <input id='WIDbody' name='WIDbody' value='缺货'/>
		</form><script>document.getElementById('alipayment').submit();</script>";
                echo $html;
                break;
            case 'cash':
                echo '您已下单成功!';
                break;
        }
    }

    /**
     * 支付结果页
     *
     * @return \think\Response
     */
    public function callback()
    {
        //接收参数 get请求
        $data = request()->param();
        //验证签名 参考alipay/return_url.php
        require_once("./plugins/alipay/config.php");
        require_once './plugins/alipay/pagepay/service/AlipayTradeService.php';
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($data);
        if($result){
            //验证成功
            $total_amount = $data['total_amount'];
            return view('paysuccess', ['total_amount' => $total_amount]);
        }else{
            return view('payfail');
        }

    }

    //异步通知地址
    public function notify()
    {
        //需要外网访问的。 post请求
        //参考aplipay/notify_url.php中的写法
        //接收参数 post请求
        $data = request()->param();
        //验证签名 参考alipay/return_url.php
        require_once("./plugins/alipay/config.php");
        require_once './plugins/alipay/pagepay/service/AlipayTradeService.php';
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($data);
        if($result){
            //验证成功
            if ($data['trade_status'] == 'TRADE_SUCCESS') {
                $order_sn = $data['out_trade_no'];
                $order = \app\home\model\Order::where('order_sn', $order_sn)->find();
                if($order->order_amount != $data['total_fee']){
                    //金额不一致
                    echo 'fail';die;
                }
                if($order->pay_status == 0){
                    $order->pay_status = 1;
                    $order->save();
                }
            }
            echo 'success';die;
        }else{
            echo 'fail';die;
        }
    }

}
