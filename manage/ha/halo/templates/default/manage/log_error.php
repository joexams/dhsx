<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'loglist';
Ha.page.colspan = 7;
Ha.page.emptyMsg = '没有找到日志数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=manage&c=log&v=ajax_list&op=<?php echo $op; ?>";

$(document).ready(function(){
	Ha.page.getList(1);

	//---------删除
	$('#loglist').on('click', 'a.delete', function(){
		var obj    = $(this);
		var logid = obj.attr('data-logid');
		if (logid > 0){
			var url = '<?php echo INDEX; ?>?m=manage&c=log&v=delete';
			var queryData = 'logid='+logid+'&op=error';
			Ha.common.ajax(url, 'json', queryData, 'post', 'container', function(data){
					if (data.status == 0) {
						obj.parent().parent('tr').remove();
					}
				}
			);
		}
		return false;
	});

	$('#fileday').on('change', function(){
		Ha.page.queryData = 'day='+$(this).val()
		Ha.page.recordNum = 0;
		Ha.page.getList(1);
	});
});
</script>

<script type="text/template" id="loglisttpl">
<tr>
	<td>${datetime}</td>
	<td>[${errorno}]</td>
	<td>${errorcontent}</td>
	<?php if ($op == 'error') { ?>
	<td>${errorfilepath}</td>
	<td style="color:red">${errorline}行</td>
	<?php } ?>
</tr>
</script>

<h2><span id="tt"><?php echo Lang('log_title'); ?></span></h2>
<div class="container" id="container">
	<div class="column whitespace cf" id="table_column">
		<div class="title">
			<div class="more" id="tblMore">
	            <div id="div_pop">
	                <select name="fileday" id="fileday" class="ipt_select">
					<?php foreach ($data['filelist'] as $value) { ?>
					<option value="<?php echo $value ?>"><?php echo $value ?></option>
					<?php } ?>
					</select>
	            </div>
	        </div>
			<?php echo Lang('log_'.ROUTE_V.'_list') ?></div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
	    	<th><?php echo Lang('date') ?></th>
	    	<th><?php echo Lang('errorno') ?></th>
	    	<th><?php echo Lang('errorcontent') ?></th>
	    	<?php if ($op == 'error') { ?>
	    	<th><?php echo Lang('errorfilepath') ?></th>
	    	<th><?php echo Lang('errorline') ?></th>
	    	<?php } ?>
	    </tr>
		</thead>
		<tbody id="loglist">
		   
		</tbody>
		</table>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>