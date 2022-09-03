<?php

namespace app\home\controller;

use app\home\model\Attribute;
use app\home\model\GoodsAttr;
use app\home\model\Goodspics;
use app\home\model\Category;
use think\Controller;
use think\Request;

class Goods extends Base
{
    public function index($cate_id)
    {
        $cate_info = Category::find($cate_id);
        $list = \app\home\model\Goods::where('cate_id', $cate_id)->paginate(10);
        $this->assign('cate_info', $cate_info);
        $this->assign('list', $list);
        return view('list');
    }
    /**
     * 商品详情
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function detail($id)
    {
        //查询商品基本信息
        $goods = \app\home\model\Goods::find($id);
        //查询商品相册信息
        $goodspics = Goodspics::where('goods_id', $id)->select();
        //查询商品对应的类型下的属性名称信息
        $attribute = Attribute::where('type_id', $goods->type_id)->select();
        //查询商品关联的属性值
        $goodsattr = GoodsAttr::where('goods_id', $id)->select();
        $new_goodsattr = [];
        foreach ($goodsattr as $v) {
            $new_goodsattr[$v['attr_id']][] = $v;
        }
        return view('detail', [
            'goods' => $goods,
            'goodspics' => $goodspics,
            'attribute' => $attribute,
            'goodsattr' => $new_goodsattr
        ]);
    }

}
