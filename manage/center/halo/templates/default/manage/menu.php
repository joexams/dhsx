<?php defined('IN_G') or exit('No permission resources.');?>
<script type="text/javascript" src="static/js/jquery.treetable-3.0.0.js"></script>
<script type="text/javascript" src="static/js/jquery.treetable-ajax-persist.js"></script>
<script type="text/javascript" src="static/js/persist-min.js"></script>
<script type="text/javascript">
$(function(){
	$("#menutree").agikiTreeTable({persist: true, persistStoreName: "files", indent: 20});
});

function noderemove(menuid, menuname, depth) {
	if (depth < 3){
		msg = "<?php echo Lang('delete_menu_confirm') ?>";
	}else {
		msg = "<?php echo Lang('delete_menu_confirm_more') ?>";
	}
	msg = msg.replace(/\${menuname}/g, menuname);
	if (confirm(msg)) {
		var url = '<?php echo INDEX; ?>?m=manage&c=menu&v=delete';
		var queryData = 'menuid='+menuid+'&menuname='+menuname;
		Ha.common.ajax(url, 'json', queryData, 'post');
	}
}

function menuManage(mid, parentid, menuname){
	var url = '<?php echo INDEX; ?>?m=manage&c=menu&v=add';
	mid = mid || 0;
	parentid = parentid || 0;
	mid = parseInt(mid);
	pid = parseInt(parentid);
	var title = mid > 0 ? '<?php echo Lang('edit_menu')?>' : '<?php echo Lang('add_menu')?>';
	var queryData = 'mid='+mid;
	if (pid > 0) {
		title = '<?php echo Lang('add_menuname_menu')?>';
		queryData += '&pid='+pid;
	}
	Ha.common.ajax(url, 'html', queryData, 'get', 'container', function(data){
		Ha.Dialog.show(data, title, 500, 'menuManageDialog');
	}, 1);
}
</script>

<h2><span id="tt"><?php echo Lang('menu_title'); ?></span></h2>
<div class="container" id="container">
<div class="column whitespace cf" id="table_column">
	<div class="title">
        <div class="more" id="tblMore">
            <div id="div_pop">
                <input class="btn_thin1" type="button" onclick="menuManage(0)" value="<?php echo Lang('add_menu')?>">
            </div>
        </div>
        <?php echo Lang('menu_list') ?>
    </div>
	<div class="column cf">
		<table id="menutree" class="treetable">
		<thead>
			<th><?php echo Lang('menu_name')?></th>
			<th><?php echo Lang('operation')?></th>
		</thead>
		<tbody>
			<?php echo $categorys ?>
		</tbody>
		</table>
	</div>
</div>
</div>