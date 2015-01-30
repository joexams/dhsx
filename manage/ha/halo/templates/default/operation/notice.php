<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'noticelist';
Ha.page.colspan = 6;
Ha.page.emptyMsg = '尚未发布游戏公告，若需要发布，点击右侧按钮，发布游戏公告。';
Ha.page.url = "<?php echo INDEX; ?>?m=operation&c=notice&v=ajax_init_list";
var ts = 0;

function pf_id_to_name(pf_id) {
    var pf = {0: '所有平台', '1': 'qq空间', '2': '朋友网', '3': '微博', '4':'q加', '5': '财付通', '6': 'qq游戏', '7': '官网', '8': '3366平台', '9': '联盟'};
    var pfstr = typeof pf[pf_id] != 'undefined' ? pf[pf_id] : '';
    return pfstr;
}

$(function(){
	/**
	 * 运营商
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
	}
	/**
	 * 切换平台
	 * @return {[type]} [description]
	 */
	$('.cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('option[value!="0"]', $('.sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('.sid');
		}else {
			$('option[value!="0"]', $('.sid')).remove();
		}
	});
	
	/**
	 * 选项卡
	 * @return {[type]} [description]
	 */
	$('.first_level_tab').on('click', 'a.noticetype', function(){
		$('.current', $('.first_level_tab')).removeClass('current');
		$(this).parent().addClass('current');
		var type = $(this).attr('type');
		switch (type){
			case '0':
				$('#submit_area').show();
				$('#list_area').hide();
				$('#submit_tbody').show();
				$('#clear_tbody').hide();
				$('#type').val(0);
				break;
			case '1':
				$('#list_area').show();
				$('#submit_area').hide();
				if ($('#noticelist').find('tr').size() <= 0){
					Ha.page.recordNum = 0;
					Ha.page.getList(1, function(data){
						ts = data.ts || 0;
					});	
				}
				break;
			case '2':
				$('#submit_area').show();
				$('#list_area').hide();
				$('#submit_tbody').hide();
				$('#clear_tbody').show();
				$('#type').val(1);
				break;
		}
	});
	/**
	 * 搜索
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1, function(data){
			ts = data.ts || 0;
		});
	});
	$('#get_search_submit').submit();
	/**
	 * 公告列表
	 * @return {[type]} [description]
	 */
	$('#noticelist').on('click', 'a.view', function(){
		var obj = $(this), nid = obj.attr('data-id');
		if (nid > 0){
			var url = '<?php echo INDEX; ?>?m=operation&c=notice&v=ajax_init_info';
			Ha.common.ajax(url, 'html', {nid: nid}, 'get', 'container', function(data){
				Ha.Dialog.show(data, '<?php echo Lang('view_notice_server'); ?>', 500, 'notice_view');
			}, 1);
		}
	});
	/**
	 * 清除提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#pop_post_submit').live('submit', function(e){
		e.preventDefault();
		var obj = $(this);
		var url = '<?php echo INDEX; ?>?m=operation&c=notice&v=clear';
		Ha.common.ajax(url, 'json', obj.serialize(), 'get');
		// $.ajax({
		// 		url: '<?php echo INDEX; ?>?m=operation&c=notice&v=clear',
		// 		data: obj.serialize(),
		// 		dataType: 'json',
		// 		type: 'GET',
		// 		success: function(data){
		// 			var alertclassname = '', time = 2;
		// 			switch (data.status){
		// 				case 0: alertclassname = 'alert_success'; break;
		// 				case 1: alertclassname = 'alert_error'; break;
		// 			}
		// 			$('#list_op_tips').attr('class', alertclassname);
		// 			$('#list_op_tips').children('p').html(data.msg);
		// 			$('#list_op_tips').fadeIn();
		// 			//dialog.close();
		// 			setTimeout( function(){
		// 				$('#list_op_tips').fadeOut();
		// 				$('#pop_btnsubmit').removeAttr('disabled');
		// 			}, ( time * 1000 ));
		// 		},
		// 		error: function() {
		// 			$('#list_op_tips').attr('class', 'alert_error');
		// 			$('#list_op_tips').children('p').html('公告发送失败，可能部分游戏服已经发送成功，请查看【游戏公告列表】确认！');
		// 			$('#list_op_tips').fadeIn();
		// 			setTimeout( function(){
		// 				$('#list_op_tips').fadeOut();
		// 				$('#pop_btnsubmit').removeAttr('disabled');
		// 			}, 4000);
		// 		}
		// 	});
		return false;
	});
	/**
	 * 清除公告
	 * @return {[type]} [description]
	 */
	$('#noticelist').on('click', 'a.clear', function(){
		var obj = $(this), nid = obj.attr('data-id');
		if (nid > 0){
			if (confirm('<?php echo Lang('delete_notice_confirm'); ?>')){
				var url = '<?php echo INDEX; ?>?m=operation&c=notice&v=clear';
				Ha.common.ajax(url, 'json', {nid: nid}, 'get', 'container', function(data){
					if (data.status == 0) {
						obj.parent().parent().remove();
					}
				});
			}
		}
	});

	$('#div_pop').on('click', '.btn_thin1', function(e){
		noticeManage(0);
	});
});


