<?php
namespace app\home\model;

use think\Model;
class Cart extends Model{

	//封装加入购物车的方法
	//控制器调用模型的方法时，需要传递三个参数
	public static function addCart($goods_id, $number, $goods_attr_ids){
		//将购物数据添加到购物车 根据登录状态判断
		if(session('?user_info')){
			//已登录，数据添加到数据表
			//获取用户id
			$user_id = session('user_info.id');
			//添加之前，先判断数据表中是否已经存在相关购物记录
			$where = array(
				'user_id' => $user_id,
				'goods_id' => $goods_id,
				'goods_attr_ids' => $goods_attr_ids
			);
			$data = self::where($where) -> find();
			if($data){
				//已经存在相同的购物记录，累加购买数量
				$data['number'] += $number;
				//重新保存
				$data->save();
				return true ;
			}else{
				//不存在相同的购物记录，直接添加
				$data = array(
					'user_id' => $user_id,
					'goods_id' => $goods_id,
					'goods_attr_ids' => $goods_attr_ids,
					'number' => $number
				);
				self::create($data);
				return true;
			}
		}else{
			//未登录，
			//先获取原始的cookie中的购物车数据，以cart名称获取
			$data = cookie('cart') ? unserialize( cookie('cart') ) : array();
			//将当前的购物记录，添加到$data中，一个键值对: 'goods_id - goods_attr_ids' => number
			$key = $goods_id . '-' . $goods_attr_ids;
			//判断$data中是否有相同的记录
			if(isset($data[$key])){
				$data[$key] += $number;
			}else{
				$data[$key] = $number;
			}
			//得到新的数据，将序列化保存到cookie  3600*24 = 86400
			cookie('cart', serialize($data), 86400 * 7);
			return true;
		}

	}

	//获取所有购物车数据的方法
	public static function getAllCart(){
		//根据登录状态判断
		if(session('?user_info')){
			//已登录，查询数据表
			$user_id = session('user_info.id');
			$data = self::where(['user_id' => $user_id])->select();
			return $data;
		}else{
			//未登录，查询cookie
			$cart = cookie('cart') ? unserialize( cookie('cart') ) : array();
			//cookie中的数据，读取之后是一维数组，将之转化为和数据表中取的数据格式一样
			$data = [];
			foreach($cart as $k => $v){
				//$k  goods_id - goods_attr_ids   $v number
				$temp = explode('-', $k);
				$goods_id = $temp[0];
				$goods_attr_ids = $temp[1];
				$number = $v;
				//组装成一条数据
				$row = array(
					'id' => '',
					'goods_id' => $goods_id,
					'goods_attr_ids' => $goods_attr_ids,
					'number' => $number
				);
				$data[] = $row;
			}
			return $data;
		}
	}

	//修改指定购物记录的购买数量
	public static function changeNum($goods_id, $number, $goods_attr_ids){
		//根据登录状态判断
		if(session('?user_info')){
			//已登录 修改数据表 根据user_id  goods_id  goods_attr_ids 为条件确定一条唯一的记录
			$user_id = session('user_info.id');
			//条件
			$where = array(
				'user_id' => $user_id,
				'goods_id' => $goods_id,
				'goods_attr_ids' => $goods_attr_ids
			);
			//要修改的数据
			$data = array(
				'number' => $number
			);
			self::where($where)->update($data);
			return true;
		}else{
			//未登录 修改cookie
			$cart = cookie('cart') ? unserialize( cookie('cart') ) : array();
			//拼接当前要修改的记录 key
			$key = $goods_id . '-' . $goods_attr_ids;
			//修改数量
			$cart[$key] = $number;
			//将修改后的数组保存到cookie中
			cookie('cart', serialize($cart), 86400 * 7);
			return true;
		}
	}

	//删除购物车中指定的记录
	public static function delCart($goods_id, $goods_attr_ids){
		//根据登录状态判断
		if(session('?user_info')){
			//已登录，删除数据表数据
			$user_id = session('user_info.id');
			//条件
			$where = array(
				'user_id' => $user_id,
				'goods_id' => $goods_id,
				'goods_attr_ids' => $goods_attr_ids
			);
			//删除数据
			self::where($where)->delete();
			return true;
		}else{
			//未登录，从cookie中删除
			$cart = cookie('cart') ? unserialize( cookie('cart') ) : array();
			//拼接要删除的记录的key
			$key = $goods_id . '-' . $goods_attr_ids;
			//从数组中删除一个键值对
			unset($cart[$key]);
			//重新保存到cookie
			cookie('cart', serialize($cart), 86400 * 7);
			return true;
		}
	}

	//迁移cookie购物车数据到数据表
	public static function cookieTodb(){
		//从cookie取出数据
		$cart = cookie('cart') ? unserialize( cookie('cart') ) : array();
		if(empty($cart)){
			return;
		}
		//$cart 中是一个一个键值对  goods_id - goods_attr_ids => number
		foreach($cart as $k => $v){
			$temp = explode('-', $k);
			$goods_id = $temp[0];
			$goods_attr_ids = $temp[1];
			$number = $v;
			//由于当前是已登录状态，可以直接调用 addCart 加入购物车的方法即可
			self::addCart($goods_id, $number, $goods_attr_ids);
		}
		//清除cookie中的购物车数据
		cookie('cart', null);
	}
}