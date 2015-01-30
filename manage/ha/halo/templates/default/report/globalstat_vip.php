<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var chart;
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
			var url = '<?php echo INDEX; ?>?m=report&c=globalstat&v=vip';

			Ha.common.ajax(url, 'json', objform.serialize(), 'get', 'container', function(data){
				if (data.status == 0){
					$('#chart_column').show();
					$("#flashChart").multiChart(data.list);
					var sum = data.sum;

					$('#table_column').show();
					var strHtml = '';
					var percent = 0;
					for (var key in data.categories){
						percent = sum > 0 ? parseInt(data.viplist[key].y) * 100/sum : 0;
						strHtml = strHtml + '<tr><td class="num">Lv.'+data.categories[key]+'</td><td>'+data.viplist[key].y+'</td><td>'+percent.toFixed(2)+'%</td></tr>';
					}
					$('#countlist').empty().append(strHtml);
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
</script>	


<h2><span id="tt"><?php echo Lang('vip_stat'); ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form name="get_submit" id="get_submit" method="get">
		<?php if ($cid > 0 && $sid > 0) { ?>
		<input type="hidden" name="cid" id="cid"  value="<?php echo $cid ?>">
		<input type="hidden" name="sid[]" id="sid"  value="<?php echo $sid ?>">
		<?php }else { ?>
	    <ul>
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
	        <li>
	            <span class="frm_info">&nbsp;</span>
	            <input type="hidden" name="doSubmit" value="1">
				<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
				<input type="reset" id="btnreset" class="btn_rst"  value="<?php echo Lang('reset'); ?>">
	        </li>
	    </ul>
	    <?php } ?>
		</form>
	</div>

	<div class="column cf" id="chart_column" style="display:none">
	<div class="flashChart_title">趋势图</div>	
	<div id="flashChart"></div>
	</div>

	<div class="column cf" id="table_column" style="display:none">
		<div class="title">详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
	    	<th class="num">VIP</th>
	    	<th><?php echo Lang('person_num'); ?></th>
	    	<th><?php echo Lang('percent'); ?></th>
		</tr>
		</thead>
		<tbody id="countlist">

		</tbody>
		</table>
		</div>
	</div>
</div>