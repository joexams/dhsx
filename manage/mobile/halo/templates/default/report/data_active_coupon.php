<?php defined('IN_G') or exit('No permission resources.'); ?>


<script type="text/javascript">
$(function() {
	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}


	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('#sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option[value!="0"]').remove();
		}
	});


	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		$('#getsubmit').attr('disabled', 'disabled');
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		if (cid>0 && sid>0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=data&v=coupon',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data) {

					$('#getsubmit').removeAttr('disabled');
				},
				error: function() {
					$('#getsubmit').removeAttr('disabled');
				}
			});
		}else {
			$('#getsubmit').removeAttr('disabled');
		}
	});

});
</script>

<script type="text/template">
<tr>
<td>${id}</td>
<td>${servername}</td>
<td>${batch_id}</td>
<td>${username}(${nickname})</td>
<td>${ctime}</td>
<td>&nbsp;</td>
</tr>
</script>


<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('active_coupon') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
	    <form id="get_search_submit" action="<?php echo INDEX; ?>?m=report&c=data&v=coupon" method="get" name="form">
	    <ul class="nav_li">
	        <li class="nobg">
	            <p>
	            	<select name="codeid" id="codeid">
	            		<option value="0"><?php echo Lang('select_active'); ?></option>
	            	</select>

	            	<select name="cid" id="cid">
	            		<option value="0"><?php echo Lang('operation_platform') ?></option>
	            	</select>
	            	<select name="sid" id="sid">
	            		<option value="0"><?php echo Lang('all_server') ?></option>
	            	</select>

	            	<select name="" id="">
	            		<option value="0">批次</option>
	            	</select>

	            	<select name="usetype" id="usetype">
	            		<option value="0">是否使用</option>
	            		<option value="1">使用</option>
	            		<option value="2">未使用</option>
	            	</select>
	            	<?php echo Lang('coupon'); ?>:
	            	<input type="text" name="name" value="">
	            	<?php echo Lang('player_name'); ?>：
	            	<input type="text" name="playername" value="">

	                <input type="submit" name="getsubmit" id="btnsubmit" value="<?php echo Lang('search'); ?>" class="button_link">
	                <input name="dogetSubmit" type="hidden" value="1">
	            </p>
	        </li>
	    </ul>
	    </form>
	</div>
	<br class="clear">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:50px;">ID</th>
					<th	style="width:10%"><?php echo Lang('server'); ?></th>
					<th style="width:10%"><?php echo Lang('coupon'); ?></th>
					<th style="width:120px;"><?php echo Lang('receive_time'); ?></th>
					<th style="width:120px;"><?php echo Lang('receive_user'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
	</div>
</div>