<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### gold_body

$list = $dbh->query("SELECT * FROM gold_body");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	$hash .= "			[".$item["id"].",".$item["lev"].",\"".$item["name"]."\",".$item["color"].",".$item["force_addition"].",".$item["stunt_addition"].",".$item["magic_addition"].
					",".$item["trigger_skill_type"].",".$item["trigger_skill_value"].",".$item["max_lucky_value"].",".$item["times_consume"].",".$item["times_min_value"].
					",".$item["times_max_value"].",".$item["star_count"].",".$item["retrieve_count"]."]";       
					
}

### 类

$str = "package com.assist.server
{
	import com.assist.view.HtmlText;
	
	public class GoldBodyType
	{
		/**
		 *  id ID
			lev 等级
			name 金身名称
			color 品质
			force_addition 武力加成%
			stunt_addition 绝技加成%
			magic_addition 法术加成%
			trigger_skill_type 触发技能类型 1:降低施放技能所需气势 2:获得未乘坐坐骑属性 3:增加培养上限
			trigger_skill_value 触发技能值(如果是百分比的话单位为%)
			max_lucky_value 最大幸运值
			times_consume 每次升级消耗内丹值
			times_min_value 每次升级获得最小幸运值
			times_max_value 每次升级获得最大幸运值
			star_count 星级
			retrieve_count 当前等级回收的内丹数量
		 */		
		public static var GoldBodyArr : Array = [
".$hash."
		];
		
		/**
		 * 根据等级获取对应数据
		 */	
		public static function getDataByLv(lv:int):Array
		{
			for each(var list:Array in GoldBodyArr)
			{
				if(list[1] == lv)
				{
					return list;
					break;
				}
			}
			return [];
		}
		
		/**
		 * 根据品质获取颜色值
		 */
		public static function getColorByQuality(quality:int):uint
		{
			switch(quality)
			{
				case 1:
					return HtmlText.Green;
					break;
				case 2:
					return HtmlText.Blue2;
					break;
				case 3:
					return HtmlText.Purple;
					break;
				case 4:
					return HtmlText.Yellow;
					break;
			}
			return HtmlText.White;
		}
		
		/**
		 * 是否有下一级
		 */		
		public static function hasNextGoldBody(lv : int) : Boolean
		{
			var nextLv:int = lv + 1;
			for each(var list:Array in GoldBodyArr)
			{
				if(list[1] == nextLv)
				{
					return true;
					break;
				}
			}
			
			return false;
		}
		
		/**
		 * 获取已开放技能列表
		 */
		public static function getSkillList(lv:int):Array
		{
			var skillList:Array = [];
			for each(var list:Array in GoldBodyArr)
			{
				if(list[1] <= lv && list[7] > 0)
				{
					//技能类型，技能加成值
					skillList.push([list[7], list[8]]);
				}
			}
			return skillList;
		}
		
		/**
		 * 获取下一个可获得的技能
		 */
		public static function getNextSkill(lv:int):Array
		{
			var list:Array;
			var arr:Array;
			for(var i:int=0; i<GoldBodyArr.length; i++)
			{
				arr = GoldBodyArr[i];
				if(arr[1] > lv && arr[7] > 0)
				{
					//技能类型，技能加成值
					list = [arr[7],arr[8]];
					return list;
				}
			}
			return [];
		}
		
	}
		
}
";

file_put_contents($desc_dir."GoldBodyType.as", addons().$str);

echo("[data] GoldBodyType DONE\n");

?>
