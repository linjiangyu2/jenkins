<?php

namespace app\admin\controller;

use app\admin\model\Role;
use think\Controller;
use think\Request;

class Manager extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $username = input('username');
        if ($username) {
            $list = \app\admin\model\Manager::where('username', 'like', "%$username%")->select();
        } else {
            $list = \app\admin\model\Manager::select();
        }
        $this->assign('list', $list);
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $role = Role::select();
        $this->assign('role', $role);
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
        $data = input();
        //参数验证 Todo
        \app\admin\model\Manager::create($data, true);
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
        return view();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $role = Role::select();
        $this->assign('role', $role);
        $info = \app\admin\model\Manager::find($id);
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
        \app\admin\model\Manager::update($data,['id' => $id], true);
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
        \app\admin\model\Manager::destroy($id);
        $this->success('操作成功', 'index');
    }

    //修改密码
    public function setpwd()
    {
        if (request()->isGet()) {
            return view();
        }
        $data = input();
        //参数验证 Todo
        $id = session('manager_info.id');
        \app\admin\model\Manager::update($data, ['id' => $id], true);
        $this->success('操作成功', 'index');
    }

    //重置密码
    public function reset($id)
    {
        \app\admin\model\Manager::update(['password' => '123456'], ['id' => $id]);
        $this->success('操作成功', 'index');
    }
}
