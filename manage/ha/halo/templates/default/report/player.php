<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript" src="static/js/jquery.AjaxQueue.js"></script>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'serverlist';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '没有找到玩家数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=develop&c=server&v=public_server_list&type=list";
Ha.page.queryData = {cid: <?php echo $data['url']['cid'] ?>};

var newQueue = $.AM.createQueue('queue');
function showStatData( data ) {
	if ($('#companyul').val() == 0 && data.count == 0){
		$('option[value="0"]', $('#companyul')).remove();
		$('#companyul').change();
		return false;
	}
	if (data.count > 0){
		<?php if ($roleid <= 3) { ?>
		//$.AM.destroyQueue('queue');
		var ssid = ccid = 0, sname = ''; 
		var pserverlist = data.list;
		for(var key in pserverlist){
			ssid = pserverlist[key].sid;
			ccid = pserverlist[key].cid;
			sname = pserverlist[key].name;
			if (ssid > 0 && ccid > 0 && pserverlist[key].api_server != '000' && pserverlist[key].opendate<=data.todaytime){
				 newQueue.offer({
					url: '<?php echo INDEX; ?>?m=server&c=get&v=server_player_stat',
					data: {cid: ccid, sid: ssid, name: sname, rnd: Math.random()},
					dataType: 'json',
					success: function(ddata){
						if (ddata.status == 0){
							if (ddata.online > 0){
								$('#s_'+ddata.sid).find('td.online').html(ddata.online);
							}
							if (ddata.active > 0){
								$('#s_'+ddata.sid).find('td.active').html(ddata.active);
							}
							if (ddata.income != null){
								$('#s_'+ddata.sid).find('td.income').html('<span class="orangetitle">'+ddata.income+'</span>');
							}
							if (ddata.consume != null){
								$('#s_'+ddata.sid).find('td.consume').html(ddata.consume);
							}
						}
					},
					error: function(e){
						// alert(ssid);
					}
				});
			}
		}
		<?php } ?>
	}
}

$(function(){
 	/**
 	* 运营平台
 	*/
 	setTimeout(function() {
	 	if (typeof global_companylist != 'undefined') {
	 		var cid = <?php echo intval($data['url']['cid']) ?>;
	 		$('#companyultpl option[value!="0"]').remove();
	 	    $('#companyultpl').tmpl(global_companylist).appendTo('#companyul');
	 	    $('.h_lib_nav').fadeIn();
	 	    if (cid > 0){
				$('option[value="'+cid+'"]', $('#companyul')).attr('selected', 'selected');
				location.hash = $('option:selected', $('#companyul')).attr('rel');
			}
	 	}
	 	Ha.page.getList(1, showStatData);
 	}, 250);
 	
 	$('#companyul').on('change', function(){
 		Ha.page.queryData = {cid: $(this).val()};
 		Ha.page.recordNum = 0;
 		Ha.page.getList(1, showStatData);
 	});

 	$('#get_search_submit').on('submit', function(e) {
 		e.preventDefault();
 		Ha.page.recordNum = 0;
 		Ha.page.queryData = $('#get_search_submit').serialize();
 		Ha.page.getList(1, showStatData);
 	});

	// $('#serverlist').one('click', 'a.playerinfo', function(){
	// 	var sid = $(this).attr('data-sid'), cid = $(this).attr('data-cid'), title = '';
	// 	if (cid > 0 && sid > 0){
	// 		for(var key in pserverlist){
	// 			if (pserverlist[key].sid == sid){
	// 				title = pserverlist[key].name;
	// 				break;
	// 			}
	// 		}
	// 		$('#container').load('<?php echo INDEX; ?>?m=report&c=player&v=player_list&sid='+sid+'&cid='+cid+'&title='+title);
	// 	}
	// });
	
});
</script>

<script type="text/template" id="companyultpl">
<option value="${cid}" rel="#app=4&cpp=35&url=${encodeurl('<?php echo $data['url']['m']; ?>', '<?php echo $data['url']['v']; ?>', '<?php echo $data['url']['c']; ?>', '&cid=')}${cid}">${fn} - ${name}</option>
</script>

<script type="text/template" id="serverlisttpl">
<tr id="s_${sid}">
	<td class="num">
	<?php if ($data['limit'] === false){ ?>${sid}<br><?php } ?>
	Ver.${server_ver}
	</td>
	<td>
	{{if api_server=='000' || api_server == ''}}
	${name}-${o_name}</a><span class="redtitle">(未配置)</span>
	{{else}}
	<a href="#app=4&cpp=35&url=${encodeurl('<?php echo $data['url']['m']; ?>', 'player_list', '<?php echo $data['url']['c'] ?>','&sid=')}${sid}%26cid%3D${cid}%26title%3D${name}" class="playerinfo" data-cid="${cid}" data-sid="${sid}">${name}-${o_name}</a>
	{{/if}}
	{{if api_server != '000' && distancedate <= 0}}<span class="orangetitle">[待开启]</span>{{/if}}
	{{if combined_to > 0}} 『<a href="#app=4&cpp=35&url=${encodeurl('<?php echo $data['url']['m']; ?>', 'player_list', '<?php echo $data['url']['c'] ?>','&sid=')}${combined_to}%26cid%3D${cid}%26title%3D${name}" class="playerinfo" data-cid="${cid}" data-sid="${combined_to}">已合服</a>』 {{/if}}
	<br>
	${server}
	</td>
	<td>${open_date}<br><span class="graytitle">${distancedate}天</span></td>
   <?php if ($roleid <= 3) { ?>
	<td>
	{{if api_server=='000' || api_server == ''}}
	<?php echo Lang('report') ?>
	{{else}}
	<a href="#app=4&cpp=35&url=${encodeurl('report', 'gamereport', 'player','&sid=')}${sid}%26title%3D${encodeURI(name!=''? name : o_name)}" class="playerreport"><?php echo Lang('report') ?></a>
	{{/if}}
	</td>
	<td class="online">--</td>
	<td class="active">--</td>
	<td class="income">--</td>
	<td class="consume">--</td>
	<?php } ?>
</tr>
</script>

<h2><span id="tt">玩家<?php echo Lang('game_server'); ?></span></h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date">
			<div class="title cf">
				<form id="get_search_submit" method="get" name="form">
				<div class="tool_group">
					<select name="cid" id="companyul" class="ipt_select">
						<option value="0"><?php echo Lang('today_new_server'); ?></option>
					</select>
					 <label>游戏服：qq_s
						<input type="text" id="sname" name="sname" class="ipt_txt_s" value="">
					 </label>
					 <input name="dogetSubmit" type="hidden" value="1">
					 <input type="submit" class="btn_sbm" value="查 询">
				</div>
				</form>
			</div>
		</div>
	</div>
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
	    	<th class="num">&nbsp;</th>
	    	<th><?php echo Lang('server_name'); ?></th>
	    	<th><?php echo Lang('server_date'); ?></th>
	    	<?php if ($roleid <= 3) { ?>
	    	<th><?php echo Lang('report'); ?></th>
	    	<th><?php echo Lang('current_online'); ?></th>
	    	<th><?php echo Lang('active'); ?></th>
	    	<th><?php echo Lang('today').Lang('income'); ?></th>
	    	<th><?php echo Lang('today').Lang('consumption'); ?></th>
	    	<?php } ?>
		</tr>
		</thead>
		<tbody id="serverlist">
			   
		</tbody>
		</table>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>
