<?php

namespace app\admin\model;

use think\Model;

class Manager extends Model
{
    protected $insert = ['password'=>'123456'];
    public function getStatusAttr($value)
    {
        $status = [ 1 => '可用', 2 => '禁用'];
        return $status[$value];
    }

    public function getLastLoginTimeAttr($value){
        return date('Y-m-d H:i:s', $value);
    }

    public function setPasswordAttr($value)
    {
        return encrypt_password($value);
    }
}
