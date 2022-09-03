<?php

namespace app\home\controller;

use app\home\model\Address;
use app\home\model\Order;
use think\Controller;
use think\Request;

class Member extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('index');
    }

    /**
     * 我的订单
     *
     * @return \think\Response
     */
    public function myorder($status = '')
    {
        $user_id = session('user_info.id');
        $list = Order::where('user_id', $user_id)->paginate(10);
        $list_pay = Order::where('user_id', $user_id)->where('pay_status', 0)->paginate(10);
        $list_receive = Order::where('user_id', $user_id)->where('pay_status', 1)->paginate(10);
        $list_remark = Order::where('user_id', $user_id)->where('pay_status', 2)->paginate(10);
        $nums = Order::where('user_id', $user_id)->group('pay_status')->column('count(*)', 'pay_status');
        return view('myorder', [
            'list' => $list,
            'list_pay' => $list_pay,
            'list_receive' => $list_receive,
            'list_remark' => $list_remark,
            'nums' => $nums
        ]);
    }

    /**
     * 评论
     *
     * @return \think\Response
     */
    public function mycomment()
    {
        return view();
    }

    /**
     * 我的收货地址
     *
     * @return \think\Response
     */
    public function myaddress()
    {
        return view();
    }

    public function newaddress()
    {
        $data = request()->param();
        $data['user_id'] = session('user_info.id');
        $address = new Address();
        if(!empty($data['id'])){
            $address->allowField(true)->isUpdate(true)->save($data);
        }
        $address->allowField(true)->save($data);
        return ['code' => 10000, 'msg' => 'success', 'data' => $address];
    }

    public function deladdress($id)
    {
        Address::destroy($id);
        return ['code' => 10000, 'msg' => 'success'];
    }
}