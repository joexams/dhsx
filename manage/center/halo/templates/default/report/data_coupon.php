<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, couponlist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=report&c=coupon&v=init&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#couponlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: showList
		});
	});
}

function showList( data ) {
	if (data.status == -1){
		$('#couponlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize );
		couponlist = data.list;
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#couponlist" ).empty();
		if (data.count > 0){
			$( "#couponlisttpl" ).tmpl( couponlist ).prependTo( "#couponlist" );
			$( "#couponlist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#couponlist" ).parent().parent('div.content').css('height', $('#couponlist').parent('table.global').css('height'));
			}
		}
	}
}

function sid_to_name(sid) {
	if (sid > 0 && typeof global_serverlist != 'undefined') {
		for(var key in global_serverlist) {
			if (global_serverlist[key].sid == sid) {
				return global_serverlist[key].name+'-'+global_serverlist[key].o_name;
			}
		}
	}
	return '';
}

var dialog  = typeof dialog != 'undefined' ? null : '';
$(function() {
	setTimeout(function() {
		if (typeof global_companylist != 'undefined') {
			$('#cid option[value!="0"]').remove();
			$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
		}
	}, 250);

	$('#cid1').on('change', function() {
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('#sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(global_serverlist).appendTo('#sid1');
		}
	});

	$('#cid2').on('change', function() {
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined') {
			$('#sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(global_serverlist).appendTo('#sid2');
		}
	});

	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		recordNum = 0;
		getList(1);
	});
	$('#get_search_submit').submit();
	/**
	 * 查看
	 * @return {[type]} [description]
	 */
	$('#couponlist').on('click', 'a.view', function() {
		var id = $(this).parent('td').attr('data-id');
		if (id > 0) {
			dialog = $.dialog({id: 'coupon_view', title: '查看兑换券记录', width:960});
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=coupon&v=show',
				data: {id: id},
				success: function(data){
					dialog.content(data);
				}
			});
		}
		return false;
	});
	/**
	 * 添加、修改
	 * @return {[type]} [description]
	 */
	$('#couponlist').on('click', 'a.edit', function() {
		var id = $(this).parent('td').attr('data-id');
		if (id > 0) {
			if ($('#submit_area').is(':hidden')){
				$('#extentfold').click();
			}
			for(var key in couponlist) {
				if (couponlist[key].id == id) {
					$('#cid1').val(couponlist[key].cid);
					$('#cid1').change();
					$('#sid1').val(couponlist[key].sid);
					$('#couponid').val(id);
					$('#name').val(couponlist[key].name);
					$('#item_val').val(couponlist[key].item_val);
					$('#num').val(couponlist[key].num);
					$('#juche').val(couponlist[key].juche);
					$('#edate').val(couponlist[key].edate);

					$('#btncancel').show();
					$('#btnreset').hide();
					$('#username').focus();
					$('#username').css('border', '1px solid #E6791C');
					setTimeout( function(){	$('#name').css('border', ''); }, ( 2000 ) );
				}
			}
		}
		return false;
	});
	//--------取消修改
	$('#btncancel').on('click', function(){
		$('#couponid').val('0');
		document.getElementById('post_submit').reset();

		$('#btncancel').hide();
		$('#btnreset').show();
	});
	/**
	 * 导出
	 * @return {[type]} [description]
	 */
	$('#couponlist').on('click', 'a.export', function() {
		var obj = $(this).parent('td');
		var id = obj.attr('data-id');
		var sname = obj.attr('data-sname');
		var name = obj.attr('data-name');
		if (id > 0) {
			var url = '<?php echo INDEX; ?>?m=report&c=coupon&v=export&id='+id+'&sname='+sname+'&name='+name;
			location.href = url;
		}
		return false;
	});
	/**
	 * 删除 
	 * @return {[type]} [description]
	 */
	$('#couponlist').on('click', 'a.delete', function() {
		var obj = $(this).parent('td'), id = obj.attr('data-id');
		if (id > 0 && confirm('您确定要删除此兑换券吗？删除后游戏服将不再支持此兑换券兑换功能！')) {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=report&c=coupon&v=delete',
				dataType: 'josn',
				data: {id: id},
				success: function(data) {
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							obj.remove();
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
	});
	//展开
	$('#extentfold').on('click', function(){
		var hidden = '<?php echo Lang("hidden"); ?>', show = '<?php echo Lang("show"); ?>';
		var obj = $(this);
		$('#submit_area').toggle("normal", function(){
			if ($(this).is(':hidden')){
				obj.html(show);
			}else {
				obj.html(hidden);
			}
		});
	});

	$('#post_submit').on('submit', function(e) {
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), cid = $('#cid1').val(), sid = $('#sid1').val();
		if (cid > 0 && sid > 0) {
			$.ajax({
					url: '<?php echo INDEX; ?>?m=report&c=coupon&v=add',
					data: objform.serialize(),
					dataType: 'json',
					type: 'POST',
					success: function(data){
						var alertclassname = '', time = 2;
						switch (data.status){
							case 0: 
								alertclassname = 'alert_success'; 
								if (data.editflag == 1){
									getList( pageIndex );

									for(var key in couponlist) {
										if (couponlist[key].id == data.info.id) {
											couponlist[key] = data.info;
										}
									}
									$( "#couponlist" ).empty();
									$( "#couponlisttpl" ).tmpl( couponlist ).prependTo( "#couponlist" );
									$('#couponid').val(0);
									$('#btncancel').hide();
									$('#btnreset').show();
								}else {
									$( "#couponlisttpl" ).tmpl( data.info ).prependTo( "#couponlist" ).fadeIn(2000, function(){
										var obj = $(this);
										obj.css('background', '#E6791C');
										setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
									});	
								}
								break;
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
					}
				});
		}else {
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html('<?php echo Lang('not_selected_company_or_server'); ?>');
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
		}
		return false;
	});
});
</script>

