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
		var obj = $(this), sid = $("#sid").val(), cid = $("#cid").val(), starttime = $("#starttime").val(), endtime = $("#endtime").val();
		starttime = starttime.replace(/-/g,'/');
		starttime = new Date(starttime); 
		endtime = endtime.replace(/-/g,'/');
		endtime = new Date(endtime);
		if (sid<=0){
			Ha.notify.show('<?php echo Lang('company_no_selected');?>', '', 'error');
		}else if (Math.floor((endtime.getTime()-starttime.getTime())/(24*3600*1000))>60){
			Ha.notify.show('日期范围不能超过60天', '', 'error');
		}else{
		var url = "<?php echo INDEX; ?>?m=report&c=pay&v=ajax_total_list";
		Ha.common.ajax(url, 'html', obj.serialize(), 'get', 'container', function(data){
			$('#templates-select').html(data);
		}, 1);
		}
	});
});

//function sid_to_name(sid){
//	return $('#sid option[value="'+sid+'"]').attr('data');
//}
//
//function cid_to_name(cid){
//	return $('#cid input:checkbox[value="'+cid+'"]').attr('data');
//}

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
	<label><input type="checkbox" name="cid[]" value="${cid}" class="company" data="${name}">${name}</label>  
</script>

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
	            <input type="text" name="starttime" id="starttime" onclick="WdatePicker()" value="<?php echo date('Y-m-01'); ?>" style="width:100px" readonly> - 
				<input type="text" name="endtime" id="endtime" onclick="WdatePicker()" value="<?php echo date('Y-m-d'); ?>" style="width:100px" readonly>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em>数据展示：</span>
	            <label><input type="checkbox" name="type[]" value="1">次数</label>  
	            <label><input type="checkbox" name="type[]" value="2">人数</label>  
	            <label><input type="checkbox" name="type[]" value="3">ARPU</label>  
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