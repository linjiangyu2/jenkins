<?php

namespace app\home\controller;

use app\home\model\Cart;
use app\home\model\User;
use think\Controller;
use think\Request;
use think\Validate;

class Login extends Controller
{
    //登录页面展示
    public function login()
    {
        //临时关闭模板布局
        $this->view->engine->layout(false);
        return view();
    }

    //登录表单提交
    public function dologin()
    {
        //接收参数
        $data = request()->param();
        $password = encrypt_password($data['password']);
        /* 第一种写法
        //查询用户表  根据用户名查询  email  phone
        $user = User::where('email', $data['username'])->whereOr('phone', $data['username'])->find();
        if ($user && $user->password == $password) {
            //用户名存在且密码正确  登录成功
            //设置登录标识到session
            session('user_info', $user);
            $this->success('登录成功', 'home/index/index');
        }else{
            $this->error('用户名或者密码错误');
        }
        */
        //第二种写法
        //查询用户表，根据用户名和密码一起查询
        $user = User::where(function ($query) use ($data) {
            $query->where('email', $data['username'])->whereOr('phone', $data['username']);
        })->where('password', $password)->find();
        if (!empty($user)) {
            //设置登录标识到session
            session('user_info', $user->toArray());
            //将cookie中的购物车数据迁移到数据表  调用Cart模型的cookieTodb
            Cart::cookieTodb();
            //先取session中的跳转地址  没有的话 再跳转首页
            $back_url = session('?back_url') ? session('back_url') : 'home/index/index';
            $this->success('登录成功', $back_url);
        } else {
            $this->error('用户名或者密码错误');
        }
    }

    //退出
    public function logout()
    {
        //清空session
        session(null);
        //页面跳转到登录页
        $this->redirect('home/login/login');
    }

    //注册
    public function register()
    {
        //临时关闭模板布局
        $this->view->engine->layout(false);
        return view();
    }

    //邮箱注册
    public function email()
    {
        //接收参数
        $data = request()->param();
//        dump($data);die;
        //参数检测
        $rule = [
            'email' => 'require|email|unique:user',
            'password' => 'require|length:6,16|confirm:repassword',
        ];
        $msg = [
            'email.require' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '邮箱已被注册',
            'password.require' => '密码不能为空',
            'password.length' => '密码长度必须为6-16个字符',
            'password.confirm' => '两次密码输入必须一致',
        ];
        $validate = new Validate($rule, $msg);
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
        //将数据添加到用户表
        $data['email_code'] = mt_rand(1000, 9999);
        $data['password'] = encrypt_password($data['password']);
        $data['username'] = $data['email'];
        $user = User::create($data, true);

        //发送激活邮件
        $subject = 'TP5商城注册邮件';
//        $url = "http://www.tpshop.com/index.php/home/login/jihuo/id/" . $user->id . "/code/" . $data['email_code'];
        $url = url('home/login/jihuo', ['id' => $user->id, 'code' => $data['email_code']], '', true);
        $body = "欢迎注册，请点击一下链接进行激活：<br><a href='$url'>$url</a>";
        //调用封装的send_email函数发送邮件
        $res = send_email($data['email'], $subject, $body);
        if ($res === true) {
            $this->success('注册成功，请登录邮箱进行激活', 'login');
        } else {
//            $this->error($res, 'login');
            $this->error('注册成功，激活邮件发送失败，请联系客服', 'login');
        }
    }

    //邮箱账号激活
    public function jihuo($id, $code){
        //以id和email_code两个条件查询user表
        $user = User::where(['id' => $id, 'email_code' => $code])->find();
        if(empty($user)){
            $this->error('激活失败', 'register');
        }
        //激活账号
        $user->is_check = 1;
        $user->save();
        $this->success('激活成功', 'login');
    }

    //ajax请求发送注册验证码
    public function sendcode($phone)
    {
        //参数验证
        if (empty($phone)) {
            return ['code' => 10002, 'msg' => '参数错误'];
        }
        //短信内容  【传智播客】您用于注册的验证码为：****，如非本人操作，请忽略。
        $code = mt_rand(1000, 9999);
        $msg = "【传智播客】您用于注册的验证码为：{$code}，如非本人操作，请忽略。";
        //发送短信
//        $res = sendmsg($phone, $msg);
        $res = true;//用于测试，并不真正发短信
        if ($res === true) {
            //发送成功，存储验证码到session 用于后续验证码的校验
            session('register_code_' . $phone, $code);
//            return ['code' => 10000, 'msg' => '发送成功'];
            return ['code' => 10000, 'msg' => '短信功能测试中,本次验证码为' . $code, 'data' => $code];
        }
        return ['code' => 10001, 'msg' => $res];
    }

    //手机号注册
    public function phone()
    {
        //接收参数
        $data = request()->param();
        //参数验证
        $rule = [
            'phone' => 'require|regex:\d{11}|unique:user',
            'code' => 'require|regex:\d{4}',
            'password' => 'require|length:6,16|confirm:repassword'
        ];
        $msg = [
            'phone.require' => '手机号不能为空',
            'phone.regex' => '手机号码格式不正确',
            'phone.unique' => '手机号码已被注册',
            'code.require' => '验证码不能为空',
            'code.regex' => '验证码格式不正确',
            'password.require' => '密码不能为空',
            'password.length' => '密码长度必须为6-16个字符',
            'password.confirm' => '两次密码输入必须一致',
        ];
        $validate = new Validate($rule, $msg);
        if(!$validate->check($data)){
            $this->error($validate->getError());
        }
        //验证码校验
        if ($data['code'] !=  session('register_code_' . $data['phone'])){
            $this->error('验证码错误');
        }
        //将数据添加到用户表
        $data['password'] = encrypt_password($data['password']);
        $data['is_check'] = 1; //手机号注册不需要激活
        $data['username'] = $data['phone'];
        User::create($data, true);
        $this->success('注册成功', 'login');
    }

    //qq登录 回调
    public function qqcallback()
    {
        //参考oauth/callback.php写法
        require_once("./plugins/qq/API/qqConnectAPI.php");
        $qc = new \QC();
        $access_token = $qc->qq_callback();
        $openid = $qc->get_openid();
        //重新实例化qc类
        $qc = new \QC($access_token, $openid);
        //获取用户信息（昵称等信息）
        $info = $qc->get_user_info();
        $nickname = $info['nickname'];
        //自动注册 将第三方账号信息，添加到用户表
        //判断账号是否已经存在
        $user = User::where('openid', $openid)->find();
        if (empty($user)) {
            //第一次登录
            User::create(['openid' => $openid, 'username' => $nickname]);
            //查询完整的用户信息
            $user = $user = User::where('openid', $openid)->find();
        } else {
            //以前登录过，更新第三方账号的信息到用户表
            $user->username = $nickname;
            $user->save();
        }
        //设置登录标识
        session('user_info', $user);
        //将cookie中的购物车数据迁移到数据表  调用Cart模型的cookieTodb
        Cart::cookieTodb();
        //页面跳转
        //先取session中的跳转地址  没有的话 再跳转首页
        $back_url = session('?back_url') ? session('back_url') : 'home/index/index';
        $this->success('登录成功', $back_url);
    }
}
