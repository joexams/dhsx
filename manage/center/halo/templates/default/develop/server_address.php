<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 50;
Ha.page.listEid = 'addresslist';
Ha.page.colspan = 5;
Ha.page.emptyMsg = '<?php echo Lang('not_find_setting_data')?>';
Ha.page.queryData = 'type=0';
Ha.page.url = "<?php echo INDEX; ?>?m=develop&c=server&v=ajax_address_list";
var addresslist;

function showtab(type){
	$('#addressheader').empty().append($( "#addressheadertpl" ).tmpl( [{type: type}] )).show();
	$('#addressform').empty().append($( "#addressformtpl" ).tmpl( [{type: type}] )).show();
	if (type==1){
		Ha.page.pageSize = 500;
	}else{
		Ha.page.pageSize = 50;
	}
	Ha.page.recordNum = 0;
	Ha.page.getList(1, function(data){
		if (data.status == 0 && data.count > 0) {
			addresslist = data.list;
		}
	});
}

$(function(){
	//添加
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=develop&c=server&v=address';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container', function(data){
			if (data.status == 0) {
				$( "#addresslisttpl" ).tmpl( data.info ).prependTo( "#addresslist" ).fadeIn(2000, function(){
					var obj = $(this);
					obj.css('background', '#E6791C');
					setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
				});	
			}
		});
		return false;
	});
	//删除
	$('#addresslist').on('click', 'input.btndelete', function(){
		if (confirm("确认要删除？")){
		var objtr = $(this).parent().parent('tr')
		var eleid = objtr.attr('id');
		var id = eleid.replace(/entry-/, '');
		if (id > 0){
			var url = '<?php echo INDEX; ?>?m=develop&c=server&v=ajax_address_delete';
			var queryData = 'id='+id;
			Ha.common.ajax(url, 'json', queryData, 'post', 'container', function(data){
				if (data.status == 0){
					objtr.hide();
				}
			});
		}
		}
	});

	$('.first_level_tab').on('click', 'a.addresstype', function(){
		var type = $(this).attr('data-type');
		if (type==0){
			$("#search_tool").show();
		}else{
			$("#search_tool").hide();
		}
		$('.current', $('.first_level_tab')).removeClass('current');
		$(this).parent().addClass('current');
		Ha.page.queryData = 'type='+$(this).attr('data-type');
		showtab($(this).attr('data-type'));
	});
	showtab(0);

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

			var url = '<?php echo INDEX; ?>?m=develop&c=server&v=address';
			var queryData = {id: id, name: name, name2: name2, name3: name3, doSubmit: 1};
			Ha.common.ajax(url, 'json', queryData, 'post', 'container', function(data){
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
	$("#search").on("click",function(){
		Ha.page.queryData = 'api_address='+$("#api_address").val()+'&recordnum=0';
		Ha.page.recordNum = 0;
		Ha.page.getList(1, function(data){
			if (data.status == 0 && data.count > 0) {
				addresslist = data.list;
			}
		});
	});
});

</script>
<script type="text/template" id="addressheadertpl">
<tr>
	<th>&nbsp;</th>
{{if type == 1}}
	<th><?php echo Lang('db_master'); ?></th>
	<th><?php echo Lang('db_slave'); ?></th>
{{else type == 2}}
<th><?php echo Lang('version'); ?></th>
<th><?php echo Lang('use_status') ?></th>
{{else}}
	<th><?php echo Lang('api_address'); ?></th>
	<th><?php echo Lang('api_address').'2'; ?></th>
	<th><?php echo Lang('server_room') ?></th>
{{/if}}
	<th><?php echo Lang('operation'); ?></th>
</tr>
</script>


<script type="text/template" id="addressformtpl">
<tr>
	<td class="num"><span class="greentitle"><?php echo Lang('add_new_record'); ?></span></td>
{{if type == 1}}
	<td><input type="text" name="name2" class="ipt_txt"></td>
	<td><input type="text" name="name" class="ipt_txt"></td>
	<input type="hidden" name="type" value="1">
{{else type == 2}}
	<td><input type="text" name="name" class="ipt_txt"></td>
	<td>
	<select name="name2" class="ipt_select">
		<option value="1"><?php echo Lang('use_status_normal'); ?></option>
		<option value="0"><?php echo Lang('use_status_none'); ?></option>
	</select>
	</td>
	<input type="hidden" name="type" value="2">
{{else}}
	<td><input type="text" name="name" class="ipt_txt"></td>
	<td><input type="text" name="name3" class="ipt_txt"></td>
	<td>
		<select name="name2" class="ipt_select">
			<option value="1"><?php echo Lang('server_room').'1'; ?></option>
			<option value="2"><?php echo Lang('server_room').'2'; ?></option>
		</select>
	</td>
	<input type="hidden" name="type" value="0">
{{/if}}
	<td>
		<p>
			<input type="hidden" name="doSubmit" value="1">
			<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('save'); ?>">
			<input type="reset" class="btn_rst" value="<?php echo Lang('reset'); ?>">
	    </p>
    </td>
