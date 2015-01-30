<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeid = 0, typeflag = 0, list;
function getsearchlogcoinList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_coin_submit').serialize()+'&typeflag='+typeflag,
			success: showlogcoinList
		});
	});
	return false;
}

function showlogcoinList( data ) {
	if (data.status == 1){
		$('#list').html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#coinstypetpl').tmpl( data.type.cons ).appendTo('#coinscons');
			$('#coinstypetpl').tmpl( data.type.get ).appendTo('#coinsget');
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		$( "#coinspager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getsearchlogcoinList });
		$( "#list" ).empty();

		if (pageIndex == 1){
			$('#getnum').html('+'+(data.allnum.getnum?data.allnum.getnum:0));
			$('#connum').html((data.allnum.connum?data.allnum.connum:0));
		}
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
function type_id_to_name(id){
	if (typelist != undefined){
		if (typelist.cons != undefined && typelist.cons.length > 0){
			for (var key in typelist.cons){
				if (typelist.cons[key].id == id){
					return typelist.cons[key].name;
				}
			}
		}
		if (typelist.get != undefined && typelist.get.length > 0){
			for (var key in typelist.get){
				if (typelist.get[key].id == id){
					return typelist.get[key].name;
				}
			}
		}
	}
	return '';
}
$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'.Lang('coin_log'); ?>");
	}

	$('.h_lib_nav').on('click', 'a.coins_type', function(){
		typeid = $(this).attr('data-id');
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_coin_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_coin_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getsearchlogcoinList(1);
	});

	$('#get_search_coin_submit').submit();;
});
</script>
<script type="text/template" id="coinstypetpl">
<li><a href="javascript:;" data-id="${id}" class="coins_type">${name}</a><span></span></li>
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i:s', change_time)}</td>
<td>${value}</td>
<td>${type_id_to_name(type)}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('con_type'); ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>
	<div class="h_lib_nav">
	<ul id="coinscons">
	</ul>
	</div>
	<br class="clear">
	<div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('get_type'); ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>
	<div class="h_lib_nav">
	<ul id="coinsget">
	</ul>
	</div>
	<br class="clear">
	<form id="get_search_coin_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
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
					<?php echo Lang('between_date'); ?>：
					<input name="starttime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00'})" type="text" value="">
					<input name="endtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59'})" type="text" value="">
					<?php echo Lang('coins') ?>
					<input type="text" name="coins" value="0">
				</p>
			</li>
			<li>
				<p>
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="hidden" name="sid" value="<?php echo $data['sid'] ?>">
					<input type="hidden" name="id" value="<?php echo $data['id'] ?>">
					<input type="hidden" name="key" value="coin">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="搜索" class="button_link">
				</p>
			</li>
				<li class="nobg">
					<p>获取类铜钱：<span class="orangetitle" id="getnum">0</span></p>
				</li>
				<li class="nobg">
					<p>消费类铜钱：<span class="greentitle" id="connum">0</span></p>
				</li>
			</ul>
		</div>
	</form>
	<br class="clear">

	<div class="content ">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
				    <th style="width:50px;">ID</th>
				    <th style="width:120px;"><?php echo Lang('op_time') ?></th>
				    <th style="width:20%"><?php echo Lang('coins') ?></th>
				    <th style="width:20%"><?php echo Lang('op_type') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="coinspager"></div>
</div>
