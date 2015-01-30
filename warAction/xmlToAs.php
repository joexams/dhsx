<?php
   require_once(dirname(__FILE__)."/monsters.php");
   
   /**
    * 抽出XML文件
	*/
	$url = dirname(__FILE__)."/changeData";
    function myglob ($path)
    {
	   $pathPattern = $path.'/*';
	   $f = glob($pathPattern);
	   
	   $len = count($f);
       for($i = 0; $i < $len; $i++)
	   {
	       $file = $f[$i];
		   renderXML($file, $path);
	   }
    }
	myglob($url);
	
	/**
     * 转意XML
	 */
	function renderXML ($fileName, $path) 
	{
	   $xml = simplexml_load_file($fileName);
       $len = count($xml);
	   
	   $newName = "";
	   $jsflName = "";
	   
	   // 获取标签名字
	   $jsflName = explode(".", $fileName);
	   $jsflName = $jsflName[0];
	   $pos = strpos($jsflName, " - ");
	   
	   $postLen = $pos + strlen($path) + 1;
	   $newName = str_replace(" - ", "", substr($jsflName, $postLen));
	   
	   $xmlList = array();
	   $strList = "";
	   $strListMini = "";
	   
	   
	   echo "read ".$newName. "-------". "\n";
	   
	   // XML转换后的数据
	   for($i = 0; $i < $len; $i++)
	   {
	   	   $attr = $xml -> data[$i] -> attributes();
		   $xmlList[$i] = $attr;
		   
		   if($attr["type"] == "STANDBY")
		   {
		       $standBy = $attr;
		   }
	   }

	   $xmlLen = count($xmlList);
	   
	   echo "length ----". $xmlLen. "\n" ;
	   if($xmlLen !=3 && $xmlLen != 5)
	   {
	       echo "num error \n";
	       return;
	   }
	   
	   for($i = 0; $i < $xmlLen; $i++)
	   {
		   $height [$i]["height"] = $xmlList[$i]["Height"];
		   $height [$i]["mainHeight"] = 0;
	       if($i > 0)
		   {
		       $height[$i]["mainHeight"] = $height[$i - 1]["mainHeight"] + $height[$i - 1]["height"];
		   }
		   $strList .= changeXMl($xmlList[$i], $height[$i]["mainHeight"], $standBy["dir"], 1);
		   $strListMini .= changeXMl($xmlList[$i], $height[$i]["mainHeight"], $standBy["dir"], 2);
	   }

	   global $monsters;
	   
	  // $newName = mb_convert_encoding($newName, "utf8", "gbk"); 

	   $signName = "";
	   $signName = $monsters[$newName];

   	   echo $newName. "----".$signName, " exists!\n";
	   renderAsData($standBy, $strList, $signName, 1);
	   renderAsData($standBy, $strListMini, $signName."Mini", 2);
	}
	
	/**
	 * 渲染XML数据
	 */
	function changeXMl ($xml, $h, $num, $mini)
	{
	    $xPoint = $xml["X"];
		$yPoint = $xml["Y"];
		$mainWidth = $xml["Frame"] * $xml["Width"];
		$mainHeight = $h;
		$width = $xml["Width"];
		
		if($num == 0)
		{
		    $xPoint = -($width - abs($xPoint));
		}
		
		$type = $xml["type"];
		$height = $xml["Height"];
		$frame = $xml["Frame"];
		$time = 1000 /$xml["Time"] + 4;
		$pointX = $xml["NameX"] * $num;
		$pointY = $xml["NameY"];
		$shadowX = $xml["shadowX"] * 0.01;
		$shadowY = $xml["shadowY"] * 0.01;
		$action = $xml["action"];
		
		if($action == "")
		{
		    
		}

		$as = "
            setDataFormat(Role." . $type. ",". round($width / $mini). ",". round($height/ $mini). ",". round($mainWidth/ $mini). ",". round($height/ $mini).","."new Point(0, ". round($mainHeight/ $mini)."), new Point(faceChanged ? ". -round($xPoint/ $mini).": ". round($xPoint/ $mini).", ". round($yPoint/ $mini).")". ", ". $action.", ". $time.");
			   ";
	    return $as;
	}

	/**
	 * 渲染AS夿
	 */
	function renderAsData ($standList, $strList, $signName, $mini)
	{
	    $lifeX = $standList["NameX"] / $mini;
		$lifeY = $standList["NameY"] / $mini;
		$shadowX = $standList["shadowX"] / $mini * 0.01;
		$shadowY = $standList["shadowY"] / $mini * 0.01;
		
		if($standList["dir"] == 0)
		{
		    $dir = "LEFT";
		}
		else
		{
		    $dir = "RIGHT";
		}
		
	   $as = "package com.assist.view.war.roles.monsters
{
	import com.assist.view.war.roles.Role;
	import flash.display.BitmapData;
	import flash.geom.Point;
				
	public class ". $signName." extends Role
    {
	    public function ". $signName." (bmp : BitmapData, frameRate : Number = 0)
		{
		    frameRate = 12;
			super(bmp, frameRate);
			_defaultFace = Role.". $dir.";
			_bloodTroughPosition = new Point(0, " . $lifeY.");
			changeFace(false);
		    this._shadowScaleX = " . $shadowX.";
			this._shadowScaleY = " . $shadowY.";
		}
			 
		override public function changeFace (faceChanged : Boolean) : void
		{
			_faceChanged = faceChanged;
			". $strList."
			showBloodTrough();
		}
	}
}";	        
	    
		append_to_asfile($as, $signName);
	}
	
	/**
    * 写入新文朿
	*/ 
	function append_to_asfile($buffer, $file)
	{
	   echo "write ".$file. "-------". "\n";
	   $newUrl = "../../client/com/assist/view/war/roles/monsters/". $file;
	   $fp = fopen($newUrl. ".as", "w");
	   $write = fputs($fp, $buffer);
	   fclose($fp);
	}
?>
















