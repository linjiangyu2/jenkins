<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/4/3
 * Time: 21:10
 */

namespace app\home\controller;

use think\Request;
use think\Controller;
use app\home\model\Category;

class Base extends Controller
{
    protected $auth_controller = ['member', 'order'];
    public function __construct(Request $request)
    {
        parent::__construct($request);
        //登录判断
        $controller = strtolower($request->controller());
        if(in_array($controller, $this->auth_controller) && !session('?user_info')){
            $this->redirect('home/login/login');
        }
	echo gethostname();
        //查询在前台首页显示的分类
//        if(!$category = cache('category')) {
            $category = Category::where('is_show', 1)->select();
            cache('category', $category);
//        }
        $this->assign('category', $category);
    }
}
