<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:72:"D:\phpStudy\WWW\tp5shop\public/../application/admin\view\role\index.html";i:1522659354;s:58:"D:\phpStudy\WWW\tp5shop\application\admin\view\layout.html";i:1522653883;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>后台管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="/static/admin/css/main.css" rel="stylesheet" type="text/css"/>
    <link href="/static/admin/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/static/admin/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
    <script src="/static/admin/js/jquery-1.8.1.min.js"></script>
    <script src="/static/admin/js/bootstrap.min.js"></script>

</head>
<body>
<!-- 上 -->
<div class="navbar">
    <div class="navbar-inner">
        <div class="container-fluid">
            <ul class="nav pull-right">
                <li id="fat-menu" class="dropdown">
                    <a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-user icon-white"></i> admin
                        <i class="icon-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a tabindex="-1" href="<?php echo url('admin/manager/setpwd'); ?>">修改密码</a></li>
                        <li class="divider"></li>
                        <li><a tabindex="-1" href="<?php echo url('admin/login/logout'); ?>">安全退出</a></li>
                    </ul>
                </li>
            </ul>
            <a class="brand" href="index.html"><span class="first">后台管理系统</span></a>
            <ul class="nav">
                <li class="active"><a href="javascript:void(0);">首页</a></li>
                <li><a href="javascript:void(0);">系统管理</a></li>
                <li><a href="javascript:void(0);">权限管理</a></li>
            </ul>
        </div>
    </div>
</div>
<!-- 左 -->
<div class="sidebar-nav">
    <?php foreach($top_nav as $k => $top_v): ?>
    <a href="#error-menu<?php echo $k; ?>" class="nav-header collapsed" data-toggle="collapse"><i class="icon-exclamation-sign"></i><?php echo $top_v['auth_name']; ?></a>
    <ul id="error-menu<?php echo $k; ?>" class="nav nav-list collapse">
        <?php foreach($sec_nav as $sec_v): if($sec_v['pid'] == $top_v['id']): ?>
        <li><a href="<?php echo url($sec_v->auth_c . '/' . $sec_v->auth_a); ?>"><?php echo $sec_v['auth_name']; ?></a></li>
        <?php endif; endforeach; ?>
    </ul>
    <?php endforeach; ?>
</div>


    <!-- 右 -->
    <div class="content">
        <div class="header">
            <h1 class="page-title">角色列表</h1>
        </div>

        <div class="well">
        <a class="btn btn-primary" href="<?php echo url('create'); ?>">新增</a>
            <!-- table -->
            <table class="table table-bordered table-hover table-condensed">
                <thead>
                    <tr>
                        <th>编号</th>
                        <th>用户组(角色名称)</th>
                        <th>权限id</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($list as $k=>$v): ?>
                    <tr class="success">
                        <td><?php echo $k+1; ?></td>
                        <td><?php echo $v['role_name']; ?></td>
                        <td><?php echo $v['role_auth_ids']; ?></td>
                        <td>
                            <a href="<?php echo url('setauth', ['id' => $v['id']]); ?>"> 分派权限 </a>
                            <a href="<?php echo url('edit', ['id' => $v['id']]); ?>"> 编辑 </a>
                            <a href="javascript:void(0);" onclick="if(confirm('确认删除？')) location.href='<?php echo url('delete', ['id' => $v['id']]); ?>'"> 删除 </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- footer -->
        <footer>
            <hr>
            <p>© 2017 <a href="javascript:void(0);" target="_blank">ADMIN</a></p>
        </footer>
    </div>


</body>
</html>