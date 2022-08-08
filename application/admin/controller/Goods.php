<?php

namespace app\admin\controller;

use app\admin\model\GoodsAttr;
use think\Controller;
use think\Request;
class Goods extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $goods_name = request()->param('goods_name');
        if(!empty($goods_name)){
            $list = \app\admin\model\Goods::where('goods_name', 'like', "%$goods_name%")->order('id desc')->paginate(2,false, ['query'=>['goods_name'=>$goods_name]]);
        }else{
            //查询所有数据  返回二维数组，里面每一条数据是一个对象，这个对象可以当做数组来使用
            $list = \app\admin\model\Goods::order('id desc')->paginate(2);
        }
        //模板赋值和模板渲染
//        $this->assign('list', $list);
        return view('index', ['list' => $list]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //查询商品分类数据，用于页面下拉列表展示
        $category = \app\admin\model\Category::where('pid', 0)->select();
        //查询商品类型数据，用于商品属性栏 下拉列表展示
        $type = \app\admin\model\Type::select();
        return view('create', ['category' => $category, 'type' => $type]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //获取输入变量
//        $data = request()->param();
        //使用依赖注入的$request对象。
        $data = $request->param();
        //富文本编辑器字段做特殊处理
        $data['goods_introduce'] = $request->param('goods_introduce', '', 'remove_xss');
        //表单验证
        //定义验证规则
        $rule = [
            'goods_name' => 'require|max:100|unique:goods',
            'goods_price' => 'require|float|gt:0',
            'goods_number' => 'require|integer|gt:0',
            'cate_id' => 'require|integer|gt:0'
        ];
        //定义提示信息（可选）
        $msg = [
            'goods_name.require' => '商品名称不能为空',
            'goods_name.max' => '商品名称长度不能超过25',
            'goods_name.unique' => '商品名称已经存在',
            'goods_price.require' => '商品价格不能为空',
            'goods_price.float' => '商品价格必须是浮点数',
            'goods_price.gt' => '商品价格必须大于0',
            'goods_number.require' => '商品数量不能为空',
            'goods_number.integer' => '商品数量必须是整数',
            'goods_number.gt' => '商品数量必须大于0',
            'cate_id.require' => '商品分类必须选择'
        ];
        //检测
        $validate = new \think\Validate($rule, $msg);
        if (!$validate->check($data)) {
            //调用validate的getError方法获取到错误信息
            //调用控制器的error方法，进行报错的页面跳转
            $this->error($validate->getError());
        }
        //商品logo图片上传
        $data['goods_logo'] = $this->upload_logo();
        //数据添加到数据表 create方法第二个参数true表示过滤非数据表中的字段
        $goods = \app\admin\model\Goods::create($data, true);
        //进行商品相册图片上传
        $this->upload_pics($goods->id);
        //商品属性值关联保存 tpshop_goods_attr表
        $attrs = $data['attr_name'];
        //结果数组
        $goodsattrs = [];
        foreach ($attrs as $k => $v) {
            //$k 就是 attr_id   $v是一个数组，包含多个 属性值
            foreach ($v as $value) {
                //$value是一个属性值
                //组装一条数据
                $row = [
                    'goods_id' => $goods->id,
                    'attr_id' => $k,
                    'attr_value' => $value
                ];
                //将组装的数据放到结果数组，用于批量添加
                $goodsattrs[] = $row;
            }
        }
        //批量添加到tpshop_goods_attr表
        $goodsattrmodel  = new \app\admin\model\GoodsAttr;
        $goodsattrmodel->saveAll($goodsattrs);
        //页面跳转 商品列表页
        $this->success('添加成功', 'index');
    }

    /**
     * 商品logo图片上传
     */
    private function upload_logo()
    {
        //获取文件信息（对象）
        $file = request()->file('goods_logo');
        if (empty($file)) {
            //必须上传商品logo图片
            $this->error('必须上传商品logo图片');
        }
        //将文件移动到指定的目录（public 目录下  uploads目录）
        $info = $file->validate(['size' => 5*1024*1024, 'ext' => ['jpg', 'png', 'gif', 'jpeg']])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if (empty($info)) {
            //上传出错
            $this->error($file->getError());
        }
        //拼接图片的访问路径
        $logo = DS . "uploads" . DS . $info->getSaveName();
        //生成缩略图
        $image = \think\Image::open('.' . $logo);
        //调用thumb方法生成缩略图并保存（直接覆盖原始图片）
        $image->thumb(200, 200)->save('.' . $logo);
        return $logo;
    }
    /**
     * 显示指定的资源(详情页)
     *
     * @param  int  $id
     * @return \think\Response
     */

    /**
     * 商品相册图片上传
     * @param $id
     * @return \think\response\View
     */
    private function upload_pics($goods_id)
    {
        //获取文件信息（多文件，获取到的是一个数组）
        $files = request()->file('goods_pics');
        //可以支持，在添加商品时，不上传相册图片，而是在修改功能当中上传。
//        if(empty($files)){
//            $this->error('没有文件被上传');
//        }
        //初始化结果数组
        $data = [];
        foreach($files as $file){
            // 对每一个相册图片 逐一进行上传操作
            $info = $file->validate(['size' => 5*1024*1024, 'ext' => ['jpg', 'jpeg', 'png', 'gif']])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                //拼接原始图片的路径   '20180315/fsdahdakfdsal.png'
                $pics = DS . 'uploads' . DS . $info->getSaveName();
                //获取子目录名称和原始图片名称
                $temp = explode(DS, $info->getSaveName());//$temp[0]  $temp[1]
                //生成三张不同规格的缩略图（800*800， 350*350， 50*50）
                $image = \think\Image::open( '.' . $pics);
                //生成800*800 大图
                $pics_big = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_big_' . $temp[1];
                $image->thumb(800, 800)->save('.' . $pics_big);
                //生成350*350 中图
                $pics_mid = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_mid_' . $temp[1];
                $image->thumb(350, 350)->save('.' . $pics_mid);
                //生成50*50 小图
                $pics_sma = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_sma_' . $temp[1];
                $image->thumb(50, 50)->save('.' . $pics_sma);

                //组装一条数据
                $row = [
                    'goods_id' => $goods_id,
                    'pics_big' => $pics_big,
                    'pics_mid' => $pics_mid,
                    'pics_sma' => $pics_sma,
                ];
//                $row = compact('goods_id', 'pics_big', 'pics_mid', 'pics_sma');
                //将一条数据放到结果数组中，用于最后批量添加
                $data[] = $row;
            }
        }
        //批量添加数据
        $goodspics = new \app\admin\model\Goodspics;
        $goodspics->saveAll($data);
    }

    public function read($id)
    {
        //查询一条数据
        $info = \app\admin\model\Goods::find($id);
        $this->assign('info', $info);
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
        //查询原始商品数据
        $info = \app\admin\model\Goods::find($id);
        //查询商品分类数据
        //所有一级
        $category_one = \app\admin\model\Category::where('pid', 0)->select();
        //当前三级
        $category_three_info = \app\admin\model\Category::find($info['cate_id']);
        //当前二级
        $category_two_info = \app\admin\model\Category::find($category_three_info['pid']);
        //当前一级下所有二级
        $category_two = \app\admin\model\Category::where('pid', $category_two_info['pid'])->select();
        //当前二级下所有三级
        $category_three = \app\admin\model\Category::where('pid', $category_three_info['pid'])->select();
        $this->assign([
           'category_one' => $category_one,
            'category_two_info' => $category_two_info,
            'category_three_info' => $category_three_info,
            'category_two' => $category_two,
            'category_three' => $category_three
        ]);
        //查询商品相册数据
        $goodspics = \app\admin\model\Goodspics::where('goods_id', $id)->select();
        //查询商品类型数据，用于商品属性栏 下拉列表展示
        $type = \app\admin\model\Type::select();
        //查询商品所属分类下属性名称信息
        $attr = \app\admin\model\Attribute::where('type_id', $info->type_id)->select();
        foreach($attr as &$v){
            $v = $v->getData();
            $v['attr_values'] = explode(',', $v['attr_values']);
        }
        unset($v);
        //查询商品关联的属性值信息
        $goodsattr = \app\admin\model\GoodsAttr::where('goods_id', $info->id)->select();
        //根据页面需求，转化格式  ['attr_id' => ['attr_value1', 'attr_value2']]
        //['7' => [['attr_id' => 7, 'attr_value'=>'澳大利亚']]，'5'=>[[], [], [], []]]
        foreach($goodsattr as $v){
            $new_goodsattr[$v->attr_id][] = $v->attr_value;
            // ['5'=>['原味']]  ['5'=>['原味'， '奶油']]   ['5'=>['原味'， '奶油']，'7'=>['澳大利亚']]
        }
        //模板赋值及模板渲染
        return view('edit', [
            'info' => $info,
            'goodspics' => $goodspics,
            'type' => $type,
            'attr' => $attr,
            'new_goodsattr' => $new_goodsattr
        ]);
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
        //获取输入变量
//        $data = input();
        $data = $request->param();
        //富文本编辑器字段做特殊处理
        $data['goods_introduce'] = $request->param('goods_introduce', '', 'remove_xss');
//        dump($data);die;
        //定义验证规则
        $rule = [
            'goods_name' => 'require|max:100',
            'goods_price' => 'require|float|gt:0',
            'goods_number' => 'require|integer|gt:0',
            'cate_id' => 'require|integer|gt:0'
        ];
        //定义提示信息（可选）
        $msg = [
            'goods_name.require' => '商品名称不能为空',
            'goods_name.max' => '商品名称长度不能超过25',
            'goods_price.require' => '商品价格不能为空',
            'goods_price.float' => '商品价格必须是浮点数',
            'goods_price.gt' => '商品价格必须大于0',
            'goods_number.require' => '商品数量不能为空',
            'goods_number.integer' => '商品数量必须是整数',
            'goods_number.gt' => '商品数量必须大于0',
            'cate_id.require' => '商品分类必须选择'
        ];
        //检测
        $validate = new \think\Validate($rule, $msg);
        if (!$validate->check($data)) {
            //调用validate的getError方法获取到错误信息
            //调用控制器的error方法，进行报错的页面跳转
            $this->error($validate->getError());
        }
        //商品logo图片修改
        $file = $request->file('goods_logo');
        if ($file) {
            //如果有logo图片上传，则进行修改logo图片地址操作
            $data['goods_logo'] = $this->upload_logo();
        }
        //进行修改操作  update方法返回值是模型对象， update(数据数组，更新条件, 允许字段)
        \app\admin\model\Goods::update($data, [], true);
        //相册图片继续上传
        $this->upload_pics($id);

        //商品属性值处理
        //删除已经关联的属性值
        GoodsAttr::where('goods_id', $id)->delete();
        //重新添加新传过来的属性值
        $attrs = $data['attr_name'];
        //结果数组
        $goodsattrs = [];
        foreach ($attrs as $k => $v) {
            //$k 就是 attr_id   $v是一个数组，包含多个 属性值
            foreach ($v as $value) {
                //$value是一个属性值
                //组装一条数据
                $row = [
                    'goods_id' => $id,
                    'attr_id' => $k,
                    'attr_value' => $value
                ];
                //将组装的数据放到结果数组，用于批量添加
                $goodsattrs[] = $row;
            }
        }
        //批量添加到tpshop_goods_attr表
        $goodsattrmodel  = new \app\admin\model\GoodsAttr;
        $goodsattrmodel->saveAll($goodsattrs);
        //修改成功，跳转到列表页
        $this->success('修改成功', 'index');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {

        //先查询再删除
        $goods = \app\admin\model\Goods::find($id);
        $goods->delete();
        //destroy方法
//        \app\admin\model\Goods::destroy($id);
        //页面跳转
        $this->success('删除成功', 'index');
    }

    /**
     * ajax删除相册图片
     */
    public function delpics($pics_id)
    {
//        $pics_id = request()->param('pics_id');
        //从相册表删除一条数据
        \app\admin\model\Goodspics::destroy($pics_id);
        return ['code' => 10000, 'msg' => '删除成功'];
    }

    //根据类型id获取类型下的属性名称信息
    public function getattr($type_id){
        //根据type_id查询tpshop_attribute表  类型下的属性名称信息
        $data = \app\admin\model\Attribute::where('type_id', $type_id)->select();

        foreach ($data as &$v) {
            //设置过获取器，这里想要得到原始数据
            $v = $v->getData();
            //根据页面需要，将attr_values由字符串分割为数组
            $v['attr_values'] = explode(',', $v['attr_values']);
        }
        return ['code' => 10000, 'msg' => 'success', 'data' => $data];
    }
}
