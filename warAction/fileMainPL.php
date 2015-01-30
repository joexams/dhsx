<?php
   require_once(dirname(__FILE__)."/mainRoles.php");
   $url = "../../../美术成品/人物/player";
   
   $pngFile = "";
   
   myglob($url, $url);
   function myglob ($folder, $path, $bool = false)
   {
       $path_pattern = $path.'/*';
	   $f = glob($path_pattern);
	   $len = count($f);

	   if($bool == false)
	   {
	       $datafile = str_replace(" - ", "", substr($folder, 1));
	   }
	   else
	   {
	       $datafile = $folder;
	   }
	   
		
	   //是否空文件夹
	   if($len <= 4)
	   {
	       echo "isEmpty". "\n";
	       return;
	   }

	   $dataList = array();
	   $dataList["png"] = null;
	   $dataList["oldUpData"] = null;
	   
	   $dataList["待机"] = null;
	   $dataList["战场待机"] = null;
	   $dataList["攻击"] = null;
	   $dataList["被攻击"] = null;
	   $dataList["蹲下"] = null;
	   $dataList["防御"] = null;
	   
	   $dataList["待机Timer"] = null;
	   $dataList["攻击Timer"] = null;
	   $dataList["被攻击Timer"] = null;
	   $dataList["蹲下Timer"] = null;
	   $dataList["防御Timer"] = null;
	   
	   $str = "";
       for($i = 0; $i < $len; $i++)
	   {
	       $file = $f[$i];
	       if(is_dir($file))
		   {
		       $newName = "";
			   $newName = renderFile($path, $file);
			   if($newName == "T0" || $newName == "T1"|| $newName == "T2"|| $newName == "T3"|| $newName == "T4")
			   {
			        $newName = $datafile.$newName;
					myglob($newName, $file, true);
			   }
			   else
			   {
			        myglob($newName, $file);
			   }
		   }
		   else
		   { 	       
			   if(strpos($file, "upDataTimer.txt"))
			   {
			       $dataList["oldUpData"] = $file;
			       $dataList["oldUpDataTimer"] = read_to_timer($file);
			   }
			   
			   if(strpos($file, "战场.png"))
			   {
			        $dataList["png"] = $file;
			        $dataList["pngTimer"]= filectime($file);
			   }
			   
			   if(strpos($file, "待机.png"))
			   {
			        $dataList["待机Timer"]= filectime($file);
			   }
			   
			   if(strpos($file, "攻击.png"))
			   {
			        $dataList["攻击Timer"]= filectime($file);
			   }
			   
			   if(strpos($file, "被攻击.png"))
			   {
			        $dataList["被攻击Timer"]= filectime($file);
			   }
			   
			   if(strpos($file, "蹲下.png"))
			   {
			        $dataList["蹲下Timer"]= filectime($file);
			   }
			   
			   if(strpos($file, "防御.png"))
			   {
			        $dataList["防御Timer"]= filectime($file);
			   }
			   
		       if(strpos($file, "_LH.txt") 
			   && (strpos($file, "攻击") || strpos($file, "待机") || strpos($file, "蹲下") || strpos($file, "防御")))
			   {
				   $fileName = trim(renderFile($path, $file));
				   $dataList[$fileName] = append_to_data($file);
			   } 
		   }
	   }

	   if($folder == $path) return;
	   global $roles, $pngFile;
	
	   // 是否有数据库
	   if (!array_key_exists($datafile, $roles))
	   {
	       append_to_txtfile($datafile);
		   return;
	   }
	   
	   $sign = $roles[$datafile];
	   // 是否有合成图
	   if($dataList["png"] == null)
	   {
	       append_to_png($datafile);
	       return;
	   }	  

	   $pngtimer = $dataList["pngTimer"];

	   // 是否缺少某个动作文本
	   if($dataList["待机"] == null ||
	      $dataList["攻击"] == null ||
		   $dataList["被攻击"] == null)
		{
		   if($dataList["待机"] == null)
	       {
	           lost_to_txt($datafile. "----待机");
	       }

	       if($dataList["攻击"] == null)
	       {
	           lost_to_txt($datafile. "----攻击");
	       }
	   
	       if($dataList["被攻击"] == null)
	       {
	           lost_to_txt($datafile. "----被攻击");
	       }
            return;
		}
		
		 // 是否缺少某个动作图片
		if($dataList["待机Timer"] == null ||
	       $dataList["攻击Timer"] == null ||
		   $dataList["被攻击Timer"] == null)
		{
		    if($dataList["待机Timer"] == null)
		   {
		       lost_to_png($datafile. "----待机");
		   }
		   
		   if($dataList["攻击Timer"] == null)
		   {
		       lost_to_png($datafile. "----攻击");
		   }
		   
		   if($dataList["被攻击Timer"] == null)
		   {
		       lost_to_png($datafile. "----被攻击");
		   }
		    return;
		}
	  
	    // 是否需要更新某个动作
	    if($dataList["待机Timer"] > $pngtimer ||
	       $dataList["攻击Timer"] > $pngtimer ||
		   $dataList["被攻击Timer"] > $pngtimer)
		{
		   if($dataList["待机Timer"] > $pngtimer)
		   {
		       updata_to_png($datafile. "----待机");
		   }
		   
		   if($dataList["攻击Timer"] > $pngtimer)
		   {
		       updata_to_png($datafile. "----攻击");
		   }
		   
		   if($dataList["被攻击Timer"] > $pngtimer)
		   {
		       updata_to_png($datafile. "----被攻击");
		   }
		   	return;
		}

	   $pngFile .= renderJsflList($path, $sign);
	   renderJsfl($pngFile);
	   upData_timer($pngtimer, $path);
	   
	   if($dataList["蹲下"] != null && $dataList["防御"] != null)
	   {
	       $str .= $dataList["攻击"];
		   $str .= $dataList["被攻击"];
		   $str .= $dataList["防御"];
		   $str .= $dataList["蹲下"];
		   if($dataList["战场待机"] != null)
	       {
	           $str .= $dataList["战场待机"];
	       }
	       else
	       {
	           $str .= $dataList["待机"];
	       }
	   }
	   else
	   {
	        if($dataList["战场待机"] != null)
	       {
	           $str .= $dataList["战场待机"];
	       }
	       else
	       {
	           $str .= $dataList["待机"];
	       }
	        $str .= $dataList["攻击"];
	        $str .= $dataList["被攻击"];
	   }

	   append_to_title($str, $datafile);
	    append_to_Log($sign, $folder);
	   echo "write". $datafile. "\n";
   }
   
   /**
    * 抽取文本
	*/
	function renderFile ($path, $file)
	{
	    $fileName = "";
		for($i = strlen($path) + 1; $i < strlen($file); $i++)
		{
		    if($file[$i] == "_")
			{
			    break;
			}
			$fileName .= $file[$i];
		}
		return $fileName;
	}
	
   /**
    * 追加内容
	*/ 
	function append_to_data($fileName)
	{
	    if(strpos($fileName, "待机"))
		{
		   $action = "<data type=\"STANDBY\" ";
		}
		
		if(strpos($fileName, "攻击"))
		{
		   $action = "<data type=\"ATTACK\"";
		}
		
		if(strpos($fileName, "被攻击"))
		{
		   $action = "<data type=\"ATTACKED\"";
		}
		
		if(strpos($fileName, "防御"))
		{
		   $action = "<data type=\"DEFENSE\"";
		}
		
		if(strpos($fileName, "蹲下"))
		{
		   $action = "<data type=\"SQUAT\"";
		}

		$read = file_get_contents($fileName);
		$len = strlen($read);
		$changeStr = "";
		 
		for($i = 5; $i < $len; $i++)
		{
		    $changeStr = $changeStr . $read[$i];
		}
		$changeStr = $action. $changeStr. "\n";
		
		return $changeStr;
	}
	
	/**
	 * 渲染JSFLList
	 */
	function renderJsflList($url, $sign)
	{
	    $url = substr($url, 9);
	    $list = "{url : \"file:///D:/work/". $url. "/\"" . ",  name : \"战场\", ". "sign : \"". $sign. "\"},\n{url : \"file:///D:/work/". $url. "/\"" . ",  name : \"战场\", ". "sign : \"". $sign. "Mini\"},\n";
		
		$list = mb_convert_encoding($list, "utf8", "gbk");
		return $list;
	}
	 
	/**
	 * 渲染JSFL
	 */
	function renderJsfl ($list)
	{
	    $jsfl = "var dom = fl.getDocumentDOM();

var swfUrl = \"file:///D:/work/dev/client/assets/roles/war/\";

var list = ["
. $list.
"];
var listLen = list.length;
var actionNum = 0;

for(var j = 0; j < listLen; j++)
{
    var items = dom.library.items;
    var len = items.length;
    for (var i = len - 1; i > -1; i--) 
	{
	    dom.library.deleteItem(items[i].name);
    }
    
	fl.trace(\"importing\" + list[j].url);
    dom.importFile(list[j].url + list[j].name + \".png\", true);
    fl.trace(\"imported\" + list[j].url);
	 
    items = dom.library.items;
    var item = items[0];

    item.linkageExportForAS = true;
    item.linkageExportInFirstFrame = true;
    item.linkageClassName = \"RoleBmd\";
    item.linkageBaseClass = \"flash.display.BitmapData\";

    dom.exportSWF(swfUrl + list[j].sign +\".swf\");
}";
        append_to_jsfl($jsfl);
	}

	/**
	 * 获取修改时间
	 */
	function read_to_timer ($fileName) 
	{
	    $read = file_get_contents($fileName);
		return $read;
	}
	
	/**
    * 数据库缺少
	*/ 
	function append_to_txtfile($buffer)
	{
	   $newUrl = "notExists";
	   $fp = fopen($newUrl. ".txt", "a");
	   $write = fputs($fp, $buffer. "\r\n");
	   fclose($fp);
	}
	
	/**
	 * 缺少完整图片
	 */
	function append_to_png ($file)
	{
	   $newUrl = "pngNotExists";
	   $fp = fopen($newUrl. ".txt", "a");
	   $write = fputs($fp, $file. "\r\n");
	   fclose($fp);
	}
	
	/**
	 * 缺少单独动作数据
	 */
	function lost_to_txt ($file)
	{
	   $newUrl = "lostTxt";
	   $fp = fopen($newUrl. ".txt", "a");
	   $write = fputs($fp, $file. "\r\n");
	   fclose($fp);
	}
	
	/**
	 * 缺少单独动作图片
	 */
	function lost_to_png ($file)
	{
	   $newUrl = "lostPng";
	   $fp = fopen($newUrl. ".txt", "a");
	   $write = fputs($fp, $file. "\r\n");
	   fclose($fp);
	}
	
	/**
	 * 需要更新图片
	 */
	function updata_to_png ($file)
	{
	   $newUrl = "upDataPng";
	   $fp = fopen($newUrl. ".txt", "a");
	   $write = fputs($fp, $file. "\r\n");
	   fclose($fp);
	}
	
	/**
	 * 标题和结尾
	 */
	function append_to_title ($fileStr, $file)
	{
	    $title = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<main name = \"ActionChange\">";
		$bottom = "</main>";
		$newStr = $title . "\n". $fileStr. "\n". $bottom;
		append_to_file($newStr, $file);
	}

   /**
    * 写入新文本
	*/ 
	function append_to_file($buffer, $file)
	{
	   echo $file."\n";
	   $fp = fopen("changeData/".$file. ".xml", "w");
	   $write = fputs($fp, $buffer);
	   fclose($fp);
	}
	
	/**
    * 写入更新时间
	*/ 
	function upData_timer($buffer, $file)
	{
	   echo $file."\n";
	   $fp = fopen($file."/upDataTimer.txt", "w");
	   $write = fputs($fp, $buffer);
	   fclose($fp);
	}
	
	/**
    * 写入jsfl
	*/ 
	function append_to_jsfl($buffer)
	{
	   $buffer = mb_convert_encoding($buffer, "gbk", "utf8");
	   $fp = fopen("action.jsfl", "w");
	   $write = fputs($fp, $buffer);
	   fclose($fp);
	}
	
			/**
    * 写入j记事本
	*/ 
	function append_to_Log($sign, $name)
	{
	    $file = ""; 
	    global $changeTime, $isChangeTime;
	    if($isChangeTime == false)
	    {
	       $file = $changeTime."--------------------------------------------------------\r\n";
	       $isChangeTime = true;
	       
	    }
	    $file .= "\"". $sign . "\",     \"". $name . "\",";
	    $logTime = date("Y-m-d");

	   $fp = fopen("log/".$logTime."MainRole". ".txt", "a");
	   $write = fputs($fp, $file. "\r\n");
	   fclose($fp);
	}
?>







