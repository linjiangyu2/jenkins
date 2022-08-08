<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Auth extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询权限表数据
        $list = \app\admin\model\Auth::select();
        //使用封装的getTree递归函数重新排序
        $list = getTree($list);
        return view('index', ['list' => $list]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //查询所有的顶级权限，用于下拉列表展示
        $top_auth = \app\admin\model\Auth::where('pid', 0)->select();
        return view('create', ['top_auth' => $top_auth]);
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
//        dump($data);
        //参数验证 略

        if($data['pid'] == 0){
            unset($data['auth_c'], $data['auth_a']);
        }
        //将数据添加到auth表
        \app\admin\model\Auth::create($data, true);
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
        $info = \app\admin\model\Auth::find($id);
        $this->assign('info', $info);
        $top_auth = \app\admin\model\Auth::where('pid', 0)->where('id', '<>', $id)->select();
        $this->assign('top_auth', $top_auth);
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
        $info = \app\admin\model\Auth::find($id);
        if($info['pid'] == 0 && $data['pid'] != 0){
            $count = \app\admin\model\Auth::where('pid', $id)->count();
            if($count){
                $this->error('当前权限下有二级权限，不能修改级别');
            }
        }
        if($data['pid'] == 0){
            $data['auth_c'] = '';
            $data['auth_a'] = '';
        }
        \app\admin\model\Auth::update($data,['id' => $id], true);
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
        $total = \app\admin\model\Auth::where('pid', $id)->count();
        if( $total ){
            $this->error('权限下有子权限，不能直接删除');
        }
        \app\admin\model\Auth::destroy($id);
        $this->success('操作成功', 'index');
    }
}
