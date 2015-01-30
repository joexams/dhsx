<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var consume_type;
$(function(){
	/**
	 * 运营平台
	 */
	<?php if (!$cid && !$sid) { ?>
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('#sid option').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option').remove();
		}
	});
	<?php } ?>

	$('#get_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		if (cid > 0 && sid != null){
			var url = '<?php echo INDEX; ?>?m=server&c=get&v=consume';
			Ha.common.ajax(url, 'json', objform.serialize(), 'get', 'container', function(data){
				$('#table_column').show();
				if (data.status == 0){
					consume_type = data.type;
					var allnum = data.allnum, alltotal = data.alltotal, allingot = data.allingot;
					for(var key in data.list){
						data.list[key].pertotal = (data.list[key].total * 100/alltotal).toFixed(2) + '%';
						data.list[key].peringot = (data.list[key].ingot * 100/allingot).toFixed(2) + '%';
					}
					$('#consumelist').empty().append($('#consumelist_tpl').tmpl(data.list));

					if (allnum > 0 && alltotal > 0){
						var strHtml = [
								'<tr>',
									'<td><strong class="greentitle">总计</strong></td>',
									'<td><strong class="redtitle">'+allnum+'</strong></td>',
									'<td><strong class="redtitle">'+alltotal+'</strong></td>',
									'<td><strong class="redtitle">'+allingot+'</strong></td>',
								'</tr>'
							].join('');
						$('#consumelist').prepend(strHtml);
					}
				}else {
					$('#consumelist').html('<tr><td colspan="4" style="text-align: left">没有找到数据。</td></tr>');
				}
			}, 1);
		}else {
			Ha.notify.show('<?php echo Lang('not_selected_company_or_server'); ?>', '', 'error');
		}
	});
	<?php if ($cid > 0 && $sid > 0) { ?>
	$('#get_submit').submit();
	<?php } ?>
});

function typeid_to_name(typeid){
	if (consume_type.length > 0 && typeid > 0){
		for(key in consume_type){
			if (consume_type[key].id == typeid){
				return consume_type[key].name;
			}
		}
	}
	return '';
}
</script>

<script type="text/template" id="consumelist_tpl">
<tr>
<td>${typeid_to_name(type)}</td>
<td>${num}</td>
<td>${total}<span class="graptitle">(${pertotal})</span></td>
<td><span class="orangetitle">${ingot}</span><span class="graptitle">(${peringot})</span></td>
</tr>
</script>


<h2><span id="tt">分级流失统计</span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form name="get_submit" id="get_submit" method="get">
	    <ul>
	    	<?php if ($cid > 0 && $sid > 0) { ?>
	    	<input type="hidden" name="sid[]" id="sid" value="<?php echo $sid ?>">
	    	<input type="hidden" name="cid" id="cid" value="<?php echo $cid ?>">
	    	<?php }else { ?>
	    	<li>
	    	    <span class="frm_info"><em>*</em><?php echo Lang('company_platform'); ?>：</span>
	    	    <select name="cid" id="cid" class="ipt_select">
	    	    	<option value="0"><?php echo Lang('operation_platform') ?></option>
	    	    </select>
	    	</li>
	    	<li>
	    	    <span class="frm_info"><em>*</em><?php echo Lang('server_list'); ?>：</span>
	    	    <select name="sid[]" multiple="multiple" id="sid" style="width:300px;height:250px;">

	    	    </select>
	    	</li>
	    	<?php } ?>
	        
	        <li>
	            <span class="frm_info"><em>*</em>天数：</span>
	            <input type="text" name="days" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('role').Lang('level'); ?>：</span>
	            <input type="text" name="start_level" class="ipt_txt_s">
				-
				<input type="text" name="end_level" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('between_date'); ?>：</span>
	            <input type="text" name="starttime" class="ipt_txt_s" readonl onclick="WdatePicker()">
				-
				<input type="text" name="endtime" class="ipt_txt_s" readonl onclick="WdatePicker()">
	        </li>
           <li>
                <span class="frm_info">&nbsp;</span>
                <input type="hidden" name="doSubmit" value="1">
                <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('search'); ?>">
    			<input type="reset" id="btnreset" class="btn_rst" value="<?php echo Lang('reset'); ?>">
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
    	    <th><?php echo Lang('type'); ?></th>
    	    <th><?php echo Lang('person_num'); ?></th>
    	    <th><?php echo Lang('times'); ?></th>
    	    <th><?php echo Lang('ingot'); ?></th>
    	</tr>
    	</thead>
    	<tbody id="consumelist">
    	
    	</tbody>
    	</table>
    	</div>
    </div>
</div>
