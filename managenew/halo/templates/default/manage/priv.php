<script type="text/javascript" src="static/js/jquery.ztree.core.min.js"></script>
<script type="text/javascript" src="static/js/jquery.ztree.excheck.min.js"></script>
<script type="text/javascript">
//菜单树
var setting = {
	check: {
		enable: true,
		chkStyle: "checkbox",
		chkboxType: {
			"Y" : "",
			"N" : "" 
		}
	},
	data: {
		simpleData: {
			enable: true
		}
	},
	callback: {
		beforeDrag: beforeDrag,
		beforeCheck: beforeCheck
	}
};
var zNodes = <?php echo $data['treelist'] ?>;

function beforeDrag(treeid, treeNode){
	return false;
}

function addNode(node) {
	if (node != null) {
		if ($('#menuid_'+node.id).html() == null) {
			$('#menutree').append('<input type="hidden" id="menuid_'+node.id+'" name="menuid[]" value="'+node.id+'" />');
		}
		if (!isNaN(parseInt(node.depth)) && parseInt(node.depth) >= 0) {
			addNode(node.getParentNode());
		}
	}
}

function beforeCheck(treeid, treeNode){
	//点击之前的状态
	var checkedmid = treeNode.id;
	if (treeNode.checked === false){
		if ($('#menuid_'+checkedmid).html() == null) {
			$('#menutree').append('<input type="hidden" id="menuid_'+checkedmid+'" name="menuid[]" value="'+checkedmid+'" />');
		}

		addNode(treeNode.getParentNode());

	}else {
		$('#menuid_'+checkedmid).remove();
		if (typeof treeNode.children != 'undefined') {
			for(var key in treeNode.children) {
				$('#menuid_'+treeNode.children[key].id).remove();
			}
		}
	}
}

$(document).ready(function (){
	$('#post_pop_submit').on('submit', function(e){
		e.preventDefault();
		$('#pop_btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=priv&v=setting',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0:  alertclassname = 'alert_success';  break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#pop_op_tips').attr('class', alertclassname);
					$('#pop_op_tips').children('p').html(data.msg);
					$('#pop_op_tips').fadeIn();
					setTimeout( function(){
						$('#pop_op_tips').fadeOut();
						$('#pop_btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				}
			});
	});
	
	$.fn.zTree.init($("#menutree"), setting, zNodes);

	if (zNodes.length > 0){
		for(var key in zNodes){
			if (zNodes[key].checked == 1){
				$('#menutree').append('<input type="hidden" id="menuid_'+zNodes[key].id+'" name="menuid[]" value="'+zNodes[key].id+'" />');
			}
		}
	}

	$('a', $('#menutree')).on({
		mouseover: function(){
			$(this).addClass('ruled');
		},
		mouseout: function(){
			$(this).removeClass('ruled');
		}
	});
});
</script>

<div class="twocolumn">
	<div class="header">
		<h2><?php echo Lang('priv_list') ?></h2>
	</div>
	<form method="post" id="post_pop_submit" action="<?php echo INDEX; ?>?m=manage&c=priv&v=setting">
	<div class="content nomargin ztree" id="menutree">
		<!-- Begin example table data -->
		
		<!-- End pagination -->
		<br class="clear">
	</div>
	<br class="clear">
	<p>
		<input type="hidden" name="doSubmit" value="1" />
		<input type="hidden" name="roleid" value="<?php echo $roleid; ?>">
		<input type="hidden" name="userid" value="<?php echo $userid; ?>">
		<input type="submit" id="pop_btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
		<input type="button" id="pop_cancelsubmit" style="cursor:pointer" onclick="dialog.close();" class="button" value="<?php echo Lang('cancel'); ?>">
	</p>
	</form>

	<div id="pop_op_tips" style="display: none;"><p></p></div>
</div>