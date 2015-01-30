<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, tmpllist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=manage&c=template&v=ajax_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#tmpllist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}

function showList( data ) {
	if (data.status == -1){
		$('#tmpllist').html(data.msg);
	}else {
		if (data.count > 0){
			recordNum = data.count;
			pageCount = Math.ceil( data.count/pageSize ), tmpllist = data.list;
			$( "#pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
			$( "#tmpllist" ).empty();
			$( "#tmpllisttpl" ).tmpl( tmpllist ).prependTo( "#tmpllist" );
			$( "#tmpllist" ).stop(true,true).hide().slideDown(400);
		}
	}
}

$(document).ready(function(){
	getList(pageIndex);
	//-------添加
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=template&v=add',
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
								$('#tid').val(0);
								$('#btncancel').hide();
								$('#btnreset').show();
							}else {
								$( "#tmpllisttpl" ).tmpl( data.info ).prependTo( "#tmpllist" ).fadeIn(2000, function(){
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
					$('.subargs').parent('p').remove();
					setTimeout( function(){
						$('#op_tips').fadeOut();
						$('#btnsubmit').removeAttr('disabled');
					}, ( time * 1000 ) );
				}
			});
	});
	//--------修改
	$('#tmpllist').on('click', 'a.edit', function(){
		if ($('#submit_area').is(':hidden')){
			$('#extentfold').click();
		}
		var obj    = $(this);
		var tid = obj.attr('data-tid');
		if (tid > 0){
			for(var key in tmpllist){
				if (tmpllist[key].tid == tid){
					$('#tid').val(tmpllist[key].tid);
					$('#key').val(tmpllist[key].key);
					$('#title').val(tmpllist[key].title);
					$('#version').val(tmpllist[key].version);
					$('#content').val(tmpllist[key].content);

					$('.subargs').parent('p').remove();
					$('#argstpl').tmpl(tmpllist[key].args).prependTo('#args');
					$('#rtnstpl').tmpl(tmpllist[key].rtns).prependTo('#rtns');

					$('#btncancel').show();
					$('#btnreset').hide();
					$('#title').focus();
					$('#title').css('border', '1px solid #E6791C');
					setTimeout( function(){	$('#title').css('border', ''); }, ( 2000 ) );
					break;
				}
			}
		}
	});
	//--------取消修改
	$('#btncancel').on('click', function(){
		$('#tid').val('0');
		document.getElementById('post_submit').reset();
		$('.subargs').parent('p').remove();
		$('#btncancel').hide();
		$('#btnreset').show();
	});

	$('#submit_area').on('click', 'a.addargs', function(){
		var htmlstr = $(this).parent('p').clone().html();
		htmlstr = htmlstr.replace(/\+/g, '-');
		htmlstr = htmlstr.replace(/addargs/g, 'subargs');
		$(this).parent('p').after('<p>'+htmlstr+'</p>');
	});
	$('#submit_area').on('click', 'a.subargs', function(){
		$(this).parent('p').remove();
	});

	$('#tmpllist').on('click', 'a.preview', function(){
		$.dialog({id: 'dialog_preview', title: "<?php echo Lang('preview') ?>", content: $(this).siblings('textarea').text()});
	});

	$('#submit_area').on('click', 'a.preview', function(){
		$.dialog({id: 'dialog_preview', title: "<?php echo Lang('preview') ?>", content: $(this).siblings('textarea').text()});
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
});
</script>

