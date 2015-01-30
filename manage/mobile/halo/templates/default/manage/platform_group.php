<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'platformlist';
Ha.page.colspan = 5;
Ha.page.emptyMsg = '<?php echo Lang('no_find_data')?>';
Ha.page.url = "<?php echo INDEX; ?>?m=manage&c=priv&v=platform_group&format=json";

$(function(){
	Ha.page.getList(1);
	/**
	 * 删除
	 * @return {[type]} [description]
	 */
	$('#platformlist').on('click', 'a.delete', function(){
		var id = $(this).attr('data-id');
		if (id > 0){
			var url = "<?php echo INDEX; ?>?m=manage&c=priv&v=platform_group_delete";
			Ha.common.ajax(url, 'json', 'gid='+id, 'get');
		}
	});
	//修改
	$('#platformlist').on('click', 'a.edit', function(){
		var id = $(this).attr('data-id');
		if (id > 0){
			platformManage(id);
		}
	});

	$('#div_pop').on('click', '.btn_thin1', function(e){
		platformManage(0);
	});
});

function platformManage(gid){
	var url = '<?php echo INDEX; ?>?m=manage&c=priv&v=platform_group_add';
	gid = gid || 0;
	gid = parseInt(gid);
	var title = gid > 0 ? '<?php echo Lang('edit_platform_priv_group')?>' : '<?php echo Lang('add_platform_priv_group')?>';
	Ha.common.ajax(url, 'html', 'gid='+gid, 'get', 'container', function(data){
		Ha.Dialog.show(data, title, 450, 'platformManageDialog');
	}, 1);
}
</script>

<script type="text/template" id="platformlisttpl">
<tr>
<td class="num">${gid}</td>
<td>${gname}</td>
<td>${description}</td>
<td>${date('Y-m-d H:i', dateline)}</td>
<td>
<a href="javascript:;" data-id="${gid}" class="edit"><?php echo Lang('edit') ?></a> | 
<a href="javascript:;" data-id="${gid}" class="delete"><?php echo Lang('delete') ?></a>
<textarea style="display:none">${cids}</textarea>
</td></tr>
</script>


<h2><span id="tt"><?php echo Lang('platform_priv_title'); ?></span></h2>
<div class="container" id="container">
<div class="column whitespace cf" id="table_column">
		<div class="title">
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="<?php echo Lang('add_platform_priv_group')?>">
	            </div>
	        </div>
	        <?php echo Lang('platform_group_list') ?>
	    </div>
		<div id="dataTable">
		<table>
			<thead>
			    <tr>
			    	<th>&nbsp;</th>
			    	<th><?php echo Lang('group_name'); ?></th>
			    	<th><?php echo Lang('group_description'); ?></th>
			    	<th><?php echo Lang('date'); ?></th>
			    	<th><?php echo Lang('operation') ?></th>
			    </tr>
			</thead>
			<tbody id="platformlist">
			   
			</tbody>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>