<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(document).ready(function(){
	/**
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
 	$('#get_submit').on('submit', function(e){
 		e.preventDefault();
 		var obj = $(this);
 		$('#get_search_submit').attr('disabled', 'disabled');
 		var cid = $('#companyul').val();
		$('#data_daily_list').html('<tr><td colspan="13">正在努力加载中...</td></tr>');

		$.ajax({
			url: '<?php echo INDEX; ?>?m=report&c=data&v=ajax_daily_list', 
			data: obj.serialize(),
			dataType: 'json',
			success: function(data){
				$('#data_daily_list').empty();
				if (data.status == 0){
					$('#data_daily_tpl').tmpl(data.list).appendTo('#data_daily_list');
				}else {
					$('#list_op_tips').attr('class', 'alert_warning');
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
					}, ( 2000 ) );
				}
				$('#get_search_submit').removeAttr('disabled');
			},
			error: function(){
				$('#get_search_submit').removeAttr('disabled');
			}
		});
 	});
 	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#companyul');
		$('#get_submit').submit();
	}

 	// $('#data_daily_list').one('click', 'a.view', function(){
 	// 	var day = $(this).attr('data');
 	// 	if (day != ''){
 	// 		$('#container').load('<?php echo INDEX; ?>?m=report&c=data&v=day_total&day='+day);
 	// 	}
 	// });
});
</script>

<script type="text/template" id="data_daily_tpl">
<tr>
	<td><a href="#app=5&cpp=57&url=${encodeurl('report', 'day_total', 'data', '&day=')}${gdate}" class="view" data="${gdate}"><?php echo Lang('view'); ?></a></td>
	<td>${gdate}({{if (week == '\u5468\u65e5' || week == '\u5468\u516d')}}<span title="redtitle">${week}</span>{{else}}${week}{{/if}})</td>
	<td>${servernum}</td>
	<td>${register_count==0 ? '-' : register_count}</td>
    <td>{{if create_count==0 && avg_create_count == 0}}-{{else}}${create_count}{{if avg_create_count>0}}<span class="graptitle">(${avg_create_count}%)</span>{{/if}}{{/if}}</td>
	<td>${login_count==0 ? '-' : login_count}</td>
	<td>${max_online_count==0 ? '-': max_online_count}</td>
	<td>${pay_player_count==0? '-': pay_player_count}</td>
	<td>${pay_num==0 ? '-': pay_num}</td>
	<td><span class="orangetitle">${pay_amount==0? '-':pay_amount}</span></td>
	<td><span class="greentitle">${arpu==0? '-': arpu}</span></td>
	<td>${consume>0 ? consume: '-'}</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('data_daily'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
		<form name="get_submit" id="get_submit" action="<?php echo INDEX; ?>?m=report&c=data&v=ajax_daily_list">
		<ul class="nav_li">
			<li>
				<p>
					<select name="cid" id="companyul">
						<option value="0"><?php echo Lang('company_platform'); ?></option>
					</select>
					&nbsp;&nbsp;<?php echo Lang('between_date'); ?>
					<input type="text" name="starttime" onclick="WdatePicker()" readonly size="10">
					-
					<input type="text" name="endtime" onclick="WdatePicker()" readonly size="10">
				</p>
			</li>
			<li class="nobg">
				<p>
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>

	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th><?php echo Lang('view'); ?></th>
					<th><?php echo Lang('date'); ?></th>
					<th><?php echo Lang('server'); ?></th>
					<th><?php echo Lang('register_count'); ?></th>
					<th><?php echo Lang('create_count'); ?></th>
					<th><?php echo Lang('login_count'); ?></th>
					<th><?php echo Lang('max_online_count'); ?></th>
					<th><?php echo Lang('pay_person_num'); ?></th>
					<th><?php echo Lang('total_pay_times'); ?></th>
					<th><?php echo Lang('pay_money'); ?></th>
					<th>ARPU</th>
					<th><?php echo Lang('consumption'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="data_daily_list">
				
			</tbody>
		</table>
		<div id="list_op_tips" style="display: none;"><p></p></div>
	</div>
</div>
