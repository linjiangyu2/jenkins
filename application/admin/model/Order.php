<?php

namespace app\admin\model;

use think\Model;

class Order extends Model
{
    //获取器  转化获取到的字段值
    public function getPayStatusAttr($value)
    {
        $pay_status = ['未付款', '已付款'];
        return $pay_status[$value];
    }

    public function getPayTypeAttr($value)
    {
        $pay_type = ['card'=>'银行卡', 'wechat'=>'微信', 'alipay'=>'支付宝', 'cash'=>'货到付款'];
        return $pay_type[$value];
    }
    public function getShippingTypeAttr($value)
    {
        $shipping_type = ['yuantong'=>'圆通','shentong'=>'申通','yunda'=>'韵达','zhongtong'=>'中通', 'shunfeng'=>'顺丰'];
        return $shipping_type[$value];
    }
}
