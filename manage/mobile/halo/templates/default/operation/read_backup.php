<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 30;
Ha.page.listEid = 'findlist';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '<?php echo Lang('backup_apply_empty_message')?>';
Ha.page.url = "<?php echo INDEX; ?>?m=operation&c=interactive&v=readbackup";


$(function(){
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
	}
	
	$('.cid').on('change', function() {
		var cid = $(this).val();
		if (cid > 0){
			$('option[value!="0"]', $('.sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('.sid');
		}else {
			$('option[value!="0"]', $('.sid')).remove();
		}
	});


	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1);
	});

	<?php if ($has_apply_priv) { ?>
	$('#div_pop').on('click', '.btn_thin1', function(e){
		readBackupManage();
	});
	<?php } ?>
	
	$('#get_search_submit').submit();
});
<?php if ($has_apply_priv) { ?>
function readBackupManage(){
	var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=add_readbackup';
	Ha.common.ajax(url, 'html', '', 'get', 'container', function(data){
		Ha.Dialog.show(data, '<?php echo Lang('add_backup_apply')?>', 600, 'readBackupManageDialog');
	}, 1);
}
<?php } ?>
</script>

<script type="text/template" id="findlisttpl">
<tr>
	<td class="num">${id}</td>
	<td>${username}<br>${nickname}</td>
	<td>${sid_to_name(sid)}</td>
	<td>${date('Y-m-d H:i:s', backuptime)}</td>
	<td>{{if applycontent != ''}}${applycontent}{{else}}--{{/if}}</td>
	<td>${userid == 0 ? nickname : ''}<br>${date('Y-m-d H:i:s', applytime)}</td>
</tr>
</script>


<h2><span id="tt"><?php echo Lang('read_backup')?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">
				<div class="tool_group">
					<label>
					<select name="cid" class="cid ipt_select">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>
					</label>
					<label>
					<select name="sid" class="sid ipt_select">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>
					</label>
					<label>
					<?php echo Lang('player') ?>：<input type="text" name="playername" class="ipt_txt" id="playername" />
					</label>
					<input type="hidden" name="dogetSubmit" value="1">
					<input type="submit" class="btn_sbm" value="<?php echo Lang('find')?>">
				</div>
			</div>
			</form>
		</div>
	</div>

	<div class="column cf" id="table_column">
		<div class="title">
			<?php if ($has_apply_priv) { ?>
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="<?php echo Lang('read_backup').Lang('apply')?>">
	            </div>
	        </div>
	        <?php } ?>
	        <?php echo Lang('read_backup').Lang('list')?>
	    </div>
		<div id="dataTable">
		<form id="check_post_submit" action="" name="form">
		<table>
			<thead>
			    <tr>
			    	<th>&nbsp;</th>
			    	<th><?php echo Lang('username'); ?></th>
			    	<th><?php echo Lang('server'); ?></th>
					<th><?php echo Lang('backup_time'); ?></th>
			    	<th><?php echo Lang('apply_case_content'); ?></th>
			    	<th><?php echo Lang('operation').Lang('person'); ?></th>
			    </tr>
			</thead>
			<tbody id="findlist">
			   
			</tbody>
			<?php if ($has_check_priv) { ?>
			<tfoot>
				<tr>
					<td class="num"><input type="checkbox" id="all_check" value="1"></td>
					<td colspan="7">
						<input type="radio" name="checktype" value="2"><span class="greentitle"><?php echo Lang('approval'); ?></span>
						<input type="radio" name="checktype" value="3"><?php echo Lang('closed'); ?>
						<input type="radio" name="checktype" value="4"><?php echo Lang('ignore'); ?>
						<input type="hidden" name="doSubmit" value="1">
						<input type="submit" class="btn_sbm" id="check_btnsubmit" value="<?php echo Lang('submit'); ?>">
					</td>
				</tr>
			</tfoot>
			<?php } ?>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>
