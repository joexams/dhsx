<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, noticelist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=operation&c=notice&v=ajax_list&top="+index+"&recordnum="+recordNum;	
	pageIndex = index;
	$( "#noticelist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}

function getsearchList(index){
	var query = "<?php echo INDEX; ?>?m=operation&c=notice&v=ajax_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#noticelist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: function(data){
				showList(data, 1);
			}
		});
	});
}

function showList( data, type) {
	if (data.status == -1){
		$('#noticelist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), noticelist = data.list;
		if (type != undefined && type == 1){
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchList });
		}else {
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		}
		$( "#noticelist" ).empty();
		if (data.count > 0){
			$( "#noticelisttpl" ).tmpl( noticelist, {ts: data.ts} ).prependTo( "#noticelist" );
			$( "#noticelist" ).stop(true,true).hide().slideDown(400);
		}
	}
}

function pf_id_to_name(pf_id) {
    var pf = {0: '所有平台', '1': 'qq空间', '2': '朋友网', '3': '微博', '4':'q加', '5': '财付通', '6': 'qq游戏', '7': '官网', '8': '3366平台', '9': '联盟'};
    var pfstr = typeof pf[pf_id] != 'undefined' ? pf[pf_id] : '';
    return pfstr;
}

$(document).ready(function(){
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
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=notice&v=setting',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: alertclassname = 'alert_success'; break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					document.getElementById('post_submit').reset();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function() {
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		return false;
	});
	/**
	 * 选项卡
	 * @return {[type]} [description]
	 */
	$('.first_level_tab').on('click', 'a.noticetype', function(){
		$('.active').removeClass('active');
		$(this).addClass('active');
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
					getList(1);	
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
		recordNum = 0;
		getsearchList(1);
	});
	/**
	 * 公告列表
	 * @return {[type]} [description]
	 */
	$('#noticelist').on('click', 'a.view', function(){
		var obj = $(this), nid = obj.attr('data-id');
		if (nid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=notice&v=ajax_info',
				data: {nid: nid},
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						dialog = $.dialog({id: 'notice_view', width: 500, title: '<?php echo Lang('view_notice_server'); ?>'});
						/*var strHtml = [
							'<form id="pop_post_submit" method="post" action="">',
							'<div class="content"><ul class="dash2">',
							$('#serverchklisttpl').tmpl(data.list).text(),
							'</ul>',
							'<p>',
							'<input type="hidden" name="nid" value="'+nid+'"/>',
							'<input id="pop_btnsubmit" type="submit" value="<?php echo Lang("clear") ?>" class="button_link">',
							'<input type="button" onclick="dialog.close();" value="<?php echo Lang("close") ?>"></p>',
							'</div>',
							'</form>'
						].join('');*/
						var strHtml = [
							'<form id="pop_post_submit" method="post" action="">',
							'<div class="content"><ul class="dash2">'
							];

							for (var key in data.list) {
								strHtml.push('<li><input type="checkbox" name="sid[]" value="'+data.list[key].sid+'">'+data.list[key].name+'</li>');
							}
							strHtml.join('');
							strHtml += [
							'</ul>',
							'<p>',
							'<input type="hidden" name="nid" value="'+nid+'"/>',
							'<input id="pop_btnsubmit" type="submit" value="<?php echo Lang("clear") ?>" class="button_link">',
							'<input type="button" onclick="dialog.close();" value="<?php echo Lang("close") ?>"></p>',
							'</div>',
							'</form>'
							].join('');	
							//strHtml.join('');
						dialog.content(strHtml);
					}
				},
				error: function() {
					dialog.content('<div>数据加载失败...</div>');
				}
			});
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
		$('#pop_btnsubmit').attr('disabled', 'disabled');
		$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=notice&v=clear',
				data: obj.serialize(),
				dataType: 'json',
				type: 'GET',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: alertclassname = 'alert_success'; break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					//dialog.close();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
						$('#pop_btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ));
				},
				error: function() {
					$('#list_op_tips').attr('class', 'alert_error');
					$('#list_op_tips').children('p').html('公告发送失败，可能部分游戏服已经发送成功，请查看【游戏公告列表】确认！');
					$('#list_op_tips').fadeIn();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
						$('#pop_btnsubmit').removeAttr('disabled');
					}, 4000);
				}
			});
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
				$.ajax({
					url: '<?php echo INDEX; ?>?m=operation&c=notice&v=clear',
					data: {nid: nid},
					dataType: 'json',
					success: function(data){
						var alertclassname = '', time = 2;
						switch (data.status){
							case 0: 
								alertclassname = 'alert_success'; 
								obj.parent().parent().remove();
								break;
							case 1: alertclassname = 'alert_error'; break;
						}
						$('#list_op_tips').attr('class', alertclassname);
						$('#list_op_tips').children('p').html(data.msg);
						$('#list_op_tips').fadeIn();
						setTimeout( function(){
							$('#list_op_tips').fadeOut();
						}, ( time * 1000 ) );
					}
				});
			}
		}
	});
});
</script>

