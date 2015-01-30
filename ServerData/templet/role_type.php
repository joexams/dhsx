<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("SELECT `id`, `sign`, `name`, `role_job_id`, `role_type`, `fame`, `strength`, `agile`, `intellect`, `initial_health`, `role_stunt_id`, `introduction` FROM `role`;");

$hash = "";
$constant = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$constant .= "\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["sign"].", \"".$item["name"]."\", ".$item["role_job_id"].", ".$item["role_type"].", ".$item["fame"].", "
                         .$item["strength"].", ".$item["agile"].", ".$item["intellect"].", ".$item["initial_health"].", ".$item["role_stunt_id"].", \"".$item["introduction"]."\"]";
	
	$constant .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";";
}

### role_job

$list = $dbh->query(
"SELECT
  r.id,
  r.sign,
  r.name,
  j.critical,
  j.dodge,
  j.hit,
  j.block,
  j.break_block,
  j.break_critical,
  j.kill
FROM role_job AS r
  LEFT JOIN role_job_level_data AS j
    ON r.id = j.job_id
      AND j.level = 1"
);

$hash1 = "";
$constant1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
		$constant1 .= "\n";
	}
	
	$hash1 .= "			".$item["id"]." : [".$item["sign"].", \"".$item["name"]."\", ".$item["critical"].", ".$item["dodge"].", ".$item["hit"].", ".
                        $item["block"].", ".$item["break_block"].", ".$item["break_critical"].", ".$item["kill"]."]";
	
	$constant1 .= "		public static const ".$item["sign"]." : String = \"".$item["sign"]."\";";
}

### role_job_level_data

$job_levels = array();

