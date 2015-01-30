<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		$('#getsubmit').attr('disabled', 'disabled');
		var objform = $(this), sid = $('#sid').val();
		if (sid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=data&v=ajax_server_total_list', 
				data: objform.serialize(),
				dataType: 'json',
				success: function(data){
					$('#server_total_list').empty();
					if (data.status == 0){
						$('#server_total_list_tpl').tmpl(data.list).appendTo('#server_total_list');
					}
					$('#getsubmit').removeAttr('disabled');
				},
				error: function(){
					$('#getsubmit').removeAttr('disabled');
				}
			});
		}else {
			
			$('#getsubmit').removeAttr('disabled');
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
		<td>&nbsp;</td>
	</tr>
</script>

<div id="bgwrap">
	<div class="locationbq">
        <div class="locLeft">
        	<?php if ($data['cpp'] == 57){ ?>
            <a href="#app=5&cpp=57&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=data&v=daily') ?>"><?php echo Lang('data_daily'); ?></a>
            <?php }else if ($data['cpp'] == 24){ ?>
            <a href="#app=5&cpp=24&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init') ?>"><?php echo Lang('game_server'); ?></a>
            <?php }else if ($data['cpp'] == 56){ ?>
            <a href="#app=5&cpp=56&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=data&v=total') ?>"><?php echo Lang('data_total'); ?></a>
            <?php } ?>
            <span>&gt;</span><?php echo $data['name']; ?>
        </div>
        <div class="logo"></div>
    </div>

	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('single_server_report'); ?></span></a></li>
	</ul>
	<br class="clear">

	<div class="nav singlenav">
	    <form id="get_search_submit" action="<?php echo INDEX; ?>?m=report&c=data&v=ajax_server_total_list" method="get" name="form">
	    <ul class="nav_li">
	        <li class="nobg">
	            <p>
	            	<?php echo Lang('date'); ?>：
	                <input name="starttime" type="text" id="starttime" value="<?php echo date('Y-m-01'); ?>" onclick="WdatePicker()" size="20" readonly>
	                -
	                <input name="endtime" type="text" id="endtime" value="<?php echo date('Y-m-d'); ?>" onclick="WdatePicker()" size="20" readonly>  
	                <input type="submit" name="getsubmit" id="btnsubmit" value="<?php echo Lang('search'); ?>" class="button_link">
	                <input type="hidden" name="sid" value="<?php echo $data['sid']; ?>" id="sid">
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
					<th style="width:80px;"><?php echo Lang('date'); ?></th>
					<th style="width:60px;"><?php echo Lang('register_count'); ?></th>
					<th style="width:60px;"><?php echo Lang('create_count'); ?></th>
					<th style="width:60px;"><?php echo Lang('login_count'); ?></th>
					<th style="width:120px;"><?php echo Lang('max_online_count').'/'.Lang('avg_online'); ?></th>
					<th style="width:120px;"><?php echo '流失人数/'.Lang('newer_loss_per'); ?></th>
					<th style="width:60px;"><?php echo Lang('pay_person_num'); ?></th>
					<th style="width:60px;"><?php echo Lang('new_pay_user'); ?></th>
					<th style="width:60px;"><?php echo Lang('pay_money'); ?></th>
					<th style="width:60px;"><?php echo Lang('pay_times'); ?></th>
					<th style="width:60px;"><?php echo Lang('ARPU'); ?></th>
					<th style="width:60px;"><?php echo Lang('consumption'); ?></th>
					<th style="width:60px;"><?php echo Lang('consumption'); ?>/<?php echo Lang('pay'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="server_total_list">
				
			</tbody>
		</table>
	</div>
</div>
