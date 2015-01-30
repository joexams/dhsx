<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript" src="static/js/jquery.AjaxQueue.js"></script>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, cid = "<?php echo $data['url']['cid'] ?>", pserverlist;
var newQueue = $.AM.createQueue('queue');
function getList(index){
	var query = "<?php echo INDEX; ?>?m=develop&c=server&v=server_list&type=list&cid="+cid+"&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#serverlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: showList
		});
	});
}
function showList( data ) {
	if (data.status == 1){
		$('#serverlist').html(data.msg);
	}else {
		if (cid == 0 && data.count == 0){
			$('option[value="0"]', $('#companyul')).remove();
			$('#companyul').change();
			return false;
		}
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), pserverlist = data.list, cid = data.cid;
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#serverlist" ).empty();
		if (data.count > 0){
			$( "#serverlisttpl" ).tmpl( pserverlist ).prependTo( "#serverlist" );
			$( "#serverlist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#serverlist" ).parent().parent('div.content').css('height', $('#serverlist').parent('table.global').css('height'));
			}

			<?php if ($roleid <= 3) { ?>
			//$.AM.destroyQueue('queue');
			var ssid = ccid = 0, sname = ''; 
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
}

$(document).ready(function(){
 	/**
 	* 运营平台
 	*/
 	setTimeout(function() {
	 	if (typeof global_companylist != 'undefined') {
	 		$('#companyultpl option[value!="0"]').remove();
	 	    $('#companyultpl').tmpl(global_companylist).appendTo('#companyul');
	 	    $('.h_lib_nav').fadeIn();
	 	    if (cid > 0){
				$('option[value="'+cid+'"]', $('#companyul')).attr('selected', 'selected');
				location.hash = $('option:selected', $('#companyul')).attr('rel');
			}
	 	}
	 	getList(pageIndex);
 	}, 250);
 	
 	$('#companyul').on('change', function(){
 		cid = $(this).val();
 		// if (cid > 0){
 		// 	location.hash = $('option:selected', $(this)).attr('rel');
 		// }else {
 		// 	location.hash = '#app=5';
 		// }
 		recordNum = 0;
 		getList(1);
 	});

 	$('#get_search_submit').on('submit', function(e) {
 		e.preventDefault();
 		recordNum = 0;
 		getList(1);
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
	// 		$('#container').load('<?php echo INDEX; ?>?m=report&c=player&v=detail_list&sid='+sid+'&cid='+cid+'&title='+title);
	// 	}
	// });
	
});
</script>

<script type="text/template" id="companyultpl">
<option value="${cid}" rel="#app=5&cpp=24&url=${encodeurl('<?php echo $data['url']['m']; ?>', '<?php echo $data['url']['v']; ?>', '<?php echo $data['url']['c']; ?>', '&cid=')}${cid}">${fn} - ${name}</option>
</script>

<script type="text/template" id="serverlisttpl">
<tr id="s_${sid}">
	<?php if ($data['limit'] === false){ ?>
	<td>${sid}</td>
	<?php } ?>
	<td><a href="#app=5&cpp=24&url=${encodeurl('<?php echo $data['url']['m']; ?>', 'detail_list', '<?php echo $data['url']['c'] ?>','&sid=')}${sid}%26cid%3D${cid}%26title%3D${name}" class="playerinfo" data-cid="${cid}" data-sid="${sid}">${name}-${o_name}</a>{{if api_server=='000'}}<span class="redtitle">(未配置)</span>{{/if}}
	{{if api_server != '000' && distancedate <= 0}}<span class="orangetitle">[待开启]</span>{{/if}}
	{{if combined_to > 0}} 『<a href="#app=5&cpp=24&url=${encodeurl('<?php echo $data['url']['m']; ?>', 'detail_list', '<?php echo $data['url']['c'] ?>','&sid=')}${combined_to}%26cid%3D${cid}%26title%3D${name}" class="playerinfo" data-cid="${cid}" data-sid="${combined_to}">已合服</a>』 {{/if}}
	<br>
	${server}
	</td>
	<td>${open_date}<br><span class="graytitle">${distancedate}天</span></td>
   <?php if ($roleid <= 3) { ?>
	<td><a href="#app=5&cpp=24&url=${encodeurl('report', 'server_total', 'data','&cpp=24&sid=')}${sid}%26name%3D${encodeURI(name!=''? name : o_name)}" class="playerreport"><?php echo Lang('report') ?></a></td>
	<td class="online">-</td>
	<td class="active">-</td>
	<td class="income">-</td>
	<td class="consume">-</td>
	<?php } ?>
	<td>&nbsp;</td>
</tr>
</script>


<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo  Lang('game_server'); ?></span></a></li>
	</ul>
	
	<br class="clear">
	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=develop&c=server&v=server_list" method="get" name="form">
		<ul class="nav_li">
			<li>
				<p>
					<select name="cid" id="companyul">
						<option value="0"><?php echo Lang('today_new_server'); ?></option>
					</select>

					<span title="graytitle">qq_s</span><input type="text" name="sname" value="" >
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

	<div class="content clear">
		<!-- Begin form elements -->
		<div id="list_op_tips" style="display: none;"><p></p></div>
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<?php if ($data['limit'] === false){ ?>
						<th style="width:50px;">SID</th>
						<?php } ?>
						<th style="width:20%"><?php echo Lang('server_name'); ?></th>
						<th style="width:120px;"><?php echo Lang('server_date'); ?></th>
						<?php if ($roleid <= 3) { ?>
						<th style="width:80px;"><?php echo Lang('report'); ?></th>
						<th style="width:70px;"><?php echo Lang('current_online'); ?></th>
						<th style="width:70px;"><?php echo Lang('active'); ?></th>
						<th style="width:70px;"><?php echo Lang('today').Lang('income'); ?></th>
						<th style="width:70px;"><?php echo Lang('today').Lang('consumption'); ?></th>
						<?php } ?>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody id="serverlist">

				</tbody>
			</table>
		<!-- End form elements -->
	</div>
		<div class="pagination pager" id="pager"></div>
</div>
