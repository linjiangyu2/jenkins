<?php

namespace app\admin\model;

use think\Model;
class Goods extends Model
{
    //定义完整的数据表名称
//    protected $table = 'tpshop_goods';

    //设置软删除
    use \traits\model\SoftDelete;
    protected $deleteTime = 'delete_time';
}
