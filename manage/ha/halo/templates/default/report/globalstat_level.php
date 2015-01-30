<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var chart;
$(function(){
	/**
	 * 运营平台
	 */
	<?php if (!$cid && !$sid) { ?>
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}

	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('#sid option').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option').remove();
		}
	});
	<?php } ?>

    $('#get_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		if (cid > 0 && sid != null){
			var url =  '<?php echo INDEX; ?>?m=report&c=globalstat&v=level';

			$('#chart_column').show();
			Ha.mask.show('chart_column');
			$('#chart_column2').hide();

			Ha.common.ajax(url, 'json', objform.serialize(), 'get', 'container', function(data){
				if (data.status == 0 && data.count > 0) {
					$("#flashChart").multiChart(data.list);


					var strHtml = '';
					if ($('#countheader').find('th').size() < 2){
						for(var key in data.categories){
							strHtml = strHtml + '<th>'+ data.categories[key] +'</th>';
						}
						$('#countheader').empty().append(strHtml);
					}

					strHtml = '<tr>';
					for(var key in data.levels){
						strHtml = strHtml + '<td>'+ (data.levels[key].y > 0 ? '<a href="javascript:;" class="level">'+data.levels[key].y+'</a>' : '-')  +'</td>'
					}
					strHtml = strHtml + '</tr>';

					strHtml = strHtml + '<tr>';
					for(var key in data.man){
						strHtml = strHtml + '<td>'+ (data.man[key].y > 0 ? '<a href="javascript:;" class="level">'+data.man[key].y+'</a>' : '-') +'</td>'
					}
					strHtml = strHtml + '</tr>';

					strHtml = strHtml + '<tr>';
					for(var key in data.female){
						strHtml = strHtml + '<td>'+ (data.female[key].y > 0 ? '<a href="javascript:;" class="level">'+data.female[key].y+'</a>' : '-')  +'</td>'
					}
					strHtml = strHtml + '</tr>';
					$('#countlist').empty().append(strHtml).show();

					$('#level-area').show();
					$('#table_column').show();
				}
				Ha.mask.clear('chart_column');
			}, 1);

		}else {
			Ha.notify.show('<?php echo Lang('not_selected_company_or_server'); ?>', '', 'error');
		}
		return false;
	});

	<?php if ($cid > 0 && $sid > 0) { ?>
	$('#get_submit').submit();
	<?php } ?>

	$('#countlist').on('click', 'a.level', function(){
		var index = $(this).parent('td').index();
		var level = $('#countheader').find('th').eq(index).html();
		if (level != ''){
			var arrlevel = level.split('~');
			var url = '<?php echo INDEX; ?>?m=report&c=globalstat&v=level';
			var queryData = $('#get_submit').serialize()+ '&startlevel='+arrlevel[0]+'&endlevel='+arrlevel[1];
			$('#flashChart2_title').html(level+'等级趋势图');
			$('#chart_column2').show();
			Ha.common.ajax(url, 'json', queryData, 'get', 'chart_column2', function(data){
				if (data.status == 0){
					$("#flashChart2").multiChart(data.list);
				}
			}, 1);
		}
	});
});
</script>

<h2><span id="tt"><?php echo Lang('level_stat'); ?></span></h2>
<div class="container" id="container">
	<div class="frm_cont" id="submit_area">
		<form name="get_submit" id="get_submit" method="get">
		<?php if ($cid > 0 && $sid > 0) { ?>
		<input type="hidden" name="cid" id="cid"  value="<?php echo $cid ?>">
		<input type="hidden" name="sid[]" id="sid"  value="<?php echo $sid ?>">
		<?php }else { ?>
	    <ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('company_platform'); ?>：</span>
	            <select name="cid" id="cid" class="ipt_select">
	            	<option value="0"><?php echo Lang('operation_platform') ?></option>
	            </select>
	        </li>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('server_list'); ?>：</span>
	            <select name="sid[]" multiple="multiple" id="sid" style="width:300px;height:250px;">

	            </select>
	        </li>
	        <li>
	            <span class="frm_info">&nbsp;</span>
	            <input type="hidden" name="doSubmit" value="1">
				<input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
				<input type="reset" id="btnreset" class="btn_rst"  value="<?php echo Lang('reset'); ?>">
	        </li>
	     <?php } ?>
	    </ul>
		</form>
	</div>
	<div class="column cf" id="chart_column" style="display:none">
	<div class="flashChart_title">趋势图</div>	
	<div id="flashChart"></div>
	</div>

	<div class="column cf" id="table_column" style="display:none;padding-bottom: 10px;">
		<div class="title">详细数据</div>
	</div>
	<div id="level-area" class = "data-sheet table-group" style="display:none">
		<div class="table-fixed" style="width:10%;">
		<table class="hasEven phone_overview">
			<thead>
				<tr class="first"><th class="col1"><span>&nbsp;</span></th></tr>
			</thead>
			<tbody>
				<tr><td><?php echo Lang('level_stat'); ?></td></tr>
				<tr><td><?php echo Lang('male'); ?></td></tr>
				<tr><td><?php echo Lang('female'); ?></td></tr>
			</tbody>
		</table>
		</div>
		<div class="table-data" style="width:90%;">
		<div class="mask">
			<table class="hasEven" id="phone_overview">
				<thead>
					<tr id="countheader"></tr>
				</thead>
				<tbody id="countlist">
				</tbody>
			</table>
		</div>
		</div>
	</div>
	<div class="column cf" id="chart_column2" style="display:none">
	<br>
	<div class="title" id="flashChart2_title">趋势图</div>	
	<div id="flashChart2"></div>
	</div>
</div>