<script type="text/template" id="argstpl">
<p>
<?php echo Lang('template_form_key_args_name') ?>  <input type="text" name="arg[]" value="${arg}" style="width:10%" />
<?php echo Lang('template_form_key_args_name_desc') ?>  <input type="text" name="arg_tips[]" value="${tips}" style="width:15%" />
<a href="javascript:;" class="subargs">-</a>
</p>
</script>
<script type="text/template" id="rtnstpl">
<p>
<?php echo Lang('template_form_key_rtns_name') ?>  <input type="text" name="rtn[]" value="${rtn}" style="width:10%" />
<?php echo Lang('template_form_key_rtns_name_desc') ?>  <input type="text" name="rtn_tips[]" value="${tips}" style="width:15%" />
<a href="javascript:;" class="subargs">-</a>
</p>
</script>
<script type="text/template" id="tmpllisttpl">
<tr>
	<td>${tid}</td>
	<td>${title}</td>
	<td>${key}</td>
	<td>
	({{each args}}<br>$${$value.arg} //${$value.tips}{{/each}}<br>)
	</td>
	<td>
	({{each rtns}}<br>'${$value.rtn}' //${$value.tips}{{/each}}<br>)
	</td>
	<td>{{if version != ''}}${version}{{else}}&nbsp;{{/if}}</td>
	<td>
		<textarea style="display: none;">${content}</textarea>
		<a href="javascript:;" class="preview"><?php echo Lang('preview') ?></a>
		<a href="javascript:;" class="edit" data-tid="${tid}">修改</a>
		<a href="javascript:;" class="delete" data-tid="${tid}" data-rolename="${rolename}">删除</a>
	</td>
	<td>&nbsp;</td>
</tr>
</script>


<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('template_title'); ?></span></a></li>
	</ul>
	<br class="clear">
	<!-- 添加用户 -->
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('add_template_title'); ?></h2>
			<ul class="second_level_tab">
				<li><a href="javascript:;" id="extentfold"><?php echo Lang('show') ?></a></li>
			</ul>
		</div>
		<div class="content" id="submit_area" style="display:none;">
			<!-- Begin form elements -->
			<form name="post_submit" id="post_submit" action="<?php echo INDEX; ?>?m=manage&c=template&v=add" method="post">
				<input type="hidden" name="doSubmit" value="1">
				<input type="hidden" name="tid" id="tid" value="0">
				<table class="global" width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<th style="width: 10%;"><?php echo Lang('template_form_title'); ?>：</th>
						<td style="width: 60%;"><input type="text" name="title" id="title" style="width:50%"></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('template_form_version'); ?>：</th>
						<td>
							<select name="version" id="version">
								<option value=""><?php echo Lang('no_setting') ?></option>
								<?php echo $str_version; ?>
							</select>
							  <?php echo Lang('template_form_version_tips') ?></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('template_form_key'); ?>：</th>
						<td><input type="text" name="key" id="key" style="width:30%" /></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('template_form_key_args'); ?>：</th>
						<td id="args">
							<p>
							<?php echo Lang('template_form_key_args_name') ?>  <input type="text" name="arg[]" style="width:10%" />
							<?php echo Lang('template_form_key_args_name_desc') ?>  <input type="text" name="arg_tips[]" style="width:15%" />
							<a href="javascript:;" class="addargs">+</a>
							</p>
							<?php echo Lang('template_form_key_args_tips') ?>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th><?php echo Lang('template_form_key_rtns'); ?>：</th>
						<td id="rtns">
							<p>
							<?php echo Lang('template_form_key_rtns_name') ?>  <input type="text" name="rtn[]" style="width:10%" />
							<?php echo Lang('template_form_key_rtns_name_desc') ?>  <input type="text" name="rtn_tips[]" style="width:15%" />
							<a href="javascript:;" class="addargs">+</a>
							</p>
							<?php echo Lang('template_form_key_rtns_tips') ?>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th style="vertical-align: top;"><?php echo Lang('template_form_content'); ?>：</th>
						<td>
							<textarea rows="5" name="content" id="content" cols="40" style="width:51%"></textarea>
							<br>
							<a href="javascript:;" class="preview"><?php echo Lang('preview') ?></a>
						</td>
						<td>&nbsp;</td>
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
			<h2><?php echo Lang('template_list') ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>
	<div class="content">
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<!-- Begin example table data -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
			    <tr>
			    	<th style="width:50px;">ID</th>
			    	<th style="width:100px;"><?php echo Lang('template_form_title'); ?></th>
			    	<th style="width:100px;"><?php echo Lang('template_form_key'); ?></th>
			    	<th style="width:20%"><?php echo Lang('template_form_key_args'); ?></th>
			    	<th style="width:20%"><?php echo Lang('template_form_key_rtns'); ?></th>
			    	<th style="width:100px;"><?php echo Lang('version'); ?></th>
			    	<th style="width:120px;"><?php echo Lang('operation'); ?></th>
			    	<th>&nbsp;</th>
			    </tr>
			</thead>
			<tbody id="tmpllist">
			   
			</tbody>
		</table>
		<div class="pagination" id="pager">
		</div>
		<!-- End pagination -->
	</div>
</div>