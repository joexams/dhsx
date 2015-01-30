<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, combinelist;
var addresslist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=develop&c=server&v=ajax_combine_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#combinedlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}
function showList( data, type) {
	if (data.status == -1){
		$('#combinedlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize );
		combinelist = data.list;
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#combinedlist" ).empty();
		if (data.count > 0){
			$( "#combinedlisttpl" ).tmpl( combinelist ).prependTo( "#combinedlist" );
			$( "#combinedlist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#combinedlist" ).parent().parent('div.content').css('height', $('#combinedlist').parent('table.global').css('height'));
			}
		}
	}
}

function cid_to_name(cid) {
	if (cid > 0 && typeof global_companylist != 'undefined') {
		for (var key in global_companylist) {
			if (global_companylist[key].cid == cid) {
				return global_companylist[key].name;
			}
		}
	}
	return '';
}

function sid_to_name(sid) {
	if (sid > 0 && typeof global_serverlist != 'undefined'){
		for (var key in global_serverlist) {
			if (global_serverlist[key].sid == sid) {
				return global_serverlist[key].name + '-' + global_serverlist[key].o_name;
			}
		}
	}
	return '';
}

function sids_to_name(sids) {
	sids = trim(sids, ',');
	if (sids != '' && typeof global_serverlist != 'undefined'){
		sids = sids.split(',');
		var names = '';
		for (var i=0; i<sids.length; i++) {
			for (var key in global_serverlist) {
				if (global_serverlist[key].sid == sids[i]) {
					names += ' <a href="javascript:;" class="s_setting" data-sid="'+sids[i]+'">'+global_serverlist[key].name + '-' + global_serverlist[key].o_name + '</a><br>';
					break;
				}
			}
		}
		names = trim(names, '<br>');
		return names;
	}
	return '';
}

function sids_to_compensation(sids) {
	sids = trim(sids, ',');
	var mintime = [];
	var str = ''
	if (sids != '' && typeof global_serverlist != 'undefined'){
		sids = sids.split(',');
		var names = '';
		for (var i=0; i<sids.length; i++) {
			for (var key in global_serverlist) {
				if (global_serverlist[key].sid == sids[i]) {
					if (global_serverlist[key].is_combined > 0) {
						mintime.push({sid: sids[i], name: global_serverlist[key].name, opendate: global_serverlist[key].oldopendate});
					}else {
						mintime.push({sid: sids[i], name: global_serverlist[key].name, opendate: global_serverlist[key].opendate});
					}
					break;
				}
			}
		}
		if (mintime.length > 0) {
			var min = 0;
			var days = 0;
			if (mintime.length > 1) {
				min = Math.min(mintime[0].opendate, mintime[1].opendate)
			}else {
				min = mintime[0].opendate;
			}

			for(var key in mintime) {
				if ( min == mintime[key].opendate) {
					str += '<span class="greentitle">'+mintime[key].name+'</span>：补偿200W铜币<br>';
				}else {
					days = Math.ceil(Math.abs(mintime[key].opendate - min) / 86400);
					str += '<span class="greentitle">'+mintime[key].name+'</span>：补偿'+(200+days*20)+'W铜币与'+(days*40)+'体力<br>';
				}
			}
		}
	}
	return str;
}

function get_server_room(api_server) {
	if (api_server != '' && api_server != '0') {
		for(var key in addresslist) {
			if (addresslist[key].name == api_server) {
				return ' (机房'+addresslist[key].name2+')';
			}
		}
	}
	return '';
}

