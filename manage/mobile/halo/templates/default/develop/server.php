<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript" src="static/js/jquery.AjaxQueue.js"></script>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'serverlist';
Ha.page.colspan = 11;
Ha.page.emptyMsg = '<?php echo Lang('not_find_setting_data')?>';
Ha.page.queryData = 'cid=<?php echo $data['url']['cid'] ?>';
Ha.page.url = "<?php echo INDEX; ?>?m=develop&c=server&v=public_server_list";


var snewQueue = $.AM.createQueue('squeue');
function showStatData(data){
	if (data.count > 0){
        $('#open').text(data.scount.open);
        $('#notsetting').text(data.scount.notsetting);
        $('#wait').text(data.scount.wait);
        $('#today').text(data.scount.today);
        var serverlist = data.list;
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
							$('#server_db_'+ddata.sid).html('<span class="redtitle">×'+ddata.msg+'</span>')
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

$(function(){
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
 	Ha.page.getList(1, showStatData);

 	Ha.common.ajax('<?php echo INDEX; ?>?m=develop&c=server&v=open_date_list', 'json', '', 'get',
		'container', function(data){
				datelist = data.list;
				$('#datelisttpl').tmpl(datelist).appendTo('#combine');
		}, 1);
 	$('#companyul').on('change', function(){
 		cid = $(this).val();
 		if (cid > 0) {
			$('#cid').val(cid);
	        $('#hcid').val(cid);
			location.hash = $('option:selected', $(this)).attr('rel');
 		}
 		Ha.page.recordNum = 0;
 		Ha.page.queryData = 'cid='+$(this).val();
 		Ha.page.getList(1, showStatData);
 	});

	$('.sd_setting').live('click', function(e){
		e.preventDefault();
		var sid = $(this).attr('data-sid');
		var url = '<?php echo INDEX; ?>?m=develop&c=server&v=ajax_setting_info';
		Ha.common.ajax(url, 'html', 'sid='+sid, 'get', 'container', function(data){
			Ha.Dialog.show(data, '<?php echo Lang('server_base_info'); ?>', 600, 'dialog_s_setting');
		}, 1);
		return false;
	});

	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=develop&c=server&v=setting';
		Ha.common.ajax(url, 'json', objform.serialize(), 'POST', 'container', function(data){
			if (data.status) {
				$('#serverlisttpl').tmpl(data.info).prependTo('#serverlist').fadeIn(2000, function(){
					var obj = $(this);
					obj.css('background', '#E6791C');
					setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
				});	
			}
		});
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		$('.hover', $('#companyul')).removeClass('hover');

		Ha.page.recordNum = 0;
 		Ha.page.queryData = $('#get_search_submit').serialize();
 		Ha.page.getList(1, showStatData);
	});
	
	//选择合服日期
	$("#combine").on('change',function(){
		$('#get_search_submit').submit();
	});
	
	$('#submit_area').on('click', 'a.addargs', function(){
		var htmlstr = $(this).parent('li').clone().html();
		htmlstr = htmlstr.replace(/\+<?php echo Lang('plus'); ?>/g, '-<?php echo Lang('subtract')?>');
		htmlstr = htmlstr.replace(/addargs/g, 'subargs');
		$(this).parent('li').after('<li>'+htmlstr+'</li>');
	});
	$('#submit_area').on('click', 'a.subargs', function(){
		$(this).parent('li').remove();
	});

//	loadaddress();
    $('.select').on('click', 'a.order', function(){
        order = $(this).attr('data');
        $('#status').val(order);
        $('.active', $('.select')).removeClass('active');
        $(this).parent().addClass('active');
        $('#hcid').val(1);
        $('#get_search_submit').submit();
    });
    $('.copyip').live('click',function(){
    	$(this).next().select();
    	
    });
});

