<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, cid = 0, recordNum = 0, rankinglist;
function getList(index){
	var query = '<?php echo INDEX; ?>?m=report&c=pay&v=ajax_rank_list';
	pageIndex = index;
	$( "#rankinglist" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: {cid: $('#cid').val(), top: index, recordnum: recordNum},
			success: showList
		});
	});
}

function showList ( data ) {
	if (data.status == -1){
		$('#rankinglist').html(data.msg);
	}else {
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), rankinglist = data.list;
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		$( "#rankinglist" ).empty();
		$('#total_amount').html(0);
		$('#ARPU').html(0);
		$('#pay_person_num').html(0);
		$('#pay_times').html(0);
		if (data.count > 0){
			$('#total_amount').html(data.total_amount);
			$('#ARPU').html(data.arpu);
			$('#pay_person_num').html(data.count);
			$('#pay_times').html(data.total_pay_times);

			$( "#rankinglisttpl" ).tmpl( rankinglist ).prependTo( "#rankinglist" );
			$( "#rankinglist" ).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$( "#rankinglist" ).parent().parent('div.content').css('height', $('#rankinglist').parent('table.global').css('height'));
			}
		}
	}
}

$(document).ready(function(){
	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
		$('#cid').change();
	}


	$('#cid').on('change', function(){
		cid = parseInt($(this).val());
		if (cid > 0){
			$('#rankinglist').html('<tr><td colspan="8">数据加载中...</td></tr>');
			recordNum = 0;
			getList(1);
		}
	});

	$('#rankinglist').html('<tr><td colspan="8">数据加载中...</td></tr>');
	getList(1);

	$('#allplatformtop').on('click', function(){
		var query = '<?php echo INDEX; ?>?m=report&c=pay&v=ajax_rank_list';
			recordNum = 0;
		$('#rankinglist').html('<tr><td colspan="8">数据加载中...</td></tr>');
		$( "#rankinglist" ).fadeOut( "medium", function () {
			$.ajax({
				dataType: "json",
				url: query,
				data: {isall: 1},
				success: function(data){
					$( "#rankinglist" ).empty();
					$('#total_amount').html(0);
					$('#ARPU').html(0);
					$('#pay_person_num').html(0);
					$('#pay_times').html(0);
					if (data.count > 0){
						$('#total_amount').html(data.total_amount);
						$('#ARPU').html(data.arpu);
						$('#pay_person_num').html(data.count);
						$('#pay_times').html(data.total_pay_times);
						$('.pager').empty();
						$( "#rankinglisttpl" ).tmpl( data.list ).prependTo( "#rankinglist" );
						$( "#rankinglist" ).stop(true,true).hide().slideDown(400);
						$( "#rankinglist" ).parent().parent('div.content').css('height', 'auto');
					}
				},
				error: function() {
					$('#rankinglist').html('<tr><td colspan="8">数据失败...</td></tr>');
				}
			});
		});
	});
});

function cid_to_name(cid){
	if (global_companylist.length > 0 && cid > 0){
		for(var key in global_companylist){
			if (global_companylist[key].cid == cid){
				return global_companylist[key].name;
			}
		}
	}
}
</script>

<script type="text/template" id="rankinglisttpl">
<tr>
	<td>${ranking}</td>
	<td>${cid_to_name(cid)}</td>
	<td>${username}{{if nickname!=''}}(${nickname}){{/if}}</td>
	<td><span class="orangetitle">${amount}</span></td>
	<td>${last_pay_amount}</td>
	<td>${date('Y-m-d H:i', last_pay_time)}</td>
	<td>${pay_num}</td>
	<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('pay_ranking') ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="nav singlenav" style="display:;">
		<ul class="nav_li">
			<li class="nobg">
				<p>
					<select name="cid" id="cid">

					</select>
					<a href="javascript:;" id="allplatformtop">所有平台排名前50</a>
				</p>
			</li>
		</ul>
	</div>
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<tbody>
				<tr >
				    <th style="width:100px"><?php echo Lang('pay_money'); ?></th>
				    <td style="width:80px" id="total_amount">0</td>
				    <th style="width:100px"><?php echo Lang('ARPU'); ?></th>
				    <td style="width:80px" id="ARPU">0</td>
				    <th style="width:100px"><?php echo Lang('pay_person_num'); ?></th>
				    <td style="width:80px" id="pay_person_num">0</td>
				    <th style="width:100px"><?php echo Lang('total_pay_times'); ?></th>
				    <td style="width:80px" id="pay_times">0</td>
				    <td>&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="content">
		<!-- Begin form elements -->
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
				    <th style="width:50px"><?php echo Lang('ranking') ?></th>
				    <th style="width:60px"><?php echo Lang('company_platform') ?></th>
				    <th style="width:300px"><?php echo Lang('player') ?></th>
				    <th style="width:80px"><?php echo Lang('pay_money') ?></th>
				    <th style="width:80px"><?php echo Lang('last_pay_money') ?></th>
				    <th style="width:120px"><?php echo Lang('last_pay_time') ?></th>
				    <th style="width:50px"><?php echo Lang('pay_times') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="rankinglist">

			</tbody>
		</table>
	<!-- End form elements -->
	</div>
	<div class="pagination pager" id="pager"></div>
	<br class="clear">
</div>
