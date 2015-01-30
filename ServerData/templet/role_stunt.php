<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role_stunt

$list = $dbh->query("select `id`, `sign`, `name`, `description` from `role_stunt`;");

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["sign"]."\", \"".$item["name"]."\", \"".$item["description"]."\"]";
	
	$constant .= "		// ".$item["name"]."\n";
	$constant .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";";
}

### 角色对应战法

$signs = array();

function concat ($list, $is_role) {
	global $signs;
	
	for ($i = 0; $i < count($list); $i++) {
		$item = $list[$i];
		
		if (array_key_exists($item["sign"], $signs) == false)
		{
			$signs[$item["sign"]] = array(0, 0);
		}
		
		if ($is_role)
		{
			$signs[$item["sign"]][0] = $item["role_stunt_id"];
		}
		else
		{
			$signs[$item["sign"]][1] = $item["role_stunt_id"];
		}
	}
}

$list1 = $dbh->query("select `id`, `sign`, `role_stunt_id` from `role`");
$list2 = $dbh->query("select `id`, `sign`, `role_stunt_id` from `monster`");

#var_export($list2);exit;

concat($list1, true);
concat($list2, false);

$stunt = "";
foreach ($signs as $sign => $list) {
	if ($stunt != "") {
		$stunt .= ",\n";
	}
	
	$stunt .= "			".repeat($sign, 20)." : [".$list[0].", ".$list[1]."]";
}

#print $stunt;exit;

### 类

