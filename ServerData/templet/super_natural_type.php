<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### resource_guide_tag

$list = $dbh->query("
SELECT
  e.id,
  e.passivity_stunt_id,
  e.name,
  e.level,
  e.value,
  e.value2,
  e.value3,
  e.description
FROM passivity_stunt_data e
");

$hash1 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash1 != "") {
		$hash1 .= ",\n";
	}
	
	$hash1 .= "			[".$item["id"].", ".$item["passivity_stunt_id"].", \""
	.$item["name"]."\", ".$item["level"].", ".$item["value"].", ".$item["value2"].", ".$item["value3"].", \"".$item["description"]."\""."]";
}

$list = $dbh->query("
SELECT
  e.id,
  e.name,
  e.description,
  e.sign,
  e.type
FROM passivity_stunt e
");
$hash2 = "";
$hash3 = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash2 != "") {
		$hash2 .= ",\n";
	}
	
	$hash2 .= "			[".$item["id"].", \"".$item["name"]."\", \"".$item["sign"]."\" ,\"".$item["description"]."\", ".$item["type"]."]";
	
	if($hash3 != "")
	{
		$hash3 .= "\n";
	}
	$hash3 .= "		public static const ".$item["sign"]." : String ='".$item["sign"]."';";
}

### 类

