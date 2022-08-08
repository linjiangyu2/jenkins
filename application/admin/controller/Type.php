<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Type extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询商品类型数据
        $list = \app\admin\model\Type::select();
        return view('index', ['list' => $list]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接收输入参数
        $data = $request->param();
        //参数检测
        if (empty($data['type_name'])) {
            $this->error('类型名称不能为空');
        }
        //将数据添加到商品类型表tpshop_type
        \app\admin\model\Type::create($data, true);
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
        $info = \app\admin\model\Type::find($id);
        $this->assign('info', $info);
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
        \app\admin\model\Type::update($data,['id' => $id], true);
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
        \app\admin\model\Type::destroy($id);
        $this->success('操作成功', 'index');
    }
}
