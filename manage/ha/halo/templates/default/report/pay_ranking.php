<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'rankinglist';
Ha.page.colspan = 7;
Ha.page.emptyMsg = '没有找到数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=report&c=pay&v=ajax_ranking_list";

function showStatData(data)
{
	if (data.count > 0) {
		$('#total_amount').html(data.total_amount);
		$('#ARPU').html(data.arpu);
		$('#pay_person_num').html(data.count);
		$('#pay_times').html(data.total_pay_times);
	}
}

$(function(){
	
	<?php if ($cid > 0 && $sid > 0) { ?>
		Ha.page.queryData = {cid : '<?php echo $cid?>', sid: '<?php echo $sid?>'};
		Ha.page.getList(1, showStatData);
	<?php }else { ?>
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
				Ha.page.recordNum = 0;
				Ha.page.pageSize = 15;
				Ha.page.queryData = {cid: cid};
				Ha.page.getList(1, showStatData);
			}
		});

		Ha.page.queryData = {cid: $('#cid').val()};
		Ha.page.getList(1, showStatData);
	<?php } ?>

	$('.first_level_tab').on('click', 'a.ranktype', function(){
		$('.current', $('.first_level_tab')).removeClass('current');
		$(this).parent().addClass('current');
		type = $(this).attr('data-type');
		if (type == 1) {
			Ha.page.pageSize = 50;
			Ha.page.recordNum = 0;
			Ha.page.queryData = {isall: 1};
			Ha.page.getList(1, showStatData);
			$('#tblMore').hide();
		}else {
			Ha.page.pageSize = 15;
			Ha.page.recordNum = 0;
			Ha.page.queryData = {cid: $('#cid').val()};
			Ha.page.getList(1, showStatData);
			$('#tblMore').show();
		}
	});
});
</script>

<script type="text/template" id="rankinglisttpl">
<tr>
	<td class="num">${ranking}</td>
	<td>${cid_to_name(cid)}</td>
	<td>${username}{{if nickname!=''}}(${nickname}){{/if}}</td>
	<td><span class="orangetitle">${amount}</span></td>
	<td>${last_pay_amount}</td>
	<td>${date('Y-m-d H:i', last_pay_time)}</td>
	<td>${pay_num}</td>
</tr>
</script>

<h2><span id="tt"><?php echo Lang('pay_ranking'); ?></span></h2>
<div class="container" id="container">
	<?php if (!$cid && !$sid) { ?>
	<div class="speed_result">
	    <div class="mod_tab_title first_level_tab">
	    	<ul>
	    		<li class="current"><a href="javascript:void(0);" data-type="0" class="ranktype">全部排行</a></li>
	    		<li><a href="javascript:void(0);" data-type="1" class="ranktype">Top50排行</a></li>
	    	</ul>
	    </div>
	</div>
	<?php } ?>
	<div class="column cf" id="table_column">
		<div class="title">
			<?php if ($cid > 0 && $sid > 0) { ?>
			<input name="cid" id="cid" type="hidden" value="<?php echo $cid ?>">
			<input name="sid" id="sid" type="hidden" value="<?php echo $sid ?>">
			<?php }else { ?>
				<div class="more" id="tblMore">
		            <div id="div_pop">
		                <select name="cid" id="cid" class="ipt_select">

		                </select>
		            </div>
		        </div>
			<?php } ?>
			详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th class="num"><?php echo Lang('ranking') ?></th>
		    <th><?php echo Lang('company_platform') ?></th>
		    <th><?php echo Lang('player') ?></th>
		    <th><?php echo Lang('pay_money') ?></th>
		    <th><?php echo Lang('last_pay_money') ?></th>
		    <th><?php echo Lang('last_pay_time') ?></th>
		    <th><?php echo Lang('pay_times') ?></th>
		</tr>
		</thead>
		<tbody id="rankinglist">
			   
		</tbody>
		</table>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>