$(function(){
	//展开
	$('#extentfold').on('click', function(){
		var hidden = '<?php echo Lang("hidden"); ?>', show = '<?php echo Lang("show"); ?>';
		var obj = $(this);
		$('#submit_area').toggle("normal", function(){
			if ($(this).is(':hidden')){
				obj.html(show);
			}else {
				obj.html(hidden);
			}
		});
	});
	$('#extentfold').click();
	/**
	 * 运营平台
	 */
	setTimeout(function() {
	 	if (typeof global_companylist != 'undefined') {
	 		$('#cid option[value!="0"]').remove();
	 		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	 	}
	}, 250);
	$.ajax({
		url: '<?php echo INDEX; ?>?m=develop&c=server&v=ajax_address_list&all=1&type=0',
		dataType: 'json',
		success: function(data) {
			if (data.status == 0) {
				addresslist = data.list;
			}
		}
	});
	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && global_serverlist){
			$('#sid option').remove();
			$('#serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option').remove();
		}
	});

	getList(1);

	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		sid = sid == null ? '' : sid;

		if (cid > 0 && sid.length == 2){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=server&v=combine',
				type: 'post',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data) {

					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							if (data.editflag == 1){
								for(var key in combinelist) {
									if (combinelist[key].id == data.info.id) {
										combinelist[key] = data.info;
										$('#combinedlist').empty();
										$( "#combinedlisttpl" ).tmpl( combinelist ).appendTo( "#combinedlist" );
										break;
									}
								}

								$('#sid option').remove();
								$('#combineid').val(0);
								$('#btncancel').hide();
								$('#btnreset').show();
							}else {
								$( "#combinedlisttpl" ).tmpl( data.info ).prependTo( "#combinedlist" ).fadeIn(2000, function(){
									var obj = $(this);
									obj.css('background', '#E6791C');
									setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
								});
							}
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					document.getElementById('post_submit').reset();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function () {
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		}else {
			var msg = '<?php echo Lang('not_selected_company_or_server'); ?>';
			if (sid.length > 2) {
				msg = '只能选择2台服务器进行合服！';
			}
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html(msg);
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
		}
	});
	/**
	 * 修改
	 * @return {[type]} [description]
	 */
	$('#combinedlist').on('click', 'a.edit', function () {
		var id = $(this).attr('data-id');
		if (id > 0 && typeof combinelist != 'undefined') {
			if ($('#submit_area').is(':hidden')){
				$('#extentfold').click();
			}
			var opendate = '';
			for(var key in combinelist) {
				if (combinelist[key].id == id) {
					$('#cid option:selected').removeAttr('selected');
					$('#cid option[value="'+combinelist[key].cid+'"]').attr('selected', 'selected');
					$('#sid option').remove();
					$('#global_serverlisttpl').tmpl(getServerByCid(combinelist[key].cid)).appendTo('#sid');

					var sids = combinelist[key].sids;
					sids = sids.split(',');
					for (var i=0; i<sids.length; i++) {
						$('#sid option[value="'+sids[i]+'"]').attr('selected', 'selected');
					}

					opendate = combinelist[key].opendate == '0' ? '' : date('Y-m-d H:i:s', combinelist[key].opendate);
					$('#opendate').val(opendate);
					$('#content').val(combinelist[key].content);
					$('#combineid').val(id);

					$('#btncancel').show();
					$('#btnreset').hide();
					$('#opendate').focus();
					setTimeout( function(){	$('#opendate').css('border', ''); }, ( 2000 ) );
					break;
				}
			}
		}
	});
    $('.point_to').live('click', function() {
        var obj = $(this);
        var combinedid = obj.attr('data-id');
        if (confirm('确定合服指向？')) {
            $.ajax({
                url: '<?php echo INDEX; ?>?m=develop&c=server&v=combined_point',
                data: {'combinedid' : combinedid},
                dataType: 'json',
                success: function(data) {
                    if (data.status == 0) {
                        obj.remove();
                    }
                    alert(data.msg);
                },
                error: function(){}
            });
        }
        return false;
    });
	//----详细设置
	$('.s_setting').live('click', function(){
		var sid = $(this).attr('data-sid');
		dialog = $.dialog({id: 'dialog_s_setting', width: 500,title: '<?php echo Lang('server_detail_setting'); ?>'}); 
		$.ajax({
		    url: '<?php echo INDEX; ?>?m=develop&c=server&v=ajax_info',
		    data: 'sid='+sid,
		    success: function (data) {
		        dialog.content(data).lock();
		    },
		    error: function(){},
		    cache: false
		});
		return false;
	});
	//--------取消修改
	$('#btncancel').on('click', function(){
		$('#combineid').val('0');
		document.getElementById('post_submit').reset();

		$('#sid option').remove();
		$('#btncancel').hide();
		$('#btnreset').show();
	});
});
</script>