$str = "package com.assist.server
{
	public class SuperNaturalType
	{
		// [id, passivity_stunt_id, level, name, value, value2, value3,description]
		public static const DataSuperNatural : Array = [
".$hash1."
		];
		
		
		/**
		 *通过Key获取对应的typeId 
		 */		
		public static function getTypeIdByKey(intId:int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intId == ary[0])
				{
					return ary[1];
				};
			}
			return 0;
		}
		
		/**
		 *通过Key获取对应的level 
		 */	
		public static function getLevelByKey(intId:int) : int
		{
			
			for each(var ary:Array in DataSuperNatural)
			{
				if(intId == ary[0])
				{
					return ary[3];
				};
			}
			return 0;
		}
		
		/**
		 *通过Key获取对应的描述 
		 */
		public static function getDescByKey(intId:int) : String
		{
			var str:String = '';
			for each(var ary:Array in DataSuperNatural)
			{
				if(intId == ary[0])
				{
					return ary[7];
				};
			}
			return str;
		}

		/**
		 *通过Key获取下一等级的描述 
		 */
		public static function getNextDescByKey(intId:int) : String
		{
			var str:String = '';
			for each(var ary:Array in DataSuperNatural)
			{
				if(intId + 1 == ary[0] && getTypeIdByKey(intId) == getTypeIdByKey(intId + 1))
				{
					return ary[7];
				};
			}
			return str;
		}

		/**
		 * 根据神通id获取神通名称 
		 * @param intId
		 * @return 
		 * 
		 */		
		public static function getNameByKey(intId : int) : String
		{
			var cout : String = '';
			for each(var tempArr : Array in DataSuperNatural )
			{
				if(intId == tempArr[0])
				{
					cout = tempArr[2];
					break;
				}
			}
			return cout;
		}
		
		
		/**
		 *通过技能Id,获取对应的等级Id 
		 */
		public static function getSupernaturalJobIdByKey(intId:int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intId == ary[0])
				{
					return getJobIdByType(ary[1]);
				};
			}
			return 0;
		}

		/**
		*根据技能type返回jobId
		*/
		public static function getJobIdByType(type : int) : int
		{
			for each(var ary : Array in DataSuperType)
			{
				if(type == ary[0])
				{
					return ary[4];
				};
			}
			return 0;
		}
		
		/**
		 *通过技能分类Id,分类等级获取对应的typeId 
		 */		
		public static function getId(intJodId:int, intJobLevel:int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intJodId == ary[1] && intJobLevel == ary[2])
				{
					return ary[0];
				};
			}
			return 0;
		};
		
		/**
		 *通过技能分类Id,分类等级获取对应的typeId 
		 */		
		public static function getTypeId(intJodId:int, intJobLevel:int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intJodId == ary[1] && intJobLevel == ary[2])
				{
					return ary[3];
				};
			}
			return 0;
		};
		
		/**
		 *通过技能Id,技能等级获取Key 
		 */
		public static function getIdByTypeIdLevel(intTypeId:int, intLevel:int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intTypeId == ary[1] && intLevel == ary[3])
				{
					return ary[0];
				};
			}
			return 0;
		}
		
		/**
		 *通过技能Id,技能等级获取JobId
		 */
		public static function getJobIdByTypeIdLevel(intTypeId:int, intLevel:int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intTypeId == ary[3] && intLevel == ary[4])
				{
					return ary[1];
				};
			}
			return 0;
		}
		
		/**
		 *通过技能Id,技能等级获取SLevel
		 */
		public static function getSLevelByTypeIdLevel(intTypeId:int, intLevel:int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intTypeId == ary[3] && intLevel == ary[4])
				{
					return ary[2];
				};
			}
			return 0;
		}
		
		/**
		 *通过技能分类Id,分类等级获取对应的等级Id 
		 */
		public static function getSupernaturalLevel(intJodId:int, intJobLevel:int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intJodId == ary[1] && intJobLevel == ary[2])
				{
					return ary[4];
				};
			}
			return 0;
		};
		
		/**
		 *通过技能分类Id,分类等级获取对应的等级Id 
		 */
		public static function getSupernaturalLevelByKey(id: int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(id == ary[0])
				{
					return ary[3];
				};
			}
			return 0;
		};
		
		/**
		 *通过技能分类Id,分类等级获取对应的名字
		 */
		public static function getName(intJodId:int, intJobLevel:int) : String
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intJodId == ary[1] && intJobLevel == ary[2])
				{
					return ary[6];
				};
			}
			return null;
		};
		
		/**
		 *通过技能分类Id,分类等级获取对应的描述 
		 */		
		public static function getDesc(intJodId:int, intJobLevel:int) : String
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intJodId == ary[1] && intJobLevel == ary[2])
				{
					return ary[7];
				};
			}
			return null;
		};
			
		
		/**
		 *获取描述  通过type_id 和 level获取对应描述 
		 */		
		public static function getDescByTypeLevel(intTypeId:int, intLevel:int) : String
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intTypeId == ary[1] && intLevel == ary[3])
				{
					return ary[7];
				};
			}
			return null;
		}
		
		/**
		 *通过key获取对应的Jobid 
		 */		
		public static function getJobIdByKey(intKey:int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intKey == ary[0])
				{
					return getJobIdByType(ary[1]);
				}
			}
			return 0;
		}
		
		/**
		 *通过key获取对应的s_level
		 */		
		public static function getSLevelByKey(intKey:int) : int
		{
			for each(var ary:Array in DataSuperNatural)
			{
				if(intKey == ary[0])
				{
					return ary[2];
				};
			}
			return 0;
		}
		
		public static const superNaturalJob : Array = [
			[1, 'ZhanShen', '战神'],
			[2, 'JinGang', '金刚'],
			[3, 'XianFa', '仙法']
		];
		public static const ZhanShen : String='ZhanShen';
		public static const JinGang : String='JinGang';
		public static const XianFa : String='XianFa';

		/**
		 *获取对应的名字 
		 */
		public static function getNameByType(intTypeId:int) : String
		{
			for each(var ary:Array in DataSuperType)
			{
				if(ary[0] == intTypeId)
				{
					return ary[1];
				}
			}
			
			return \"\";
		};

		public static function getSuperNaturalJobIdBySign(sign : String) : int
		{
			for each(var tempArr : Array in superNaturalJob)
			{
				if(tempArr[1] == sign)
				{
					return tempArr[0];
				}
			}
			return -1;
		}

		public static function getSuperNaturalJobSignById(id : int) : String
		{
			for each(var tempArr : Array in superNaturalJob)
			{
				if(tempArr[0] == id)
				{
					return tempArr[1];
				}
			}
			return \"\";
		}

		/**
		 * 根据SuperNaturalJob Id 获取名字 
		 * @param id
		 * @return 
		 * 
		 */		
		public static function getSuperNaturalJobNameById(id : int) : String
		{
			for each(var tempArr : Array in superNaturalJob)
			{
				if(tempArr[0] == id)
				{
					return tempArr[2];
				}
			}
			return '';
		}
		
		/**
		 * 根据SuperNaturalJob Id 获取名字 
		 * @param id
		 * @return 
		 * 
		 */		
		public static function getSuperNaturalJobNameBySign(sign : String) : String
		{
			for each(var tempArr : Array in superNaturalJob)
			{
				if(tempArr[1] == sign)
				{
					return tempArr[2];
				}
			}
			return '';
		}

		public static const DataSuperType : Array = [
".$hash2."
		];
		
".$hash3."
		
		/**
		 *每个技能对应的标识 
		 */	
		public static function getSign(intTypeId:int) : String
		{
			for each(var ary:Array in DataSuperType)
			{
				if(ary[0] == intTypeId)
				{
					return ary[2];
				}
			}
			
			return null;
		};

		public static function getSuperTypeIdBySign(sign : String) : int
		{
			for each(var tempArr : Array in DataSuperType)
			{
				if(tempArr[1] == sign)
				{
					return tempArr[0];
				}
			}
			return -1;
		}
	}   
}
";

file_put_contents($desc_dir."SuperNaturalType.as", addons().$str);

print repeat("[data] supernatural", 75, ".")."DONE.\n";
?>