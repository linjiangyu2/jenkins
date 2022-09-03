<?php

namespace app\admin\controller;

use app\admin\model\Auth;
use think\Controller;
use think\Request;

class Role extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询角色表数据
        $list = \app\admin\model\Role::select();
        return view('index', ['list' => $list]);
    }

    /**
     * 为角色分配权限表单展示
     *
     * @return \think\Response
     */
    public function setauth($id)
    {
        //查询角色信息（角色名称）
        $info = \app\admin\model\Role::find($id);
        //分别查询所有的顶级权限和二级权限
        $top_auth = Auth::where('pid', 0)->select();
        $sec_auth = Auth::where('pid', '>', 0)->select();
        return view('setauth', ['info' => $info, 'top_auth' => $top_auth, 'sec_auth' => $sec_auth]);
    }

    /**
     * 为角色分配权限表单提交
     *
     * @return \think\Response
     */
    public function saveauth($id)
    {
        //接收表单中的id参数  接收的是数组，需要使用/a变量修饰符
        $auth_ids = request()->param('auth_ids/a');
        //将id数组转化为数据表需要的字符串
        $role_auth_ids = implode(',', $auth_ids);
        //处理role_auth_ac字段
        //根据id权限主键数组，查询对应的权限信息（控制器名、方法名）
        $row = [
            'id' => $id,
            'role_auth_ids' => $role_auth_ids
        ];
        //将数据保存到role表
        \app\admin\model\Role::update($row);
        $this->success('操作成功', 'index');
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
        $data = input();
        \app\admin\model\Role::create($data, true);
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
        $info = \app\admin\model\Role::find($id);
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
        \app\admin\model\Role::update($data,['id' => $id], true);
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
        \app\admin\model\Role::destroy($id);
        $this->success('操作成功', 'index');
    }
}