//function loadaddress(){
//	$.ajax({
//		url: '<?php echo INDEX; ?>?m=develop&c=server&v=ajax_address_list&all=1',
//		dataType: 'json',
//		success: function(data){
//			if (data.status == 0 && data.list.length > 0) {
//				var list = data.list;
//				var apis = [];
//				var dbs = [];
//				var vers = [];
//				for(var key in list) {
//					switch(list[key].type){
//						case '0': 
//							apis.push(list[key]);
//							break;
//						case '1':	
//							dbs.push(list[key]);
//							break;
//						case '2': 
//							vers.push(list[key]);
//							break;
//					}
//				}
//				$('#addresslisttpl').tmpl(apis).appendTo('#apis');
//				$('#addresslisttpl').tmpl(dbs).appendTo('#dbs');
//				$('#addresslisttpl').tmpl(vers).appendTo('#vers');
//			}
//		}
//	});
//}
</script>
<script type="text/template" id="serverlisttpl">
<tr>
	<td class="num">${sid}</td>
	<td id="server_db_${sid}">{{if db_server == '000'}}<span class="redtitle"><?php echo Lang('no_setting')?></span>{{else}}-{{/if}}</td>	
	<td>{{if name!=''}}${name}{{/if}}{{if logserver}}[已合服]{{/if}}<br>
	{{if o_name!=''}}<span class="graytitle">${o_name}</span>{{else}}&nbsp;{{/if}}</td>
	<td>{{if server!=''}}{{html server.replace(/\|/i,'<br>')}}{{else}}&nbsp;{{/if}}</td>
	<td>${api_server}{{if api_port != ''}}:${api_port}{{else}}&nbsp;{{/if}}<a href="http://{{if logserver}}${logserver}{{else}}${server}{{/if}}/${name}/game_log.php?pwd=093ff0a821d5255f481e68476fc28340" target="_blank">错误日志</a></td>

	<td>{{if db_server != ''}}[${db_server}]<br>${db_name}{{else}}&nbsp;{{/if}}</td>
	<td>{{if open_date!=''}}${open_date}{{else}}&nbsp;{{/if}}</td>
	<td>{{if server_ver!=''}}${server_ver}{{else}}&nbsp;{{/if}}</td>
	<td>{{if open == 1}}{{if opendate > '<?php echo time(); ?>'}}<span class="orangetitle">√<?php echo Lang('wait')?>{{else}}<span class="greentitle">√{{/if}}<?php echo Lang('open'); ?>{{else}}<span class="redtitle">×<?php echo Lang('close'); ?>{{/if}}</span></td>
	<td>{{if test == 1}}<span class="redtitle"><?php echo Lang('test'); ?></span>{{else}}<?php echo Lang('normal'); ?>{{/if}}</td>
	<td><a href="javascript:;" data-sid="${sid}" class="sd_setting"><?php echo Lang('server_detail_setting') ?></a></td>
</tr>
</script>

<!--<script type="text/template" id="addresslisttpl">
	<option value="${name}">${name}</option>
</script>-->
<script type="text/template" id="datelisttpl">
<option value="${open_date}">${open_date}[${num}台]</option>
</script>

<script type="text/template" id="companyultpl">
<option value="${cid}" rel="#app=5&cpp=26&url=${encodeurl('<?php echo $data['url']['m']; ?>', '<?php echo $data['url']['v']; ?>', '<?php echo $data['url']['c']; ?>', '&cid=')}${cid}">${fn} - ${name}</option>
</script>

