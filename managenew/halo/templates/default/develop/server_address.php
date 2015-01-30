<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 50, recordNum = 0, addresslist, type = 0;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=develop&c=server&v=ajax_address_list&type="+type+"&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#addresslist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}
function showList( data ) {
	if (data.status == -1){
		$('#addresslist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), addresslist = data.list, type = data.type;
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#addresslist" ).empty();
		if (data.count > 0){
			$( "#addresslisttpl" ).tmpl( addresslist ).prependTo( "#addresslist" );
			$( "#addresslist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#addresslist" ).parent().parent('div.content').css('height', $('#addresslist').parent('table.global').css('height'));
			}
		}
	}
}

function showtab(){
	$('#addressheader').empty();
	$( "#addressheadertpl" ).tmpl( [{type: type}] ).prependTo( "#addressheader" );
	$('#addressform').empty();
	$( "#addressformtpl" ).tmpl( [{type: type}] ).prependTo( "#addressform" );
	pageIndex = 1, pageCount = 0, recordNum = 0;
	getList( pageIndex );		
}

$(document).ready(function(){
	//添加
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=server&v=address',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							$( "#addresslisttpl" ).tmpl( data.info ).prependTo( "#addresslist" ).fadeIn(2000, function(){
								var obj = $(this);
								obj.css('background', '#E6791C');
								setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
							});	
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					document.getElementById('post_submit').reset();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function() {
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		return false;
	});
	//删除
	$('#addresslist').on('click', 'a.delete', function(){
		var obj    = $(this);
		var id = obj.attr('data-id'), address1 = obj.attr('data-address1'), address2 = obj.attr('data-address2');
		if (id > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=server&v=ajax_address_delete',
				data: 'id='+id+'&address1='+address1+'&address2='+address2,
				dataType: 'json',
				type: 'post',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: alertclassname = 'alert_success'; break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					obj.parent().parent('tr').remove();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
					}, ( time * 1000 ) );
				}
			});
		}
	});

	$('.first_level_tab').on('click', 'a.addresstype', function(){
		$('.active').removeClass('active');
		$(this).addClass('active');
		type = $(this).attr('data-type');
		showtab()
	});
	showtab();

	$('#addresslist').on('click', 'span.entryline', function(){
		var eleid = $(this).parent().parent('tr').attr('id');
		var id = eleid.replace(/entry-/, '');
		if (id > 0){
			for(var key in addresslist){
				if (addresslist[key].id == id){
					$('#'+eleid).html( $('#editaddresstpl').tmpl(addresslist[key]) );
					break;
				}
			}
		}
	});

	$('#addresslist').on('click', 'input.btnsave', function(){
		var objtr = $(this).parent().parent('tr')
		var eleid = objtr.attr('id');
		var id = eleid.replace(/entry-/, '');
		if (id > 0){
			var type = $(this).attr('data-type');
			var name = '', name2 = '', name3 = '';

			name = objtr.find('input[name="name"]').val();
			if (type == 1){
				name2 = objtr.find('input[name="name2"]').val();
			}else if (type == 2){
				name2 = objtr.find('select[name="name2"]').val();
			}else {
				name3 = objtr.find('input[name="name3"]').val();
				name2 = objtr.find('select[name="name2"]').val();
			}
			
			$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=server&v=address',
				data: {id: id, name: name, name2: name2, name3: name3, doSubmit: 1},
				dataType: 'json',
				type: 'POST',
				success: function(data){
					if (data.status == 0){
						for(var key in addresslist){
							if (addresslist[key].id == id){
								addresslist[key].name = name;
								addresslist[key].name2 = name2;
								addresslist[key].name3 = name3;
								$('#'+eleid).html( $('#addresslisttpl').tmpl(addresslist[key]).html() );
								break;
							}
						}
					}
				}
			});
		}
	});

	$('#addresslist').on('click', 'input.btncancel', function(){
		var eleid = $(this).parent().parent('tr').attr('id');
		var id = eleid.replace(/entry-/, '');
		if (id > 0){
			for(var key in addresslist){
				if (addresslist[key].id == id){
					$('#'+eleid).html( $('#addresslisttpl').tmpl(addresslist[key]).html() );
					break;
				}
			}
		}
	});
});

</script>
<script type="text/template" id="addressheadertpl">
<tr>
	<th style="width:80px">ID</th>
{{if type == 1}}
	<th style="width:200px"><?php echo Lang('db_master'); ?></th>
	<th style="width:200px"><?php echo Lang('db_slave'); ?></th>
{{else type == 2}}
<th style="width:200px"><?php echo Lang('version'); ?></th>
<th style="width:200px"><?php echo Lang('use_status') ?></th>
{{else}}
	<th style="width:200px"><?php echo Lang('api_address'); ?></th>
	<th style="width:300px"><?php echo Lang('api_address').'2'; ?></th>
	<th style="width:150px"><?php echo Lang('server_room') ?></th>
{{/if}}
	<th style="width:200px"><?php echo Lang('operation'); ?></th>
	<th>&nbsp;</th>
</tr>
</script>


