<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	/**
	 * 运营平台
	 */
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);
	

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('#sid option').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option').remove();
		}
	});

	$('#get_submit').on('submit', function(e) {
		e.preventDefault();
		var cid = $('#cid').val(), sid = $('#sid').val();
		var objform = $(this);
		if (cid > 0 && sid.length > 0) {
			var url = '<?php echo INDEX; ?>?m=report&c=querytool&v=ingotstock';
			Ha.common.ajax(url, 'json', objform.serialize(), 'get', 'container', function(data){
				if (data.status == 0) {
        			$('#totalpayingot').html(parseInt(data.totalpayingot));
					$('#overpayingot').html(data.overpayingot);
					$('#overgiveingot').html(data.overgiveingot);
					$('#table_column').show();
				}
			}, 1);
		}else {
			Ha.notify.show('<?php echo Lang('not_selected_company_or_server'); ?>', '', 'error');
		}
	});
});
</script>


<h2><span id="tt"><?php echo Lang('ingotstock'); ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form name="get_submit" id="get_submit" method="get">
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
	    	<th class="num">充值元宝（不含赠送）</th>
	    	<th class="num">充值元宝存量</th>
	    	<th class="num">赠送元宝存量</th>
		</tr>
		</thead>
		<tbody>
			<tr>
				<td class="num" id="totalpayingot">--</td>
				<td class="num"><strong id="overpayingot" class="orangetitle">--</strong></td>
				<td class="num"><strong id="overgiveingot" class="greentitle">--</strong></td>
			</tr>
		</tbody>
		</table>
		</div>
	</div>	
</div>