$list = $dbh->query("
	select `job_id`, `level`, `require_exp`
	from `role_job_level_data`
	order by `job_id`, `level`
");
$len = count($list);
for ($i = 0; $i < $len; $i++) {
	$item = $list[$i];
	
	if (! array_key_exists($item["job_id"], $job_levels)) {
		$job_levels[$item["job_id"]] = array();
	}
	
	array_push($job_levels[$item["job_id"]], $item["require_exp"]);
}

file_put_contents($client_dir."assets/templet/role/role_job_level_data.txt", json_encode($job_levels));

$list = $dbh->query("SELECT	* FROM role_job_change_to_role_job");
$len = count($list);
$hash3 = "";
for ($i = 0; $i < $len; $i++) {
	$item = $list[$i];
	
	if ($hash3 != "") {
		$hash3 .= ",\n";
	}
	
	$hash3 .= "			[".$item["from_role_job_id"].",".$item["to_role_job_id"]."]";
}

### 类

$str = "package com.assist.server
{
	import com.assist.view.HtmlText;
	public class RoleType
	{
		include \"source/RoleTypeData0.as\";
		
		// role_id : [role_sign, name, role_job_id, role_type, fame, strength, agile, intellect, initial_health, role_stunt_id]
		private static const Roles : Object = {
".$hash."
		};
		
".$constant."
		
		// job_id : [job_sign, name, critical, dodge, hit, block, break_block, break_critical, kill]
		private static const Jobs : Object = {
".$hash1."
		};
		
		//[旧角色id，新角色id]
		public static const ChangeList:Array = [
".$hash3."
		];

".$constant1."
		
		// 男
		public static const Nan : String = \"Nan\";
		
		// 女
		public static const Nv : String = \"Nv\";
		
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
		*获取旧的角色标识
		*/
		public static function getOldRoleSign(roleId : int) : String
		{
			if (Roles[roleId])
			{
				return changeSign(Roles[roleId][0]);
			}
			
			return \"\";
		}
		
		/**
		 * 是否新角色标识 
		 * @param mSign
		 * @return 
		 */
		public static function isNewRoleSign(mSign : String) : Boolean
		{
			if(mSign == \"JianLingNanFeiYu\" ||
				mSign==\"JianLingNanWuSheng\" ||
				mSign==\"JianLingNvFeiYu\" ||
				mSign==\"JianLingNvWuSheng\" ||
				mSign==\"WuShengNanFeiYu\" ||
				mSign==\"WuShengNanJianLing\" ||
				mSign==\"WuShengNvFeiYu\" ||
				mSign==\"WuShengNvJianLing\" ||
				mSign==\"FeiYuNanWuSheng\" ||
				mSign==\"FeiYuNanJianLing\" ||
				mSign==\"FeiYuNvJianLing\" ||
				mSign==\"FeiYuNvWuSheng\")
				return true;
			return false;
		}
		
		/**
		 * 新标识转为久标识
		*/
		public static function changeSign(mSign : String) : String
		{
			//资源替换
			if(mSign==\"JianLingNanFeiYu\")
			{
				mSign=\"FeiYuNan\";
			}
			if(mSign==\"JianLingNanWuSheng\")
			{
				mSign=\"WuShengNan\";
			}
			if(mSign==\"JianLingNvFeiYu\")
			{
				mSign=\"FeiYuNv\";
			}
			if(mSign==\"JianLingNvWuSheng\")
			{
				mSign=\"WuShengNv\";
			}
			if(mSign==\"WuShengNanFeiYu\")
			{
				mSign=\"FeiYuNan\";
			}
			if(mSign==\"WuShengNanJianLing\")
			{
				mSign=\"JianLingNan\";
			}
			if(mSign==\"WuShengNvFeiYu\")
			{
				mSign=\"FeiYuNv\";
			}
			if(mSign==\"WuShengNvJianLing\")
			{
				mSign=\"JianLingNv\";
			}
			if(mSign==\"FeiYuNanWuSheng\")
			{
				mSign=\"WuShengNan\";
			}
			if(mSign==\"FeiYuNanJianLing\")
			{
				mSign=\"JianLingNan\";
			}
			if(mSign==\"FeiYuNvJianLing\")
			{
				mSign=\"JianLingNv\";
			}
			if(mSign==\"FeiYuNvWuSheng\")
			{
				mSign=\"WuShengNv\";
			}
			return mSign;
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
		 * 获取职业标识
		 *
		 * @param jobId int
		 */
		public static function getJobSign (jobId : int) : String
		{
			return Jobs[jobId] ? Jobs[jobId][0] : \"\";
		}
		
		/**
		 * 获取职业标识
		 *
		 * @param roleId int
		 */
		public static function getJobSignByRoleId (roleId : int) : String
		{
			var jobId : int = getJobIdByRoleId(roleId);
			return getJobSign(jobId);
		}
		
		/**
		 * 获取职业名称
		 * 
		 * @param jobId int
		 */
		public static function getJobName (jobId : int) : String
		{
			return Jobs[jobId] ? Jobs[jobId][1] : \"\";
		}
		
        /**
		 * 通过角色id获取邀请伙伴所需的声望
		 *
		 * @param roleId int
		 */
		public static function getRoleFame (roleId : int) : int
		{
			return Roles[roleId] ? Roles[roleId][4] : 0;
		}
        
		/**
		 * 通过角色标识获取职业id
		 * 
		 * @param sign String
		 */
		public static function getJobIdByRoleSign (sign : String) : int
		{
			var roleId : int = getRoleId(sign);
			
			return getJobIdByRoleId(roleId);
		}
		
		/**
		 * 通过职业标识获取职业id
		 * 
		 * @param sign String
		 */
		public static function getJobIdByJobSign (sign : String) : int
		{
			for (var id : Object in Jobs)
			{
				if (Jobs[id][0] == sign)
				{
					return id as int;
				}
			}
			
			return 0;
		}
		
		/**
		 * 通过角色标识获取职业名称
		 * 
		 * @param sign String
		 */
		public static function getJobNameByRoleSign (sign : String) : String
		{
			var jobId : int = getJobIdByRoleSign(sign);
			
			return getJobName(jobId);
		}

		/**
		 * 通过职业标识获取职业名称
		 * 
		 * @param sign String
		 */
		public static function getJobNameByJobSign (sign : String) : String
		{
			var jobId : int = getJobIdByJobSign(sign);
			
			return getJobName(jobId);
		}
		
		/**
		 * 是否为主角色
		 *
		 * @param sign String
		 */
		public static function isMainRole (sign : String) : Boolean
		{
			return (
				JianLingNan   == sign
				|| JianLingNv == sign
				|| WuShengNan == sign
				|| WuShengNv  == sign
				|| FeiYuNan   == sign
				|| FeiYuNv    == sign
			);
		}

		/**
		 * 通过新角色id找旧的角色id
		 *
		 * @param sign String
		 */
		public static function getOldJobRoleId (id:int) : int
		{
			for(var i:int = 0;i < ChangeList.length;i++)
			{
				if((ChangeList[i][1] == id)  && ChangeList[i][0] < 7)
				{
					return ChangeList[i][0];
				}
			}
			return id;
		}
		
		/**
		 * 根据新角色id找到旧职业Id 
		 * @param id
		 * @return 
		 * 
		 */		
		public static function getOldJobIdByRoleId(id : int) : int
		{
			return RoleType.getJobIdByRoleId(RoleType.getOldJobRoleId(id));
		}
		
		/**
		 * 是否新角色
		 *
		 * @param sign String
		 */
		public static function isNewJobRole (id:int) : Boolean
		{
			for(var i:int = 0;i < ChangeList.length;i++)
			{
				if((ChangeList[i][1] == id))
				{
					return true;
				}
			}
			return false;
		}
		
		/**
		 * 获取性别
		 * @param roleId int
		 */
		public static function getRoleGender (roleId : int) : String
		{
			var sign : String = getRoleSign(roleId);
			
			//return /Nan$/.test(sign) ? Nan : Nv;
			return (sign.indexOf(Nan) > 0) ? Nan : Nv;
		}
        
		/**
		 * 获取角色类型
		 * @param roleId int
		 */
		public static function getRoleType (roleId : int) : int
		{
			if (Roles[roleId])
			{
				return Roles[roleId][3];
			}			
			return 0;
		}
		
		/**
		 * 是否紫色伙伴
		 * @param roleId int
		 */
		public static function isPurpleRole (roleId : int) : Boolean
		{
			return getRoleType(roleId) == 1;
		}
        
        /**
		 * 通过角色id获取初始普攻
		 *
		 * @param roleId int
		 */
		public static function getRoleStrength (roleId : int) : int
		{
			return Roles[roleId] ? Roles[roleId][5] : 0;
		}
		
		/**
		 * 通过角色id获取初始绝攻
		 *
		 * @param roleId int
		 */
		public static function getRoleAgile (roleId : int) : int
		{
			return Roles[roleId] ? Roles[roleId][6] : 0;
		}
		
		/**
		 * 通过角色id获取初始法攻
		 *
		 * @param roleId int
		 */
		public static function getRoleIntellect (roleId : int) : int
		{
			return Roles[roleId] ? Roles[roleId][7] : 0;
		}
		
		/**
		 * 通过角色id获取初始血量
		 *
		 * @param roleId int
		 */
		public static function getRoleInitialHealth (roleId : int) : int
		{
			return Roles[roleId] ? Roles[roleId][8] : 0;
		}     
        
        		/**
		 * 通过角色id获取初始血量
		 *
		 * @param roleId int
		 */
		public static function getRoleStuntId (roleId : int) : int
		{
			return Roles[roleId] ? Roles[roleId][9] : 0;
		}   
        
		/**
		 * 获取职业暴击
		 * @param roleId int
		 */
		public static function getJobCritical (jobId : int) : int
		{
			return Jobs[jobId] ? Jobs[jobId][2] : 0;
		}

		/**
		 * 获取职业闪避
		 * @param roleId int
		 */
		public static function getJobDodge (jobId : int) : int
		{
			return Jobs[jobId] ? Jobs[jobId][3] : 0;
		}
		
		/**
		 * 获取职业命中
		 * @param roleId int
		 */
		public static function getJobHit (jobId : int) : int
		{
			return Jobs[jobId] ? Jobs[jobId][4] : 0;
		}
		
		/**
		 * 获取职业格挡
		 * @param roleId int
		 */
		public static function getJobBlock (jobId : int) : int
		{
			return Jobs[jobId] ? Jobs[jobId][5] : 0;
		}
		
		/**
		 * 获取职业破击
		 * @param roleId int
		 */
		public static function getJobBreakBlock (jobId : int) : int
		{
			return Jobs[jobId] ? Jobs[jobId][6] : 0;
		}
		
		/**
		 * 获取职业韧性
		 * @param roleId int
		 */
		public static function getJobBreakCritical (jobId : int) : int
		{
			return Jobs[jobId] ? Jobs[jobId][7] : 0;
		}
		
		/**
		 * 获取职业必杀
		 * @param roleId int
		 */
		public static function getJobKill (jobId : int) : int
		{
			return Jobs[jobId] ? Jobs[jobId][8] : 0;
		}
		
		/**
		 * 通过角色id获取邀请伙伴信息
		 *
		 * @param roleId int
		 */
		public static function getRoleInfo (roleId : int) : String
		{
			return Roles[roleId] ? Roles[roleId][10] : \"\";
		}
		
		/**
		 * 通过伙伴id获取伙伴品质颜色
		 * */
		public static function getRoleColorById (id:int):uint
		{
			var roleType :int = getRoleType(id);
			if(roleType == 1)
			{
				return HtmlText.Purple;
			}
			else if(roleType == 3)
			{
				return HtmlText.Yellow;
			}
			return HtmlText.White;
		}
		
    }
}
";

file_put_contents($desc_dir."RoleType.as", addons().$str);

echo "[data] role_type [Done]\n";
?>