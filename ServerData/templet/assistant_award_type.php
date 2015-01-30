<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### assistant_award

$list = $dbh->query("select `id`, `vip`, `times`, `name`, `skill`, `card_num`,`long_yu_ling` from `assistant_award`;");

$hash = "";

for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [".$item["vip"].", ".$item["times"].", \"".$item["name"]."\", ".$item["skill"].", ".$item["card_num"].", ".$item["long_yu_ling"]."]";
	

}

### 类

$str = "package com.assist.server
{
	public class AssistantAwardType
	{
		// id : [description, vip]
		private static const Data : Object = {
".$hash."
		};
		
        /**
         * 获取宝箱ID数组
         */
        public static function getIdList () : Array
        {
            var ary : Array = [];
            for(var strId : String in Data)
            {
                ary.push(int(strId));
            }
            return ary;
        }
        
		/**
		 * 获取vip
		 * @param id int
		 */
		public static function getVIP (id : int) : int
		{
			return Data[id] ? Data[id][0] : 0;
		}
        
        /**
		 * 获取次数
		 * @param id int
		 */
		public static function getTimes (id : int) : int
		{
			return Data[id] ? Data[id][1] : 0;
		}
       
        /**
		 * 获取名字
		 * @param id int
		 */
		public static function getName (id : int) : String
		{
			return Data[id] ? Data[id][2] : \"\";
		}
        
        /**
		 * 获取阅历
		 * @param id int
		 */
		public static function getSkill (id : int) : int
		{
			return Data[id] ? Data[id][3] : 0;
		}
        
        /**
		 * 获取玉牌个数
		 * @param id int
		 */
		public static function getCardNum (id : int) : int
		{
			return Data[id] ? Data[id][4] : 0;
		}
		
		  /**
		 * 获取龙鱼令
		 * @param id int
		 */
		public static function getLingNum (id : int) : int
		{
			return Data[id] ? Data[id][5] : 0;
		}
		
		
        
		/**
         * 获取玩家所能完成的最大次数
         */
        public static function getMaxTimes() : int
        {
            var intMax : int = 0;
            for each(var obj : Object in Data)
            {
                if(intMax < obj[1])
                    intMax = obj[1];
            }
            
            return intMax;
        }
		
		/**
		 * 根据vip等级获取玩家能领取的宝箱数量
		 */ 
		public static function getBoxCount(vipLevel : int) : int
		{
			var intCount : int = 0;
			for each(var obj : Object in Data)
			{
				if(obj[0] <= vipLevel)
					intCount ++;
			}
			
			return intCount;
		}
	}
}
";

file_put_contents($desc_dir."AssistantAwardType.as", addons().$str);

echo "[data] assistant_award  [Done]\n";
?>