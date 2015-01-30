<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), sid = $('#sid').val();
		if (sid > 0){
			var url = '<?php echo INDEX; ?>?m=report&c=data&v=ajax_servertotal_list';
			Ha.common.ajax(url, 'json', objform.serialize(), 'get', 'container', function(data){
				if (data.status == 0){
					if (data.count > 0){
						$('#server_total_list').empty().append($('#server_total_list_tpl').tmpl(data.list)).show();
					}else {
						$( "#server_total_list").html('<tr><td colspan="13" style="text-align: left">没有找到数据。</td></tr>');
					}
				}else {
					$( "#server_total_list").html('<tr><td colspan="13" style="text-align: left">没有找到数据。</td></tr>');
				}
			}, 1);
		}
	});

	$('#get_search_submit').submit();
});
</script>

<script type="text/template" id="server_total_list_tpl">
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

<?php if ($data['cpp'] > 0) { ?>
<h2>
	<span id="tt">
		<?php echo Lang('single_server_report'); ?>：
		<?php if ($data['cpp'] == 57){ ?>
	    <a href="#app=5&cpp=57&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=data&v=daily') ?>"><?php echo Lang('data_daily'); ?></a>
	    <?php }else if ($data['cpp'] == 123){ ?>
	    <a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init') ?>"><?php echo Lang('game_server'); ?></a>
	    <?php }else if ($data['cpp'] == 56){ ?>
	    <a href="#app=5&cpp=56&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=data&v=total') ?>"><?php echo Lang('data_total'); ?></a>
	    <?php } ?>
	    <span>&gt;</span><?php echo $data['name']; ?>
	</span></h2>
<?php } ?>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form name="get_search_submit" id="get_search_submit" method="get">
				<div class="tool_group">
					<label><?php echo Lang('date'); ?>： </label>
 	                <input name="starttime" type="text" id="starttime" class="ipt_txt_s" value="<?php echo date('Y-m-01'); ?>" onclick="WdatePicker()" readonly>
 	                -
 	                <input name="endtime" type="text" id="endtime" class="ipt_txt_s" value="<?php echo date('Y-m-d'); ?>" onclick="WdatePicker()" readonly>
 	                <input type="hidden" name="sid" value="<?php echo $data['sid']; ?>" id="sid">
 	                <input name="dogetSubmit" type="hidden" value="1">
					 <input type="submit" class="btn_sbm" value="查 询">
				</div>
				</form>
			</div>
		</div>
	</div>

<div class="column whitespace cf" id="table_column">
	<div id="dataTable">
	<table>
		<thead>
		    <tr>
		    	<th><?php echo Lang('date'); ?></th>
		    	<th><?php echo Lang('register_count'); ?></th>
		    	<th><?php echo Lang('create_count'); ?></th>
		    	<th><?php echo Lang('login_count'); ?></th>
		    	<th><?php echo Lang('max_online_count').'/'.Lang('avg_online'); ?></th>
		    	<th><?php echo '流失人数/'.Lang('newer_loss_per'); ?></th>
		    	<th><?php echo Lang('pay_person_num'); ?></th>
		    	<th><?php echo Lang('new_pay_user'); ?></th>
		    	<th><?php echo Lang('pay_money'); ?></th>
		    	<th><?php echo Lang('pay_times'); ?></th>
		    	<th><?php echo Lang('ARPU'); ?></th>
		    	<th><?php echo Lang('consumption'); ?></th>
		    	<th><?php echo Lang('consumption'); ?>/<?php echo Lang('pay'); ?></th>
		    </tr>
		</thead>
		<tbody id="server_total_list">
		   
		</tbody>
	</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>