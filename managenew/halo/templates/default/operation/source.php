<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">

<?php if ($data['key'] == 'drop_player_fates') { ?>
var extendlist, fatelist;
function fate_id_to_name(id, fate_level){
	if (extendlist.fate != undefined && extendlist.fate.length > 0){
		for (var key in extendlist.fate){
			if (extendlist.fate[key].id == id){
				return (extendlist.fate[key].type == 2 ? '<span class="graytitle">【暗命格】</span>' : '') + extendlist.fate[key].name + '(' + extendlist.fate[key].qualityname + ' Lv.'+ fate_level + ')';
			}
		}
	}
	return '';
}
<?php } ?>
$(document).ready(function(){
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		}
	}, 250);
	
	$('#cid').on('change', function() {
		var cid = $(this).val();
		if (cid > 0){
			$('option[value!="0"]', $('#sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('option[value!="0"]', $('#sid')).remove();
		}
	});

	$('#sid').on('change', function() {
		var sid = $(this).val();
		var key = $('#key').val();
		var version = $('#sid').find('option:selected').attr('data-ver');
		if (sid > 0 && key != ''  && $('#sourcetmpl').children().is('tr') == false){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=template&v=ajax_info',
				data: 'sid='+sid+'&key='+key+'&version='+version,
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						$('#sourcetmpl').html(data.info.content);
						$('#tid').val(data.info.tid);
					}else{
						var time = 2;
						$('#op_tips').attr('class', 'alert_error');
						$('#op_tips').children('p').html(data.msg);
						$('#op_tips').fadeIn();
						setTimeout( function(){
							$('#op_tips').fadeOut();
							$('#btnsubmit').removeAttr('disabled');
						}, ( time * 1000 ) );
					}
				}
			});
		}
	});

	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val(), player = $.trim($('#player').val());
		if (cid > 0 && sid > 0 && player != '') {
			$.ajax({
					url: '<?php echo INDEX; ?>?m=operation&c=source&v=give',
					data: objform.serialize(),
					dataType: 'json',
					type: 'POST',
					success: function(data){
						var alertclassname = '', time = 2;
						switch (data.status){
							case 0: alertclassname = 'alert_success'; break;
							case 1: alertclassname = 'alert_error'; break;
						}
						$('#op_tips').attr('class', alertclassname);
						$('#op_tips').children('p').html(data.msg);
						$('#op_tips').fadeIn();
						$('#player').val('');
						//document.getElementById('post_submit').reset();
						setTimeout( function(){
							$('#op_tips').fadeOut();
							$('#btnsubmit').removeAttr('disabled');
						}, ( time * 1000 ) );
					},
					error: function() {
						$('#btnsubmit').removeAttr('disabled');
					}
				});
		}else {
			var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
			if (sid > 0 && player == '') {
				msg = '<?php echo Lang('please_input_player_name'); ?>';
			}
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html(msg);
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
			$('#btnsubmit').removeAttr('disabled');
		}
		return false;
	});

	<?php if ($data['key'] == 'drop_player_fates') { ?>
		$('#player').on('blur', function() {
			var playername = $.trim($(this).val());
			var cid = $('#cid').val(), sid = $('#sid').val();
			var player_type = $('#player_type').val();
			if (playername != '' && cid > 0 && sid > 0) {
				$.ajax({
					url: '<?php echo INDEX; ?>?m=server&c=get&v=player_info',
					data: {sid: sid, cid: cid, key: 'fate', playername: playername, player_type: player_type},
					dataType: 'json',
					success: function(data) {
						$('#player_fate').empty();
						if (data.status == 0){
							if (data.type != undefined){
								extendlist = data.type;
							}
							if (data.count > 0){
								$( "#fatelisttpl" ).tmpl( data.list ).prependTo( "#player_fate" );
							}
						}
					}
				});
			}else {
				var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
				if (sid > 0 && player == '') {
					msg = '<?php echo Lang('please_input_player_name'); ?>';
				}
				$('#op_tips').attr('class', 'alert_warning');
				$('#op_tips').children('p').html(msg);
				$('#op_tips').fadeIn();
				setTimeout( function(){
					$('#op_tips').fadeOut();
				}, ( 2 * 1000 ) );
			}
		});
	<?php } ?>
});
</script>

<?php if ($data['key'] == 'drop_player_fates') { ?>
<script type="text/template" id="fatelisttpl">
	<li><input type="checkbox" name="player_fate_ids[][player_fate_id]" value="${id}"> {{html fate_id_to_name(fate_id, fate_level)}}</li>
</script>
<?php } ?>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang($data['key']) ?></span></a></li>
	</ul>
	<div class="clear"></div>
	<div class="content" id="submit_area">
		<!-- Begin form elements -->			
			<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=operation&c=source&v=give" method="post">
				<input type="hidden" name="doSubmit" value="1">
				<input type="hidden" name="key" id="key" value="<?php echo $data['key'] ?>">
				<input type="hidden" name="tid" id="tid" value="0">
				<table class="global" width="100%" cellpadding="0" cellspacing="0">
					<tr class="betop">
						<th style="width: 10%;"><?php echo Lang('server') ?></th>
						<td style="width: 40%;"> 
							<select name="cid" id="cid">
								<option value="0"><?php echo Lang('operation_platform') ?></option>
							</select>
							<select name="sid" id="sid">
								<option value="0"><?php echo Lang('all_server') ?></option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('player') ?></th>
						<td> 
							<select name="player_type" id="player_type">
								<option value="2"><?php echo Lang('player_nick') ?></option>
								<option value="1"><?php echo Lang('player_name') ?></option>
							</select>
							<input type="text" name="player" id="player" style="width: 40%" >
						</td>
						<td>&nbsp;</td>
					</tr>
					<tbody id="sourcetmpl">

					</tbody>
					<tr>
						<th><?php echo Lang('operat_reason') ?></th>
						<td> 
							<input type="text" name="reason" id="reason" style="width: 60%" >
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td> 
							<p>
							<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
							<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
							</p>
						</td>
						<td>&nbsp;</td>
					</tr>
				</table>
		    </form>
		    <div id="op_tips" style="display: none;"><p></p></div>
		<!-- End form elements -->
	</div>
</div>