<script type="text/template" id="addressformtpl">
<tr>
	<th><?php echo Lang('add_new_record'); ?></th>
{{if type == 1}}
	<td><input type="text" name="name2" style="width:90%"></td>
	<td><input type="text" name="name" style="width:90%"></td>
	<input type="hidden" name="type" value="1">
{{else type == 2}}
	<td><input type="text" name="name" style="width:90%"></td>
	<td>
	<select name="name2">
		<option value="1"><?php echo Lang('use_status_normal'); ?></option>
		<option value="0"><?php echo Lang('use_status_none'); ?></option>
	</select>
	</td>
	<input type="hidden" name="type" value="2">
{{else}}
	<td><input type="text" name="name" style="width:90%"></td>
	<td><input type="text" name="name3" style="width:90%"></td>
	<td>
		<select name="name2">
			<option value="1"><?php echo Lang('server_room').'1'; ?></option>
			<option value="2"><?php echo Lang('server_room').'2'; ?></option>
		</select>
	</td>
	<input type="hidden" name="type" value="0">
{{/if}}
	<td>
		<p>
			<input type="hidden" name="doSubmit" value="1">
			<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('save'); ?>">
	    </p>
    </td>
	<td>&nbsp;</td>
</tr>
</script>


<script type="text/template" id="editaddresstpl">
	<td>${id}</td>
	{{if type == 1}}
		<td><input type="text" name="name2" value="${name2}" class="field" style="width:90%"></td>
		<td><input type="text" name="name" value="${name}" class="field" style="width:90%"></td>
	{{else type == 2}}
		<td><input type="text" name="name" value="${name}" class="field" style="width:90%"/></td>
		<td>
		<select name="name2" class="field">
			<option value="1"{{if name2 == 1}} selected{{/if}}><?php echo Lang('use_status_normal'); ?></option>
			<option value="0"{{if name2 == 0}} selected{{/if}}><?php echo Lang('use_status_none'); ?></option>
		</select>
		</td>
	{{else}}
		<td><input type="text" name="name" value="${name}" class="field" style="width:90%" /></td>
		<td><input type="text" name="name3" value="${name3}" class="field" style="width:90%" /></td>
		<td>
			<select name="name2" class="field">
				<option value="1"{{if name2 == 1}} selected{{/if}}><?php echo Lang('server_room').'1'; ?></option>
				<option value="2"{{if name2 == 2}} selected{{/if}}><?php echo Lang('server_room').'2'; ?></option>
			</select>
		</td>
	{{/if}}

	<td>
	<input type="button" class="btnsave btn" data-type="${type}" value="<?php echo Lang('save'); ?>">
	<input type="button" class="btncancel" value="<?php echo Lang('cancel'); ?>">
	</td>
	<td>&nbsp;</td>
</script>


<script type="text/template" id="addresslisttpl">
<tr id="entry-${id}">
	<td>${id}</td>
{{if type == 1}}
	<td><span class="entryline">${name2}</span>{{if count>0}}  <span style="color: #E6791C">${count}<?php echo Lang('server_num_item'); ?></span>{{/if}}</td>
	<td><span class="entryline">${name}</span></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
{{else type == 2}}
	<td><span class="entryline">${name}</span>{{if count>0}}  <span style="color: #E6791C">${count}<?php echo Lang('server_num_item'); ?></span>{{/if}}</td>
	<td>{{if name2 == 1}}<span class="entryline greentitle">√<?php echo Lang('use_status_normal'); ?>{{else}}<span class="entryline redtitle">×<?php echo Lang('use_status_none'); ?>{{/if}}</span></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
{{else}}
	<td><span class="entryline">${name}</span>{{if count>0}}  <span style="color: #E6791C">${count}<?php echo Lang('server_num_item'); ?></span>{{/if}}</td>
	<td><span class="entryline">${name3}</span>&nbsp;</td>
	<td><span class="entryline">{{if name2 == 1}}<?php echo Lang('server_room').'1'; ?>{{else}}<?php echo Lang('server_room').'2'; ?>{{/if}}</span></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
{{/if}}
</tr>
</script>


<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('server_address_title'); ?></span></a></li>
	</ul>
	<br class="clear">
	<ul class="first_level_tab">
		<li><a href="javascript:;" data-type="0" class="addresstype active"><?php echo Lang('api_address'); ?></a></li>
		<li><a href="javascript:;" data-type="1" class="addresstype"><?php echo Lang('server_db_title'); ?></a></li>
		<li><a href="javascript:;" data-type="2" class="addresstype"><?php echo Lang('server_version_title'); ?></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="content" id="submit_area">
			<!-- Begin form elements -->
				<form id="post_submit" action="<?php echo INDEX; ?>?m=develop&c=server&v=address" method="post">
				<table class="global" width="100%" cellpadding="0" cellspacing="0">
					<thead id="addressheader">
					    
					</thead>
					<tbody id="addressform">

					</tbody>
					<tbody id="addresslist">

					</tbody>
				</table>
				</form>
				<div id="list_op_tips" style="display: none;"><p></p></div>
			<!-- End form elements -->
		</div>
		<div class="pagination pager" id="pager"></div>
	</div>
</div>