<script type="text/template" id="couponlisttpl">
<tr>
	<td>${id}</td>
	<td>${sid_to_name(sid)}</td>
	<td>${name}</td>
	<td>${item_name}(${item_val})</td>
	<td>${num}</td>
	<td>${code_num}</td>
	<td>{{if juche == 1}}支持{{else}}不支持{{/if}}</td>
	<td>${ctime}</td>
	<td>{{if edate == '9999-01-01'}}永久{{else}}${edate}{{/if}}</td>
	<td data-id="${id}" data-sname="${sid_to_name(sid)}" data-name="${name}">
		<a href="javascript:;" class="view"><?php echo Lang('view'); ?></a>  
		<a href="javascript:;" class="edit"><?php echo Lang('additional'); ?>/<?php echo Lang('edit'); ?></a>
		{{if num > 0}}<a href="javascript:;" class="export"><?php echo Lang('export'); ?></a>{{/if}}
		<a href="javascript:;" class="delete"><?php echo Lang('delete'); ?></a>
	</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('coupon_log') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('add_coupon'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('show'); ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area" style="display:none;">
			<!-- Begin form elements -->
			<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=report&c=coupon&v=add" method="post">
				<input type="hidden" name="doSubmit" value="1">
				<input type="hidden" name="couponid" id="couponid" value="0">
				<table class="global" width="100%" style="min-width:500px" cellpadding="0" cellspacing="0">
					<tbody>
					<tr>
						<th style="width:100px;"><?php echo Lang('company_platform'); ?></th>
						<td>
							<select name="cid" class="cid" id="cid1">
								<option value="0"><?php echo Lang('operation_platform') ?></option>
							</select>
							<select name="sid" id="sid1">
								<option value="0"><?php echo Lang('server') ?></option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('use'); ?></th>
						<td><input type="text" name="name" id="name" value="新手卡大放送">本批兑换券用途</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('coupon_item'); ?></th>
						<td>
							<strong>神秘礼包</strong> (包含58元宝和一品武力、绝技、法术丹各一颗)
							<input type="hidden" name="item_id" value="520">
							<input type="hidden" name="item_name" value="神秘礼包">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('coupon_num'); ?></th>
						<td><input type="text" name="item_val" id="item_val" value="1"></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('generate_num'); ?></th>
						<td><input type="text" name="num" id="num" value="0">一次输入最高请勿超过10000，超过10000请提交后再追加</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('self_generated'); ?></th>
						<td>
							<select name="juche" id="juche">
								<option value="1">支持</option>
								<option value="0">不支持</option>
							</select>
							 设置为[支持]，玩家可在运营平台提供的生成页面自主生成兑换券，一个号只能生成一个(生成公式：md5(游戏登陆帐号_游戏服二级域名))
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('overdate'); ?></th>
						<td><input type="text" name="edate" id="edate" value="9999-01-01" >本批兑换券在此日期后过期，不选择为永久不过期</td>
						<td></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td> 
							<p>
							<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
							<input type="button" id="btncancel" class="button" style="display: none;" value="<?php echo Lang('edit_cancel'); ?>">
							<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
							</p>
						</td>
						<td>&nbsp;</td>
					</tr>
					</tbody>
				</table>
				<div id="op_tips" style="display: none;width:100%"><p></p></div>
		    </form>
			<!-- End form elements -->
		</div>
	</div>
	<br class="clear">
	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=report&c=coupon&v=init" method="get" name="form">
		<ul class="nav_li">
			<li class="nobg">
				<p>
					<select name="cid" class="cid" id="cid2">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>
					<select name="sid" id="sid2">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
					<input type="hidden" name="dogetSubmit" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>
	<br class="clear">
	<div class="content">
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:50px;">ID</th>
					<th style="width:100px;"><?php echo Lang('server'); ?></th>
					<th style="width:80px;"><?php echo Lang('use'); ?></th>
					<th style="width:80px;"><?php echo Lang('get_content'); ?></th>
					<th style="width:80px;"><?php echo Lang('generate_num'); ?></th>
					<th style="width:80px;"><?php echo Lang('already_receive'); ?></th>
					<th style="width:50px;"><?php echo Lang('self_generated'); ?></th>
					<th style="width:120px;"><?php echo Lang('create_time'); ?></th>
					<th style="width:120px;"><?php echo Lang('overdate'); ?></th>
					<th style="width:120px;"><?php echo Lang('operation'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="couponlist">

			</tbody>
		</table>
	</div>
</div>