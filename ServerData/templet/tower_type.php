<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### tower_layer

$list = $dbh->query("
	select
		`tl`.`layer` as `tl_layer`,
		`tl`.`sequence` as `tl_sequence`,
		`tl`.`monster_team_id` as `tl_monster_team_id`,
		`tl`.`award_experience` as `tl_award_experience`,
		`monster_id`
	from
		`tower_layer` `tl`
	left join `mission_monster_team` `mmt`
		on `mmt`.`id` = `tl`.`monster_team_id`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			[".$item["tl_layer"].", ".$item["tl_sequence"].", ".$item["tl_monster_team_id"].", ".$item["monster_id"].", ".$item["tl_award_experience"]."]";
}

### tower_layer_soul

$list = $dbh->query("select `layer`, `sequence`, `soul_quality_id`, `soul_location_id` from `tower_layer_soul`;");

$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			[".$item["layer"].", ".$item["sequence"].", ".$item["soul_quality_id"].", ".$item["soul_location_id"]."]";
}

### mission_monster_team

### 类

$str = "package com.assist.server
{
	public class TowerType
	{
		include \"./source/TowerTypeData0.as\";
		
		// [[layer, sequence, monster_team_id, monster_id, award_experience], ...]
		private static var _Layer : Array = null;
		
		public static function get Layer () : Array
		{
			if (_Layer == null) throw new Error(\"还未赋值！\");
			
			return _Layer;
		}
		
		public static function set Layer (value : Array) : void
		{
			if (_Layer != null) throw new Error(\"非法赋值\");
			
			_Layer = value;
		}
		
		// [[layer, sequence, soul_quality_id, soul_location_id], ...]
		private static var _LayerSoul : Array = null;
		
		public static function get LayerSoul () : Array
		{
			if (_LayerSoul == null) throw new Error(\"还未赋值！\");
			
			return _LayerSoul;
		}
		
		public static function set LayerSoul (value : Array) : void
		{
			if (_LayerSoul != null) throw new Error(\"非法赋值\");
			
			_LayerSoul = value;
		}
		
		/**
		 * 获取某层塔数据
		 * @param layer int
		 * @param sequence int
		 */
		private static function getLayerItem (layer : int, sequence : int) : Array
		{
			var obj : Array;
			
			var len : int = Layer.length;
			for (var i : int = 0; i < len; i++)
			{
				if (Layer[i][0] == layer && Layer[i][1] == sequence)
				{
					obj = Layer[i];
					break;
				}
			}
			
			return obj;
		}
		
		/**
		 * 获取某层塔的灵件数据
		 * @param layer int
		 * @param sequence int
		 */
		private static function getLayerSoulItem (layer : int, sequence : int) : Array
		{
			var obj : Array = [];
			
			var len : int = LayerSoul.length;
			for (var i : int = 0; i < len; i++)
			{
				if (LayerSoul[i][0] == layer && LayerSoul[i][1] == sequence)
				{
					obj.push(LayerSoul[i]);
				}
			}
			
			return obj;
		}
		
		/**
		 * 获取怪物团id
		 * @param layer int
		 * @param sequence int
		 */
		public static function getMonsterTeamId (layer : int, sequence : int) : int
		{
			var item : Array = getLayerItem(layer, sequence);
			return item ? item[2] : 0;
		}
		
		/**
		 * 获取怪物id
		 * @param layer int
		 * @param sequence int
		 */
		public static function getMonsterId (layer : int, sequence : int) : int
		{
			var item : Array = getLayerItem(layer, sequence);
			return item ? item[3] : 0;
		}
		
		/**
		 * 获取奖励经验
		 * @param layer int
		 * @param sequence int
		 */
		public static function getAwardExp (layer : int, sequence : int) : int
		{
			var item : Array = getLayerItem(layer, sequence);
			return item ? item[4] : 0;
		}
		
		/**
		 * 获取灵件id列表
		 *
		 * @param layer int
		 * 层
		 * @param sequence int
		 * 顺序
		 */
		public static function getSoulIdList (layer : int, sequence : int) : Array
		{
			var temp : Array = [];
			
			var list : Array = getLayerSoulItem(layer, sequence);
			for (var i : int = 0; i < list.length; i++)
			{
				var qualityId : int = list[i][2];
				var locationId : int = list[i][3];
				temp = temp.concat(SoulType.getSoulIdList(qualityId, locationId));
			}
			
			var arr : Array = [];
			
			temp = temp.sortOn(\"qualityId\", Array.NUMERIC | Array.DESCENDING);
			for (i = 0; i < temp.length; i++)
			{
				arr.push(temp[i][\"soulId\"]);
			}
			
			return arr;
		}
	}
}
";

file_put_contents($desc_dir."TowerType.as", addons().$str);
file_put_contents($desc_dir."source/TowerTypeData.as", addons()."package com.assist.server.source
{
	public class TowerTypeData
	{
		// [[layer, sequence, monster_team_id, monster_id, award_experience], ...]
		public static const Layer : Array = [
".$hash."
		];
		
		// [[layer, sequence, soul_quality_id, soul_location_id], ...]
		public static const LayerSoul : Array = [
".$hash1."
		];
	}
}
");

echo "[data] tower_type [Done]\n";
?>