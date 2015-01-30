<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	/**
	 * 运营平台
	 */
	<?php if (!$cid && !$sid) { ?>
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
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
		var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';

		<?php if ($type == 1) { ?>
		var daynum = !isNaN(parseInt($('#daynum').val())) ? parseInt($('#daynum').val()) : 0;
		msg = msg + (daynum <= 0 ? '<?php echo Lang('input_daynum_tips'); ?>' : '');
		if (cid > 0 && sid != null && daynum > 0){
		<?php }else { ?>
		var deadline = $('#deadline').val();
		msg = msg + (deadline == '' ? '未填写截止登录时间' : '');
		if (cid > 0 && sid != null && deadline != ''){
		<?php } ?>

			var url = '<?php echo INDEX; ?>?m=report&c=data&v=lossrate';
			Ha.common.ajax(url, 'json', objform.serialize(), 'get', 'container', function(data){
				if (data.status == 0 && data.list != null){
					$('#table_column').show();
					$('#lossratelist').empty().append($('#lossratelisttpl').tmpl(data.list)).show();
				}else {
					$('#lossratelist').html('<tr><td colspan="10" style="text-align: left">没有找到数据。</td></tr>');
				}
			}, 1);
		}else {
			Ha.notify.show(msg, '', 'error');
		}
	});
	<?php if ($cid > 0 && $sid > 0) { ?>
	$('#get_submit').submit();
	<?php } ?>
});
</script>

<script type="text/template" id="companyultpl">
<option value="${cid}">${fn} - ${name}</option>
</script>

<script type="text/template" id="servertpl">
	<option value="${sid}" data-ver="${server_ver}">${name}-${o_name}</option>
</script>

<script type="text/template" id="lossratelisttpl">
<tr>
	<td class="num">{{if level > 0}}${level}{{else}}<span class="redtitle">总计</span>{{/if}}</td>
	<td>${num>0?num:'-'}</td>
	<td>${lossnum>0?lossnum:'-'}</td>
	<td>${lossnum >0 ? (lossnum*100/num).toFixed(2) + '%' : '-'}</td>
	<td><span style="color:#058DC7">${paynum>0?paynum:'-'}</span></td>
	<td>${losspaynum>0?losspaynum:'-'}</td>
	<td>${losspaynum > 0 ? (losspaynum*100/paynum).toFixed(2) + '%' : '-'}</td>
	<td>${vipnum>0?vipnum:'-'}</td>
	<td>${lossvipnum>0?lossvipnum:'-'}</td>
	<td>${lossvipnum>0 ? (lossvipnum*100/vipnum).toFixed(2) + '%' : '-'}</td>
</tr>
</script>


<h2><span id="tt"><?php if ($type == 2) { ?>登录分级流失统计<?php }else { ?>分级流失统计<?php } ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form name="get_submit" id="get_submit" method="get">
	    <ul>
	    	<?php if ($cid > 0 && $sid > 0) { ?>
	    	<input type="hidden" name="cid" id="cid"  value="<?php echo $cid ?>">
	    	<input type="hidden" name="sid[]" id="sid"  value="<?php echo $sid ?>">
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
	    	<?php if ($type == 1) { ?>
	    	<li>
	    	    <span class="frm_info"><?php echo Lang('between_day'); ?>：</span>
	    	    <input type="text" name="daynum" value="5" id="daynum" class="ipt_txt_s">
	    	    <span class="graytitle"><?php echo Lang('between_day_tips'); ?></span>				
	    	</li>
	    	<?php }else { ?>
	    	<li>
	    	    <span class="frm_info">截止登录时间：</span>
	    	    <input type="text" name="deadline" value="<?php echo date('Y-m-d') ?>" id="deadline" readonly onclick="WdatePicker()" class="ipt_txt_s">				
	    	</li>
	    	<?php } ?>
	    	<li>
	    	    <span class="frm_info"><?php echo Lang('register_date'); ?>：</span>
	    	    <input type="text" name="starttime" readonly onclick="WdatePicker()" class="ipt_txt_s">
				-
				<input type="text" name="endtime" readonly onclick="WdatePicker()" class="ipt_txt_s">
	    	    <span class="graytitle"><?php echo Lang('register_date_tips'); ?></span>				
	    	</li>
	    	<li>
	    	    <span class="frm_info"><?php echo Lang('level'); ?>：</span>
	    	    <input type="text" name="start_level" class="ipt_txt_s" value="<?php echo $minlevel; ?>">
				-
				<input type="text" name="end_level" class="ipt_txt_s" value="<?php echo $maxlevel; ?>">		
	    	</li>
	        <li>
	            <span class="frm_info">&nbsp;</span>
	            <input type="hidden" name="doSubmit" value="1">
				<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('search'); ?>">
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
		    <th class="num"><?php echo Lang('level'); ?></th>
		    <th><?php echo Lang('create_num'); ?></th>
		    <th><?php echo Lang('losser_num'); ?></th>
		    <th><?php echo Lang('loss_rate'); ?></th>
		    <th><?php echo Lang('pay_person_num'); ?></th>
		    <th><?php echo Lang('pay').Lang('losser_num'); ?></th>
		    <th><?php echo Lang('pay').Lang('loss_rate'); ?></th>
		    <th>VIP<?php echo Lang('person_num'); ?></th>
		    <th>VIP<?php echo Lang('losser_num'); ?></th>
		    <th>VIP<?php echo Lang('loss_rate'); ?></th>
		</tr>
		</thead>
		<tbody id="lossratelist">
			   
		</tbody>
		</table>
		</div>
	</div>
</div>