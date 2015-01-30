<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=develop&c=company&v=ajax_setting_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#companylist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}

function getsearchList(index){
	var query = "<?php echo INDEX; ?>?m=develop&c=company&v=ajax_setting_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#companylist" ).fadeOut( "medium", function () {
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
		$('#companylist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize );
		if (type != undefined && type == 1){
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		}else {
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchList });
		}
		$( "#companylist" ).empty();
		if (data.count > 0){
			$( "#companylisttpl" ).tmpl( data.list ).prependTo( "#companylist" );
			$( "#companylist" ).stop(true,true).hide().slideDown(400);
		}
	}
}

var dialog = dialog != undefined ? null : '';
$(document).ready(function(){
	getList( pageIndex );

	$('#companylist').on('click', 'a.cd_setting', function(){
		var cid = $(this).attr('data-cid');
		if (cid > 0){
			dialog = $.dialog({id: 'dialog_c_setting_'+cid, width: 600, title: '<?php echo Lang('server_detail_setting'); ?>', drag: true}); 
			$.ajax({
			    url: '<?php echo INDEX; ?>?m=develop&c=company&v=ajax_setting_info',
			    data: 'cid='+cid,
			    success: function (data) {
			        dialog.content(data);
			    },
			    cache: false
			});
		}
	});

	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=company&v=setting',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							$('#companylisttpl').tmpl(data.info).prependTo('#companylist').fadeIn(2000, function(){
									var obj = $(this);
									obj.css('background', '#E6791C');
									setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
								});	
							break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#list_op_tips').attr('class', alertclassname);
					$('#list_op_tips').children('p').html(data.msg);
					$('#list_op_tips').fadeIn();
					setTimeout( function(){
						$('#list_op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				},
				error: function() {
					$('#btnsubmit').removeAttr('disabled');
				}
			});
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchList(1);
	});
});
</script>
<script type="text/template" id="companylisttpl">
<tr>
	<td>${cid}</td>
	<td>${corder}</td>
	<td>{{if name!=''}}${name}{{else}}&nbsp;{{/if}}</td>
	<td>{{if slug!=''}}${slug}{{else}}&nbsp;{{/if}}</td>
	<td>{{if web!=''}}${web}{{else}}&nbsp;{{/if}}</td>
	<td>{{if game_name!=''}}${game_name}{{else}}&nbsp;{{/if}}</td>
	<td>${type}<?php echo Lang('company_platform_type_item') ?></td>
	<td><a href="javascript:;" data-cid="${cid}" class="cd_setting"><?php echo Lang('server_detail_setting') ?></a></td>
	<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('company_setting') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=develop&c=company&v=ajax_setting_list" method="get" name="form">
		<ul class="nav_li">
			<li>
				<p>
					<?php echo Lang('company_name'); ?>：	
					<input name="name" type="text" value="" size="20"> 
					<?php echo Lang('company_website'); ?>：	
					<input name="web" type="text" value="" size="30"> 
					<select name="type">
						<option value=""><?php echo Lang('type'); ?></option>
						<option value="1">1类</option>
						<option value="2">2类</option>
						<option value="3">3类</option>
					</select>
				</p>
			</li>
			<li class="nobg">
				<p>
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>


	<div class="content" id="submit_area">
		<!-- Begin form elements -->
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<div class="clear"></div>
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th style="width:50px;">CID</th>
						<th style="width:50px;"><?php echo Lang('listsort'); ?></th>
						<th style="width:150px"><?php echo Lang('company_name'); ?></th>
						<th style="width:100px;"><?php echo Lang('company_short_name'); ?></th>
						<th style="width:150px;"><?php echo Lang('company_domain'); ?></th>
						<th style="width:150px;"><?php echo Lang('company_game_title'); ?></th>
						<th style="width:50px;"><?php echo Lang('company_platform_type'); ?></th>
						<th style="width:50px;"><?php echo Lang('server_detail_setting'); ?></th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody id="companylist">

				</tbody>
			</table>
		<div class="pagination pager" id="pager"></div>
		<br class="clear">
		<form name="post_submit" id="post_submit" method="post" action="<?php echo INDEX; ?>?m=develop&c=company&v=setting">
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th style="width:120px;">&nbsp;</th>
						<th style="width:150px;"><?php echo Lang('company_name'); ?></th>
						<th style="width:90px;"><?php echo Lang('company_short_name'); ?></th>
						<th style="width:200px;"><?php echo Lang('company_domain'); ?></th>
						<th style="width:200px;"><?php echo Lang('company_game_title'); ?></th>
						<th style="width:50px;"><?php echo Lang('company_platform_type'); ?></th>
						<th style="width:50px;"><?php echo Lang('post'); ?></th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><?php echo Lang('add_new_record') ?></th>
						<td><input type="text" name="name" id="name" style="width:90%"></td>
						<td><input type="text" name="slug" id="slug" style="width:90%"></td>
						<td><input type="text" name="web" id="web" style="width:90%"></td>
						<td><input type="text" name="game_name" id="game_name" style="width:90%"></td>
						<td><select name="type" id="type">
							<option value="1">1<?php echo Lang('company_platform_type_item') ?></option>
							<option value="2">2<?php echo Lang('company_platform_type_item') ?></option>
							<option value="3">3<?php echo Lang('company_platform_type_item') ?></option>
						</select></td>
						<td>
							<input type="hidden" name="doSubmit" value="1">
							<input type="submit" id="btnsubmit" class="button" value="提交"></td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</form>
		<!-- End form elements -->
	</div>
</div>
