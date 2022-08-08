<?php

namespace app\admin\controller;

use app\admin\model\Type;
use think\Controller;
use think\Request;

class Attribute extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询所有的属性数据  需要连表tpshop_type表 查询商品类型名称type_name字段值
        $list = \app\admin\model\Attribute::alias('a')
            ->field('a.*, t.type_name')
            ->join('tpshop_type t', 'a.type_id = t.id', 'left')
            ->select();
        return view('index', ['list' => $list]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //查询所有的商品类型,用于下拉列表展示
        $type = Type::select();
        return view('create', ['type' => $type]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接收请求参数
        $data = $request->param();
        //参数验证  略
        //添加到数据表tpshop_attribute
        \app\admin\model\Attribute::create($data, true);
        //页面跳转
        $this->success('操作成功', 'index');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $info = \app\admin\model\Attribute::find($id)->getData();
        $this->assign('info', $info);
        //查询所有的商品类型,用于下拉列表展示
        $type = Type::select();
        $this->assign('type', $type);
        return view();
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
        $data = input();
        //参数验证 Todo
        \app\admin\model\Attribute::update($data,['id' => $id], true);
        $this->success('操作成功', 'index');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        \app\admin\model\Attribute::destroy($id);
        $this->success('操作成功', 'index');
    }
}
