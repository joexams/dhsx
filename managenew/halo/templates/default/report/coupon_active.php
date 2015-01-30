<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, couponlist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=report&c=coupon&v=active&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#couponlist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_submit').serialize(),
			success: showList
		});
	});
}

function showList( data ) {
	if (data.status == -1){
		$('#couponlist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize );
		couponlist = data.list;
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#couponlist" ).empty();
		if (data.count > 0){
			$( "#couponlisttpl" ).tmpl( couponlist ).prependTo( "#couponlist" );
			$( "#couponlist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#couponlist" ).parent().parent('div.content').css('height', $('#couponlist').parent('table.global').css('height'));
			}
		}
	}
}

function sid_to_name(sid) {
	if (sid < 1) {
		return '';
	}
	if (typeof global_serverlist != 'undefined') {
		for(var key in global_serverlist) {
			if (global_serverlist[key].sid == sid) {
				return '-'+global_serverlist[key].name;
			}
		}
	}
	return '';
}
function cid_to_name(cid) {
	if (typeof global_companylist != 'undefined') {
		for(var key in global_companylist) {
			if (global_companylist[key].cid == cid) {
				return global_companylist[key].name;
			}
		}
	}
	return '';
}

$(function() {
	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
	}


	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('#sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option[value!="0"]').remove();
		}
	});

	/**
	 * 搜索
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		$('#getsubmit').attr('disabled', 'disabled');
		var objform = $(this);
		var codeid = $('#codeid').val();
		if (codeid != '0'){
			recordNum = 0;
			getList(1);
		}else {
			$('#getsubmit').removeAttr('disabled');
		}
	});
	
	/**
	 * 导出
	 */
	$('#exportcoupon').on('click', function(e) {
		var codeid = $('#codeid').val();
		if (codeid != '0'){
			var url = "<?php echo INDEX; ?>?m=report&c=coupon&v=export_active&"+$('#get_search_submit').serialize();
			location.href = url;
		}
	});

	$('#codeid').on('change', function() {
		var code = $(this).val(), times = $('option:selected', $(this)).attr('data');
		var str = '';
		if (code != '0') {
			times = isNaN(parseInt(times)) ? 1 : parseInt(times);
			times = Math.max(times, 1);
			for(var i=1; i<=times; i++) {
				str += '<option value="'+i+'">'+i+'</option>';
			}
		}
		$('#times option[value!="0"]').remove();
		$('#times').append(str);
	});
});
</script>

<script type="text/template" id="couponlisttpl">
<tr>
<td>${id}</td>
<td>${cid_to_name(cid)}${sid_to_name(sid)}</td>
<td>${code}</td>
<td>${ctime}</td>
<td>{{if player_id > 0}}${username}(${nickname}){{else}}-{{/if}}</td>
<td>&nbsp;</td>
</tr>
</script>


<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('active_coupon') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav">
	    <form id="get_search_submit" action="<?php echo INDEX; ?>?m=report&c=data&v=coupon" method="get" name="form">
	    <ul class="nav_li">
	        <li class="nobg">
	            <p>
	            	<select name="table_name" id="codeid" class="table_name">
	            		<option value="0"><?php echo Lang('select_active'); ?></option>
	            		<?php foreach ($activelist as $key => $value) {
	            			echo '<option value="'.$value['table_name'].'" data="'.$value['times'].'">'.$value['table_comment'].'</option>';
	            		} ?>
	            	</select>

	            	<select name="cid" class="cid" id="cid">
	            		<option value="0"><?php echo Lang('operation_platform') ?></option>
	            	</select>
	            	<select name="sid" id="sid">
	            		<option value="0"><?php echo Lang('all_server') ?></option>
	            	</select>

	            	<select name="times" id="times">
	            		<option value="0">批次</option>
	            	</select>

	            	<select name="usetype" id="usetype">
	            		<option value="0">是否使用</option>
	            		<option value="1">使用</option>
	            		<option value="2">未使用</option>
	            	</select>
	            	<?php echo Lang('coupon'); ?>:
	            	<input type="text" name="code" value="">
	            	<?php echo Lang('player_name'); ?>：
	            	<input type="text" name="playername" value="">

	                <input type="submit" name="getsubmit" id="btnsubmit" value="<?php echo Lang('search'); ?>" class="button_link">
	                <input type="button" id="exportcoupon" value="搜索并导出兑换券" style="cursor: pointer;">
	                <input name="dogetSubmit" type="hidden" value="1">
	            </p>
	        </li>
	    </ul>
	    </form>
	</div>
	<br class="clear">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:50px;">ID</th>
					<th	style="width:10%"><?php echo Lang('server'); ?></th>
					<th style="width:10%"><?php echo Lang('coupon'); ?></th>
					<th style="width:120px;"><?php echo Lang('receive_time'); ?></th>
					<th style="width:120px;"><?php echo Lang('receive_user'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="couponlist">

			</tbody>
		</table>
	</div>
	<div class="pagination pager" id="pager"></div>
</div>