function noticeManage(userid){
	var url = '<?php echo INDEX; ?>?m=operation&c=notice&v=setting';
	userid = userid || 0;
	userid = parseInt(userid);
	var title = userid > 0 ? '修改游戏公告' : '新增游戏公告';
	Ha.common.ajax(url, 'html', 'userid='+userid, 'get', 'container', function(data){
		Ha.Dialog.show(data, title, 500, 'noticeManageDialog');
	}, 1);
}
</script>

<script type="text/template" id="serverchklisttpl">
<div>
	<li><input type="checkbox" name="sid[]" value="${sid}">${name}</li>
</div>
</script>

<script type="text/template" id="noticelisttpl">
	<tr id="notice_${nid}">
	<td class="num">${nid}</td>
	<td>${pf_id_to_name(pf_id)}</td>
	<td>${name}</td>
	<td>
	{{if urllink != ''}}
	<a href="${urllink}" target="_blank">${content}</a>
	{{else}}
	${content}
	{{/if}}
	</td>
	<td>
	{{if lastdate - <?php echo time() ?> < 0 }}
	<span class="redtitle"><?php echo Lang('is_over') ?></span>
	{{else}}
	${date('Y-m-d H:i:s', lastdate)}
	{{/if}}
	</td>
	<td>
	<a href="javascript:;" data-id="${nid}" class="view"><?php echo Lang('view').Lang('server') ?></a>
	<a href="javascript:;" data-id="${nid}" class="clear"><?php echo Lang('clear_notice') ?></a>
	</td>
	</tr>
</script>


<h2><span id="tt"><?php echo Lang('publish_game_notice'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form id="get_search_submit" method="get" name="form">
				<div class="tool_group">
					 <label>
						<select name="cid" class="cid ipt_select">
							<option value="0"><?php echo Lang('operation_platform') ?></option>
						</select></label>
					 <label>
					 	<select name="sid" class="sid ipt_select">
							<option value="0"><?php echo Lang('all_server') ?></option>
						</select></label>
					<input type="hidden" name="dogetSubmit" value="1">
					<input type="submit" class="btn_sbm" value="查 询">
				</div>
				</form>
			</div>
		</div>
	</div>

	<div class="column cf" id="table_column">
		<div class="title">
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <input class="btn_thin1" type="button" value="<?php echo Lang('publish_game_notice') ?>">
	            </div>
	        </div>
	        <?php echo Lang('game_notice') ?>
	    </div>
		<div id="dataTable">
		<table>
			<thead>
			    <tr>
			    	<th>&nbsp;</th>
			    	<th>QQ平台</th>
			    	<th><?php echo Lang('company_title') ?></th>
			    	<th><?php echo Lang('notice_content'); ?></th>
			    	<th><?php echo Lang('overdate'); ?></th>
			    	<th><?php echo Lang('operation'); ?></th>
			    </tr>
			</thead>
			<tbody id="noticelist">
			   
			</tbody>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>