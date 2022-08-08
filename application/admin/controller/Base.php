<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/3/12
 * Time: 16:08
 */
namespace app\admin\controller;

use app\admin\model\Auth;
use app\admin\model\Role;
use think\Controller;

class Base extends Controller
{
    public function __construct()
    {
        //先实现父类的构造方法，防止被直接重写掉
        parent::__construct();
        //登录验证
        if ( !session('manager_info') ) {
            //没有登录 跳转到登录页面
            $this->redirect('admin/login/login');
        }
        //先检测权限
        $this->checkauth();
        //调用getnav方法
        $this->getnav();

    }

    //获取左侧菜单权限
    public function getnav()
    {
        //获取当前登录的管理员，左侧菜单显示的权限
        //获取当前管理员的信息（role_id）
        $role_id = session('manager_info.role_id');
        //如果是超级管理员，直接查询权限表
        if ($role_id == 1) {
            //分别查询顶级菜单权限和二级菜单权限
            $top_nav = Auth::where(['pid' => 0, 'is_nav' => 1])->select();
            $sec_nav = Auth::where(['pid' => ['>', 0], 'is_nav' => 1])->select();
        } else {
            //如果是普通管理员，先查询角色表 取到role_auth_ids
            $role = Role::find($role_id);
            $role_auth_ids = $role->role_auth_ids;
            //分别查询顶级菜单权限和二级菜单权限
            $top_nav = Auth::where([
                'pid' => 0,
                'is_nav' => 1,
                'id' => ['in', $role_auth_ids]
            ])->select();
            $sec_nav = Auth::where([
                'pid' => ['>', 0],
                'is_nav' => 1,
                'id' => ['in', $role_auth_ids]
            ])->select();
        }
        //变量赋值
        $this->assign('top_nav', $top_nav);
        $this->assign('sec_nav', $sec_nav);
    }

    //检测当前访问的权限
    public function checkauth()
    {
        //获取管理员信息（role_id）
        $role_id = session('manager_info.role_id');
        if ($role_id == 1) {
            //超级管理员，拥有所有权限，不需要继续检测
            return ;
        }
        //普通管理员 需要检测权限
        //分别获取当前请求的控制器和方法名称
        $controller = request()->controller();
        $action = request()->action();
        if (strtolower($controller) == 'index' && strtolower($action) == 'index'){
//            if ($controller == 'Index' && $action == 'index'){
            //特殊页面 比如后台首页，不需要检测权限
            return;
        }
        $ac = $controller . '-' . $action;
        //判断$ac 是否在已经拥有的权限role_auth_ac里面
        //查询当前角色信息
        $role = Role::find($role_id);
        $auth = Auth::where(['auth_c'=>$controller, 'auth_a'=>$action])->find();
        $role_auth_ids = explode(',', $role->role_auth_ids);
        if (!in_array($auth['id'], $role_auth_ids)) {
            $this->error('没有权限访问', 'admin/index/index');
        }
    }

}