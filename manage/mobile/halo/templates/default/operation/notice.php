<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'noticelist';
Ha.page.colspan = 6;
Ha.page.emptyMsg = '<?php echo Lang('gamenotice_empty_message')?>';
Ha.page.url = "<?php echo INDEX; ?>?m=operation&c=notice&v=ajax_init_list";
var ts = 0;

function pf_id_to_name(pf_id) {
    var pf = {0: '<?php echo Lang('all_platform')?>', '1': '<?php echo Lang('qzone')?>', '2': '<?php echo Lang('pengyou')?>', '3': '<?php echo Lang('taqq')?>', '4':'<?php echo Lang('qplus')?>', '5': '<?php echo Lang('tenpay')?>', '6': '<?php echo Lang('qqgame')?>', '7': '<?php echo Lang('official_website')?>', '8': '3366<?php echo Lang('platform')?>', '9': '<?php echo Lang('union')?>'};
    var pfstr = typeof pf[pf_id] != 'undefined' ? pf[pf_id] : '';
    return pfstr;
}

$(function(){
	/**
	 * 运营商
	 */
	if (typeof global_companylist != 'undefined') {
		$('option[value!="0"]', $('.cid')).remove();
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
	 * 清除公告
	 * @return {[type]} [description]
	 */
//	$('#noticelist').on('click', 'a.clear', function(){
//		var obj = $(this), nid = obj.attr('data-id');
//		if (nid > 0){
//			if (confirm('<?php echo Lang('delete_notice_confirm'); ?>')){
//				var url = '<?php echo INDEX; ?>?m=operation&c=notice&v=clear';
//				Ha.common.ajax(url, 'json', {nid: nid}, 'post', 'container', function(data){
//					if (data.status == 0) {
//						obj.parent().parent().remove();
//					}
//				});
//			}
//		}
//	});

	$('#div_pop').on('click', '.btn_thin1', function(e){
		noticeManage(0);
	});
});


function noticeManage(userid){
	var url = '<?php echo INDEX; ?>?m=operation&c=notice&v=setting';
	userid = userid || 0;
	userid = parseInt(userid);
	var title = userid > 0 ? '<?php echo Lang('edit_game_notice')?>' : '<?php echo Lang('add_game_notice')?>';
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
	${date('Y-m-d H:i:s', notice_time)}
	</td>
	<td>
	<a href="javascript:;" data-id="${nid}" class="view"><?php echo Lang('view').Lang('server') ?></a>
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
					<input type="submit" class="btn_sbm" value="<?php echo Lang('find') ?>">
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
			    	<th><?php echo Lang('platform') ?></th>
			    	<th><?php echo Lang('company_title') ?></th>
			    	<th><?php echo Lang('notice_content'); ?></th>
			    	<th><?php echo Lang('notice_time'); ?></th>
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