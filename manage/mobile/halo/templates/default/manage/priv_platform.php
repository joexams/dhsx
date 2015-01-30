<script type="text/javascript">
var sids = '<?php echo $data["sids"]; ?>', cids = '<?php echo $data['cids']; ?>';
function findSelected(sid) {
	return sids.indexOf(','+sid+',') >= 0 ? true : false;
}

function findCSelected(cid) {
	return cids.indexOf(','+cid+',') >= 0 ? true : false;
}

$(function(){

	<?php if ($mulit === false) { ?>

	if (typeof global_companylist != 'undefined') {
		$('#popcompanylisttpl').tmpl(global_companylist).appendTo('#popcompanylist');
	}

	<?php if (strpos($data['cids'], ',') !== false && strpos($data['sids'], ',') !== false) { ?>
		cids = trim(cids, ',');
		var arrcid = cids.split(',');
		var str = '';
		for (var i=0; i < arrcid.length; i++) {
			str = '';
			for(var key in global_companylist) {
				if (global_companylist[key].cid == arrcid[i]) {
					str += '<optgroup label="'+global_companylist[key].name+'" id="optgroup_'+global_companylist[key].cid+'">';
					break;
				}
			}
			$.each(getServerByCid(arrcid[i]), function(i,item){
				if (findSelected(item.sid)) {
					str += '<option value="'+item.sid+'" data="'+item.name+'" selected="selected">'+item.name+'：'+item.o_name+'</option>';
				}else {
					str += '<option value="'+item.sid+'" data="'+item.name+'">'+item.name+'：'+item.o_name+'</option>';
				}
			});
			str += '</optgroup>';
			$('#sid').append(str);
			$('#hidden_area').prepend('<input type="hidden" id="cid_'+arrcid[i]+'" name="cid[]" value="'+arrcid[i]+'">');
		}
	<?php } ?>

	<?php }else { ?>
	$.ajax({
		url: '<?php echo INDEX; ?>?m=manage&c=priv&v=ajax_platform_list',
		data: {all: 1},
		dataType: 'json',
		success: function(data){
			if (data.list.length > 0){
				$('#popplatformlisttpl').tmpl(data.list).appendTo('#popcompanylist');
			}
		}
	});

	<?php } ?>
	//关闭提示层
	$('#pop_op_tips').on('click', function() {
		$(this).hide();
	});
	//选择
	$('#popcompanylist').on('click', 'a.companychk', function(){
		var obj = $(this).parent('td'), id = $(this).attr('data-id');
		
		if (obj.hasClass('hover')){
			$('#pop_op_tips').click();
			$('#pop_op_tips').children('p').html('');
			obj.removeClass('hover');
			$('#gid').val(0);

			$('#optgroup_'+id).remove();
			$('#cid_'+id).remove();
		}else {
			<?php if ($mulit === false){ ?>
			if (id > 0) {
				if ($('#optgroup_'+id).html() == null) {
					var str = '';
					for(var key in global_companylist) {
						if (global_companylist[key].cid == id) {
							str += '<optgroup label="'+global_companylist[key].name+'" id="optgroup_'+id+'">';
							break;
						}
					}
					$.each(getServerByCid(id), function(i,item){
						if (findSelected(item.sid)) {
							str += '<option value="'+item.sid+'" data="'+item.name+'" selected="selected">'+item.name+'：'+item.o_name+'</option>';
						}else {
							str += '<option value="'+item.sid+'" data="'+item.name+'">'+item.name+'：'+item.o_name+'</option>';
						}
					});
					str += '</optgroup>';
					$('#sid').append(str);
				}
				if ($('#cid_'+id)) {
					$('#hidden_area').prepend('<input type="hidden" id="cid_'+id+'" name="cid[]" value="'+id+'">');
				}
			}
			<?php }else { ?>
			$('#popcompanylist').find('td.hover').removeClass('hover');
			$('#gid').val(id);
			if ($('#pop_op_tips').is(':hidden')){
				$('#pop_op_tips').show();
			}
			$('#pop_op_tips').children('p').html($(this).siblings('textarea').text());
			<?php } ?>
			obj.addClass('hover');
		}
	});	
	//提交
	$('#pop_post_submit').on('submit', function(e) {
		e.preventDefault();	
		$('#pop_btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=priv&v=setting_platform',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data) {
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: alertclassname = 'success'; break;
						case 1: alertclassname = 'error'; break;
					}
					$('#pop_op_tips').attr('class', alertclassname);
					$('#pop_op_tips').children('p').html(data.msg);
					$('#pop_op_tips').fadeIn();
					setTimeout( function() {
						$('#pop_op_tips').fadeOut();
						$('#pop_btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
					$.dialog({id:'dialog_priv'}).close();
					Ha.notify.show(data.msg, '', alertclassname);
				}
			});
		return false;
	});
});
</script>

<script type="text/template" id="popcompanylisttpl">
	<td{{if findCSelected(cid) }} class="hover"{{/if}}><a href="javascript:;" data-id="${cid}" class="companychk">${name}</a></td>
</script>

<script type="text/template" id="popplatformlisttpl">
	<td{{if gid == <?php echo $data['gid']; ?>}} class="hover"{{/if}}>
	<textarea style="display:none">${description}</textarea>
	<a href="javascript:;" data-id="${gid}" class="companychk">${gname}</a></td>
</script>

<script type="text/template" id="popserverlisttpl">
<option value="${sid}"{{if findSelected(sid)}} selected{{/if}}>${o_name}</option>
</script>

<div id="bgwrap" style="width:500px">
	<div id="pop_op_tips" style="width:500px;display: none;margin:auto;background:#5BC0DE;border:1px solid #2F96B4;cursor:pointer;color:white;font-weight:bold;"><p></p></div>
	<div class="column" style="width:500px">
		<table>
			<tbody>
				<tr id="popcompanylist">
				
				</tr>
			</tbody>
		</table>
		
	</div>
	<div class="content" style="width:500px">
		<form id="pop_post_submit" method="post">
			<?php if ($mulit === false){ ?>
			<table class="global" width="50%" cellpadding="0" cellspacing="0" style="min-width:500px;">
				<tbody>
					<tr>
						<td>
							<select style="width:500px;height:200px;" name="sid[]" id="sid" multiple="multiple">
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			
			<?php } ?>
			<div class="float_footer">    
			<div id="hidden_area" class="frm_btn"> 
				<input type="hidden" name="doSubmit" value="1" />
				<input type="hidden" name="userid" value="<?php echo $userid; ?>">
				<input type="hidden" name="gid" id="gid" value="<?php echo $data['gid']; ?>">
				<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
				<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'dialog_priv'}).close();">
			</div>
			</div>
		</form>
	</div>
</div>