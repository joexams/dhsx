<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript" src="static/js/jquery.AjaxQueue.js"></script>
<script type="text/javascript">
var snewQueue = $.AM.createQueue('squeue');
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, cid = "<?php echo $data['url']['cid'] ?>", serverlist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=develop&c=server&v=server_list&cid="+cid+"&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#serverlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}

function getsearchList(index){
	var query = "<?php echo INDEX; ?>?m=develop&c=server&v=server_list&cid="+cid+"&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#serverlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: function (data) {
				showList(data, 1)
			}
		});
	});
}

function showList( data, type) {
	if (data.status == -1){
		$('#serverlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), serverlist = data.list, cid = data.cid;
		if (type != undefined && type == 1){
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchList });
		}else {
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		}
		$( "#serverlist" ).empty();
		if (data.count > 0){
			$( "#serverlisttpl" ).tmpl( serverlist ).appendTo( "#serverlist" );
			$( "#serverlist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#serverlist" ).parent().parent('div.content').css('height', $('#serverlist').parent('table.global').css('height'));
			}
            $('#open').text(data.scount.open);
            $('#notsetting').text(data.scount.notsetting);
            $('#wait').text(data.scount.wait);
            $('#today').text(data.scount.today);

			var ssid = 0;
			for(var key in serverlist){
				ssid = serverlist[key].sid;
            	if (ssid > 0 && serverlist[key].db_server != '000' && serverlist[key].db_server != ''){
					snewQueue.offer({
						url: '<?php echo INDEX; ?>?m=develop&c=server&v=test_db_connect',
						data: 'sid='+ssid,
		  				dataType: 'json',
						success: function(ddata){
							if (ddata.status == 0){
								$('#server_db_'+ddata.sid).html('<span class="greentitle">√<?php echo Lang('success'); ?></span>')
							}else {
								$('#server_db_'+ddata.sid).html('<span class="redtitle">×<?php echo Lang('failure'); ?></span>')
							}
						},
						error: function(e){
							$('#server_db_'+ssid).html('<span class="redtitle">×<?php echo Lang('failure'); ?></span>')
						}
					});
				}
			}
		}
	}
}

