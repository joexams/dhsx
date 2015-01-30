<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">

<?php if ($data['key'] == 'drop_player_fates') { ?>
var extendlist, fatelist;
function fate_id_to_name(id, fate_level){
	if (extendlist.fate != undefined && extendlist.fate.length > 0){
		for (var key in extendlist.fate){
			if (extendlist.fate[key].id == id){
				return (extendlist.fate[key].type == 2 ? '<span class="graytitle"><?php echo Lang('secret_fate');?></span>' : '') + extendlist.fate[key].name + '(' + extendlist.fate[key].qualityname + ' Lv.'+ fate_level + ')';
			}
		}
	}
	return '';
}
<?php } ?>
<?php if ($data['key'] == 'delete_item') { ?>
var extendlist, itemlist;
function item_id_to_name(id){
		if (extendlist.item != undefined && extendlist.item.length > 0){
		for (var key in extendlist.item){
			if (extendlist.item[key].id == id){
				return extendlist.item[key].name;
			}
		}
	}
	return '';
}
<?php } ?>
$(function(){
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

	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val(), player = $.trim($('#player').val()), reason = $.trim($('#reason').val()), msg;		
		if (cid > 0 && sid > 0 && player != '' && reason != '') {
			var url = '<?php echo INDEX; ?>?m=operation&c=source&v=give';
			Ha.common.ajax(url, 'json', objform.serialize(), 'post');
		}else {
			if (cid == 0 || sid == 0){
				msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
			}else if (player == ''){
				msg = '<?php echo Lang('please_input_player_name'); ?>';
			}else if (reason == ''){
				msg = '<?php echo Lang('please_input_operat_reason'); ?>';
			}
			Ha.notify.show(msg, '', 'error');
		}
		return false;
	});

	<?php if ($data['key'] == 'drop_player_fates') { ?>
		$('#player').on('blur', function() {
			var playername = $.trim($(this).val());
			var cid = $('#cid').val(), sid = $('#sid').val();
			var player_type = $('#player_type').val();
			if (playername != '' && cid > 0 && sid > 0) {
				var url = '<?php echo INDEX; ?>?m=server&c=get&v=player_info';
				Ha.common.ajax(url, 'json', {sid: sid, cid: cid, key: 'fate', playername: playername, player_type: player_type}, 'get', 'container', function(data){
					$('label', $('#player_fate')).remove();
					if (data.status == 0){
						if (data.type != undefined){
							extendlist = data.type;
						}
						if (data.count > 0){
							$( "#player_fate" ).empty();
							$( "#fatelisttpl" ).tmpl( data.list ).appendTo( "#player_fate" );
						}
					}else {
						$('label', $('#player_fate')).remove();
					}
				}, 1);
			}else {
				var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
				if (sid > 0 && playername == '') {
					msg = '<?php echo Lang('please_input_player_name'); ?>';
				}
				Ha.notify.show(msg, '', 'error');
			}
		});
	<?php } ?>

	<?php if ($data['key'] == 'delete_item') { ?>
		$('#player').on('blur', function() {
			var playername = $.trim($(this).val());
			var cid = $('#cid').val(), sid = $('#sid').val();
			var player_type = $('#player_type').val();
			if (playername != '' && cid > 0 && sid > 0) {
				var url = '<?php echo INDEX; ?>?m=server&c=get&v=player_info';
				Ha.common.ajax(url, 'json', {sid: sid, cid: cid, key: 'item', playername: playername, player_type: player_type}, 'get', 'container', function(data){
					$('label', $('#player_item')).remove();
					if (data.status == 0){
						if (data.type != undefined){
							extendlist = data.type;
						}
						if (data.count > 0){
							$( "#itemlisttpl" ).tmpl( data.list ).appendTo( "#player_item" );
						}
					}else {
						$('label', $('#player_item')).remove();
					}
				}, 1);
			}else {
				var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
				if (sid > 0 && playername == '') {
					msg = '<?php echo Lang('please_input_player_name'); ?>';
				}
				Ha.notify.show(msg, '', 'error');
			}
		});
	<?php } ?>
});
</script>

<?php if ($data['key'] == 'drop_player_fates') { ?>
<script type="text/template" id="fatelisttpl">
	<label style="position:absolute;left:350px;"><input type="checkbox" name="player_fate_ids[][player_fate_id]" value="${id}"> {{html fate_id_to_name(fate_id, fate_level)}}</label><br>
</script>
<?php } ?>
<?php if ($data['key'] == 'delete_item') { ?>
<script type="text/template" id="itemlisttpl">
	<label style="position:absolute;left:350px;"><input type="checkbox" name="player_item_ids[][player_item_id]" value="${id}"> {{html item_id_to_name(item_id)}}</label><br>
</script>
<?php } ?>


<h2><span id="tt"><?php echo Lang($data['key']); ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form name="post_submit" id="post_submit" method="post">
	    <ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('server') ?>：</span>
	           <select name="cid" id="cid" class="ipt_select" style="width:130px;">
					<option value="0"><?php echo Lang('operation_platform') ?></option>
				</select>
				<select name="sid" id="sid" class="ipt_select">
					<option value="0"><?php echo Lang('all_server') ?></option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('player') ?>：</span>
				<select name="player_type" id="player_type" class="ipt_select" style="width:130px;">
					<option value="2"><?php echo Lang('player_nick') ?></option>
					<option value="1"><?php echo Lang('player_name') ?></option>
				</select>
				<input type="text" name="player" id="player" class="ipt_txt">
	        </li>
	        <?php echo $sourcetmpl ?>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('operat_reason') ?>：</span>
				<input type="text" name="reason" id="reason" class="ipt_txt_xl">
	        </li>
           <li>
                <span class="frm_info">&nbsp;</span>
                <input type="hidden" name="doSubmit" value="1">
                <input type="hidden" name="key" id="key" value="<?php echo $data['key'] ?>">
                <input type="hidden" name="tid" id="tid" value="<?php echo $data['tid']; ?>">
                <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
    			<input type="reset" id="btnreset" class="btn_rst" value="<?php echo Lang('reset'); ?>">
            </li>	        
        </ul>
    	</form>
    </div>
</div>