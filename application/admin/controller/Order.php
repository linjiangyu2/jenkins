<?php

namespace app\admin\controller;

use app\admin\model\GoodsAttr;
use app\admin\model\OrderGoods;
use think\Controller;
use think\Request;

class Order extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询订单表数据
        $list = \app\admin\model\Order::order('id desc')->select();
        return view('index', ['list' => $list]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //查询订单基本信息
        $order = \app\admin\model\Order::find($id);
        //查询订单商品信息
        $ordergoods = OrderGoods::where('order_id', $id)->select();

        //订单下商品 选中的属性值和属性名称
        foreach ($ordergoods as &$v){
            $v['goodsattr'] = GoodsAttr::alias('t1')
                ->join('tpshop_attribute t2', 't1.attr_id = t2.id', 'left')
                ->field('t1.*, t2.attr_name')
                ->where('t1.id', 'in', $v['goods_attr_ids'])
                ->select();
        }
        unset($v);
        //查询快递信息  假设  快递公司  yunda  快递编号3101314976598
        // 接口地址 https://www.kuaidi100.com/query?type=yunda&postid=3101314976598
        $type = 'yunda';
        $postid = '3101314976598';
        $url = "https://www.kuaidi100.com/query?type={$type}&postid={$postid}";
        //调用curl_request函数 发送get请求  https
        $res = curl_request($url, false, [], true);
        if(!$res){
            //请求失败  比如网络原因导致
            $kuaidi = [];
        }
        //将json格式字符串转化为数组
        $arr = json_decode($res, true);
        if($arr['status'] != 200){
            $kuaidi = [];
        }
        $kuaidi = $arr['data'];
        return view('read', ['order' => $order, 'ordergoods' => $ordergoods, 'kuaidi' => $kuaidi]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
