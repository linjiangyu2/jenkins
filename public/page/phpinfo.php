<?php
if(file_exists('./static.html')){
    if(time() - filemtime('./static.html') < 60){
        //跳转访问静态页面
//        header('location:http://www.tpshop.com/page/static.html');
    }
}
//开启ob缓存
ob_start();
//输出
phpinfo();
//从ob缓存中获取并清空数据
$html = ob_get_clean();
//$html = ob_get_contents();
//echo $html;
//将获取到的html数据写入一个静态html文件
file_put_contents('./static.html', $html);
//跳转访问静态页面
//header('location:http://www.tpshop.com/page/static.html');
echo $html;