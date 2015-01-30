<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'info_list';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '没有找到充值数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=report&c=pay&v=ajax_log_list";

$(function(){
	Ha.page.recordNum = 0;
	Ha.page.queryData = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&playername=<?php echo $data['playername'] ?>&key=pay";
	Ha.page.getList(1);
	

	<?php if ($data['isall'] == 1){ ?>
		/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('option[value!="0"]', $('#sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('option[value!="0"]', $('#sid')).remove();
		}
	});

	$('#get_pay_search_submit').on('submit', function(e){
		e.preventDefault();
		Ha.page.recordNum = 0;
		
		Ha.page.queryData = $('#get_pay_search_submit').serialize();
		Ha.page.getList(1);
	});
//	$('#get_search_submit').submit();
	<?php } ?>

	$('#moreQuery').on('click', function(e){
		$('#moreConditions').toggle();
	});
});
</script>

<script type="text/template" id="info_listtpl">
<tr>
<td class="num">${pid}</td>
<?php if ($data['isall'] == 1){ ?>
<td>${sid_to_name(sid)}</td>
<td>${nickname}</td>
<?php } ?>
<td><span class="orangetitle">${amount}</span></td>
<td>${coins}</td>
<td>${oid}</td>
<td>${dtime}</td>
<td>{{if success==1}}<span class="greentitle"><?php echo Lang('pay_success') ?>{{else}}<span class="redtitle"><?php echo Lang('pay_wait') ?>{{/if}}</span></td>
</tr>
</script>

<h2><span id="tt"><?php echo Lang('pay_log'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_pay_search_submit" method="get" name="form">
			<div class="title cf">	
				<div class="tool_group">
					 <label><?php echo Lang('player') ?>：
						<input type="text" id="playername" name="playername" class="ipt_txt" value="">
					 </label>
					 <label><?php echo Lang('pay_order_no') ?>：
						<input type="text" id="oid" name="oid" class="ipt_txt" value="">
					 </label>
					 <label>
					 	<select name="success" class="ipt_select" style="width:100px;">
					 		<option value="2"><?php echo Lang('pay_wait') ?></option>
					 		<option value="1"><?php echo Lang('pay_success') ?></option>
					 		<option value="3"><?php echo Lang('test') ?></option>
					 	</select>
					 </label>
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="submit" class="btn_sbm" value="查询" id="query"> 
				</div>
				<div class="more">
					<a href="javascript:;" id="moreQuery"><i class="i_triangle"></i>高级查询</a>
				</div>
			</div>
			<div class="control cf" id="moreConditions" style="display: none;">
				<div class="frm_cont">
					<ul>
						<li name="condition">
							<?php if ($data['cid'] > 0 || $data['sid'] > 0) { ?>
							<input name="cid" type="hidden" value="<?php echo $data['cid'] ?>">
							<input name="sid" type="hidden" value="<?php echo $data['sid'] ?>">
							<?php }else { ?>
							<label class="frm_info">服务器：</label>
							<select name="cid" id="cid" class="ipt_select" style="width:120px;">
						 		<option value="0"><?php echo Lang('operation_platform') ?></option>
						 	</select>
						 	<select name="sid" id="sid" class="ipt_select" style="width:120px;">
						 		<option value="0"><?php echo Lang('all_server') ?></option>
						 	</select>
						 	<?php } ?>
						 	日期：
						 	<input type="text" id="starttime" name="starttime" class="ipt_txt_s" value="" readonly onclick="WdatePicker()">
							 - 
							<input type="text" id="endtime" name="endtime" class="ipt_txt_s" value="" readonly onclick="WdatePicker()">
						</li>
					</ul>
				</div>
			</div>
			</form>
		</div>
	</div>

	<div class="column cf" id="table_column">
		<div class="title">详细数据</div>
		<!-- <div class="speed_result">
			<div class="stime">
		        <h4 style="padding:0px;">充值金额 <span class="orangetitle">2.493</span> 元</h4>
		        <ul>
		            <li>充值次数 <em><span>0</span></em> 次</li>
		            <li>充值人数 <em><span>0</span></em> 人</li>
		            <li>ARPU <em><span>0</span></em></li>
		        </ul>
		    </div>
		</div> -->
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
	    	<th>&nbsp;</th>
		    <?php if ($data['isall'] == 1){ ?>
		    <th><?php echo Lang('server') ?></th>
		    <th><?php echo Lang('player') ?></th>
		    <?php } ?>
		    <th><?php echo Lang('pay_money') ?></th>
		    <th><?php echo Lang('ingot') ?></th>
		    <th><?php echo Lang('pay_order_no') ?></th>
		    <th><?php echo Lang('pay_order_time') ?></th>
		    <th><?php echo Lang('status') ?></th>
		</tr>
		</thead>
		<tbody id="info_list">
			   
		</tbody>
		</table>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>