<?php defined('IN_G') or exit('No permission resources.');?>
<link href="static/css/jquery.treetable.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="static/js/jquery.treetable-3.0.0.js"></script>
<script type="text/javascript" src="static/js/jquery.treetable-ajax-persist.js"></script>
<script type="text/javascript" src="static/js/persist-min.js"></script>
<script type="text/javascript">
$(function(){
	$("#menutree").agikiTreeTable({persist: true, persistStoreName: "files", indent: 20});
});

function noderemove(blockid, blockname, depth) {
	var msg = '';
	if (depth < 3){
		msg = "<?php echo Lang('delete_block_confirm') ?>";
	}else {
		msg = "<?php echo Lang('delete_block_confirm_more') ?>";
	}
	msg.replace(/${blockname}/, blockname);

	if (confirm(msg)) {
		var url = '<?php echo INDEX; ?>?m=manage&c=block&v=delete';
		Ha.common.ajax(url, 'json', 'blockid='+blockid+'&blockname='+blockname, 'post');
	}
	
}

function blockManage(bid, pid, menuname){
	var url = '<?php echo INDEX; ?>?m=manage&c=block&v=add';
	bid = bid || 0;
	pid = pid || 0;
	bid = parseInt(bid);
	pid = parseInt(pid);
	var title = bid > 0 ? '修改模块' : '新增模块';
	var queryData = 'bid='+bid;
	if (pid > 0) {
		title = '添加【'+menuname+'】子模块';
		queryData += '&pid='+pid;
	}
	Ha.common.ajax(url, 'html', queryData, 'get', 'container', function(data){
		Ha.Dialog.show(data, title, 500, 'blockManageDialog');
	}, 1);
}
</script>


<h2><span id="tt"><?php echo Lang('block_title'); ?></span></h2>
<div class="container" id="container">
<div class="column whitespace cf" id="table_column">
	<div class="title">
        <div class="more" id="tblMore">
            <div id="div_pop">
                <input class="btn_thin1" type="button" onclick="blockManage(0)" value="新增模块">
            </div>
        </div>
        <?php echo Lang('block_list') ?>
    </div>
	<div class="column cf">
		<table id="menutree" class="treetable">
		<thead>
			<th>模块名</th>
			<th>操作</th>
		</thead>
		<tbody>
			<?php echo $categorys ?>
		</tbody>
		</table>
	</div>
</div>
</div>