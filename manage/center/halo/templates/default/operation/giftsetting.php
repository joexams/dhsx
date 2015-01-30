<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'giftlist';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '<?php echo Lang('giftsetting_empty_message'); ?>';
Ha.page.url = "<?php echo INDEX; ?>?m=operation&c=giftsetting&v=ajax_list";
$(function() {
	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1);
	});
	Ha.page.getList(1);
	$('#giftlist').on('click', 'a.view', function() {
		var obj    = $(this);
		var giftid = obj.attr('data-giftid');
		var urla = '<?php echo INDEX; ?>?m=operation&c=giftsetting&v=ajax_giftsetting_log';
		var title = 'ID: '+giftid;
		Ha.common.ajax(urla, 'html', 'giftid='+giftid, 'get', 'container', function(data){
			Ha.Dialog.show(data, title, 600, 'authManageDialog');
		}, 1);
	});
	$('#giftlist').on('click', 'a.edit', function() {
		var obj    = $(this);
		var giftid = obj.attr('data-giftid');
		if (giftid > 0){
			giftSetting(giftid);
		}
	});
	$('#div_pop').on('click', '.btn_thin1', function(e){
		giftSetting(0);
	});
});
	
function giftSetting(giftid){
	var urlb = '<?php echo INDEX; ?>?m=operation&c=giftsetting&v=add';
	giftid = giftid || 0;
	giftid = parseInt(giftid);
	var title = giftid > 0 ? '<?php echo Lang('edit_giftsetting'); ?>' : '<?php echo Lang('add_gift_title'); ?>';
	Ha.common.ajax(urlb, 'html', 'giftid='+giftid, 'get', 'container', function(data){
		Ha.Dialog.show(data, title, 700, 'itemApplyManageDialog');
	}, 1);
}

</script>

<script type="text/template" id="giftlisttpl">
<tr>
	<td>${giftid}</td>
	<td>${giftname}</td>
	<td>{{if gifttype==1}}<?php echo Lang('each_server_everyday_number'); ?> {{else}} <?php echo Lang('each_server_totle'); ?>{{/if}}</td>
	<td>${limitnumber}</td>
	<td>${starttime}</td>
	<td>${endtime}</td>
	<td>${message}</td>
	<td>
		<a href="javascript:;" class="edit" data-giftid="${giftid}"><?php echo Lang('edit') ?></a>
		<a href="javascript:;" class="view" data-giftid="${giftid}"><?php echo Lang('view'); ?></a>
	</td>
</tr>
</script>

<h2><span id="tt"><?php echo Lang('gift_title'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">
				<div class="tool_group">
					<label>
					<?php echo Lang('giftname') ?>：<input type="text" class="ipt_txt" name="giftname" value=""/>
					</label>
					<label>
					<?php echo Lang('giftid'); ?>： <input type="text" class="ipt_txt" name="giftid" value="" />
					</label>
					<input type="hidden" name="dogetSubmit" value="1">
					<input type="submit" class="btn_sbm" value="<?php echo Lang('find'); ?>">
				</div>
			</div>
			</form>
		</div>
	</div>

	<div class="column cf" id="table_column">
		<div class="title">
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="<?php echo Lang('add_gift_title') ?>">
	            </div>
	        </div>
	        <?php echo Lang('gift_list') ?>
	    </div>
		<div id="dataTable">
		<table>
			<thead>
			    <tr>
			    	<th>ID</th>
			    	<th><?php echo Lang('giftname'); ?></th>
			    	<th><?php echo Lang('gifttype'); ?></th>
			    	<th><?php echo Lang('limitnumber'); ?></th>
			    	<th><?php echo Lang('starttime'); ?></th>
			    	<th><?php echo Lang('endtime'); ?></th>
			    	<th><?php echo Lang('message'); ?></th>
			    	<th><?php echo Lang('operation'); ?></th>
			    </tr>
			</thead>
			<tbody id="giftlist">
			   
			</tbody>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>
