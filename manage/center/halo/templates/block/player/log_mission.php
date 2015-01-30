<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeflag = 0, list, typelist;
function getlogmissionList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&key=mission&typeflag="+typeflag+"&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: data,
			success: showlogmissionList
		});
	});
	return false;
}
function getsearchlogmissionList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_mission_submit').serialize(),
			success: function(data){
				showlogmissionList(data, 1);
			}
		});
	});
	return false;
}
function mission_id_to_name(id){
	if (typelist != undefined){
		if (typelist != undefined){
			for (var key in typelist){
				if (key == id){
					return typelist[key].sectionname +"-"+ typelist[key].missionname;
				}
			}
		}
	}
	return '';
}
function showlogmissionList( data, type ) {
	if (data.status == 1){
		$('#list').html(data.msg);
	}else {
		if (data.mission != undefined){
			typeflag = 1;
			typelist = data.mission;
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		
		if (type != undefined && type == 1){
			$( "#missionpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogmissionList });
		}else {
			$( "#missionpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getlogmissionList });
		}
		$( "#list" ).empty();
		if (data.count > 0){
			$( "#listtpl" ).tmpl( list ).prependTo( "#list" );
			$( "#list" ).stop(true,true).hide().slideDown(400);

			if (pageCount > 1){
				$( "#list" ).parent().parent('div.content').css('height', $('#list').parent('table.global').css('height'));
			}
		}
	}
	return false;
}
$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'; ?>"+'<?php echo Lang('mission').Lang('log'); ?>');
	}
	getlogmissionList( pageIndex );

	$('#get_search_mission_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogmissionList(1);
	});
});
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${mission_id}</td>
<td>${mission_id_to_name(mission_id)}</td>
<td>${times}</td>
<td>${failed_challenge}</td>
<td>${rank}</td>
<td>{{if is_finished>0}}<span class="greentitle">OK</span>{{else}}<span class="redtitle">NO</span>{{/if}}</td>
<td>${date('Y-m-d H:i',first_challenge_time)}</td>
<td>${date('Y-m-d H:i',challenge_time)}</td>
<td>${hero_remain_times}</td>
<td>${nickname}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<form id="get_search_mission_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
	<div class="nav singlenav">
		<ul class="nav_li">
			<?php if ($data['id'] <= 0 && empty($_GET['title'])){ ?>
			<li>
				<p>
					<?php echo Lang('player'); ?>：
					<select name="playertype">
						<option value=""><?php echo Lang('player_name'); ?></option>
						<option value="1"><?php echo Lang('player_nick'); ?></option>
					</select>
					<input type="text" name="playername">
				</p>
			</li>
			<?php } ?>
			<li>
				<p>
					<?php echo Lang('mission'); ?>：
					<input type="text" name="name" value="">
					<?php echo Lang('between_date'); ?>：
				<input name="starttime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00'})" type="text" value="">
					<input name="endtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59'})" type="text" value="">
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="hidden" name="sid" value="<?php echo $data['sid'] ?>">
					<input type="hidden" name="id" value="<?php echo $data['id'] ?>">
					<input type="hidden" name="key" value="mission">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
				</p>
			</li>
		</ul>
	</div>
	</form>
	<br class="clear">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>	
				    <th style="width:50px;">ID</th>
				    <th style="width:150px;"><?php echo Lang('副本'); ?></th>
				    <th style="width:60px;"><?php echo Lang('挑战次数'); ?></th>
				    <th style="width:60px;"><?php echo Lang('战败次数'); ?></th>
				    <th style="width:50px;"><?php echo Lang('评定'); ?></th>
				    <th style="width:50px;"><?php echo Lang('完成'); ?></th>
				    <th style="width:120px;"><?php echo Lang('第一过关时间'); ?></th>
				    <th style="width:120px;"><?php echo Lang('最后过关时间'); ?></th>
					<th style="width:120px;"><?php echo Lang('英雄副本剩余次数'); ?></th>
					<th style="width:150px"><?php echo Lang('player') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="missionpager"></div>
</div>
