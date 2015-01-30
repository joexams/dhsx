<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 200;
Ha.page.listEid = 'log_list';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '<?php echo Lang('no_find_data')?>'
Ha.page.url = "<?php echo INDEX; ?>?m=report&c=pay&v=income&dogetSubmit=1";

$(function(){
	Ha.page.getList(1);
	/**
	* 运营平台
	*/
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
	}
	$('#cid1').on('change', function(){
		$('#toolbar li').eq($('.current', $('#toolbar')).index()).children().click();
	});
	/**
	* 切换
	* @return {[type]} [description]
	*/
	$('#toolbar').on('click', 'a.othermenu', function(){
		$('.current',$('#toolbar')).removeClass('current');
		$(this).parent().addClass('current');
		var type = $(this).attr('data-type');
		switch (type)
		{
		case 'month':
			Ha.page.pageSize = 200;
			break;
		default:
			Ha.page.pageSize = 20;
		}
		var cid = $('#cid1').val();
		Ha.page.queryData = 'type='+type+'&cid='+cid;
		Ha.page.getList(1);
	});
});
</script>
<script type="text/template" id="log_listtpl">
<tr>
<td><strong>${idate}</strong></td>
<td>&nbsp;<p style="float: left;background-color:#ED561B;width:${max}px">&nbsp;</p><span class="orangetitle">${pay_amount}</span> <?php echo Lang('yuan')?></td>
<td>${pay_player_count}</td>
<td>${pay_num}</td>
<td>{{if arpu}}${arpu}{{else}}-{{/if}}</td>
<td>${new_player}</td>
</tr>
</script>
<h2><span id="tt"><?php echo Lang('pay_income_report') ?></span></h2>
<div class="container" id="container">
<div class="tool_date cf">
        <div class="title cf">
            <div class="tool_group">
                <select name="cid" id="cid1" class="cid ipt_select">
                    <option value="0"><?php echo Lang('company_platform'); ?></option>
                </select>
            </div>
        </div>
</div>
<div class="speed_result">
            <div class="mod_tab_title first_level_tab">
                <ul id="toolbar">
                    <li><a class="othermenu" href="javascript:void(0);" data-type="year"><?php echo Lang('year_report')?></a></li>
                    <li class="current"><a class="othermenu" href="javascript:void(0);" data-type="month"><?php echo Lang('month_report')?></a></li>
                    <li><a class="othermenu" href="javascript:void(0);" data-type="week"><?php  echo Lang('week_report')?></a></li>
                    <li><a class="othermenu" href="javascript:void(0);" data-type="day"><?php echo Lang('day_report')?></a></li>
                </ul>
            </div>
            </div>
	<div class="column cf" id="table_column">
		<div id="submit_area">
				<form id="post_submit" method="post">
				<table>
					<thead>
					<tr>
					<th><?php echo Lang('date')?></th>	
					 <th><?php echo Lang('total_income')?></th>
					 <th><?php echo Lang('pay_person_num')?></th>
					 <th><?php echo Lang('total_pay_times')?></th>
					 <th>ARPU</th>
					 <th><?php echo Lang('new_pay_user')?></th>
					</tr> 
					</thead>
					<tbody id="log_list">

					</tbody>
				</table>				
				</form>
		</div>
		<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>
</div>