<script type="text/template" id="serverchklisttpl">
<div>
	<li><input type="checkbox" name="sid[]" value="${sid}">${name}</li>
</div>
</script>

<script type="text/template" id="noticelisttpl">
	<tr id="notice_${nid}">
	<td>${nid}</td>
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
	{{if lastdate - $item.ts < 0 }}
	<span style="color:red"><?php echo Lang('is_over') ?></span>
	{{else}}
	${date('Y-m-d H:i:s', lastdate)}
	{{/if}}
	</td>
	<td>
	<a href="javascript:;" data-id="${nid}" class="view"><?php echo Lang('view').Lang('server') ?></a>
	<a href="javascript:;" data-id="${nid}" class="clear"><?php echo Lang('clear_notice') ?></a>
	</td>
	<td>&nbsp;</td>
	</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('publish_game_notice') ?></span></a></li>
	</ul>
	<br class="clear">
	<ul class="first_level_tab">
		<li><a href="javascript:;" type="0" class="noticetype active"><?php echo Lang('publish_game_notice') ?></a></li>
		<li><a href="javascript:;" type="1" class="noticetype"><?php echo Lang('game_notice') ?></a></li>
		<!-- <li><a href="javascript:;" type="2" class="noticetype"><?php echo Lang('clear_notice') ?></a></li> -->
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="content" id="submit_area">
			<!-- Begin form elements -->			
				<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=operation&c=notice&v=setting" method="post">
					<table class="global" width="100%" cellpadding="0" cellspacing="0">
						<tr class="betop">
							<th style="width: 10%;"><?php echo Lang('server') ?></th>
							<td style="width: 30%;"> 
								<select name="cid" class="cid">
									<option value="0"><?php echo Lang('company_name') ?></option>
								</select>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<th><?php echo Lang('server') ?></th>
							<td> 
								<select multiple name="sid[]" class="sid" style="width:200px;height:300px;">
									
								</select>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tbody id="submit_tbody">
							<tr>
								<th><?php echo Lang('notice_platform') ?></th>
								<td>
                                    <select name="pf_id" id="pf_id">
                                      <option value="0">所有平台</option>
                                      <option value="1">qq空间</option>
                                      <option value="2">朋友网</option>
                                      <option value="3">微博</option>
                                      <option value="4">q加</option>
                                      <option value="5">财付通</option>
                                      <option value="6">qq游戏</option>
                                      <option value="7">官网</option>
                                      <option value="8">3366平台</option>
                                      <option value="9">联盟</option>
                                    </select> 
								</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th><?php echo Lang('notice_content') ?></th>
								<td>
									<input type="text" name="content" id="content" style="width: 50%" >
									<?php echo Lang('max_notice_content_char_length') ?>
								</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th><?php echo Lang('urllink') ?></th>
								<td>
									<input type="text" name="urllink" id="urllink" style="width: 60%" >
								</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th><?php echo Lang('overdate') ?></th>
								<td> 
									<input type="text" name="lastdate" id="lastdate" value="<?php echo date('Y-m-d H:i:s',  time()+3600) ?>" >
								</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td> 
									<p>
									<input type="hidden" name="doSubmit" value="1">
									<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
									<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
									</p>
								</td>
								<td>&nbsp;</td>
							</tr>
						</tbody>
						<tbody id="clear_tbody" style="display:none">
							<tr>
								<td>&nbsp;</td>
								<td>
									<p>
									<input type="hidden" name="type" id="type" value="0">
									<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('clear'); ?>">
									</p>
								</td>
								<td>&nbsp;</td>
							</tr>
						</tbody>
					</table>
			    </form>
                 <div id="op_tips" style="display: none;"><p></p></div>
			<!-- End form elements -->
		</div>

		<div class="content" id="list_area" style="display:none">
			<div class="nav singlenav">
				<form id="get_search_submit" action="<?php echo INDEX; ?>?m=operation&c=notice&v=ajax_list" method="get" name="form">
				<ul class="nav_li">
					<li>
						<p>
							<select name="cid" class="cid">
								<option value="0"><?php echo Lang('operation_platform') ?></option>
							</select>
							<select name="sid" class="sid">
								<option value="0"><?php echo Lang('all_server') ?></option>
							</select>
						</p>
					</li>
					<li class="nobg">
						<p>
							<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
							<input type="hidden" name="dogetSubmit" value="1">
						</p>
						<p>
						</p>
					</li>
				</ul>
				</form>
			</div>
			<div id="list_op_tips" style="display: none;width:60%"><p></p></div>
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th style="width:50px;">ID</th>
						<th style="width:80px;">QQ平台</th>
						<th	style="width:100px;"><?php echo Lang('company_title') ?></th>
						<th style="width:250px;"><?php echo Lang('notice_content'); ?></th>
						<th style="width:120px;"><?php echo Lang('overdate'); ?></th>
						<th style="width:150px;"><?php echo Lang('operation'); ?></th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody id="noticelist">

				</tbody>
			</table>
			<div class="pagination pager" id="pager"></div>
		</div>

	</div>
</div>
