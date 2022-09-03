<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Login extends Controller
{
    public function login()
    {
        //get请求  页面展示
        //临时关闭模板布局
        $this->view->engine->layout(false);
        return view();
    }

    //后台退出
    public function logout()
    {
        //清空session
        session(null);
        //跳转到登录页
        $this->redirect('admin/login/login');
    }

    //ajax登录方法
    public function ajaxlogin()
    {
        //获取输入变量
        $data = input();
        //进行验证码校验 使用手动验证方法
        if (!captcha_check($data['code'])) {
            //验证码错误   犹豫当前是ajax请求，框架底层会自动将数组转化为json格式字符串
            return ['code' => 10001, 'msg' => '验证码错误'];
        }
        //根据用户名和密码（加密后的密码），查询管理员用户表
        $where = [
            'username' => $data['username'],
            'password' => encrypt_password($data['password'])
        ];
        $info = \app\admin\model\Manager::where($where)->find();
        if(!$info){
            //用户名或者密码错误
            return ['code' => 10002, 'msg' => '用户名或者密码错误'];
        }
        //设置session登录标识
        session('manager_info', $info);
        //登录成功
        return ['code' => 10000, 'msg' => '登录成功'];
    }
}
