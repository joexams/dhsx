<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}
### dream_role
$list = $dbh->query("
	SELECT
		`role_levelup_info`.`role_id` AS d1,
		`role_levelup_info`.`levelup_role_id` AS d2,
		`role_levelup_info`.`favor_item_id` AS d3,
		`role_levelup_info`.`need_favor_value` AS d4,
		`role`.`strength` AS r1,
		`role`.`agile` AS r2,
		`role`.`intellect` AS r3,
		`role`.`initial_health` AS r4,
		`role`.`role_stunt_id` AS r5,
		`role_levelup_info`.`award_aura` AS d5,
		`role_levelup_info`.`award_strength` AS d6,
		`role_levelup_info`.`award_agile` AS d7,
		`role_levelup_info`.`award_intellect` AS d8,
		`role_levelup_info`.`star` AS d9
	FROM
		`role_levelup_info`,`role`
	WHERE `role`.`id`=`role_levelup_info`.`levelup_role_id`
");
$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash != "") {
		$hash .= ",\n";
	}
	$list1 =  $dbh->query("
	SELECT `strength` as r1,`agile` as r2,`intellect` as r3,`initial_health` as r4,`role_stunt_id` as r5 FROM `role` WHERE `id` =".$item["d1"]."
	");
	$item1 = $list1[0];
	$hash .= "			[".$item["d1"].", ".$item["d2"].",".$item["d3"].",".$item["d4"].", ".$item["r1"].", ".$item["r2"].", ".$item["r3"].", ".$item["r4"].", ".$item["r5"].", ".$item1["r1"].", ".$item1["r2"].", ".$item1["r3"].", ".$item1["r4"].", ".$item1["r5"].",".$item["d5"].",".$item["d6"].",".$item["d7"].",".$item["d8"].",".$item["d9"]."]";
	
}
$list = $dbh->query("
	SELECT
		`role_id` AS f1,
		`content` AS f2,
		`type` AS f3
	FROM
		`furnace_talk`
");
$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	$hash1 .= "			[".$item["f1"].", '".$item["f2"]."',".$item["f3"]."]";
	
}

