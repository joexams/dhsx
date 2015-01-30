<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'combinedlist';
Ha.page.colspan = 7;
Ha.page.emptyMsg = '没有找到合服数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=develop&c=server&v=ajax_combine_list";


var addresslist;
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
	/**
	 * 运营平台
	 */
	setTimeout(function() {
	 	if (typeof global_companylist != 'undefined') {
	 		$('#cid option[value!="0"]').remove();
	 		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	 	}
	}, 250);
	Ha.common.ajax('<?php echo INDEX; ?>?m=develop&c=server&v=ajax_address_list&all=1&type=0', 'json', '', 'get', 
		'container', function(data){
			if (data.status == 0) {
				addresslist = data.list;
			}
		}, 1);

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && global_serverlist){
			$('#sid option').remove();
			$('#serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option').remove();
		}
	});

	Ha.page.getList(1);

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
        	var url = '<?php echo INDEX; ?>?m=develop&c=server&v=combined_point';
        	Ha.common.ajax(url, 'json', {'combinedid' : combinedid}, 'get', 'container', function(data){
        		if (data.status == 0) {
        		    obj.remove();
        		}
        	});
        }
        return false;
    });
	//----详细设置
	$('.s_setting').live('click', function(){
		var sid = $(this).attr('data-sid');
		var url = '<?php echo INDEX; ?>?m=develop&c=server&v=ajax_setting_info';
		Ha.common.ajax(url, 'html', 'sid='+sid, 'get', function(data){
			Ha.Dialog.show(data, '<?php echo Lang('server_detail_setting'); ?>', 600, 'dialog_s_setting');
		}, 1);
		return false;
	});

	$('#div_pop').on('click', '.btn_thin1', function(e){
		combinedManage(0);
	});
});

function combinedManage(combinedid){
	var url = '<?php echo INDEX; ?>?m=develop&c=server&v=combined_add';
	combinedid = combinedid || 0;
	combinedid = parseInt(combinedid);
	var title = combinedid > 0 ? '修改合服计划' : '新增合服计划';
	Ha.common.ajax(url, 'html', 'combinedid='+combinedid, 'get', 'container', function(data){
		Ha.Dialog.show(data, title, 500, 'combinedManageDlg');
	}, 1);
}
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
</tr>
</script>


<h2><span id="tt"><?php echo Lang('combined_server_list'); ?></span></h2>
<div class="container" id="container">
	<div class="column cf<?php if ($op == 'source') { ?> whitespace<?php } ?>" id="table_column">
		<div class="title">
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="增加合服计划">
	            </div>
	        </div>
	        合服详细数据
	    </div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
	    	<th>&nbsp;</th>
		    <th><?php echo Lang('server_date'); ?> </th>
			<th><?php echo Lang('company_platform'); ?></th>
			<th><?php echo Lang('combined_server'); ?></th>
			<th><?php echo Lang('taget_server'); ?></th>
			<th><?php echo Lang('remark'); ?></th>
			<th><?php echo Lang('operation'); ?></th>
		</tr>
		</thead>
		<tbody id="combinedlist">
			   
		</tbody>
		</table>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>