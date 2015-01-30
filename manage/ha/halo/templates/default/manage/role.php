<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 10;
Ha.page.listEid = 'rolelist';
Ha.page.colspan = 5;
Ha.page.emptyMsg = '没有找到数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=manage&c=role&v=ajax_init_list";

$(function(){
	Ha.page.getList(1);
	
	//--------修改
	$('#rolelist').on('click', 'a.edit', function(){
		var obj    = $(this);
		var roleid = obj.attr('data-roleid');
		if (roleid > 0){
			roleManage(roleid);
		}
	});

	//-----------权限
	$('#rolelist').on('click', 'a.priv', function(){
		var roleid = $(this).attr('data-roleid'), rolename = $(this).attr('data-rolename');
		var title  = rolename+' <?php echo Lang('priv_setting') ?>';
		var url = '<?php echo INDEX; ?>?m=manage&c=priv&v=ajax_show';
		Ha.common.ajax(url, 'html', 'roleid='+roleid+'&rolename='+rolename, 'get', 'container', function(data){
			Ha.Dialog.show(data, title, '', 'dialog_priv');
		}, 1);
	});

	//---------删除
	$('#rolelist').on('click', 'a.delete', function(){
		var obj    = $(this);
		var roleid = obj.attr('data-roleid');
		var rolename = obj.attr('data-rolename');
		if (roleid > 0){
			var url = '<?php echo INDEX; ?>?m=manage&c=role&v=delete';
			Ha.common.ajax(url, 'json', 'roleid='+roleid+'&rolename='+rolename, 'post', 'container');
		}
	});

	$('#rolelist').on('click', 'a.member', function() {
		var obj    = $(this);
		var roleid = obj.attr('data-roleid');
		var rolename = obj.attr('data-rolename');
		if (roleid > 0){
			location.hash = 'app=6&cpp=10&url='+encodeurl('manage', 'init', 'user', '&roleid='+roleid);
		}
	});

	$('#div_pop').on('click', '.btn_thin1', function(e){
		roleManage(0);
	});
});

function roleManage(roleid) {
	var url = '<?php echo INDEX; ?>?m=manage&c=role&v=add';
	roleid = roleid || 0;
	roleid = parseInt(roleid);
	var title = roleid > 0 ? '修改角色' : '新增角色';
	Ha.common.ajax(url, 'html', 'roleid='+roleid, 'get', 'container', function(data){
		Ha.Dialog.show(data, title, 350, 'roleManageDialog');
	}, 1);
}
</script>

<script type="text/template" id="rolelisttpl">
<tr>
	<td class="num">${roleid}</td>
	<td>${rolename}</td>
	<td>${description}</td>
	<td>
	{{if disabled == 0}}<span class="greentitle"><?php echo Lang('abled'); ?>{{else}}<<span class="redtitle"></span><?php echo Lang('disabled'); ?>{{/if}}</span>
	</td>
	<td>
		{{if roleid == 1}} 
		<span class="graytitle"><?php echo Lang('priv_setting'); ?></span> | 
		<a href="javascript:;" class="member" data-roleid="${roleid}" data-rolename="${rolename}"><?php echo Lang('member_manage'); ?></a> | 
		<span class="graytitle"><?php echo Lang('edit') ?></span>
		{{else}}
		<a href="javascript:;" class="priv" data-roleid="${roleid}" data-rolename="${rolename}"><?php echo Lang('priv_setting'); ?></a> | 
		<a href="javascript:;" class="member" data-roleid="${roleid}" data-rolename="${rolename}"><?php echo Lang('member_manage'); ?></a> | 
		<a href="javascript:;" class="edit" data-roleid="${roleid}"><?php echo Lang('edit') ?></a>
		{{/if}}
	</td>
</tr>
</script>


<h2><span id="tt"><?php echo Lang('role_title'); ?></span></h2>
<div class="container" id="container">
<div class="column cf" id="table_column">
		<div class="title">
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="新增角色">
	            </div>
	        </div>
	        <?php echo Lang('role_list') ?>
	    </div>
		<div id="dataTable">
		<table>
			<thead>
			    <tr>
			    	<th>&nbsp;</th>
			    	<th><?php echo Lang('rolename'); ?></th>
			    	<th><?php echo Lang('description'); ?></th>
			    	<th><?php echo Lang('disabledstatus'); ?></th>
			    	<th><?php echo Lang('operation'); ?></th>
			    </tr>
			</thead>
			<tbody id="rolelist">
			   
			</tbody>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>