<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `reel_id`, `item_id`, `item_number`, `item_description`, `mission_id` from `facture_reel_stuff`;");

$list1 = $dbh->query("select `reel_id`, `item_id`, `item_number`, `ingot` from `facture_reel_product`;");

$hash = ""; 
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			[".$item["reel_id"].",".$item["item_id"].",".$item["item_number"].",\"".$item["item_description"]."\",".$item["mission_id"]."]";
}

$hash1 = ""; 
for ($i = 0; $i < count($list1); $i++) {
	$item = $list1[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			".$item["reel_id"]." : [".$item["item_id"].",".$item["item_number"].",".$item["ingot"]."]";
}

### 类

$str = "package com.assist.server
{
	public class FactureReelType
	{
	
	// 卷轴ID， 物品ID， 物品数量， 物品信息， 副本ID
        private static const _factureReelList : Array =
        [
".$hash ."
        ]
	
	// 卷轴ID， 物品ID， 物品数量， 花费元宝
        private static const _reelProductData : Object =
        {
".$hash1 ."
        }
	
        
	    /**
	     * 输入卷轴ID获取对应的物品信息
	     */
	    public static function getFactureItem (reelId : int) : Array
	    {
	        if(_factureData[reelId])
	        {
		        return _factureData[reelId];
	        }
	    
	        var len : int = _factureReelList.length;
	        for(var i : int = 0; i < len; i++)
	        {
		        var list : Array = _factureReelList[i];
				var obj : Object = {};
				obj.reelId = list[0];
				obj.itemId = list[1];
				obj.itemNumber = list[2];
				obj.itemDescription = list[3];
				obj.missionId = list[4];
				
				if(_factureData[obj.reelId] == null)
				{
					_factureData[obj.reelId] = [];
				}
				
				_factureData[obj.reelId].push(obj);
	        }
	    
	        return _factureData[reelId] || [];
	    }
	    
	    // 缓存查询过的数据 (KEY : REEL_ID)
	    private static var _factureData : Object = {};
	    
	     /**
	     * 输入物品ID获取对应卷轴
	     */
	    public static function getFactureReel (itemId : int) : Array
	    {
	        if(_factureItemData[itemId])
	        {
		        return _factureItemData[itemId];
	        }
	    
	        var len : int = _factureReelList.length;
	        for(var i : int = 0; i < len; i++)
	        {
		        var list : Array = _factureReelList[i];
				var obj : Object = {};
				obj.reelId = list[0];
				obj.itemId = list[1];
				obj.itemNumber = list[2];
				obj.itemDescription = list[3];
				obj.missionId = list[4];
				
				if(_factureItemData[obj.itemId] == null)
				{
					_factureItemData[obj.itemId] = [];
				}
				
				_factureItemData[obj.itemId].push(obj);
	        }
	    
	        return _factureItemData[itemId] || [];
	    }
	    
	    // 缓存查询过的数据 (KEY : ITEM_ID)
	    private static var _factureItemData : Object = {};
	    
	    /**
	     * 通过卷轴ID获取成品ID
	     */
	     public static function getItemId (reelId : int) : int
	     {
		    return _reelProductData[reelId][0] || 0;
	     }
	     
	    /**
	     * 通过卷轴ID获取物品数量
	     */
	     public static function getItemNumber (reelId : int) : int
	     {
		    return _reelProductData[reelId][1] || 0;
	     }
	     
	    /**
	     * 通过卷轴ID获取需要花费的元宝数量
	     */
	     public static function getIngot (reelId : int) : int
	     {
		    return _reelProductData[reelId][2] || 0;
	     }
	}
}
";

file_put_contents($desc_dir."FactureReelType.as", addons().$str);

echo "[data] facture_reel  [Done]\n";
?>