$list = $dbh->query("
	SELECT
		`src_item_id` AS s1,
		`dst_item_id` AS s2
		FROM
		`favor_item_regular`
");
$hash2 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	$hash2 .= "			[".$item["s1"].", ".$item["s2"]."]";
}
file_put_contents($desc_dir."source/FurnaceTypeData.as", addons()."package com.assist.server.source
{
	public class FurnaceTypeData
	{
		// [旧伙伴ID, 新伙伴ID,喜好品Id,好感要求， 武力，绝技，法术，生命，绝招 ,旧伙伴（武力，绝技，法术，生命，绝招）,挑战后奖励(武魂，对应伙伴武力，绝技，法术)]
		public static const roleList : Array = [
".$hash."
		];
		// [伙伴ID,对话内容,标示]
		public static const roleChat : Array = [
".$hash1."
		];
		// [材料Id,合成品Id]//为0表示命格碎片
		public static const favorItemList : Array = [
".$hash2."
		];
	}
}
");
### 类

$str = 'package com.assist.server
{
	import com.assist.server.source.FurnaceTypeData;
	public class FurnaceType
	{
		public static const ItemLevel:int = 89;//多少级获得黄玉牌
		public static const PurpleItemId:int = 346;//紫玉牌Id
		public static const NvWaShiItemId:int = 1269;//女娲石Id
		private static var _roleList:Array;
		private static var _favItemList:Array;
		public function FurnaceType():void
		{
		}
		public static function clear():void
		{
			_roleList = null;
			_favItemList = null;
		}
		public static  function get roleList():Array
		{
			if(_roleList)
			{
				return _roleList;
			}
			var arr:Array = FurnaceTypeData.roleList;
			var rList:Array=new Array();
			var rArr:Array = new Array();
			for(var i:int = 0;i < arr.length;i++)
			{
				rArr = arr[i]
				var idx:int = 0;
				var obj:Object = new Object();
				obj.canLevel = true;
				obj.roleId = rArr[idx++];
				obj.sign = RoleType.getRoleSign(obj.roleId);
				obj.name = RoleType.getRoleName(obj.roleId);
				obj.have_amount = 0; // 已赠送喜好品数量;
				
				var childObj:Object = new Object();
				childObj.roleId = rArr[idx++];
				childObj.star = rArr[rArr.length - 1];//推荐指数
				obj.itemId = rArr[idx++];//需要喜好品Id
				obj.needItems = rArr[idx++];//需要喜好品数量
				
				var chat:String = FurnaceType.getChat(obj.roleId,FurnaceType.COMMONFLAG);
				obj.chatList = chat.split("|");
				chat = FurnaceType.getChat(obj.roleId,FurnaceType.LEVELFLAG);
				obj.levelChat = chat;
				
				childObj.sign = RoleType.getRoleSign(childObj.roleId);
				childObj.name = RoleType.getRoleName(childObj.roleId);
				childObj.itemId = obj.itemId;
				childObj.strength = rArr[idx++];
				childObj.agile = rArr[idx++];
				childObj.intellect = rArr[idx++];
				childObj.initial_health = rArr[idx++];
				childObj.canLevel = false;
				childObj.wared = false;
				var role_stunt_id:int = rArr[idx++];
				var role_stunt_sine:String=RoleStunt.getStuntSign(role_stunt_id);
				
				childObj.role_stunt_Name =RoleStunt.getStuntName( role_stunt_sine);
				childObj.stunt_content = RoleStunt.getStuntDescription(role_stunt_id);//战法描述
				
				chat = FurnaceType.getChat(childObj.roleId,FurnaceType.COMMONFLAG);
				childObj.chatList = chat.split("|");
				chat = FurnaceType.getChat(childObj.roleId,FurnaceType.LEVELFLAG);
				childObj.levelChat = chat;
				childObj.have_amount = 0;
				childObj.inBody = false;
				obj.childObj = childObj;
				obj.text = "测试测试";
				
				//旧伙伴基础信息

				obj.strength = rArr[idx++];
				obj.agile = rArr[idx++];
				obj.intellect = rArr[idx++];
				obj.initial_health = rArr[idx++];
				role_stunt_id = rArr[idx++];
				role_stunt_sine=RoleStunt.getStuntSign(role_stunt_id);
				
				obj.role_stunt_Name =RoleStunt.getStuntName( role_stunt_sine);
				obj.stunt_content = RoleStunt.getStuntDescription(role_stunt_id);//战法描述
				obj.inBody = false;
				rList.push(obj);
			}
			
			return _roleList = rList;
		}
		
		/**
		 * 获取可制作喜好品id
		 * */
		public static function get favItemIdList():Array
		{
			if(_favItemList)
			{
				return _favItemList;
			}
			_favItemList=new Array();
			var arr:Array = FurnaceTypeData.favorItemList;
			var temList:Array = new Array();
			for(var i:int = 0 ; i< arr.length;i++)
			{
				var fArr:Array = arr[i];
				var bool:Boolean = false;
				for(var j:int = 0; j<_favItemList.length;j++)
				{
					var obj1:Object = _favItemList[j];
					if(obj1.oldId == fArr[0])
					{
						obj1.list.push(fArr[1]);
						bool = true;
						break;
					}
				}
				if(bool)
				{
					continue;
				}
				var obj2:Object = new Object();
				obj2.oldId = fArr[0];
				obj2.list=[fArr[1]];
				_favItemList.push(obj2);
			}
			return _favItemList;
		}
		/**
		 * 通过材料id获得喜好品产物
		 * */
		public static function getItemByOldId(id:int):Array
		{
			for(var i:int=0;i<favItemIdList.length;i++)
			{
				if(favItemIdList[i].oldId == id)
				{
					return _favItemList[i].list.concat();
				}
			}
			return new Array();
		}
		/**
		 * 通过喜好品获得对应伙伴名字 
		 * */
		public static function getRoleNameByFavId(id:int):String
		{
			for(var i:int=0;i<roleList.length;i++)
			{
				if(roleList[i].itemId == id)
				{
					return roleList[i].name;
				}
			}
			return "";
		}
		/**
		 * 通过id获取伙伴
		 * */
		public static function getRoleObjById(roleId:int):Object
		{
			for(var i:int = 0; i < roleList.length; i++)
			{
				if(roleList[i].roleId == roleId)
				{
					return roleList[i];
				}
			}
			return new Object;
		}
		
		/**
		 * 通过伙伴id和表示搜索对话文本
		 * */
		public static var COMMONFLAG:int = 0;
		public static var LEVELFLAG:int = 1;
		public static function getChat(roleId:int,flag:int):String
		{
			var roleChatList:Array = FurnaceTypeData.roleChat;
			for(var i:int = 0; i < roleChatList.length; i++)
			{
				var arr:Array = roleChatList[i];
				if(arr[0]==roleId && flag == arr[2])
				{
					return arr[1];
				}
			}
			return "";
		}
		
		/**
		 * 材料和喜好品对应
		 * */
		private static var _itemList:Array;
		public static function get itemList():Array
		{
			if(_itemList)
			{
				return _itemList;
			}
			_itemList = new Array();
			var arr:Array = FurnaceTypeData.favorItemList;
			for(var i:int =0; i < arr.length; i++)
			{
				var obj:Object = null;
				for(var j:int = 0; j < _itemList.length; j++)
				{
					if(_itemList[j].srcItemId == arr[i][0])
					{
						obj = _itemList[j];
						break;
					}
				}
				if(!obj)
				{
					obj = new Object();
					obj.srcItemId = arr[i][0];
					obj.dscItemList = new Array();
					_itemList.push(obj);
				}
				obj.dscItemList.push(arr[i][1]);
			}
			return _itemList;
		}
		
		/**
		 * 通过新伙伴Id获取喜好品Id
		 * */
		public static function getFavItemIdByRole(roleId:int):int
		{
			var arr:Array = FurnaceTypeData.roleList;
			for(var i:int = 0;i < arr.length; i++)
			{
				if(arr[i][1] == roleId)
				{
					return arr[i][2];
				}
			}
			return 0;
		}
		/**
		 * 通过新伙伴Id来找对应的挑战成功的奖励
		 * */
		public static function getAwardByRoleId(roleId:int):Array
		{
			var awardArr:Array = [];
			var arr:Array = FurnaceTypeData.roleList;
			for(var i:int = 0; i < arr.length; i++)
			{
				if(arr[i][1] == roleId)
				{
					awardArr = [arr[i][14],arr[i][15],arr[i][16],arr[i][17]];
				}
			}
			return awardArr;
		}
		
		/**
		 * 返回是不是可以升级伙伴
		 * */
		public static function isCanLvRole(roleId:int):Boolean
		{
			var arr:Array = FurnaceTypeData.roleList;
			for(var i:int = 0; i < arr.length; i++)
			{
				if(arr[i][0] == roleId)
				{
					return true;
				}
			}
			return false;
		}
		
		/**
		 * 通过新的伙伴获取对应旧伙伴Id
		 * */
		public static function getOldRoleIdById(roleId:int):int
		{
			var arr:Array = FurnaceTypeData.roleList;
			for(var i:int = 0; i < arr.length; i++)
			{
				if(arr[i][1] == roleId)
				{
					return arr[i][0];
				}
			}
			return 0;
		}
	}
}';
file_put_contents($desc_dir."FurnaceType.as", addons().$str);

echo "[data] furnace_type  [Done]\n";
?>