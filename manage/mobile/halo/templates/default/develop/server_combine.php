<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'combinedlist';
Ha.page.colspan = 7;
Ha.page.emptyMsg = '<?php echo Lang('not_find_combined_data')?>';
Ha.page.url = "<?php echo INDEX; ?>?m=develop&c=server&v=ajax_combine_list";


var addresslist,datelist;
function sids_to_name(sids) {
	sids = trim(sids, ',');
	if (sids != '' && typeof global_serverlist != 'undefined'){
		sids = sids.split(',');
		var names = '';
		for (var i=0; i<sids.length; i++) {
			for (var key in global_serverlist) {
				if (global_serverlist[key].sid == sids[i]) {
					names += ' <a href="javascript:;" class="s_setting" data-sid="'+sids[i]+'">'+global_serverlist[key].name + '-' + global_serverlist[key].o_name + '</a> / API:'+global_serverlist[key].api_server+' / DB:'+global_serverlist[key].db_server+' / url:'+global_serverlist[key].server+'<br>';
					break;
				}
			}
		}
		names = trim(names, '<br>');
		return names;
	}
	return '';
}
function sids_to_server_api(sids) {
	if (sids != '' && typeof global_serverlist != 'undefined'){
		var names = '';
			for (var key in global_serverlist) {
				if (global_serverlist[key].sid == sids) {
					names = global_serverlist[key].server;
					break;
				}
			}
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
					str += '<span class="greentitle">'+mintime[key].name+'</span>：<?php echo Lang('compensation')?>200W<?php echo Lang('coins')?><br>';
				}else {
					days = Math.ceil(Math.abs(mintime[key].opendate - min) / 86400);
					str += '<span class="greentitle">'+mintime[key].name+'</span>：<?php echo Lang('compensation')?>'+(200+days*20)+'W<?php echo Lang('coins').Lang('and')?>'+(days*40)+'<?php echo Lang('power')?><br>';
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
				return ' (<?php echo Lang('server_room')?>'+addresslist[key].name2+')';
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
	Ha.common.ajax('<?php echo INDEX; ?>?m=develop&c=server&v=combine_date_list', 'json', '', 'get',
		'container', function(data){
				datelist = data.list;
				$('#datelisttpl').tmpl(datelist).appendTo('#combine');
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
		if (id > 0) {
			combinedManage(id);
		}
	});
    $('.point_to').live('click', function() {
        var obj = $(this);
        var combinedid = obj.attr('data-id');
        if (confirm('<?php echo Lang('confirm_combined_to')?>')) {
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
		Ha.common.ajax(url, 'html', 'sid='+sid, 'get','container', function(data){
			Ha.Dialog.show(data, '<?php echo Lang('server_detail_setting'); ?>', 600, 'dialog_s_setting');
		}, 1);
		return false;
	});

	$('#div_pop').on('click', '.btn_thin1', function(e){
		combinedManage(0);
	});
	//选择合服日期
	$("#combine").on('change',function(){
		Ha.page.queryData = {opendate: $(this).val(),recordnum:0};
		Ha.page.getList(1);
	});
	$(".daytype").on('click',function(){
		$('.active').removeClass('active');
		Ha.page.queryData = {daytype:$(this).attr('data'),recordnum:0};
		Ha.page.getList(1);
		$(this).parent().addClass('active');
	});
});

function combinedManage(combinedid){
	var url = '<?php echo INDEX; ?>?m=develop&c=server&v=combined_add';
	combinedid = combinedid || 0;
	combinedid = parseInt(combinedid);
	var title = combinedid > 0 ? '<?php echo Lang('edit').Lang('combined_server_list')?>' : '<?php echo Lang('add').Lang('combined_server_list')?>';
	Ha.common.ajax(url, 'html', 'combinedid='+combinedid, 'get', 'container', function(data){
		Ha.Dialog.show(data, title, 500, 'combinedManageDlg');
	}, 1);
}
</script>

<script type="text/template" id="datelisttpl">
<option value="${open_date}">${open_date}[${num}台]</option>
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
    <span class="bluetitle"><?php echo Lang('not_combined_to')?></span>
    {{/if}}
    {{if sids_to_server_api(combined_to)==''}}
    <br>
    <span class="orangetitle">未配置合服</span>
    {{/if}}
    </td>
	<td>${cid_to_name(cid)}</td>
	<td>{{html sids_to_name(sids)}}</td>
	<td>
	{{if combined_to > 0}}${sid_to_name(combined_to)}&nbsp;[已合服]{{else}}-{{/if}}
	&nbsp;
	</td>
	<td>
	${content}
	<br>
	{{html sids_to_compensation(sids)}}
	<span class="graytitle"><?php echo Lang('register_seven_day_get_combined_compensation')?></span><br>
	<span>./combined.py ${sids_to_sername(sids)} ${sids_to_sername(combined_to)}</span>
	</td>
	<td>
	<a href="javascript:;" data-sid="${combined_to}" class="s_setting"><?php echo Lang('server_detail_setting') ?></a>
    {{if status != 1 }}
    <a href="javascript:;" class="edit" data-id="${id}"><?php echo Lang('edit'); ?></a>
    
    {{/if}}
    <a href="javascript:;" class="point_to" data-id="${id}"><?php echo Lang('now_combined_to')?></a>
	</td>
</tr>
</script>


<h2><span id="tt"><?php echo Lang('combined_server_list'); ?></span></h2>
<div class="container" id="container">
<div class="tool_date cf">
        <div class="title cf">
            <div class="tool_group">
                <select name="combine" id="combine" class="combine ipt_select">
                    <option value="0"><?php echo Lang('select_date'); ?></option>
                </select>
            </div>
            <div class="more" style="float:left;">
                <ul class="select" id="toolbar">
                    <li><a class="daytype" href="javascript:void(0);" data="1"><?php echo Lang('today_combined')?></a></li>
                    <li><a class="daytype" href="javascript:void(0);" data="2"><?php echo Lang('not_combined_to')?></a></li>
                </ul>
            </div>
        </div>
    </div>
	<div class="column cf<?php if ($op == 'source') { ?> whitespace<?php } ?>" id="table_column">
		<div class="title">
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="<?php echo Lang('add').Lang('combined_server_list')?>">
	            </div>
	        </div>
	        <?php echo Lang('combined_message')?>
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