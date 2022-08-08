<?php

namespace app\home\controller;

use app\home\model\Address;
use app\home\model\Goods;
use app\home\model\GoodsAttr;
use app\home\model\Order;
use app\home\model\OrderGoods;
use think\Controller;
use think\Request;

class Cart extends Base
{
    /**
     * 加入购物车
     *
     * @return \think\Response
     */
    public function addcart()
    {
        //接收数据
        $data = request()->param();
        //参数检测 略
        //处理数据， 调用模型的方法，传递对应的参数
        \app\home\model\Cart::addCart($data['goods_id'], $data['number'], $data['goods_attr_ids']);
        //查询商品基本信息，用于页面展示
        $goods = Goods::find($data['goods_id']);
        return view('addcart', ['goods' => $goods]);
    }

    /**
     * 购物车列表.
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询购物车数据
        $list = \app\home\model\Cart::getAllCart();
        //遍历数组，查询每一条数据商品信息
        foreach ($list as &$v) {
            //商品基本信息
            $v['goods'] = Goods::find($v['goods_id']);
            //查询选中的商品属性值、属性名称  连表查询 tpshop_goods_attr   tpshop_attribute
            //SELECT t1.*, t2.attr_name FROM `tpshop_goods_attr` t1 left join tpshop_attribute t2 on t1.attr_id = t2.id where t1.id in (17,22);
            $v['goodsattr'] = GoodsAttr::alias('t1')
                ->join('tpshop_attribute t2', 't1.attr_id = t2.id', 'left')
                ->field('t1.*, t2.attr_name')
                ->where('t1.id', 'in', $v['goods_attr_ids'])
                ->select();
        }
//        unset($v);
        return view('index', ['list' => $list]);
    }

    //ajax请求修改购买数量
    public function changenum()
    {
        //接收数据
        $data = request()->param();
        //参数验证 略
        //处理数据  调用模型的方法
        \app\home\model\Cart::changeNum($data['goods_id'], $data['number'], $data['goods_attr_ids']);
        //返回数据
        return ['code' => 10000, 'msg' => 'success'];
    }

    //ajax请求删除指定的购物记录
    public function delcart()
    {
        //接收参数
        $data = request()->param();
        //参数验证 略
        //数据处理
        \app\home\model\Cart::delCart($data['goods_id'], $data['goods_attr_ids']);
        //返回数据
        return ['code' => 10000, 'msg' => 'success'];
    }

}
