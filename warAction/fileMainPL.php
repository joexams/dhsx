<?php
   require_once(dirname(__FILE__)."/mainRoles.php");
   $url = "../../../������Ʒ/����/player";
   
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
	   
		
	   //�Ƿ���ļ���
	   if($len <= 4)
	   {
	       echo "isEmpty". "\n";
	       return;
	   }

	   $dataList = array();
	   $dataList["png"] = null;
	   $dataList["oldUpData"] = null;
	   
	   $dataList["����"] = null;
	   $dataList["ս������"] = null;
	   $dataList["����"] = null;
	   $dataList["������"] = null;
	   $dataList["����"] = null;
	   $dataList["����"] = null;
	   
	   $dataList["����Timer"] = null;
	   $dataList["����Timer"] = null;
	   $dataList["������Timer"] = null;
	   $dataList["����Timer"] = null;
	   $dataList["����Timer"] = null;
	   
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
			   
			   if(strpos($file, "ս��.png"))
			   {
			        $dataList["png"] = $file;
			        $dataList["pngTimer"]= filectime($file);
			   }
			   
			   if(strpos($file, "����.png"))
			   {
			        $dataList["����Timer"]= filectime($file);
			   }
			   
			   if(strpos($file, "����.png"))
			   {
			        $dataList["����Timer"]= filectime($file);
			   }
			   
			   if(strpos($file, "������.png"))
			   {
			        $dataList["������Timer"]= filectime($file);
			   }
			   
			   if(strpos($file, "����.png"))
			   {
			        $dataList["����Timer"]= filectime($file);
			   }
			   
			   if(strpos($file, "����.png"))
			   {
			        $dataList["����Timer"]= filectime($file);
			   }
			   
		       if(strpos($file, "_LH.txt") 
			   && (strpos($file, "����") || strpos($file, "����") || strpos($file, "����") || strpos($file, "����")))
			   {
				   $fileName = trim(renderFile($path, $file));
				   $dataList[$fileName] = append_to_data($file);
			   } 
		   }
	   }

	   if($folder == $path) return;
	   global $roles, $pngFile;
	
	   // �Ƿ������ݿ�
	   if (!array_key_exists($datafile, $roles))
	   {
	       append_to_txtfile($datafile);
		   return;
	   }
	   
	   $sign = $roles[$datafile];
	   // �Ƿ��кϳ�ͼ
	   if($dataList["png"] == null)
	   {
	       append_to_png($datafile);
	       return;
	   }	  

	   $pngtimer = $dataList["pngTimer"];

	   // �Ƿ�ȱ��ĳ�������ı�
	   if($dataList["����"] == null ||
	      $dataList["����"] == null ||
		   $dataList["������"] == null)
		{
		   if($dataList["����"] == null)
	       {
	           lost_to_txt($datafile. "----����");
	       }

	       if($dataList["����"] == null)
	       {
	           lost_to_txt($datafile. "----����");
	       }
	   
	       if($dataList["������"] == null)
	       {
	           lost_to_txt($datafile. "----������");
	       }
            return;
		}
		
		 // �Ƿ�ȱ��ĳ������ͼƬ
		if($dataList["����Timer"] == null ||
	       $dataList["����Timer"] == null ||
		   $dataList["������Timer"] == null)
		{
		    if($dataList["����Timer"] == null)
		   {
		       lost_to_png($datafile. "----����");
		   }
		   
		   if($dataList["����Timer"] == null)
		   {
		       lost_to_png($datafile. "----����");
		   }
		   
		   if($dataList["������Timer"] == null)
		   {
		       lost_to_png($datafile. "----������");
		   }
		    return;
		}
	  
	    // �Ƿ���Ҫ����ĳ������
	    if($dataList["����Timer"] > $pngtimer ||
	       $dataList["����Timer"] > $pngtimer ||
		   $dataList["������Timer"] > $pngtimer)
		{
		   if($dataList["����Timer"] > $pngtimer)
		   {
		       updata_to_png($datafile. "----����");
		   }
		   
		   if($dataList["����Timer"] > $pngtimer)
		   {
		       updata_to_png($datafile. "----����");
		   }
		   
		   if($dataList["������Timer"] > $pngtimer)
		   {
		       updata_to_png($datafile. "----������");
		   }
		   	return;
		}

	   $pngFile .= renderJsflList($path, $sign);
	   renderJsfl($pngFile);
	   upData_timer($pngtimer, $path);
	   
	   if($dataList["����"] != null && $dataList["����"] != null)
	   {
	       $str .= $dataList["����"];
		   $str .= $dataList["������"];
		   $str .= $dataList["����"];
		   $str .= $dataList["����"];
		   if($dataList["ս������"] != null)
	       {
	           $str .= $dataList["ս������"];
	       }
	       else
	       {
	           $str .= $dataList["����"];
	       }
	   }
	   else
	   {
	        if($dataList["ս������"] != null)
	       {
	           $str .= $dataList["ս������"];
	       }
	       else
	       {
	           $str .= $dataList["����"];
	       }
	        $str .= $dataList["����"];
	        $str .= $dataList["������"];
	   }

	   append_to_title($str, $datafile);
	    append_to_Log($sign, $folder);
	   echo "write". $datafile. "\n";
   }
   
   /**
    * ��ȡ�ı�
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
    * ׷������
	*/ 
	function append_to_data($fileName)
	{
	    if(strpos($fileName, "����"))
		{
		   $action = "<data type=\"STANDBY\" ";
		}
		
		if(strpos($fileName, "����"))
		{
		   $action = "<data type=\"ATTACK\"";
		}
		
		if(strpos($fileName, "������"))
		{
		   $action = "<data type=\"ATTACKED\"";
		}
		
		if(strpos($fileName, "����"))
		{
		   $action = "<data type=\"DEFENSE\"";
		}
		
		if(strpos($fileName, "����"))
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
	 * ��ȾJSFLList
	 */
	function renderJsflList($url, $sign)
	{
	    $url = substr($url, 9);
	    $list = "{url : \"file:///D:/work/". $url. "/\"" . ",  name : \"ս��\", ". "sign : \"". $sign. "\"},\n{url : \"file:///D:/work/". $url. "/\"" . ",  name : \"ս��\", ". "sign : \"". $sign. "Mini\"},\n";
		
		$list = mb_convert_encoding($list, "utf8", "gbk");
		return $list;
	}
	 
	/**
	 * ��ȾJSFL
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
	 * ��ȡ�޸�ʱ��
	 */
	function read_to_timer ($fileName) 
	{
	    $read = file_get_contents($fileName);
		return $read;
	}
	
	/**
    * ���ݿ�ȱ��
	*/ 
	function append_to_txtfile($buffer)
	{
	   $newUrl = "notExists";
	   $fp = fopen($newUrl. ".txt", "a");
	   $write = fputs($fp, $buffer. "\r\n");
	   fclose($fp);
	}
	
	/**
	 * ȱ������ͼƬ
	 */
	function append_to_png ($file)
	{
	   $newUrl = "pngNotExists";
	   $fp = fopen($newUrl. ".txt", "a");
	   $write = fputs($fp, $file. "\r\n");
	   fclose($fp);
	}
	
	/**
	 * ȱ�ٵ�����������
	 */
	function lost_to_txt ($file)
	{
	   $newUrl = "lostTxt";
	   $fp = fopen($newUrl. ".txt", "a");
	   $write = fputs($fp, $file. "\r\n");
	   fclose($fp);
	}
	
	/**
	 * ȱ�ٵ�������ͼƬ
	 */
	function lost_to_png ($file)
	{
	   $newUrl = "lostPng";
	   $fp = fopen($newUrl. ".txt", "a");
	   $write = fputs($fp, $file. "\r\n");
	   fclose($fp);
	}
	
	/**
	 * ��Ҫ����ͼƬ
	 */
	function updata_to_png ($file)
	{
	   $newUrl = "upDataPng";
	   $fp = fopen($newUrl. ".txt", "a");
	   $write = fputs($fp, $file. "\r\n");
	   fclose($fp);
	}
	
	/**
	 * ����ͽ�β
	 */
	function append_to_title ($fileStr, $file)
	{
	    $title = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<main name = \"ActionChange\">";
		$bottom = "</main>";
		$newStr = $title . "\n". $fileStr. "\n". $bottom;
		append_to_file($newStr, $file);
	}

   /**
    * д�����ı�
	*/ 
	function append_to_file($buffer, $file)
	{
	   echo $file."\n";
	   $fp = fopen("changeData/".$file. ".xml", "w");
	   $write = fputs($fp, $buffer);
	   fclose($fp);
	}
	
	/**
    * д�����ʱ��
	*/ 
	function upData_timer($buffer, $file)
	{
	   echo $file."\n";
	   $fp = fopen($file."/upDataTimer.txt", "w");
	   $write = fputs($fp, $buffer);
	   fclose($fp);
	}
	
	/**
    * д��jsfl
	*/ 
	function append_to_jsfl($buffer)
	{
	   $buffer = mb_convert_encoding($buffer, "gbk", "utf8");
	   $fp = fopen("action.jsfl", "w");
	   $write = fputs($fp, $buffer);
	   fclose($fp);
	}
	
			/**
    * д��j���±�
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







