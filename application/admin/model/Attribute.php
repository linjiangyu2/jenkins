<?php

namespace app\admin\model;

use think\Model;

class Attribute extends Model
{
    //获取器 对attr_type值 和attr_input_type值进行转化
    public function getAttrTypeAttr($value)
    {
        // 0 唯一属性 ；1 单选属性
        return $value ? '单选属性' : '唯一属性';
    }

    public function getAttrInputTypeAttr($value)
    {
        // 0 input输入框 ； 1 下拉列表 ； 2 多选框
//        return $value == 0 ? 'input输入框' : ($value == 1 ? '下拉列表' : '多选框' );
        $input_type = [ 'input输入框', '下拉列表', '多选框'];
        return $input_type[$value];
    }
}
