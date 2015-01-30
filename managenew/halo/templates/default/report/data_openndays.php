<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	var datelist = <?php echo $data['list']; ?>;
	$('#datelisttpl').tmpl(datelist).appendTo('#datelist');

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		$('#getsubmit').attr('disabled', 'disabled');
		var objform = $(this), date = $('#datelist').val();
		if (date != 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=data&v=openndays',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data){
					$('#openndayslist').empty();
					if (data.status == 0 && data.list.length>0){
						$('#openndayslisttpl').tmpl(data.list).appendTo('#openndayslist');
					}
					$('#btnsubmit').removeAttr('disabled');
				},
				error: function(){
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		}else {
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html('<?php echo Lang('not_selected_date'); ?>');
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#lossratelist').empty();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
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
		<td colspan="12"><a href="#app=5&cpp=56&url=${encodeurl('report','server_total','data','&cpp=56&sid=')}${sid}%26name%3D${encodeURI(name)}" target="_blank">${name}</a></td>
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
		<td>&nbsp;</td>
	</tr>
</script>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('openndays'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
	    <form id="get_search_submit" action="<?php echo INDEX; ?>?m=report&c=data&v=openndays" method="get" name="form">
	    <ul class="nav_li">
	        <li class="nobg">
	            <p>
	            	<?php echo Lang('server_date'); ?>：
	            	<select name="date" id="datelist">
	            		<option value="0"><?php echo Lang('date'); ?></option>
	            	</select>
	            	前
	            	<input name="ndays" type="text" id="ndays" value="3" size="5">
	            	天数据
	                <input type="submit" name="getsubmit" id="btnsubmit" value="<?php echo Lang('search'); ?>" class="button_link">
	                <input name="dogetSubmit" type="hidden" value="1">
	            </p>
	        </li>
	    </ul>
	    </form>
	</div> 

	<br class="clear">
	<div class="content">
		<div id="op_tips" style="display: none;width:100%"><p></p></div>
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:80px;"><?php echo Lang('date'); ?></th>
					<th style="width:60px;"><?php echo Lang('register_count'); ?></th>
					<th style="width:60px;"><?php echo Lang('create_count'); ?></th>
					<th style="width:60px;"><?php echo Lang('login_count'); ?></th>
					<th style="width:120px;"><?php echo Lang('max_online_count').'/'.Lang('avg_online'); ?></th>
					<th style="width:120px;"><?php echo Lang('losser_num').'/'.Lang('newer_loss_per'); ?></th>
					<th style="width:60px;"><?php echo Lang('pay_person_num'); ?></th>
					<th style="width:60px;"><?php echo Lang('new_pay_user'); ?></th>
					<th style="width:60px;"><?php echo Lang('pay_money'); ?></th>
					<th style="width:60px;"><?php echo Lang('pay_times'); ?></th>
					<th style="width:60px;"><?php echo Lang('ARPU'); ?></th>
					<th style="width:60px;"><?php echo Lang('consumption'); ?></th>
					<th style="width:60px;"><?php echo Lang('consume_pay_per'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="openndayslist">
			</tbody>
		</table>
	</div>
</div>