var dialog = typeof dialog != 'undefined' ? null : '';
$(document).ready(function(){
 	if (typeof global_companylist != 'undefined'){
 		$( "#companyultpl" ).tmpl( global_companylist ).appendTo( "#companyul" );
 		$( "#companyultpl" ).tmpl( global_companylist ).appendTo( "#cid" );
 		$('.h_lib_nav').fadeIn();

 		if (cid > 0){
 			$('option[value="'+cid+'"]', $('#companyul')).attr('selected', 'selected');
            $('#hcid').val(cid);
 			// location.hash = $('option:selected', $('#companyul')).attr('rel');
 		}
 	}
 	getList(pageIndex);

 	$('#companyul').on('change', function(){
 		cid = $(this).val();
 		$('#cid').val(cid);
        $('#hcid').val(cid);
 		location.hash = $('option:selected', $(this)).attr('rel');
 		recordNum = 0;
 		getList(1);
 	});

	$('.sd_setting').live('click', function(){
		var sid = $(this).attr('data-sid');
		dialog = $.dialog({id: 'dialog_s_setting', width: '100%', title: '<?php echo Lang('server_detail_setting'); ?>'}); 
		$.ajax({
		    url: '<?php echo INDEX; ?>?m=develop&c=server&v=ajax_setting_info',
		    data: 'sid='+sid,
		    success: function (data) {
		        dialog.content(data).lock();
		    },
		    cache: false
		});
		return false;
	});

	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		$.ajax({
				url: '<?php echo INDEX; ?>?m=develop&c=server&v=setting',
				data: objform.serialize(),
				dataType: 'json',
				type: 'POST',
				success: function(data){
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0: 
							alertclassname = 'alert_success'; 
							$('#serverlisttpl').tmpl(data.info).prependTo('#serverlist').fadeIn(2000, function(){
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
		cid = -1;
		$('.hover', $('#companyul')).removeClass('hover');
		getsearchList(1);
	});

	$('#submit_area').on('click', 'a.addargs', function(){
		var htmlstr = $(this).parent('li').clone().html();
		htmlstr = htmlstr.replace(/\+<?php echo Lang('plus'); ?>/g, '-减');
		htmlstr = htmlstr.replace(/addargs/g, 'subargs');
		$(this).parent('li').after('<li>'+htmlstr+'</li>');
	});
	$('#submit_area').on('click', 'a.subargs', function(){
		$(this).parent('li').remove();
	});

	loadaddress();
    $('.menu_search').on('click', 'a.order', function(){
        order = $(this).attr('data');
        $('#status').val(order);
        $('.on', $('.menu_search')).removeClass('on');
        $(this).addClass('on');
        $('#get_search_submit').submit();
    });
});

function loadaddress(){
	$.ajax({
		url: '<?php echo INDEX; ?>?m=develop&c=server&v=ajax_address_list&all=1',
		dataType: 'json',
		success: function(data){
			if (data.status == 0 && data.list.length > 0) {
				var list = data.list;
				var apis = [];
				var dbs = [];
				var vers = [];
				for(var key in list) {
					switch(list[key].type){
						case '0': 
							apis.push(list[key]);
							break;
						case '1':	
							dbs.push(list[key]);
							break;
						case '2': 
							vers.push(list[key]);
							break;
					}
				}
				$('#addresslisttpl').tmpl(apis).appendTo('#apis');
				$('#addresslisttpl').tmpl(dbs).appendTo('#dbs');
				$('#addresslisttpl').tmpl(vers).appendTo('#vers');
			}
		}
	});
}
</script>
<script type="text/template" id="serverlisttpl">
<tr>
	<td id="server_db_${sid}">{{if db_server == '000'}}<span class="redtitle">未配置</span>{{else}}-{{/if}}</td>	
	<td>{{if name!=''}}${name}<br>{{else}}&nbsp;{{/if}}
	{{if o_name!=''}}<span class="graytitle">${o_name}</span>{{else}}&nbsp;{{/if}}</td>
	<td>{{if server!=''}}{{html server.replace(/\|/i,'<br>')}}{{else}}&nbsp;{{/if}}</td>
	<td><a href="http://${server}/${name}/game_log.php?pwd=093ff0a821d5255f481e68476fc28340" target="_blank">${api_server}{{if api_port != ''}}:${api_port}{{else}}&nbsp;{{/if}}</a></td>

	<td>{{if db_server != ''}}[${db_server}]  ${db_name}{{else}}&nbsp;{{/if}}</td>
	<td>{{if open_date!=''}}${open_date}{{else}}&nbsp;{{/if}}</td>
	<td>{{if server_ver!=''}}${server_ver}{{else}}&nbsp;{{/if}}</td>
	<td>{{if open == 1}}{{if opendate > '<?php echo time(); ?>'}}<span class="orangetitle">√待{{else}}<span class="greentitle">√{{/if}}<?php echo Lang('open'); ?>{{else}}<span class="redtitle"><?php echo Lang('close'); ?>{{/if}}</span></td>
	<td>{{if test == 1}}<span class="redtitle"><?php echo Lang('test'); ?></span>{{else}}<?php echo Lang('normal'); ?>{{/if}}</td>
	<td><a href="javascript:;" data-sid="${sid}" class="sd_setting"><?php echo Lang('server_detail_setting') ?></a></td>
	<td>&nbsp;</td>
</tr>
</script>

<script type="text/template" id="addresslisttpl">
	<option value="${name}">${name}</option>
</script>

<script type="text/template" id="companyultpl">
<option value="${cid}" rel="#app=4&url=${encodeurl('<?php echo $data['url']['m']; ?>', '<?php echo $data['url']['v']; ?>', '<?php echo $data['url']['c']; ?>', '&cid=')}${cid}">${fn} - ${name}</option>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('server_setting'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=develop&c=server&v=server_list" method="get" name="form">
		<ul class="nav_li">
			<li>
				<p>
					SID： <input type="text" name="sid" value="" size="10">
				</p>
				<p>
					<?php echo Lang('server_o_name'); ?>：<input type="text" name="name" value="" size="10">
				</p>
			</li>
			<li>
				<p>
					<select name="apis" id="apis">
						<option value="" class="select"><?php echo Lang('api_address'); ?></option>
					</select>
				</p>
				<p>
					<select name="dbs" id="dbs">
						<option value="" class="select"><?php echo Lang('db_address'); ?></option>
					</select>
				</p>
			</li>
			<li>
				<p>
					<select name="vers" id="vers">
						<option value="" class="select"><?php echo Lang('version'); ?></option>
					</select>
				</p>
				<p>
					<select name="combined_to">
						<option value="" class="select"><?php echo Lang('server_combined_selected'); ?></option>
						<option value="1"><?php echo Lang('server_yes_combined'); ?></option>
					</select>
				</p>
			</li>
			<li class="nobg">
				<p>
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
					<input type="hidden" name="cid" id="hcid" value="-1">
                    <input type="hidden" name="status" id="status" value="0">
					<input type="hidden" name="dogetSubmit" value="1">
				</p>
				<p>
				</p>
			</li>
		</ul>
		</form>
	</div>
	<br class="clear">
	<div class="h_lib_nav" style="display: none;">
		<strong><?php echo Lang('company_platform_selected'); ?>：</strong>
		<select name="cid" id="companyul"></select>
	</div>
    <div class="option_area2">
        <div>
            <div class="menu_search">
                <a href="javascript:;" class="order" data="1">已配置待开启 <span class="greentitle" id="wait">-</span> 台</a><span class="bar">|</span>
                <a href="javascript:;" class="order" data="2">预定未配置 <span class="orangetitle" id="notsetting">-</span> 台</a><span class="bar">|</span>
                <a href="javascript:;" class="order" data="3">今日新开 <span id="today" class="bluetitle">-</span> 台</a><span class="bar">|</span>
                <a href="javascript:;" class="order" data="4">已开启 <span id="open" class="redtitle">-</span> 台</a>
            </div>
        </div>
    </div>
	<div class="content">
		<!-- Begin form elements -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:60px;">DB<?php echo Lang('status'); ?></th>
					<th style="width:80px;"><?php echo Lang('server_o_name'); ?>-<?php echo Lang('server_name'); ?></th>
					<th style="width:120px;"><?php echo Lang('server_game_url'); ?></th>
					<th style="width:160px;"><?php echo Lang('server_api_port_pwd'); ?></th>
					<th style="width:150px;"><?php echo Lang('server_db_root_pwd'); ?></th>
					<th style="width:120px;"><?php echo Lang('server_date'); ?></th>
					<th style="width:70px;"><?php echo Lang('server_version'); ?></th>
					<th style="width:80px;"><?php echo Lang('server_is_open'); ?></th>
					<th style="width:40px;"><?php echo Lang('server_is_test'); ?></th>
					<th style="width:80px;"><?php echo Lang('server_detail_setting'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="serverlist">

			</tbody>
		</table>
	</div>
		<div class="pagination pager" id="pager"></div>
		<br class="clear">
	<div class="content" id="submit_area">
		<form id="post_submit" action="<?php echo INDEX; ?>?m=develop&c=server&v=setting" method="post">
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th style="width:100px;">&nbsp;</th>
						<th style="width:150px;"><?php echo Lang('company_name'); ?></th>
						<th style="width:100px;"><?php echo Lang('server_o_name'); ?></th>
						<th style="width:150px;"><?php echo Lang('server_name'); ?></th>
						<th style="width:200px;"><?php echo Lang('server_game_url'); ?></th>
						<th style="width:100px;"><?php echo Lang('server_date'); ?></th>
						<th style="width:80px;"><?php echo Lang('submit'); ?></th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><?php echo Lang('add_new_record') ?></th>
						<td>
							<select name="cid" id="cid"></select>
						</td>
						<td><input type="text" name="name" size="10" value="qq_s"></td>
						<td><input type="text" name="o_name" size="20" value="大话神仙s"></td>
						<td>
							<ul>
								<li>
									<input type="text" name="server[]" size="20" />
									<a href="javascript:;" class="addargs">+<?php echo Lang('plus'); ?></a> 
								</li>
							</ul>
						</td>
						<td>
							<input type="text" name="open_date" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00'})" value="<?php echo date('Y-m-d 10:00:00'); ?>" readonly>
						</td>
						<td>
							<input type="hidden" name="doflag" value="quick">
							<input type="hidden" name="doSubmit" value="1">
							<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
						</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</form>
		<div id="list_op_tips" style="display: none;"><p></p></div>
		<br class="clear"><br class="clear">
		<!-- End form elements -->
	</div>
</div>
