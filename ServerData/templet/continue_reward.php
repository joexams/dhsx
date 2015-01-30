<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `day`, `coin`, `fame`, `skill` from `continue_reward`;");

$hash = "";
$hash1 = "              ";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
		$hash1 .= ",";
	}
	
	$hash .= "			".$item["day"]." : [\"".$item["coin"]."\",\"".$item["fame"]."\",\"".$item["skill"]."\"]";
	$hash1 .= $item["day"];
}

### 类

$str = "package com.assist.server
{
	public class ContinueRewardType
	{
	    // day
	    public static const AwardList : Array =
		[
".$hash1."			
		]
		
		// day : coin, fame, skill
		private static const AwardData : Object = {
".$hash."
		};
		
		/**
		 * 获取铜钱
		 *
		 * @param day int
		 */
		public static function getCoin (day : int) : int
		{
			return AwardData[day][0] || 0;
		}
		
		/**
		 * 获取声望
		 *
		 * @param day int
		 */
		public static function getFame (day : int) : int
		{
			return AwardData[day][1] || 0;
		}
		
		/**
		 * 获取阅历
		 *
		 * @param day int
		 */
		public static function getSkill (day : int) : int
		{
			return AwardData[day][2] || 0;
		}
	}
}
";

file_put_contents($desc_dir."ContinueRewardType.as", addons().$str);

echo "[data] continu_reward_type  [Done]\n";
?>