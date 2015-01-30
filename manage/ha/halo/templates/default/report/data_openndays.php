<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	var datelist = <?php echo $data['list']; ?>;
	$('#datelisttpl').tmpl(datelist).appendTo('#datelist');

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), date = $('#datelist').val();
		if (date != 0){
			var url = '<?php echo INDEX; ?>?m=report&c=data&v=openndays';
			Ha.common.ajax(url, 'json', objform.serialize(), 'get', 'container', function(data){
				if (data.status == 0){
					if (data.count > 0){
						$( "#openndayslist" ).empty().append($( "#openndayslisttpl" ).tmpl( data.list )).show();
					}else {
						$( "#openndayslist").html('<tr><td colspan="13" style="text-align: left">没有找到数据。</td></tr>');
					}
				}
			}, 1);
		}else {
			Ha.notify.show('<?php echo Lang('not_selected_date'); ?>', '', 'error');
		}
	});
});
</script>
<script type="text/template" id="datelisttpl">
	<option value="${opendate}">${opendate} 【${servernum}台】</option>
</script>

<script type="text/template" id="openndayslisttpl">
	{{if order==0}}
	<tr class="selected">
		<td>&nbsp;</td>
		<td colspan="12"><a href="#app=5&cpp=56&url=${encodeurl('report','servertotal','data','&cpp=56&sid=')}${sid}%26name%3D${encodeURI(name)}" target="_blank">${name}</a></td>
		<td>&nbsp;</td>
	</tr>
	{{/if}}
	<tr>
		<td>${gdate}</td>
		<td>${register_count}</td>
		<td>${create_count}{{if avg_create_count>0}}<span class="graptitle">(${avg_create_count}%)</span>{{/if}}</td>
		<td>${login_count==0 ? '-' : login_count}</td>
		<td>${max_online_count}{{if avg_online_count>0}}<span class="graptitle">(${avg_online_count})</span>{{/if}}</td>
		<td>${out_count}{{if user_loss_per>0}}<span class="graptitle">(${user_loss_per}%)</span>{{/if}}</td>
		<td>${pay_player_count==0 ? '-': pay_player_count}</td>
		<td>${new_player==0 ? '-': new_player}</td>
		<td><span class="orangetitle">${pay_amount==0? '-':pay_amount}</span></td>
		<td>${pay_num==0 ? '-' : pay_num}</td>
		<td><span class="greentitle">${arpu==0? '-': arpu}</span></td>
		<td>${consume==0 ? '-' : consume}</td>
		<td>${consume_pay_per==0 ? '-' : consume_pay_per}</td>
	</tr>
</script>

<h2><span id="tt"><?php echo Lang('openndays'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form name="get_search_submit" id="get_search_submit" method="get">
				<div class="tool_group">
					 <label>
					 <?php echo Lang('server_date'); ?>：
					 <select name="date" id="datelist" class="ipt_select">
					 	<option value="0"><?php echo Lang('date'); ?></option>
					 </select>
					 前
					 <input name="ndays" type="text" id="ndays" value="3" class="ipt_txt_s">
					 天数据</label>
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
		    <th><?php echo Lang('date'); ?></th>
		    <th><?php echo Lang('register_count'); ?></th>
		    <th><?php echo Lang('create_count'); ?></th>
		    <th><?php echo Lang('login_count'); ?></th>
		    <th><?php echo Lang('max_online_count').'/'.Lang('avg_online'); ?></th>
		    <th><?php echo Lang('losser_num').'/'.Lang('newer_loss_per'); ?></th>
		    <th><?php echo Lang('pay_person_num'); ?></th>
		    <th><?php echo Lang('new_pay_user'); ?></th>
		    <th><?php echo Lang('pay_money'); ?></th>
		    <th><?php echo Lang('pay_times'); ?></th>
		    <th><?php echo Lang('ARPU'); ?></th>
		    <th><?php echo Lang('consumption'); ?></th>
		    <th><?php echo Lang('consume_pay_per'); ?></th>
		</tr>
		</thead>
		<tbody id="openndayslist">
			  <tr><td colspan="13" style="text-align: left">请选择开服日期。</td></tr>
		</tbody>
		</table>
		</div>
	</div>
</div>
