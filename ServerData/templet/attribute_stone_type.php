<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### role

$list = $dbh->query("select `item_id`,
		    `lv`,
		    `war_attribute_type_id`,
		    `value`,
		    `need_item_id`,
		    `need_item_count`,
		    `src_item_id`,
		    `book_id`,
		    `change_need_coin`,
		    `out_need_coin`,
		    `merge_need_coin`
		     from `attribute_stone`;");

$hash = "";
$hash1 = "              ";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= "\n";
	}
	
	$hash .= "			["
	.$item["item_id"].", "
	.$item["lv"].", "
	.$item["war_attribute_type_id"].", "
	.$item["value"].", "
	.$item["need_item_id"].", "
	.$item["need_item_count"].", "
	.$item["src_item_id"].", "
	.$item["book_id"].", "
	.$item["change_need_coin"].", "
	.$item["out_need_coin"].", "
	.$item["merge_need_coin"]."],";
}

### 类

$str = "package com.assist.server
{
	public class AttributeStoneType
	{
	
	    /**
	     *item_id,
	     *lv,
	     *war_attribute_type_id,
	     *value,
	     *need_item_id,
	     *need_item_count,
	     *src_item_id,
	     *book_id,
	     *change_need_coin,
	     *out_need_coin,
	     *merge_need_coin
	     */
	    private static var _attributeStoneList : Array =
	    [
		
".$hash."	
	    ]
	    
	    /**
	     * 通过物品ID获取灵石信息
	     */
	     public static function getStoneDataForItemId (itemId : int) : Object
	     {
		    var len : int = _attributeStoneList.length;
		    for (var i : int = 0; i < len; i++)
		    {
			    var list : Array = _attributeStoneList[i];
			    if(list[0] == itemId)
			    {
			        var obj : Object = renderStoneData(list);
				    return obj;
			    }
		    }
		    
		    return {};
	     }
	     
	    /**
	     * 通过物品等级获取同等级信息
	     */
	     public static function getStoneDataForLv (lv : int) : Array
	     {
		    var len : int = _attributeStoneList.length;
		    var newList : Array = [];
		    for (var i : int = 0; i < len; i++)
		    {
			    var list : Array = _attributeStoneList[i];
			    if(list[1] == lv)
			    {
			    	var obj : Object = renderStoneData(list);
				     newList.push(obj);
			    }
		    }
		    
		    return newList;
	     }

            /**
	     * 通过获取同等级信息
	     * WATypeId = war_attribute_type_id
	     */
	     public static function getStoneDataForWATypeId(WATypeId : int) : Array
	     {
		    var len : int = _attributeStoneList.length;
		    var newList : Array = [];
		    for (var i : int = 0; i < len; i++)
		    {
			    var list : Array = _attributeStoneList[i];
			    if(list[2] == WATypeId)
			    {
			    	var obj : Object = renderStoneData(list);
				     newList.push(obj);
			    }
		    }
		    
		    return newList;
	     }
	     
	     /**
	      * 转换数据
	      */
	      private static function renderStoneData (list : Array) : Object
	      {
	      		var obj : Object = {};
			    obj.itemId = list[0];
			    obj.lv = list[1];
			    obj.warAttributeTypeId = list[2];
			    obj.value = list[3];
			    obj.needItemId = list[4];
			    obj.needItemCount = list[5];
			    obj.srcItemId = list[6];
			    obj.bookId = list[7];
			    obj.changeNeedCoin = list[8];
			    obj.outNeedCoin = list[9];
			    obj.mergeNeedCoin = list[10];
			    return obj;
	      }
	}
}
";

file_put_contents($desc_dir."AttributeStoneType.as", addons().$str);

echo "[data] attribute_stone_type  [Done]\n";
?>