$str = "package com.assist.server
{
	import com.assist.view.war.roles.RoleAction;
	
	public class RoleStunt
	{
		// 由 package com.assist.server.source.RoleStuntData.Stunts 设置
		// role_stunt_id : [stunt_sign, name, content]
		private static var _Stunts : Object = null;
		
		public static function get Stunts () : Object
		{
			if (_Stunts == null) throw new Error(\"还未赋值！\");
			
			return _Stunts;
		}
		
		public static function set Stunts (value : Object) : void
		{
			if (_Stunts != null) throw new Error(\"非法赋值\");
			
			_Stunts = value;
		}
		
".$constant."

        // 神兵技能
		public static const SBDu : String = \"SBDu\";
		public static const SBQiShi : String = \"SBQiShi\";
		public static const SBHuiFuMin : String = \"SBHuiFuMin\";
		public static const SBHuiFuMax : String = \"SBHuiFuMax\";
		public static const SBHuDun : String = \"SBHuDun\";
		public static const SBFuHuo : String = \"SBFuHuo\";
		public static const HLSummer : String = \"HLSummer\";
		public static const ShenJia : String = \"ShenJia\";
		
		// 主角被动技能
		public static const BDDecStuntHurt : String = \"BDDecStuntHurt\";
		public static const BDDecMomentum : String = \"BDDecMomentum\";
		public static const BDAttackPoisioning : String = \"BDAttackPoisioning\";
		public static const BDPartnerDead : String = \"BDPartnerDead\";
		public static const BDIncHealth : String = \"BDIncHealth\";
		public static const BDCantDead : String = \"BDCantDead\";
		public static const BDRoleInvincible : String = \"BDRoleInvincible\";
		
		// 附加状态
		public static const Reel : String = \"Reel\";
		
		public static const YueShiMark : String = \"YueShiMark\";  
		
		public static const TaiMeiDeShangTongMark : String = \"TaiMeiDeShangTongMark\";
		
		public static const TianMeiMark : String = \"TianMeiMark\";
		
		public static const TianShaGuXingMark : String = \"TianShaGuXingMark\";
		
		public static const BeiShuiYiZhanMark : String = \"BeiShuiYiZhanMark\";
		
		public static const HunDunZhenYaMark : String = \"HunDunZhenYaMark\";
		public static const FengTianYinDiMark : String = \"FengTianYinDiMark\";
		
		public static const GuoPoJingJueMark : String = \"GuoPoJingJueMark\";
		public static const FengHuaJueDaiMark : String = \"FengHuaJueDaiMark\";
		
		public static const WanMuZhiChunMark : String = \"WanMuZhiChunMark\";
		
		//召唤替补
		public static const SummerTiBu : String = \"SummerTiBu\";
		
		public static const QiSiHuiSheng : String = \"QiSiHuiSheng\";
		public static const QiSiHuiShengFirst : String = \"QiSiHuiShengFirst\";
		
		public static const DragonEnemy : String = \"DragonEnemy\";
		public static const DragonFriend : String = \"DragonFriend\"
		
		// 龙珠
		public static const DBGaiLvJiSha : String = \"DBGaiLvJiSha\";
		public static const DBHuiFuShengMing : String = \"DBHuiFuShengMing\";
		public static const DBJianShaoShangHai : String = \"DBJianShaoShangHai\";
		public static const DBZongXiangJianFang : String = \"DBZongXiangJianFang\";
		public static const DBQiang : String = \"DBQiang\";
		public static const DBJianShaoGongJi : String = \"DBJianShaoGongJi\";
		
		public static const QiShiErBianDiDan : String = \"QiShiErBianDiDan\";
		public static const DBJiangQiShi : String = \"DBJiangQiShi\";
		public static const DBJiaQiShi : String = \"DBJiaQiShi\";
		
		// 由 package com.assist.server.source.RoleStuntData.RoleWithStunt() 设置
		// role_sign : [`role`.`role_stunt_id`, `monster`.`role_stunt_id`]
		private static var _RoleWithStunt : Object = null;
		
		public static function get RoleWithStunt () : Object
		{
			if (_RoleWithStunt == null) throw new Error(\"还未赋值！\");
			
			return _RoleWithStunt;
		}
		
		public static function set RoleWithStunt (value : Object) : void
		{
			if (_RoleWithStunt != null) throw new Error(\"非法赋值\");
			
			_RoleWithStunt = value;
		}
		
		//----------------------------------------------------------------------
		//
		//  逻辑处理
		//
		//----------------------------------------------------------------------
		
		public static function getStuntSign (id : int) : String
		{
			return _Stunts[id] ? _Stunts[id][0] : \"\";
		}
		
		/**
		 * 战法名称
		 * @param id int
		 */
		public static function getStuntName (stuntSign : String) : String
		{
			var name : String = \"\";
			for each (var item : Object in _Stunts)
			{
				if (item[0] == stuntSign)
				{
					name = item[1];
					break;
				}
			}
			
			return name;
		}
		
		/**
		 * 战法描述
		 * @param id int
		 */
		public static function getStuntDescription (id : int) : String
		{
			return _Stunts[id] ? _Stunts[id][2] : \"\";
		}

		/**
		 * 名字2
		 * @param id int
		 */
		public static function getStuntName2(id : int) : String
		{
			return _Stunts[id] ? _Stunts[id][1] : \"\";
		}
		
		public static function getRoleStunt (sign : String) : String
		{
			var stuntId : int = RoleWithStunt[sign][0];
			return stuntId ? Stunts[stuntId][0] : \"\";
		}
		
		public static function getMonsterStunt (sign : String) : String
		{
			var stuntId : int = RoleWithStunt[sign][1];
			return stuntId ? Stunts[stuntId][0] : \"\";
		}
		
		//----------------------------------------------------------------------
		//
		//  加载资源用
		//
		//----------------------------------------------------------------------
		
		/**
		 * 获取角色对应的战法
		 *
		 * @param roleSigns Array
		 */
		public static function getRoleStunts (roleSigns : Array) : Array
		{
			var stunts : Array = getStuntsBase(roleSigns, 0);
			
			if (stunts.indexOf(MengJi) == -1)
			{
				stunts.push(MengJi);
			}
			
			return stunts;
		}
		
		/**
		 * 获取怪物的战法
		 * 
		 * @param roleSigns Array
		 */
		public static function getMonsterStunts (roleSigns : Array) : Array
		{
			return getStuntsBase(roleSigns, 1);
		}
		
		private static function getStuntsBase (roleSigns : Array, index : int) : Array
		{
			var stunts : Array = [];
			var temp : Object = {};
			
			var len : int = roleSigns.length;
			for (var i : int = 0; i < len; i++)
			{
				var list : Array = RoleWithStunt[roleSigns[i]];
				if (list == null) continue;
				var roleStuntId : int = list[index];
				
				if (roleStuntId)
				{
					var stuntSign : String = Stunts[roleStuntId][0];
					
					if (! temp[stuntSign])
					{
						stuntSign = RoleAction.sameStunt(stuntSign);
						temp[stuntSign] = true;
						
						stunts.push(stuntSign);
						
						var tempSign : String = RoleAction.addonEffect(stuntSign);
						if (tempSign != \"\")
						{
							stunts.push(tempSign);
						}
					}
				}
			}
			
			return stunts;
		}
		
		/**
		 * 附加的战法
		 */
		public static function addonRoleStunts () : Array
		{
			return [MengJi];
		}
	}
}
";

file_put_contents($desc_dir."RoleStunt.as", addons().$str);

file_put_contents($desc_dir."source/RoleStuntData.as", addons()."package com.assist.server.source
{
	public class RoleStuntData
	{
		// role_stunt_id : [stunt_sign, name, content]
		public static const Stunts : Object = {
".$hash."
		};
		
		// role_sign : [`role`.`role_stunt_id`, `monster`.`role_stunt_id`]
		public static const RoleWithStunt : Object = {
".$stunt."
		};
		
		/*
		public static function init () : void
		{
			RoleStunt.Stunts = Stunts;
			RoleStunt.RoleWithStunt = RoleWithStunt;
			
			RoleStunt.init();
		}
		*/
	}
}
");

echo "[data] role_stunt [Done]\n";
?>