<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
<?php if ($data['isall'] == 1){ ?>
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, info_list;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=report&c=pay&v=ajax_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#info_list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			success: showList
		});
	});
}

function getsearchList(index){
	var query = "<?php echo INDEX; ?>?m=report&c=pay&v=ajax_list&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#info_list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: function(data){
				showList(data, 1);
			},
			error: function() {
				$('#info_list').html('<tr><td colspan="8">数据加载异常...</td></tr>');
			}
		});
	});
}


function showList( data, type) {
	if (data.status == -1){
		$('#info_list').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), info_list = data.list, cid = data.cid;
		if (type != undefined && type == 1){
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchList });
		}else {
			$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		}
		$( "#info_list" ).empty();
		if (data.count > 0){
			$( "#listtpl" ).tmpl( info_list ).prependTo( "#info_list" );
			$( "#info_list" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#info_list" ).parent().parent('div.content').css('height', $('#info_list').parent('table.global').css('height'));
			}
		}else {
			$('#info_list').html('<tr><td colspan="8">暂无充值记录...</td></tr>');
		}
	}
}

<?php }else { ?>
function getList(){
	var query = "<?php echo INDEX; ?>?m=report&c=pay&v=ajax_list",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&key=pay";
	$( "#info_list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: data,
			success: function(data){
				if (data.status == 1){
					$('#info_list').html(data.msg);
				}else {
					if (data.list.length > 0) {
						$( "#listtpl" ).tmpl( data.list ).prependTo( "#info_list" );
						$( "#info_list" ).stop(true,true).hide().slideDown(400);
					}else {
						$('#info_list').html('<tr><td colspan="8">暂无充值记录...</td></tr>');
					}
				}
			},
			error: function() {
				$('#info_list').html('<tr><td colspan="8">数据加载异常...</td></tr>');
			}
		});
	});
	return false;
}
<?php } ?>

function sid_to_name(sid) {
	if (sid > 0 && typeof global_serverlist != 'undefined'){
		for (var key in global_serverlist) {
			if (global_serverlist[key].sid == sid) {
				return global_serverlist[key].name + '-' + global_serverlist[key].o_name;
			}
		}
	}
	return '';
}

$(document).ready(function(){
	<?php if ($data['isall'] != 1){ ?>
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo urlencode($_GET['title']).Lang('pay_log'); ?>");
	}
	$('#info_list').html('<tr><td colspan="8">数据加载中...</td></tr>');
	getList(1);
	<?php } ?>

	<?php if ($data['isall'] == 1){ ?>
		/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('option[value!="0"]', $('#sid')).remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('option[value!="0"]', $('#sid')).remove();
		}
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		$('#info_list').html('<tr><td colspan="8">数据加载中...</td></tr>');
		getsearchList(1);
	});
	$('#get_search_submit').submit();
	<?php } ?>
});
</script>

<script type="text/template" id="listtpl">
<tr>
<td>${pid}</td>
<?php if ($data['isall'] == 1){ ?>
<td>${sid_to_name(sid)}</td>
<?php } ?>
<td><span class="orangetitle">${amount}</span></td>
<td>${coins}</td>
<td>${oid}</td>
<td>${dtime}</td>
<td>{{if success==1}}<span class="greentitle"><?php echo Lang('pay_success') ?>{{else}}<span class="redtitle"><?php echo Lang('pay_wait') ?>{{/if}}</span></td>
<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<?php if ($data['isall'] == 1){ ?>
	<ul class="dash1">
		<li class="fade_hover selected"><a><span><?php echo Lang('pay_log') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
		<form id="get_search_submit" action="<?php echo INDEX; ?>?m=report&c=pay&v=ajax_list" method="get" name="form">
		<ul class="nav_li">
			<li class="nobg">
				<p>
					<select name="cid" id="cid">
						<option value="0"><?php echo Lang('operation_platform') ?></option>
					</select>
					<select name="sid" id="sid">
						<option value="0"><?php echo Lang('all_server') ?></option>
					</select>
					<?php echo Lang('player') ?>：<input type="text" name="playername" id="playername" />
					<?php echo Lang('pay_order_no') ?>：<input type="text" name="oid" id="oid" />
					<?php echo Lang('date') ?>：<input type="text" name="starttime" readonly onclick="WdatePicker()" id="starttime" /style="width:100px"> - <input type="text" name="endtime" readonly onclick="WdatePicker()" id="endtime" style="width:100px">
					<select name="success">
						<option value=""><?php echo Lang('status') ?></option>
						<option value="2"><?php echo Lang('pay_wait') ?></option>
						<option value="1"><?php echo Lang('pay_success') ?></option>
						<option value="3"><?php echo Lang('test') ?></option>
					</select>
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
					<input type="hidden" name="dogetSubmit" value="1">
				</p>
			</li>
		</ul>
		</form>
	</div>
	<br class="clear">
	<?php } ?>
	<div class="content">
			<table class="global" width="100%" style="max-width:100%;min-width:600px" cellpadding="0" cellspacing="0">
			<thead>
			    <th style="width:80px;">ID</th>
			    <?php if ($data['isall'] == 1){ ?>
			    <th style="width:200px;"><?php echo Lang('server') ?></th>
			    <?php } ?>
			    <th style="width:80px;"><?php echo Lang('pay_money') ?></th>
			    <th style="width:80px;"><?php echo Lang('pay_ingot') ?></th>
			    <th style="width:250px;"><?php echo Lang('pay_order_no') ?></th>
			    <th style="width:120px;"><?php echo Lang('pay_order_time') ?></th>
			    <th style="width:50px;"><?php echo Lang('status') ?></th>
				<th>&nbsp;</th>
			</thead>
			<tbody id="info_list">
				
			</tbody>
		</table>
	</div>
    <div class="pagination pager" id="pager"></div>
</div>
