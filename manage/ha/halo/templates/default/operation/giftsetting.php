<?php defined('IN_G') or exit('No permission resources.');
//include template('operation', 'header'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 50, recordNum = 0, giftlist;
function getgiftlist(index){
	var query = "<?php echo INDEX; ?>?m=operation&c=giftsetting&v=ajax_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#giftlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showgiftlist
		});
	});
}
function getsearchList(index){
	var query = "<?php echo INDEX; ?>?m=operation&c=giftsetting&v=ajax_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#giftlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: function(data){
				showgiftlist(data, 1);
			}
		});
	});
}

function showgiftlist( data, type) {
	if (data.status == -1){
		$('#giftlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), giftlist = data.list;
		if (type != undefined && type == 1){
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchList });
		}else {
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getgiftlist });
		}
		$( "#giftlist" ).empty();
		if (data.count > 0){
			$( "#giftlisttpl" ).tmpl( giftlist ).prependTo( "#giftlist" );
			$( "#giftlist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#giftlist" ).parent().parent('div.content').css('height', $('#giftlist').parent('table.global').css('height'));
			}
		}
	}
}

var dialog;
$(document).ready(function(){
	getgiftlist( pageIndex );

	//--------添加
	$('#post_gift_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=giftsetting&v=add',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							if (data.editflag == 1){
								getgiftlist( pageIndex );
								$('#giftid').val(0);
								$('#btncancel').hide();
								$('#btnreset').show();
							}else {
								$( "#giftlisttpl" ).tmpl( data.info ).prependTo( "#giftlist" ).fadeIn(2000, function(){
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
					document.getElementById('post_gift_submit').reset();
					var str = '';
					str = '<div>'+
		   					'物品I  D：<input type="text" name="item[id][]" value="" size="10"> &nbsp;&nbsp;'+
		   					'物品等级：<input type="text" name="item[level][]" value="1" size="10"> &nbsp;&nbsp;'+
		    				'物品数量：<input type="text" name="item[number][]" value="1" size="10"> &nbsp;&nbsp;'+
	    					'<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>'+
	    				'</div>';
					$('#itemlist').html(str);
					str = '<div>'+
							'灵件I  D：<input type="text" name="soul[id][]" value="" size="10"> &nbsp;&nbsp;'+
							'灵件数量：<input type="text" name="soul[number][]" value="1" size="10"> &nbsp;&nbsp;'+
							'<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>'+
					    '</div>';
					$('#soullist').html(str);
					str = '<div>'+
							'灵件I  D：<input type="text" name="soul[id][]" value="" size="10"> &nbsp;&nbsp;'+
							'灵件数量：<input type="text" name="soul[number][]" value="1" size="10"> &nbsp;&nbsp;'+
							'<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>'+
					    '</div>';
					$('#soullist').html(str);
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
	//--------修改
	$('#giftlist').on('click', 'a.edit', function(){
		if ($('#submit_area').is(':hidden')){
			$('#extentfold').click();
		}
		var obj    = $(this);
		var giftid = obj.attr('data-giftid');
		if (giftid > 0){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=giftsetting&v=ajax_info',
				data: 'giftid='+giftid,
				dataType: 'json',
				type: 'get',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0:
							if (data.info.giftid > 0){
								$('#giftid').val(data.info.giftid);
								$('#giftname').val(data.info.giftname);
								$('#message').val(data.info.message);
								$('#starttime').val(data.info.starttime);
								$('#endtime').val(data.info.endtime);
								$('#limitnumber').val(data.info.limitnumber);
								if (data.info.gifttype == 1){
									$('#gifttype1').attr('checked', 'checked');
								}else {
									$('#gifttype0').attr('checked', 'checked');
								}
								$('#fame').val(data.info.fame);
								$('#skill').val(data.info.skill);
								$('#ingot').val(data.info.ingot);
								$('#coins').val(data.info.coin);

								var itemlist = data.info.itemlist;
								var fatelist = data.info.fatelist;
								var soullist = data.info.soullist;
								var str = '';
								if (itemlist.length > 0) {
									for(var key in itemlist) {
										str +='<div>'+
										'物品I  D：<input type="text" name="item[id][]" value="'+itemlist[key].item_id+'" size="10"> &nbsp;&nbsp;'+
										'物品等级：<input type="text" name="item[level][]" value="'+itemlist[key].level+'" size="10"> &nbsp;&nbsp;'+
										'物品数量：<input type="text" name="item[number][]" value="'+itemlist[key].number+'" size="10"> &nbsp;&nbsp;';
										if (key == 0) {
											str += ' <a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>';
										}else {
											str += ' <a href="javascript:;" style="font-size:14px;font-weight:600" class="sub">-</a>';
										}
										str += '</div>';
									}
								}else {
									str = '<div>'+
						   					'物品I  D：<input type="text" name="item[id][]" value="" size="10"> &nbsp;&nbsp;'+
						   					'物品等级：<input type="text" name="item[level][]" value="1" size="10"> &nbsp;&nbsp;'+
						    				'物品数量：<input type="text" name="item[number][]" value="1" size="10"> &nbsp;&nbsp;'+
					    					'<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>'+
					    				'</div>';
								}
								$('#itemlist').html(str);

								str = '';
								if (fatelist.length > 0) {
									for(var key in fatelist) {
										str +='<div>'+
										'命格I  D：<input type="text" name="fate[id][]" value="'+fatelist[key].fate_id+'" size="10"> &nbsp;&nbsp;'+
										'命格等级：<input type="text" name="fate[level][]" value="'+fatelist[key].level+'" size="10"> &nbsp;&nbsp;'+
										'命格数量：<input type="text" name="fate[number][]" value="'+fatelist[key].number+'" size="10"> &nbsp;&nbsp;';
										if (key == 0) {
											str += ' <a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>';
										}else {
											str += ' <a href="javascript:;" style="font-size:14px;font-weight:600" class="sub">-</a>';
										}
										str += '</div>';
									}
								}else {
									str = '<div>'+
										'命格I  D：<input type="text" name="fate[id][]" value="" size="10"> &nbsp;&nbsp;'+
										'命格等级：<input type="text" name="fate[level][]" value="1" size="10"> &nbsp;&nbsp;'+
										'命格数量：<input type="text" name="fate[number][]" value="1" size="10"> &nbsp;&nbsp;'+
										'<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>'+
								    '</div>';
								}
								$('#fatelist').html(str);

								str = '';
								if (soullist.length > 0) {
									for(var key in soullist) {
										str +='<div>'+
										'灵件I  D：<input type="text" name="soul[id][]" value="'+soullist[key].soul_id+'" size="10"> &nbsp;&nbsp;'+
										'灵件数量：<input type="text" name="soul[number][]" value="'+soullist[key].number+'" size="10"> &nbsp;&nbsp;';
										if (key == 0) {
											str += ' <a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>';
										}else {
											str += ' <a href="javascript:;" style="font-size:14px;font-weight:600" class="sub">-</a>';
										}
										str += '</div>';
									}
								}else {
									str = '<div>'+
											'灵件I  D：<input type="text" name="soul[id][]" value="" size="10"> &nbsp;&nbsp;'+
											'灵件数量：<input type="text" name="soul[number][]" value="1" size="10"> &nbsp;&nbsp;'+
											'<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>'+
									    '</div>';
								}
								$('#soullist').html(str);

								$('#btncancel').show();
								$('#btnreset').hide();
								$('#giftname').focus();
								$('#giftname').css('border', '1px solid #E6791C');
								setTimeout( function(){	$('#giftname').css('border', ''); }, ( 2000 ) );
							}
							break;
						case 1: 
							alertclassname = 'alert_error'; 
							$('#op_tips').attr('class', alertclassname);
							$('#op_tips').children('p').html(data.msg);
							$('#op_tips').fadeIn();
							setTimeout( function(){$('#op_tips').fadeOut();}, ( time * 1000 ) );
							break;
					}
				},
				error: function() {
					$('#btnsubmit').removeAttr('disabled');
				}
			});
		}
	});

	//--------取消修改
	$('#btncancel').on('click', function(){
		$('#giftid').val('0');
		document.getElementById('post_gift_submit').reset();
		var str = '';
		str = '<div>'+
			'物品I  D：<input type="text" name="item[id][]" value="" size="10"> &nbsp;&nbsp;'+
			'物品等级：<input type="text" name="item[level][]" value="1" size="10"> &nbsp;&nbsp;'+
	    	'物品数量：<input type="text" name="item[number][]" value="1" size="10"> &nbsp;&nbsp;'+
			'<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>'+
		'</div>';
		$('#itemlist').html(str);
		str = '<div>'+
			'灵件I  D：<input type="text" name="soul[id][]" value="" size="10"> &nbsp;&nbsp;'+
			'灵件数量：<input type="text" name="soul[number][]" value="1" size="10"> &nbsp;&nbsp;'+
			'<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>'+
	    '</div>';
		$('#soullist').html(str);
		str = '<div>'+
			'灵件I  D：<input type="text" name="soul[id][]" value="" size="10"> &nbsp;&nbsp;'+
			'灵件数量：<input type="text" name="soul[number][]" value="1" size="10"> &nbsp;&nbsp;'+
			'<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>'+
		    '</div>';
		$('#soullist').html(str);
		$('#btncancel').hide();
		$('#btnreset').show();
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
	//查看
	$('#giftlist').on('click', 'a.view', function() {
		var obj    = $(this);
		var giftid = obj.attr('data-giftid');
		var dialog = $.dialog({id: 'dlg_giftsetting', width: 880, title:'ID: '+giftid});
		$.ajax({
			url: '<?php echo INDEX; ?>?m=operation&c=giftsetting&v=ajax_giftsetting_log',
			data: 'giftid='+giftid,
			success: function(data){
				dialog.content(data);
			},
			error: function() {
				
			}
		});
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchList(1);
	});

	<?php if ($data['show']){ ?>
	$('#extentfold').click();
	<?php } ?>

	$('#gifttag').on('click', 'a.sub', function() {
	  $(this).parent().remove();
	});
	$('#gifttag').on('click', 'a.add', function() {
	  var str = $(this).parent().clone().html();
	  str = str.replace(/\+/g, '-').replace(/add/g, 'sub');
	  $('<div>'+str+'</div>').appendTo($(this).parent().parent());
	});
});
</script>

<script type="text/template" id="giftlisttpl">
<tr>
	<td>${giftid}</td>
	<td>${giftname}</td>
	<td>{{if gifttype==1}}每服每日领取次数 {{else}} 每服总领取次数{{/if}}</td>
	<td>${limitnumber}</td>
	<td>${starttime}</td>
	<td>${endtime}</td>
	<td>${message}</td>
	<td>
		<a href="javascript:;" class="edit" data-giftid="${giftid}"><?php echo Lang('edit') ?></a>
		<a href="javascript:;" class="view" data-giftid="${giftid}"><?php echo Lang('view'); ?></a>
	</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('gift_title'); ?></span></a></li>
	</ul>
	<br class="clear">

	<!-- 添加用户 -->
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('add_gift_title'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('show') ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area" style="display: none;">
			<!-- Begin form elements -->
			<form name="post_gift_submit" id="post_gift_submit" action="<?php echo INDEX; ?>?m=operation&c=giftsetting&v=add" method="post">
				<input type="hidden" name="doSubmit" value="1">
				<input type="hidden" name="giftid" id="giftid" value="0">
				<table class="global" width="100%" cellpadding="0" cellspacing="0" id="gifttag">
					<tbody>
					<tr>
						<th style="width: 10%;"><?php echo Lang('giftname'); ?>：</th>
						<td style="width: 20%"><input type="text" name="giftname" id="giftname" style="width:90%"></td>
						<td style="width: 10%"></td>
						<td style="width: 20%"></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th width="120"><?php echo Lang('gifttype'); ?>：</th>
						<td>
							<input type="radio" name="gifttype" value="0" id="gifttype0" <?php if ($giftarr['gifttype'] != 1){ ?>checked<?php } ?>>每服总领取次数
							<input type="radio" name="gifttype" value="1" id="gifttype1" <?php if ($giftarr['gifttype'] == 1){ ?>checked<?php } ?>>每服每日领取次数
						</td>
						<th width="width: 10%">礼包领取限制次数</th>
					    <td style="width: 20%">
					  		<input type="text" name="limitnumber" id="limitnumber" value="1" size="10">
					    </td>
					    <td>&nbsp;</td>
					</tr>
					<tr>
					  <th style="width: 10%;">礼包时间限制</th>
					  <td colspan="3">
					    <?php echo Lang('starttime'); ?>：<input type="text" name="starttime" id="starttime" value="<?php echo $starttime; ?>"  onclick="WdatePicker()" readonly size="15">
					     - 
					    <?php echo Lang('endtime'); ?>：<input type="text" name="endtime" id="endtime" value="<?php echo $endtime; ?>"  onclick="WdatePicker()" readonly size="15">
					  </td>
					    <td>&nbsp;</td>
					</tr>
					<tr>
						<th style="width: 10%;">礼包领取提示</th>
						<td colspan="3">
							<input type="text" name="message" id="message" value="恭喜你获得活动大礼包" size="50">
						 <span class="graytext"></span>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th style="width: 10%;">铜钱数量</th>
						<td style="width: 20%">
							<input type="text" name="coins" id="coins" value="0" size="10">
						</td>
						<th style="width: 10%;">阅历数量</th>
						<td style="width: 20%">
							<input type="text" name="skill" id="skill" value="0" size="10">
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th style="width: 10%;">声望数量</th>
						<td><input type="text" name="fame" id="fame" value="0" size="10"></td>
						<th style="width: 10%;">元宝数量</th>
						<td><input type="text" name="ingot" id="ingot" value="0" size="10"></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th style="width: 10%;">道具物品：</th>
						<td colspan="3">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
					  <td style="width: 10%;">&nbsp;</td>
					  <td colspan="3" id="itemlist">
					    <div>
						   	物品I  D：<input type="text" name="item[id][]" value="" size="10"> &nbsp;&nbsp;
						   	物品等级：<input type="text" name="item[level][]" value="1" size="10"> &nbsp;&nbsp;
						    物品数量：<input type="text" name="item[number][]" value="1" size="10"> &nbsp;&nbsp;
					    	<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>
					    </div>
					  </td>
					  <td>&nbsp;</td>
					</tr>
					<tr>
						<th style="width: 10%;">命格：</th>
						<td colspan="3">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
					  <td style="width: 10%;">&nbsp;</td>
					  <td colspan="3" id="fatelist">
					    <div>
							命格I  D：<input type="text" name="fate[id][]" value="" size="10"> &nbsp;&nbsp;
							命格等级：<input type="text" name="fate[level][]" value="1" size="10"> &nbsp;&nbsp;
							命格数量：<input type="text" name="fate[number][]" value="1" size="10"> &nbsp;&nbsp;
							<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>
					    </div>
					  </td>
					  <td>&nbsp;</td>
					</tr>
					<tr>
						<th style="width: 10%;">灵件：</th>
						<td colspan="3">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
					  <td style="width: 10%;">&nbsp;</td>
					  <td colspan="3" id="soullist">
					    <div>
							灵件I  D：<input type="text" name="soul[id][]" value="" size="10"> &nbsp;&nbsp;
							灵件数量：<input type="text" name="soul[number][]" value="1" size="10"> &nbsp;&nbsp;
							<a href="javascript:;" style="font-size:14px;font-weight:600" class="add">+</a>
					    </div>
					  </td>
					  <td>&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="3"> 
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
		    </form>
		    <div id="op_tips" style="display: none;"><p></p></div>
			<!-- End form elements -->
		</div>
	</div>

	<!-- 用户列表 -->
	<br class="clear">
	
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('gift_list') ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>

	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=operation&c=giftsetting&v=ajax_list" method="get" name="form">
		<ul class="nav_li">
			<li class="nobg">
				<p>
					<?php echo Lang('giftname'); ?>	：
					<input name="giftname" type="text" value="" size="20">	
					<?php echo Lang('giftid'); ?>	：
					<input name="giftid" type="text" value="" size="20">	
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
					<input name="dogetSubmit" type="hidden" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>

	<div class="content">
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<!-- Begin example table data -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
			    <tr>
			    	<!-- <th style="width:5%">
			    		<input type="checkbox" id="check_all" name="check_all">
			    	</th> -->
			    	<th style="width:50px;">ID</th>
			    	<th style="width:200px"><?php echo Lang('giftname'); ?></th>
			    	<th style="width:100px"><?php echo Lang('gifttype'); ?></th>
			    	<th style="width:50px;"><?php echo Lang('limitnumber'); ?></th>
			    	<th style="width:80px;"><?php echo Lang('starttime'); ?></th>
			    	<th style="width:80px;"><?php echo Lang('endtime'); ?></th>
			    	<th style="width:300px"><?php echo Lang('message'); ?></th>
			    	<th style="width:80px"><?php echo Lang('operation'); ?></th>
			    	<th>&nbsp;</th>
			    </tr>
			</thead>
			<tbody id="giftlist">
			   
			</tbody>
		</table>
		<!-- End pagination -->			
	</div>
	<div class="pagination pager" id="pager"></div>
</div>