<h2><span id="tt"><?php echo Lang('server_setting'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">				
				<div class="more">
	                <ul class="select" id="toolbar">
	                	<li class="active"><a class="order" href="javascript:void(0);" data="0"><?php echo Lang('all')?></a></li>
	                	<li><a class="order" href="javascript:void(0);" data="1"><?php echo Lang('wait').Lang('open')?><span class="greentitle" id="wait">-</span></a></li>
	                	<li><a class="order" href="javascript:void(0);" data="2"><?php echo Lang('no_setting')?><span class="orangetitle" id="notsetting">-</span></a></li>
	                	<li><a class="order" href="javascript:void(0);" data="3"><?php echo Lang('today_open_server')?><span id="today" class="bluetitle">-</span></a></li>
	                	<li><a class="order" href="javascript:void(0);" data="4"><?php echo Lang('opened')?><span id="open" class="redtitle">-</span></a></li>
	                </ul>
	            </div>
				<form id="get_search_submit" method="get" name="form">
				<div class="tool_group">
					 <label>SID：
						<input type="text" id="sid" name="sid" class="ipt_txt_s" value="">
					 </label>
					 <label><?php echo Lang('server_o_name'); ?>：qq_s
						<input type="text" id="name" name="name" class="ipt_txt_s" value="">
					 </label>
					 <label><?php echo Lang('server_game_url'); ?>：
						<input type="text" id="url" name="url" class="ipt_txt_s" value="">
					 </label>
					 <label><?php echo Lang('api_address'); ?>：
						<input type="text" id="apis" name="apis" class="ipt_txt_s" value="">
					 </label>
					 <label><?php echo Lang('db_address'); ?>：
						<input type="text" id="dbs" name="dbs" class="ipt_txt_s" value="">
					 </label>
					 <!--<select name="apis" id="apis" class="ipt_select" style="width:150px;">
					 	<option value="" class="select"><?php echo Lang('api_address'); ?></option>
					 </select>
					 <select name="dbs" id="dbs" class="ipt_select" style="width:120px;">
						<option value="" class="select"><?php echo Lang('db_address'); ?></option>
					</select>-->
					<!-- <select name="vers" id="vers" class="ipt_select" style="width:100px;">
						<option value="" class="select"><?php echo Lang('version'); ?></option>
					</select> -->
					<select name="combined_to" class="ipt_select" style="width:100px;">
						<option value="" class="select"><?php echo Lang('server_combined_selected'); ?></option>
						<option value="1"><?php echo Lang('server_yes_combined'); ?></option>
						<option value="2"><?php echo Lang('server_no_combined'); ?></option>
					</select>
					<select name="opendate" id="combine" class="combine ipt_select">
                    	<option value="0"><?php echo Lang('select_date'); ?></option>
                	</select>
 					<input type="hidden" name="cid" id="hcid" value="1">
                     <input type="hidden" name="status" id="status" value="0">
 					<input type="hidden" name="dogetSubmit" value="1">
					 <input type="submit" class="btn_sbm" value="<?php echo Lang('search'); ?>">
				</div>
				</form>
			</div>
		</div>
	</div>
	<div class="column cf" id="table_column">
		<div class="title">
	        <div class="more" id="tblMore">
	            <div id="div_pop">
	                <select name="cid" id="companyul" class="ipt_select">
	                	<option value="0"><?php echo Lang('company_platform_selected'); ?></option>
	                </select>
	            </div>
	        </div>
	        <?php echo Lang('server_list') ?>
	    </div>
		<div id="submit_area">
		<form id="post_submit" method="post">
		<table>
		<thead>
		<tr id="dataTheadTr">
			<th>&nbsp;</th>
	    	<th>DB<?php echo Lang('status'); ?></th>
	    	<th><?php echo Lang('server_o_name'); ?>-<?php echo Lang('server_name'); ?></th>
	    	<th><?php echo Lang('server_game_url'); ?></th>
	    	<th><?php echo Lang('server_api_port_pwd'); ?></th>
	    	<th><?php echo Lang('server_db_root_pwd'); ?></th>
	    	<th><?php echo Lang('server_date'); ?></th>
	    	<th><?php echo Lang('server_version'); ?></th>
	    	<th><?php echo Lang('server_is_open'); ?></th>
	    	<th><?php echo Lang('server_is_test'); ?></th>
	    	<th><?php echo Lang('server_detail_setting'); ?></th>
		</tr>
		</thead>
		<tbody id="serverlist">
			   
		</tbody>
		<tfoot>
		<tr>
			<td class="num"><span class="greentitle"><?php echo Lang('add_new_record') ?></span></td>
			<td>
				<select name="cid" id="cid" class="ipt_select" style="width:70px;"></select>
			</td>
			<td><input type="text" name="name" class="ipt_txt_s" value="qq_s"></td>
			<td><input type="text" name="o_name" class="ipt_txt_s" value="<?php echo Lang('da_hua_shen_xian')?>s"></td>
			<td>
				<ul>
					<li>
						<input type="text" name="server[]" class="ipt_txt_s" />
						<a href="javascript:;" class="addargs">+<?php echo Lang('plus'); ?></a> 
					</li>
				</ul>
			</td>
			<td colspan="2">
				<input type="text" name="open_date" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00'})" value="<?php echo date('Y-m-d 10:00:00'); ?>"  class="ipt_txt" readonly>
			</td>
			<td>
				<input type="hidden" name="doflag" value="quick">
				<input type="hidden" name="doSubmit" value="1">
				<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		</tfoot>
		</table>
		</form>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>