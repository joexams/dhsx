<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `upgrade_level`, `item_type_id`, `item_quality_id`, `upgrade_price` from `item_upgrade_price`;");

$hash = ""; 
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			[".$item["upgrade_level"].",".$item["item_type_id"].",".$item["item_quality_id"].",".$item["upgrade_price"]."]";
}

### 类

$str = "package com.assist.server
{
	public class ItemUpgradePrice
	{
	
	// 强化等级， 物品类型ID, 物品品质, 强化价格
        private static const _upgradeLevel : Array =
        [
". $hash ."
        ]
	
         /**
	     * 连表查询价格
	     */
	     public static function checkUpGradePrice (upgradeLevel : int,
	                                               itemType : int,
						                           qualityId : int) : Number
	     {
		     if(_oldCheckPrice[upgradeLevel] != null
		      &&_oldCheckPrice[upgradeLevel][itemType] != null
		      && _oldCheckPrice[upgradeLevel][itemType][qualityId] != null)
		     {
		         return _oldCheckPrice[upgradeLevel][itemType][qualityId];
		     }
		
		     var len : int = _upgradeLevel.length;
		     var maxUpGradeLevel : int = _upgradeLevel[len - 1][0];
		     var upgradePrice : Number = 0;
		     for(var i : int = 0; i < len; i++)
		     {
		         var upList : Array = _upgradeLevel[i];
		         if(upList[0] <= maxUpGradeLevel)
		         {
			         if(upList[1] == itemType && upList[2] == qualityId)
			         {
			             upgradePrice = upList[3];
				         if(_oldCheckPrice[upList[0]] == null)
				         {
					         _oldCheckPrice[upList[0]] = {};
				         }  

				         if(_oldCheckPrice[upList[0]][upList[1]] == null)
				         {
			                 _oldCheckPrice[upList[0]][upList[1]] = {};
				         }
				     
			             _oldCheckPrice[upList[0]][upList[1]][upList[2]]
			                                          = upgradePrice;
			         }
		         }
		     }
		
		     return _oldCheckPrice[upgradeLevel][itemType][qualityId];
	     }
	
	    /**
	     * 缓存已查找过的价格
	     */
	     private static var _oldCheckPrice : Object = {};
		
	}
}
";

file_put_contents($desc_dir."ItemUpgradePrice.as", addons().$str);

echo "[data] item_upgrade_price  [Done]\n";
?>