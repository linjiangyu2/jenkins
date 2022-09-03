<?php

namespace app\admin\model;

use think\Model;

class Auth extends Model
{
    //通过获取器，对is_nav字段值进行转化  方法名 get字段名Attr
    public function getIsNavAttr($value)
    {
        //is_nav字段  1 是， 0 否
        return $value ? '是' : '否';
    }
}
