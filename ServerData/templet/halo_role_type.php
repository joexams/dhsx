<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `id`, `sign`, `name`, `role_job_id` , `lock` from `role` where role_type = 2 order by `lock`;");


$hash = "";
$constant = "";
$buttle = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
		$buttle.=",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["sign"].", \"".$item["name"]."\", ".$item["role_job_id"].", ".$item["lock"]."]";
	
	$constant .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";";
	
	$buttle.= "             "."\"".$item["sign"]."\"";
}


### 类

$str = "package com.assist.server
{
	public class HaloRoleType
	{
		// role_id : [role_sign, name, role_job_id]
		private static const Roles : Object = {
".$hash."
		};
		
".$constant."
                
		public static const ButtleList : Array =
		[
".$buttle."
		]
		
		public static const DramaData : Object =
		{
			42 : { start :\"ZhouNianJuQing7.xml\", end : \"ZhouNianJuQing8.xml\"},
            43 : { start :\"ZhouNianJuQing9.xml\", end : \"ZhouNianJuQing10.xml\"},
            44 : { start :\"ZhouNianJuQing3.xml\", end : \"ZhouNianJuQing4.xml\"},
            45 : { start :\"ZhouNianJuQing1.xml\", end : \"ZhouNianJuQing2.xml\"},
            46 : { start :\"ZhouNianJuQing11.xml\", end : \"ZhouNianJuQing12.xml\"},
            47 : { start :\"ZhouNianJuQing5.xml\", end : \"ZhouNianJuQing6.xml\"},
            56 : { start :\"ZhouNianJuQing13.xml\", end : \"ZhouNianJuQing14.xml\"},
            57 : { start :\"ZhouNianJuQing15.xml\", end : \"ZhouNianJuQing16.xml\"}		
		}
		
		/**
		 * 获取角色id
		 *
		 * @param gender String
		 * @param sign : String
		 */
		public static function getRoleIdByMix (gender : String, role : String) : int
		{
			var sign : String = role + gender;
			
			return getRoleId(sign);
		}
		
		/**
		 * 获取角色id
		 * 
		 * @param sign String
		 */
		public static function getRoleId (sign : String) : int
		{
			for (var id : Object in Roles)
			{
				if (Roles[id][0] == sign)
				{
					return id as int;
				}
			}
			
			return 0;
		}
		
		/**
		 * 获取角色标识
		 *
		 * @param roleId int
		 */
		public static function getRoleSign (roleId : int) : String
		{
			if (Roles[roleId])
			{
				return Roles[roleId][0];
			}
			
			return \"\";
		}
		
		/**
		 * 获取角色标识列表
		 *
		 * @param roleIds Array
		 */
		public static function getRoleSigns (roleIds : Array) : Array
		{
			var list : Array = [];
			
			for each (var id : Object in roleIds)
			{
				var sign : String = getRoleSign(id as int);
				if (sign) list.push(sign);
			}
			
			return list;
		}
		
		/**
		 * 获取角色名称
		 * 
		 * @param roleId int
		 */
		public static function getRoleName (roleId : int) : String
		{
			return Roles[roleId] ? Roles[roleId][1] : \"\";
		}
		
		/**
		 * 获取角色名称
		 *
		 * @param sign String
		 */
		public static function getRoleNameByRoleSign (sign : String) : String
		{
			var name : String = \"\";
			for each (var arr : Array in Roles)
			{
				if (arr[0] == sign)
				{
					return arr[1];
				}
			}
			
			return name;
		}
		
		/**
		 * 通过角色名称获取角色标识
		 * 
		 * @param roleName String
		 */
		public static function getRoleSignByRoleName (roleName : String) : String
		{
			for each(var arr : Array in Roles)
			{
				if(arr[1] == roleName)
				{
					return arr[0];
				}
			}
			
			return \"\";
		}
		
		/**
		 * 通过角色id获取职业id
		 *
		 * @param roleId int
		 */
		public static function getJobIdByRoleId (roleId : int) : int
		{
			return Roles[roleId] ? Roles[roleId][2] : 0;
		}

		/**
		 * 通过角色id获取权值
		 * @param roleId int
		 */
		public static function getLockByRoleId (roleId : int) : int
		{
			return Roles[roleId] ? Roles[roleId][3] : 0;
		}
		
		/**
		 * 通过权值获取当前挑战的是第几个对手
		 * @param lock int
		 */
		public static function getRankByLock (buttleLock : int) : int
		{
		    var len : int = ButtleList.length;
			var rank : int = 0;
			for(var i : int = 0; i < len; i++)
			{
	             var sign : String = ButtleList[i];
			     var id : int = getRoleId(sign);
			     var lock : int = getLockByRoleId(id);
			     if(lock == buttleLock)
			     {
			        rank = i+1;
				    break;
			     }
			}
			return rank;
		}
		
		/**
		 * 获取开场剧情
		 * @param roleId int
		 */
		public static function getStartDrama (roleId : int) : String
		{
			return DramaData[roleId].start;
		}
		
		/**
		 * 获取结束剧情
		 * @param roleId int
		 */
		public static function getEndDrama (roleId : int) : String
		{
			return DramaData[roleId].end;
		}
	}
}
";

file_put_contents($desc_dir."HaloRoleType.as", addons().$str);

print repeat("[data] halo_role_type", 75, ".")."DONE.\n";
?>