</tr>
</script>


<script type="text/template" id="editaddresstpl">
	<td class="num">${id}</td>
	{{if type == 1}}
		<td><input type="text" name="name2" value="${name2}" class="ipt_txt field"></td>
		<td><input type="text" name="name" value="${name}" class="ipt_txt field"></td>
	{{else type == 2}}
		<td><input type="text" name="name" value="${name}" class="ipt_txt field"/></td>
		<td>
		<select name="name2" class="ipt_select field">
			<option value="1"{{if name2 == 1}} selected{{/if}}><?php echo Lang('use_status_normal'); ?></option>
			<option value="0"{{if name2 == 0}} selected{{/if}}><?php echo Lang('use_status_none'); ?></option>
		</select>
		</td>
	{{else}}
		<td><input type="text" name="name" value="${name}" class="ipt_txt field"/></td>
		<td><input type="text" name="name3" value="${name3}" class="ipt_txt field"/></td>
		<td>
			<select name="name2" class="ipt_select field">
				<option value="1"{{if name2 == 1}} selected{{/if}}><?php echo Lang('server_room').'1'; ?></option>
				<option value="2"{{if name2 == 2}} selected{{/if}}><?php echo Lang('server_room').'2'; ?></option>
			</select>
		</td>
	{{/if}}

	<td>
	<input type="button" class="btn_sbm btnsave" data-type="${type}" value="<?php echo Lang('save'); ?>">
	<input type="button" class="btn_rst btncancel" value="<?php echo Lang('cancel'); ?>">
	<input type="button" class="btn_sbm btndelete" value="<?php echo Lang('delete'); ?>">
	</td>
</script>


<script type="text/template" id="addresslisttpl">
<tr id="entry-${id}">
	<td class="num">${id}</td>
{{if type == 1}}
	<td><span class="entryline">${name2}</span>{{if count>0}}  <span class="orangetitle">${count}<?php echo Lang('server_num_item'); ?></span>{{/if}}</td>
	<td><span class="entryline">${name}</span></td>
	<td>&nbsp;</td>
</tr>
{{else type == 2}}
	<td><span class="entryline">${name}</span>{{if count>0}}  <span class="orangetitle">${count}<?php echo Lang('server_num_item'); ?></span>{{/if}}</td>
	<td>{{if name2 == 1}}<span class="entryline greentitle">√<?php echo Lang('use_status_normal'); ?>{{else}}<span class="entryline redtitle">×<?php echo Lang('use_status_none'); ?>{{/if}}</span></td>
	<td>&nbsp;</td>
</tr>
{{else}}
	<td><span class="entryline">${name}</span>{{if count1>0}}  <span class="orangetitle">${count1}<?php echo Lang('server_num_item'); ?></span>{{/if}}{{if count2}}+<span class="entryline">${count2}<?php echo Lang('server_num_item').Lang('server_yes_combined'); ?></span>{{/if}}</td>
	<td><span class="entryline">{{if name3 != ''}}${name3}{{else}}--{{/if}}</span>&nbsp;</td>
	<td><span class="entryline">{{if name2 == 1}}<?php echo Lang('server_room').'1'; ?>{{else}}<?php echo Lang('server_room').'2'; ?>{{/if}}</span></td>
	<td>&nbsp;</td>
{{/if}}
</tr>
</script>

<div id="tipsBar" class="msg_tips"> 
    <i class="i_tips"></i>
    <p id="p_tips"><?php echo Lang('click_record_to_write')?></p>
</div>
<h2><span id="tt"><?php echo Lang('server_address_title'); ?></span></h2>
<div class="container" id="container">
	<div class="speed_result">
	    <div class="mod_tab_title first_level_tab">
	    	<ul>
	    		<li class="current"><a href="javascript:void(0);" data-type="0" class="addresstype"><?php echo Lang('api_address'); ?></a></li>
	    		<li><a href="javascript:void(0);" data-type="1" class="addresstype"><?php echo Lang('server_db_title'); ?></a></li>
	    		<li><a href="javascript:void(0);" data-type="2" class="addresstype"><?php echo Lang('server_version_title'); ?></a></li>
	    	</ul>
	    </div>
	</div>
	<div class="tool_date cf" id="search_tool">
        <div class="title cf">
            <div class="tool_group">
            	API地址:<input type="text" name="api_address" id="api_address" class="ipt_txt" value="">
            	<input type="submit" id="search" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
            </div>
        </div>
    </div>
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<form id="post_submit" action="" method="post">
		<table>
		<thead id="addressheader">
		</thead>
		<tbody id="addressform" class="tfoot" style="vertical-align: middle;border-color: inherit;">
		</tbody>
		<tbody id="addresslist">
		</tbody>
		</table>
		</form>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>