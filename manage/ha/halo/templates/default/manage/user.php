<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'userlist';
Ha.page.colspan = 7;
Ha.page.emptyMsg = '尚未设置访问账号，点击右侧新增按钮，新增访问账号。';
Ha.page.url = "<?php echo INDEX; ?>?m=manage&c=user&v=ajax_list&roleid=<?php echo $data['roleid']; ?>";

var jsonrole = <?php echo $data['jsonrole']; ?>;
function get_rolename_byroleId(roleid){
	var rolename = '';
	for(var key in jsonrole){
		if (jsonrole[key].roleid == roleid){
			rolename = jsonrole[key].rolename;
			break;
		}
	}
	return rolename;
}
$(function(){
	Ha.page.getList(1);

	//---------权限
	$('#userlist').on('click', 'a.priv', function(){
		var userid = $(this).attr('data-userid'), roleid = $(this).attr('data-roleid'), username = $(this).attr('data-username');
		var title  = username + " <?php echo Lang('priv'); ?>";

		if (userid > 0){
			var url = '<?php echo INDEX; ?>?m=manage&c=priv&v=ajax_show';
			Ha.common.ajax(url, 'html', 'userid='+userid+'&roleid='+roleid, 'get', 'container', function(data){
				Ha.Dialog.show(data, title,  350, 'dialog_priv');
			}, 1);
		}
		return false;
	});
	//---------平台权限
	$('#userlist').on('click', 'a.pf_priv', function(){
		var userid = $(this).attr('data-userid'), roleid = $(this).attr('data-roleid'), username = $(this).attr('data-username');
		var title  = username + " <?php echo Lang('platform').Lang('priv'); ?>";

		if (userid > 0){
			var url = '<?php echo INDEX; ?>?m=manage&c=priv&v=ajax_platform_show';
			Ha.common.ajax(url, 'html', 'userid='+userid+'&roleid='+roleid, 'get', 'container', function(data){
				Ha.Dialog.show(data, title, 350, 'dialog_priv');
			}, 1);
		}
		return false;
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1);
	});

	$('#div_pop').on('click', '.btn_thin1', function(e){
		authManage(0);
	});
	//--------修改
	$('#userlist').on('click', 'a.edit', function(){
		var obj    = $(this);
		var userid = obj.attr('data-userid');
		if (userid > 0){
			authManage(userid);
		}
	});

	//--------修改
	$('#userlist').on('click', 'a.clear', function(){
		var obj    = $(this);
		var userid = obj.attr('data-userid'), username = $(this).attr('data-username');
		if (userid > 0){
			var url = '<?php echo INDEX; ?>?m=manage&c=user&v=clear';
			Ha.common.ajax(url, 'json', 'userid='+userid+'&username='+username, 'get', 'container', function(data){
				if (data.status == 0) {
					obj.remove();
				}
			});
		}
	});
});

function authManage(userid){
	var url = '<?php echo INDEX; ?>?m=manage&c=user&v=add';
	userid = userid || 0;
	userid = parseInt(userid);
	var title = userid > 0 ? '修改访问账号' : '新增访问帐号';
	Ha.common.ajax(url, 'html', 'userid='+userid, 'get', 'container', function(data){
		Ha.Dialog.show(data, title, 350, 'authManageDialog');
	}, 1);
}
</script>

<script type="text/template" id="userlisttpl">
<tr>
	<?php if (!isset($data['hiddenId'])) { ?>
	<td class="num">${userid}</td>
	<?php } ?>
	<td>${username}</td>
	<td>${get_rolename_byroleId(roleid)}</td>
	<td>{{if status == 1}} <span class="redtitle">已冻结</span> {{else}} <span class="greentitle">正常</span> {{/if}}</td>
	<td>{{if lastloginip != ''}}${lastloginip}{{else}}--{{/if}}</td>
	<td>{{if lastlogintime < 10}}尚未登录{{else}}${date('Y-m-d H:i:s', lastlogintime)}{{/if}}</td>
	<td>
		{{if roleid == 1}}
		<span class="graytitle"><?php echo Lang('priv_setting'); ?></span> | 
		<span class="graytitle"><?php echo Lang('platform').Lang('priv'); ?></span> | 
		<a href="javascript:;" class="edit" data-userid="${userid}"><?php echo Lang('edit') ?></a>
		{{else}}
		<a href="javascript:;" class="priv" data-roleid="${roleid}" data-userid="${userid}" data-username="${username}"><?php echo Lang('priv_setting'); ?></a> | 
		<a href="javascript:;" class="pf_priv" data-roleid="${roleid}" data-userid="${userid}" data-username="${username}"><?php echo Lang('platform').Lang('priv'); ?></a> | 
		<a href="javascript:;" class="edit" data-userid="${userid}"><?php echo Lang('edit') ?></a>
		{{if isrolepriv == 1}}  | 
		<a href="javascript:;" class="clear" data-userid="${userid}" data-username="${username}">清除个人权限</a> {{/if}}
		{{/if}}
	</td>
</tr>
</script>


<h2><span id="tt">访问权限管理<?php echo !empty($data['title']) ? '：'.$data['title'] : ''; ?></span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div class="title">
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="新增访问帐号">
	            </div>
	        </div>
	        <?php echo Lang('user_list') ?>
	    </div>
		<div id="dataTable">
		<table>
			<thead>
			    <tr>
			    	<?php if (!isset($data['hiddenId'])) { ?>
			    	<th>&nbsp;</th>
			    	<?php } ?>
			    	<th><?php echo Lang('username'); ?></th>
			    	<th><?php echo Lang('role'); ?></th>
					<th><?php echo Lang('status'); ?></th>
			    	<th><?php echo Lang('lastloginip'); ?></th>
			    	<th><?php echo Lang('lastlogintime'); ?></th>
			    	<th><?php echo Lang('operation'); ?></th>
			    </tr>
			</thead>
			<tbody id="userlist">
			   
			</tbody>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>