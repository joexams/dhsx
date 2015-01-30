<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var type=0, typecount = 1;
$(function(){
	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#companytpl').tmpl(global_companylist).appendTo('#cid');
	}
	/**
	 * 改变平台
	 * @return {[type]} [description]
	 */
	$('#cid').on('change', 'input.company', function(){
		var obj	= $(this), cid = obj.val(), cname = obj.attr('data');
		if (obj.is(':checked')){
			if (cid > 0 && typeof global_serverlist != 'undefined'){
				var str = '<optgroup label="'+cname+'" id="optgroup_'+cid+'">';
				$.each(getServerByCid(cid), function(i,item){
					str += '<option value="'+item.sid+'" data="'+item.name+'">'+item.name+'：'+item.o_name+'</option>';
				});
				str += '</optgroup>';
				$('#sid').append(str);
			}
		}else {
			$('#optgroup_'+cid).remove();
		}
	});
	/**
	 * 所有时间
	 * @return {[type]} [description]
	 */
	$('#timeall').on('change', function(){
		var obj = $(this);
		if (obj.is(':checked')){
			$('input[name="starttime"]').val('');
			$('input[name="endtime"]').val('');
		}
	});
	/**
	 * 平台全选
	 * @return {[type]} [description]
	 */
	// $('#cidall').on('change', function(){
	// 	if ($(this).is(':checked')){
	// 		$('#cid input:checkbox').each(function(){
	// 			$(this).not("input:checked").attr('checked', 'checked');
	// 			$(this).change();
	// 		});
	// 	}else {
	// 		$('#cid input:checked').removeAttr('checked');
	// 		$('#sid').empty();
	// 	}
	// });
	/**
	 * 服务器全选
	 * @return {[type]} [description]
	 */
	// $('#sidall').on('change', function(){
	// 	if ($(this).is(':checked')){
	// 		$('#sid option').not('option:selected').attr('selected', 'selected');
	// 	}else {
	// 		$('#sid option:selected').removeAttr('selected');
	// 		$('#sid').focus();
	// 	}
	// });
	/**
	 * 搜索提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#get_submit').on('submit', function(e){
		e.preventDefault();
		var obj = $(this);
		var url = "<?php echo INDEX; ?>?m=report&c=pay&v=ajax_total_list";
		Ha.common.ajax(url, 'html', obj.serialize(), 'get', 'container', function(data){
			$('#templates-select').html(data);
		}, 1);
	});
});

function sid_to_name(sid){
	return $('#sid option[value="'+sid+'"]').attr('data');
}

function cid_to_name(cid){
	return $('#cid input:checkbox[value="'+cid+'"]').attr('data');
}

function changeTwoDecimal(x)
{
	var f_x = parseFloat(x);
	if (isNaN(f_x))
	{
		return 0;
	}
	f_x = Math.round(f_x *100)/100;
	return f_x;
}
</script>

<script type="text/template" id="companytpl">
	<input type="checkbox" name="cid[]" value="${cid}" class="company" data="${name}">${name}  
</script>
<!--
<script type="text/template" id="list_left_tpl">
    {{if $item.index($item.data)==1}}
	<tr>
		<th colspan="2">&nbsp;</th>
	</tr>
	{{/if}}
	<tr>
		<td rowspan="${typecount}">{{if type == 1}}${cid_to_name(cid)}{{else}}${sid_to_name(sid)}{{/if}}</td>
		<th>金额</th>
	</tr>
	{{if typeof pay_num != 'undefined'}}
	<tr>
	  <th>次数</th>
	</tr>
	{{/if}}
	{{if typeof pay_player_count != 'undefined'}}
	<tr>
	  <th>人数</th>
	</tr>
	{{/if}}
	{{if typeof arpu != 'undefined'}}
	<tr>
	  <th>ARPU</th>
	</tr>
	{{/if}}
</script>

<script type="text/template" id="list_right_tpl">
<tr>
	<td>&nbsp;</td>
</tr>
{{if typeof pay_num != 'undefined'}}
<tr>
  <th>&nbsp;</th>
</tr>
{{/if}}
{{if typeof pay_player_count != 'undefined'}}
<tr>
  <th>&nbsp;</th>
</tr>
{{/if}}
{{if typeof arpu != 'undefined'}}
<tr>
  <th>&nbsp;</th>
</tr>
{{/if}}
</script>
-->


<h2><span id="tt"><?php echo Lang('pay_total'); ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="ajax-submit-area0">
		<form name="get_submit" id="get_submit" method="get">
	    <ul>
	        <li id="cid">
	            <span class="frm_info"><em>*</em><?php echo Lang('company_platform'); ?>：</span>
	            
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('server_list'); ?>：</span>
	            <select name="sid[]" multiple="multiple" id="sid" style="width:300px;height:250px;">

	            </select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em>时间范围：</span>
	            <input type="text" name="starttime" onclick="WdatePicker()" value="<?php echo date('Y-m-01'); ?>" style="width:100px" readonly> - 
				<input type="text" name="endtime" onclick="WdatePicker()" value="<?php echo date('Y-m-d'); ?>" style="width:100px" readonly>
				<input type="checkbox" name="timeall" id="timeall" value="1"><?php echo Lang('all_time');?>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em>数据展示：</span>
	            <input type="checkbox" name="type[]" value="1">次数  
	            <input type="checkbox" name="type[]" value="2">人数  
	            <input type="checkbox" name="type[]" value="3">ARPU
	        </li>
	        <li>
	            <span class="frm_info">&nbsp;</span>
				<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
				<input type="reset" id="btnreset" class="btn_rst"  value="<?php echo Lang('reset'); ?>">
	        </li>
	    </ul>
		</form>
	</div>
	
	<div id = "templates-select" class = "data-sheet table-group">
	
	</div>
</div>