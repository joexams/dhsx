<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	/**
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
 	$('#get_search_submit').on('submit', function(e){
 		e.preventDefault();
 		var obj = $(this);
 		var cid = $('#companyul').val();

 		var url = "<?php echo INDEX; ?>?m=report&c=data&v=ajax_daily_list";
 		Ha.common.ajax(url, 'json', obj.serialize(), 'get', 'container', function(data){
 			if (data.status == 0){
 				if (data.count > 0){
 					$( "#data_daily_list" ).empty().append($( "#data_daily_tpl" ).tmpl( data.list )).show();
 				}else {
 					$( "#data_daily_list").html('<tr><td colspan="12" style="text-align: left">没有找到数据。</td></tr>');
 				}
 			}
 		}, 1);
 	});
 	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#companyul');
		$('#get_search_submit').submit();
	}

 	 $('#data_daily_list').one('click', 'a.view', function(){
 	 	var day = $(this).attr('data');
 	 	if (day != ''){
 	 		$('#container').load('<?php echo INDEX; ?>?m=report&c=data&v=day_total&day='+day);
 	 	}
 	 });
 	 $("#month").on('change',function(){
 	 	var month = $(this).val();
 	 	if (month){
	 	 	var startdate = new Date(month+'-01');
	 	 	$("#starttime").val(month+'-01');
			startdate.setMonth( startdate.getMonth()+1 );
			startdate.setDate(0);
			$("#endtime").val(month+'-'+startdate.getDate());
			$('#get_search_submit').submit();
 	 	}
 	 });
});
</script>

<script type="text/template" id="data_daily_tpl">
<tr>
	<td class="num"><a href="#app=4&cpp=42&url=${encodeurl('report', 'day_total', 'data', '&day=')}${gdate}" class="view" data="${gdate}"><?php echo Lang('view'); ?></a></td>
	<td>${gdate}({{if (week == '\u5468\u65e5' || week == '\u5468\u516d')}}<span title="redtitle">${week}</span>{{else}}${week}{{/if}})</td>
	<td>${servernum}</td>
	<td>${register_count==0 ? '-' : register_count}</td>
    <td>{{if create_count==0 && avg_create_count == 0}}-{{else}}${create_count}{{if avg_create_count>0}}<span class="graptitle">(${avg_create_count}%)</span>{{/if}}{{/if}}</td>
	<td>${login_count==0 ? '-' : login_count}</td>
	<td>${max_online_count==0 ? '-': max_online_count}</td>
	<td>${pay_player_count==0? '-': pay_player_count}</td>
	<td>${new_player==0? '-': new_player}</td>
	<td>${pay_num==0 ? '-': pay_num}</td>
	<td><span class="orangetitle">${pay_amount==0? '-':pay_amount}</span></td>
	<td><span class="greentitle">${arpu==0? '-': arpu}</span></td>
	<td>${consume==0 ? '-': consume}</td>
</tr>
</script>

<h2><span id="tt"><?php echo Lang('data_daily'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form name="get_search_submit" id="get_search_submit" method="get">
				<div class="tool_group">
					 <label>
					 <select name="cid" id="companyul" class="ipt_select">
					 	<option value="0"><?php echo Lang('company_platform'); ?></option>
					 </select>
					 <?php echo Lang('between_date');?>
					 <input type="text" id="starttime" name="starttime" onclick="WdatePicker()" readonly class="ipt_txt_s">
					 -
					 <input type="text" id="endtime" name="endtime" onclick="WdatePicker()" readonly class="ipt_txt_s"></label>
					 <?php echo Lang('month'); ?>
					 <select name="month" id="month" class="ipt_select">
					 	<option value=""><?php echo Lang('month'); ?></option>
					 	<?php
					 	foreach ($month_arr as $key=>$value){
					 	?>
					 	<option value=<?php echo $value; ?>><?php echo $value; ?></option>
					 	<?php
					 	}
					 	?>
					 </select>
					 <input name="dogetSubmit" type="hidden" value="1">
					 <input type="submit" class="btn_sbm" value="查 询">
				</div>
				</form>
			</div>
		</div>
	</div>
	<div class="column cf" id="table_column">
		<div class="title">详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th class="num"><?php echo Lang('view'); ?></th>
		    <th><?php echo Lang('date'); ?></th>
		    <th><?php echo Lang('server'); ?></th>
		    <th><?php echo Lang('register_count'); ?></th>
		    <th><?php echo Lang('create_count'); ?></th>
		    <th><?php echo Lang('login_count'); ?></th>
		    <th><?php echo Lang('max_online_count'); ?></th>
		    <th><?php echo Lang('pay_person_num'); ?></th>
		    <th><?php echo Lang('new_pay_user_num'); ?></th>
		    <th><?php echo Lang('total_pay_times'); ?></th>
		    <th><?php echo Lang('pay_money'); ?></th>
		    <th>ARPU</th>
		    <th><?php echo Lang('consumption'); ?></th>
		</tr>
		</thead>
		<tbody id="data_daily_list">
			   
		</tbody>
		</table>
		</div>
	</div>
</div>