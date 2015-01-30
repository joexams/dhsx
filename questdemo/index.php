<?php
	
	if (isset ($_GET['savedata']) && $urlstr = $_GET['savedata']){
		$urlstr 		= $_GET['valuestr'];
		file_put_contents ("data.dat",$urlstr);
		echo $urlstr;
		exit;
	}
	if (isset ($_GET['resavedata']) && $urlstr = $_GET['resavedata']){
		echo file_get_contents  ("data.dat");
		exit;
	}
	if (isset ($_GET['hoxi']) && $urlstr = $_GET['hoxi']){
		$hoxi			= (int)$_GET['hoxi'] -1;
		$updatacount 	= (int)$_GET['updatacount'];
		$urlstr 		= $_GET['valuestr'];

		$urls = explode("-", $urlstr);
		$up =  $urls[0];
		$down =  $urls[1];
		$uplist = explode ("|", $up);
		$downlist = explode ("|", $down);
		$orderlist = array();
		
		$order = $hoxi;

				$Probability = $uplist[$order];
				if( get_reandom ($Probability)  ) {
                    if( $order == 4 && $updatacount < 42) {
                        
                    }else if ($order == 8 && $updatacount < 210){
						
					}
					else{

                        $order ++;
                        if($order == 9) break;
                    }
				} else {
					if( $order > 0 ){
						$Probability = $downlist [$order-1];
						if( get_reandom ($Probability)  ) {
							$order --;
						}
					}
				}

			
		$Result = array($order +1 ,$updatacount +1, $urlstr); 
		echo json_encode  ($Result);
		exit;
	}
	if (isset ($_GET['valuestr']) && $urlstr = $_GET['valuestr']){
		$rcount = $_GET['rcount'];
		$mcount = $_GET['mcount'];
		$urls = explode("-", $urlstr);
		$up =  $urls[0];
		$down =  $urls[1];
		$uplist = explode ("|", $up);
		$downlist = explode ("|", $down);
		$orderlist = array();

		for ( $i = 0; $i < $mcount ; $i ++ ){
			$order = 0;
			for ( $j = 0 ; $j < $rcount ; $j++ ) {
				$Probability = $uplist[$order];
				if( get_reandom ($Probability)  ) {
                    if( $order == 4 && $j < 42) {
                        
                    }else if ($order == 8 && $j < 210){
						
					}
					else{
                        $order ++;
                        if($order == 9) break;
                    }
				} else {
					if( $order > 0 ){
						$Probability = $downlist [$order -1];
						if( get_reandom ($Probability)  ) {
							$order --;
						}
					}
				}
			}
			$orderlist[$order] ++;
		}
		echo json_encode  ($orderlist);
		exit;
	}
	function get_reandom ($val){
		if($val == 0) return 0;
		$n = rand(1,100);
		if ($val > $n ) return 1;
		else	return 0;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>任务模拟器</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script type="text/javascript" src="script/xmlHttp.js"></script>
    <style type="text/css">
	
<!--
.STYLE1 {color: #339900}
.STYLE3 {color: #CCCC33}
.STYLE4 {color: #FF0000}
-->
    </style>
</head>
  <body>

  <div align="center">
        <style type="text/css">
      .report_item{
          float: left;
          height: 100%;
          border-right:1px solid teal;
      }
        </style>

    <span class="STYLE1">任务模拟器</span>  
    <input name="submit2" type="button" value="保存数据" onClick="save_data()" /><input name="submit2" type="button" value="恢复概率数据" onClick="resave_data()" />
  </div>
  <center>
        <form action="index1.php" method="post">
          <table width="1200px" border="0" bgcolor="#333333">
                            <tr align="center" bgcolor="#FFFFFF">

              <td width="80">任务模拟</td>
              <td width="1120">
                  <table border="0" cellspacing="1" bgcolor="#333222" width="100%">
                      <tr align="center" bgcolor="#CCCCFF">
                          <td>星级</td>
                          <td>一星级</td>
                          <td>二星级</td>
                          <td>三星级</td>
                          <td>四星级</td>
                          <td>五星级</td>
                          <td>六星级</td>
                          <td>七星级</td>
                          <td>八星级</td>
                          <td>九星级</td>
                          <td>十星级</td>
                      </tr>
                                       <tr align="center" bgcolor="#FFFFFF">
                          <td><span class="STYLE3">升级概率</span></td>
                          <td><input style="color:blue" id="up_1" type ="text" value="45" size="3" onBlur="up_blur(this.id,this.value)" /></td>
                          <td><input style="color:blue" id="up_2" type ="text" value="78" size="3" onBlur="up_blur(this.id,this.value)" /></td>
                          <td><input style="color:blue" id="up_3" type ="text" value="54" size="3" onBlur="up_blur(this.id,this.value)" /></td>
						  <td><input style="color:blue" id="up_4" type ="text" value="75" size="3" onBlur="up_blur(this.id,this.value)" /></td>
						  <td><input style="color:blue" id="up_5" type ="text" value="2" size="3" onBlur="up_blur(this.id,this.value)" /></td>
						  <td><input style="color:blue" id="up_6" type ="text" value="15" size="3" onBlur="up_blur(this.id,this.value)" /></td>
						  <td><input style="color:blue" id="up_7" type ="text" value="65" size="3" onBlur="up_blur(this.id,this.value)" /></td>
						  <td><input style="color:blue" id="up_8" type ="text" value="84" size="3" onBlur="up_blur(this.id,this.value)" /></td>
						  <td><input style="color:blue" id="up_9" type ="text" value="2" size="3" onBlur="up_blur(this.id,this.value)" /></td>
						  <td>%</td>
						  
                      </tr>
					  
					  <tr align="center" bgcolor="#FFFFFF">
                          <td><span class="STYLE3">维持</span></td>
                          <td><span id = "safe_1"> 55 </span></td>
                          <td><span id = "safe_2"> -56 </span></td>
                          <td><span id = "safe_3"> -8 </span> </td>
                          <td><span id = "safe_4"> -50 </span></td>
						  <td><span id = "safe_5"> 73 </span></td>
						  <td><span id = "safe_6"> 70 </span></td>
						  <td><span id = "safe_7"> -30 </span></td>
						  <td><span id = "safe_8"> -68 </span></td>
						  <td><span id = "safe_9"> 23 </span></td>
						  <td><span id = "safe_10"> 65 </span></td>
						  
                      </tr>
					  
                                   <tr align="center" bgcolor="#FFFFFF">
                          <td><span class="STYLE3">降级概率</span></td>
                          <td>-</td>
                          <td><input style="color:blue" id="down_2" type ="text" value="78" size="3" onBlur="down_blur(this.id,this.value)"  /></td>
						  <td><input style="color:blue" id="down_3" type ="text" value="54" size="3" onBlur="down_blur(this.id,this.value)"  /></td>
						  <td><input style="color:blue" id="down_4" type ="text" value="75" size="3" onBlur="down_blur(this.id,this.value)"  /></td>
						  <td><input style="color:blue" id="down_5" type ="text" value="25" size="3" onBlur="down_blur(this.id,this.value)"  /></td>
						  <td><input style="color:blue" id="down_6" type ="text" value="15" size="3" onBlur="down_blur(this.id,this.value)"  /></td>
						  <td><input style="color:blue" id="down_7" type ="text" value="65" size="3" onBlur="down_blur(this.id,this.value)"  /></td>
						  <td><input style="color:blue" id="down_8" type ="text" value="84" size="3" onBlur="down_blur(this.id,this.value)"  /></td>
						  <td><input style="color:blue" id="down_9" type ="text" value="75" size="3" onBlur="down_blur(this.id,this.value)"  /></td>
						  <td><input style="color:blue" id="down_10" type ="text" value="35" size="3" onBlur="down_blur(this.id,this.value)"  /></td>
                      </tr>
                              </table>              </td>
          </tr>
                    <tr bgcolor="#FFFFFF" align="center">
              <td colspan="2">
                  刷新次数:
                    <input id="runcount" type="text" value="100" size="6" />
                人数:
                <input id="membercount" type="text" value="200" size="5" />
              <input name="submit" type="button" value="执  行" onClick="get_result_server()" />              </td></tr>
			  <tr bgcolor="#FFFFFF" align="center">
              <td colspan="2">
                  (<span class="STYLE4">单次刷新</span>)刷新次数:
                     <span id="updatacount"> 0 </span>
                星级:
                <span id="hoxi"> 1 </span>
              <input name="submit" type="button" value="刷  新" onClick="onece_result_server()" />
              <input name="submit3" type="button" value="清  空" onClick="clear_result_server()" /></td>
			  </tr>
          <tr bgcolor="#FFFFFF"><td align="center">结果:<br><br></td><td><table border="0" cellspacing="1" bgcolor="#333222" width="100%">
            <tr align="center" bgcolor="#CCCCFF">
              <td>星级</td>
              <td>一星级</td>
              <td>二星级</td>
              <td>三星级</td>
              <td>四星级</td>
              <td>五星级</td>
              <td>六星级</td>
              <td>七星级</td>
              <td>八星级</td>
              <td>九星级</td>
              <td>十星级</td>
            </tr>
            <tr align="center" bgcolor="#FFFFFF">
              <td><span class="STYLE3">人数</span></td>
              <td><span id = "result_0"> 0 </span></td>
              <td><span id = "result_1"> 0 </span></td>
              <td><span id = "result_2"> 0 </span></td>
			  <td><span id = "result_3"> 0 </span></td>
			  <td><span id = "result_4"> 0 </span></td>
			  <td><span id = "result_5"> 0 </span></td>
			  <td><span id = "result_6"> 0 </span></td>
			  <td><span id = "result_7"> 0 </span></td>
			  <td><span id = "result_8"> 0 </span></td>
              <td><span id = "result_9"> 0 </span></td>
            </tr>

          </table></td></tr>
          </table>
        </form>
  </center>

      <script language="javascript">
	  		function up_blur(obj, value){
				var idlist = obj.split("_");
				var id = idlist[1];

				var downvalue = 0;
				if (id > 1)
					 downvalue = document.getElementById("down_"+id).value;
				
				var value = 100 - value - downvalue;
				document.getElementById("safe_"+id).innerHTML = value;
			}
			function down_blur(obj, value){
				var idlist = obj.split("_");
				var id = idlist[1];

				var downvalue = 0;
				if (id < 10)
					 downvalue = document.getElementById("up_"+id).value;
				
				var value = 100 - value - downvalue;
				document.getElementById("safe_"+id).innerHTML = value;
			}
	  		function get_element_up_str() {
				up_1 = document.getElementById("up_1").value;
				up_2 = document.getElementById("up_2").value;
				up_3 = document.getElementById("up_3").value;
				up_4 = document.getElementById("up_4").value;
				up_5 = document.getElementById("up_5").value;
				up_6 = document.getElementById("up_6").value;
				up_7 = document.getElementById("up_7").value;
				up_8 = document.getElementById("up_8").value;
				up_9 = document.getElementById("up_9").value;
				var result_string  = up_1
					+ "|" + up_2
					+ "|" + up_3 
					+ "|" + up_4 
					+ "|" + up_5 
					+ "|" + up_6 
					+ "|" + up_7 
					+ "|" + up_8 
					+ "|" + up_9 ;
				return result_string;
			}
			function get_element_down_str() {
				down_2 = document.getElementById("down_2").value;
				down_3 = document.getElementById("down_3").value;
				down_4 = document.getElementById("down_4").value;
				down_5 = document.getElementById("down_5").value;
				down_6 = document.getElementById("down_6").value;
				down_7 = document.getElementById("down_7").value;
				down_8 = document.getElementById("down_8").value;
				down_9 = document.getElementById("down_9").value;
				down_10 = document.getElementById("down_10").value;
				var result_string  = down_2
					+ "|" + down_3
					+ "|" + down_4 
					+ "|" + down_5 
					+ "|" + down_6 
					+ "|" + down_7 
					+ "|" + down_8 
					+ "|" + down_9 
					+ "|" + down_10 ;
				return result_string;
			}
			function get_result_server () {
				var   newtime   =   new   Date();
				var  runcount = document.getElementById("runcount").value;
				var  membercount = document.getElementById("membercount").value;
				var UrlString = get_element_up_str() + "-" + get_element_down_str();
				var url = '?valuestr='+UrlString+'&rcount='+runcount+'&mcount='+membercount+'&ti='+newtime.getTime();
				delete newtime;
				newtime = null;
				Request.sendGET(url, callback);
				url = null;
			}
			
			function callback(req,data)
			{
				var i = 0;
				if(req.responseText && req.responseText.length>1){

					var datajson = eval('(' + req.responseText + ')');
					for (i = 0 ;i < 10 ; i++) {
						if (datajson[i] == undefined){
							document.getElementById("result_"+i).innerHTML = 0;
						}
						else {
							var value = datajson[i];
							document.getElementById("result_"+i).innerHTML = value;
						}
					}
					fil = null;
				}
				req = null;
				data = null;
			}
			
			function callbackonece(req,data)
			{
				var i = 0;
				if(req.responseText && req.responseText.length>1){
					var datajson = eval('(' + req.responseText + ')');

					document.getElementById("updatacount").innerHTML = datajson[1];
					document.getElementById("hoxi").innerHTML = datajson[0];
					//alert( datajson[2]);
				}
				req = null;
				data = null;
			}
			function onece_result_server () {
				var updatacount = document.getElementById("updatacount").innerText;
				var hoxi = document.getElementById("hoxi").innerText;
				var   newtime   =   new   Date();
				var UrlString = get_element_up_str() + "-" + get_element_down_str();
				var url = '?hoxi='+hoxi+'&updatacount='+updatacount+'&valuestr='+UrlString+'&ti='+newtime.getTime();
				delete newtime;
				newtime = null;
				Request.sendGET(url, callbackonece);
				url = null;
			}
			
			function callbacksave(req,data)
			{
				var i = 0;
				if(req.responseText && req.responseText.length>1){
					alert("当前数据已保存");
				}
				req = null;
				data = null;
			}
			
			function save_data () {
				var   newtime   =   new   Date();
				var UrlString = get_element_up_str() + "-" + get_element_down_str();
				var url = '?savedata=1&valuestr='+UrlString+'&ti='+newtime.getTime();
				delete newtime;
				newtime = null;
				Request.sendGET(url, callbacksave);
				url = null;
			}
			function callbackresave(req,data)
			{
				var i = 0;
				if(req.responseText && req.responseText.length>1){
					var Result = req.responseText;
					var ResultList = Result.split("-");
					var Up	=ResultList [0];
					var Down=ResultList [1];
					var UpList = Up.split("|");
					var DownList = Down.split("|");
					document.getElementById("up_1").value = UpList[0];
					document.getElementById("up_2").value = UpList[1];
					document.getElementById("up_3").value = UpList[2];
					document.getElementById("up_4").value = UpList[3];
					document.getElementById("up_5").value = UpList[4];
					document.getElementById("up_6").value = UpList[5];
					document.getElementById("up_7").value = UpList[6];
					document.getElementById("up_8").value = UpList[7];
					document.getElementById("up_9").value = UpList[8];
	
					document.getElementById("down_2").value = DownList[0];
					document.getElementById("down_3").value = DownList[1];
					document.getElementById("down_4").value = DownList[2];
					document.getElementById("down_5").value = DownList[3];
					document.getElementById("down_6").value = DownList[4];
					document.getElementById("down_7").value = DownList[5];
					document.getElementById("down_8").value = DownList[6];
					document.getElementById("down_9").value = DownList[7];
					document.getElementById("down_10").value = DownList[8];
					alert("恢复完成");
				}
				req = null;
				data = null;
			}
			
			function resave_data () {
				var   newtime   =   new   Date();
				var UrlString = get_element_up_str() + "-" + get_element_down_str();
				var url = '?resavedata=1&ti='+newtime.getTime();
				delete newtime;
				newtime = null;
				Request.sendGET(url, callbackresave);
				url = null;
			}
			function clear_result_server() {
				document.getElementById("updatacount").innerHTML = 0;
				document.getElementById("hoxi").innerHTML = 1;
			}
      </script>
  </body>
</html>