<script type="text/template" id="serverlisttpl">
{{if combined_to == 0}}
<option value="${sid}" data-ver="${server_ver}">${name}-${o_name}${get_server_room(api_server)}</option>
{{/if}}
</script>

<script type="text/template" id="combinedlisttpl">
<tr>
	<td>${id}</td>
	<td>${date('Y-m-d H:i', opendate)}
    {{if status != 1 }}
    <br>
    <span class="orangetitle">未合服指向</span>
    {{/if}}
    </td>
	<td>${cid_to_name(cid)}</td>
	<td>{{html sids_to_name(sids)}}</td>
	<td>
	{{if combined_to > 0}}${sid_to_name(combined_to)}&nbsp;{{else}}-{{/if}}
	&nbsp;
	</th>
	<td>
	${content}
	<br>
	{{html sids_to_compensation(sids)}}
	<span class="graytitle">注册7天（含7天）以上的角色方能获得合服补偿。</span>
	</td>
	<td>
	<a href="javascript:;" data-sid="${combined_to}" class="s_setting"><?php echo Lang('server_detail_setting') ?></a>
    {{if status != 1 }}
    <a href="javascript:;" class="edit" data-id="${id}"><?php echo Lang('edit'); ?></a>
    <a href="javascript:;" class="point_to" data-id="${id}">立即指向</a>
    {{/if}}
	</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('combined_server_list') ?></span></a></li>
	</ul>
	<div class="clear"></div>

	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('combined_server_input'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('display'); ?></a></li>
			</ul>
		</div>

	<div class="content" id="submit_area">
		<!-- Begin form elements -->
		<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=develop&c=server&v=combine" method="post">
			<input type="hidden" name="doSubmit" value="1">
			<table class="global" width="100%" style="min-width:500px" cellpadding="0" cellspacing="0">
				<tbody>
				<tr>
					<th style="width:100px;"><?php echo Lang('server_date'); ?></th>
					<td><input type="text" name="opendate" id="opendate" value="<?php echo date('Y-m-d H:i:s') ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" readonly></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('company_platform'); ?></th>
					<td>
						<select name="cid" id="cid">
							<option value="0"><?php echo Lang('operation_platform'); ?></option>
						</select>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('server'); ?></th>
					<td>
						<select name="sid[]" multiple="multiple" id="sid" style="width:250px;height:200px;"></select>
					</td>	
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('remark'); ?></th>
					<td><textarea name="content" id="content" style="width:400px;height:100px;"></textarea></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td> 
						<p>
						<input type="hidden" id="combineid" name="combineid" value="0">
						<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
						<input type="button" id="btncancel" class="button" style="display: none;" value="<?php echo Lang('edit_cancel'); ?>">
						<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
						</p>
					</td>
					<td>&nbsp;</td>
				</tr>
				</tbody>
			</table>
	    </form>
	    <div id="op_tips" style="display: none;width:100%"><p></p></div>
		<!-- End form elements -->
	</div>
</div>
<br class="clear">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:40px;">ID</th>
					<th	style="width:120px;"><?php echo Lang('server_date'); ?> </th>
					<th style="width:50px"><?php echo Lang('company_platform'); ?></th>
					<th style="width:150px"><?php echo Lang('combined_server'); ?></th>
					<th style="width:200px"><?php echo Lang('taget_server'); ?></th>
					<th style="width:200px"><?php echo Lang('remark'); ?></th>
					<th style="width:80px;"><?php echo Lang('operation'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="combinedlist">

			</tbody>
		</table>
	</div>
	<div class="pagination pager" id="pager"></div>
</div>
