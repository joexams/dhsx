<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 50;
Ha.page.listEid = 'versionlist';
Ha.page.colspan = 5;
Ha.page.emptyMsg = '<?php echo Lang('no_find_data')?>';
Ha.page.url = "<?php echo INDEX; ?>?m=develop&c=server&v=sql_tool";
$(function(){
	/**
	 * submit
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		
		var objform = $(this);
		var sname = !isNaN($('#sname').val()) ? $('#sname').val() : 0;
		var limit = !isNaN($('#limit').val()) ? $('#limit').val() : 0;
		var sql = $("#sql").val();
		if (trim(sname) <0){
			Ha.notify.show("<?php echo Lang('input_game_server')?>", '', 'error');
		}else if (trim(sql) == ''){
			Ha.notify.show("<?php echo Lang('input_sql')?>", '', 'error');
		}else if (trim(limit) < 1){
			Ha.notify.show("<?php echo Lang('show_record_number_error')?>", '', 'error');
		}else{
			Ha.page.queryData = objform.serialize();
			Ha.page.getList(1, function(data){
				if (data.status == 0 && data.list != null){
					var list_length = data.list.length,content='';
					$('#table_column').show();
					$('#dataTheadTr').empty();
					for(var attr in data.list[0]){
						$('#dataTheadTr').append("<th>"+attr+"</th>");
					}
					
					for (var i=0;i<list_length;i++){
						content += "<tr>";
						for(var attr in data.list[i]){
							content += "<td>"+data.list[i][attr]+"</td>";
						}
						content += "</tr>";
					}
					$("#infolist").html(content);
				}else {
//					Ha.notify.show(data.msg, '', 'error');
				}
			}, 1);
		}
	});
});
</script>

<div id="tipsBar" class="msg_tips"> 
    <i class="i_tips"></i>
    <p id="p_tips"><?php echo Lang('red_font_is_senior_tester')?></p>
</div>
<h2><span id="tt">SQL<?php echo Lang('tool'); ?></span></h2>
<div class="container" id="container">
<div class="frm_cont" id="submit_area">
		<form name="post_submit" id="post_submit" method="get">
	    <ul>
	    	<li>
	    	    <span class="frm_info"><em>*</em><?php echo Lang('game_server'); ?>:</span>
	    	    qq_s<input type="text" name="sname" id="sname" class="ipt_txt_s" value="0" onfocus="if(value=='0'){value=''}" onblur="if(value==''){value='0'}">
	    	    <?php if ($_SESSION['roleid'] == 1){?><input type="checkbox" name="game_manage" id="game_manage" value="1"><?php echo Lang('game_manage_server');}?>
	    	</li>
	    	<li>
	    	    <span class="frm_info"><em>*</em>SQL:</span>
	    	    <textarea name="sql" id="sql" class="ipt_textarea" style="width:80%;height:100px"></textarea>
	    	</li>
	    	<li>
	    	    <span class="frm_info"><em>*</em><?php echo Lang('show_record_number'); ?>:</span>
	    	    <input type="text" name="limit" id="limit" class="ipt_txt_s" value="10" onfocus="if(value=='10'){value=''}" onblur="if(value==''){value='10'}">
	    	    <input type="checkbox" name="exec_like_sql" id="exec_like_sql" value="1"><?php echo Lang('exec_like_sql');?>
	    	</li>
	        <li>
	            <span class="frm_info">&nbsp;</span>
	            <input type="hidden" name="doSubmit" value="1">
				<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
				<input type="reset" id="btnreset" class="btn_rst"  value="<?php echo Lang('reset'); ?>">
	        </li>
	    </ul>
		</form>
	</div>
	<div class="column cf" id="table_column" style="display:none">
		<div class="title">详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    
		</tr>
		</thead>
		<tbody id="infolist">
			   
		</tbody>
		</table>
		</div>
	</div>
</div>