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
			var url = '<?php echo INDEX; ?>?m=report&c=querytool&v=regincome';
			Ha.common.ajax(url, 'json', objform.serialize(), 'get', 'container', function(data){
				if (data.status == 0) {
        			$('.enddate').html($('#enddate').val());
					$('.regdate').html($('#regdate').val());

					$('#regcount').html(data.regcount);
					$('#paycount').html(data.paycount);
					$('#amount').html(data.amount);

        			$('#arpu').html(data.arpu);
        			$('#regincome').html(data.regincome);
        			$('#penetration').html(data.penetration);

        			$('#table_column').show();
				}
			}, 1);
		}else {
			Ha.notify.show('<?php echo Lang('not_selected_company_or_server'); ?>', '', 'error');
		}
	});
});
</script>

<div id="tipsBar" class="msg_tips"> 
    <i class="i_tips"></i>
    <p id="p_tips">ARPU = 充值总额/付费人数；注收比= 充值总额/注册人数；渗透率 = 付费人数/注册人数。</p>
</div>
<h2><span id="tt"><?php echo Lang('regincome'); ?></span></h2>
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
	            <span class="frm_info"><em>*</em><?php echo Lang('register_date'); ?>：</span>
	            <input type="text" name="regdate" id="regdate" readonl onclick="WdatePicker()" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('end_date'); ?>：</span>
	            <input type="text" name="enddate" id="enddate" readonl onclick="WdatePicker()" class="ipt_txt_s">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('channel').'via'; ?>：</span>
	            <input type="text" name="source" id="source" class="ipt_txt_s">
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
	    	<th class="num"><span class="regdate redtitle"></span>注册人数</th>
	    	<th class="num">截止 <span class="enddate redtitle"></span>付费人数</th>
	    	<th class="num">截止 <span class="enddate redtitle"></span>充值总额</th>
	    	<th class="num">ARPU</th>
	    	<th class="num">注收比</th>
	    	<th class="num">渗透率</th>
		</tr>
		</thead>
		<tbody>
			<tr>
				<td class="num"><strong id="regcount" class="orangetitle">0</strong></td>
				<td class="num"><strong id="paycount" class="orangetitle">0</strong></td>
				<td class="num"><strong id="payamount" class="greentitle">0</strong></td>
				<td class="num"><strong id="arpu" class="greentitle">0</strong></td>
				<td class="num"><strong id="regincome" class="greentitle">0</strong></td>
				<td class="num"><strong id="penetration" class="greentitle">0</strong></td>
			</tr>
		</tbody>
		</table>
		</div>
	</div>
</div>