<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var chart;
$(function(){
	/**
	 * 运营平台
	 */
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

    $('#get_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this), cid = $('#cid').val(), sid = $('#sid').val();
		if (cid > 0 && sid != null){
			$.ajax({
				url: '<?php echo INDEX; ?>?m=server&c=get&v=level_stat',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						var sum = data.sum;
						chart = new Highcharts.Chart({
							chart: {
								renderTo: 'graph_wrapper',
								borderWidth: 1,
			                    plotBorderColor: '#A47D7C',
			                    plotBorderWidth: 1,
								type: 'column'
							},
							colors: [
								'#ED561B',
								'#058DC7',
								'#55BF3B',
								'#DDDF0D',
								'#7798BF', 
								'#80699B',
								'#3D96AE',
								'#A47D7C',
								'#B5CA92'
							],
							title: {
								text: '<?php echo Lang('level_stat'); ?>'
							},
							xAxis: {
								categories: data.categories
							},
							yAxis: {
								min: 0,
								title: {
									text: '<?php echo Lang('person_num'); ?>'
								}
							},
							tooltip: {
								formatter: function() {
									return '<b>'+ this.series.name +'Lv.'+this.x+'：</b>'+this.y +' ('+ (this.y * 100/sum).toFixed(2) +'%)';
								}
							},
							series: [{
								name: '<?php echo Lang('level_stat'); ?>',
								data: data.list.level
							},{
								name: '<?php echo Lang('male'); ?>',
								data: data.list.man
							},{
								name: '<?php echo Lang('female'); ?>',
								data: data.list.female
							}]
						});

						var strHtml = '';
						if ($('#countheader').find('th').size() < 2){
							for(var key in data.categories){
								strHtml = strHtml + '<th>'+ data.categories[key] +'</th>';
							}
							$('#countheader').append(strHtml);
						}

						$('#countlist').empty();

						strHtml = '<tr><td style="color:#ED561B"><?php echo Lang('level_stat'); ?></td>';
						for(var key in data.list.level){
							strHtml = strHtml + '<td>'+ (data.list.level[key] > 0 ? '<a href="javascript:;" class="level">'+data.list.level[key]+'</a>' : '-')  +'</td>'
						}
						strHtml = strHtml + '</tr>';

						strHtml = strHtml + '<tr><td style="color:#058DC7"><?php echo Lang('male'); ?></td>';
						for(var key in data.list.man){
							strHtml = strHtml + '<td>'+ (data.list.man[key] > 0 ? '<a href="javascript:;" class="level">'+data.list.man[key]+'</a>' : '-') +'</td>'
						}
						strHtml = strHtml + '</tr>';

						strHtml = strHtml + '<tr><td style="color:#55BF3B"><?php echo Lang('female'); ?></td>';
						for(var key in data.list.female){
							strHtml = strHtml + '<td>'+ (data.list.female[key] > 0 ? '<a href="javascript:;" class="level">'+data.list.female[key]+'</a>' : '-')  +'</td>'
						}
						strHtml = strHtml + '</tr>';

						$('#countlist').append(strHtml);
					}
				}
			});
		}else {
			$('#op_tips').attr('class', 'alert_warning');
			$('#op_tips').children('p').html('<?php echo Lang('not_selected_company_or_server'); ?>');
			$('#op_tips').fadeIn();
			setTimeout( function(){
				$('#op_tips').fadeOut();
				$('#btnsubmit').removeAttr('disabled');
			}, ( 2 * 1000 ) );
		}
		return false;
	});

	$('#countlist').on('click', 'a.level', function(){
		var index = $(this).parent('td').index();
		var level = $('#countheader').find('th').eq(index).html();
		if (level != ''){
			var arrlevel = level.split('~');
			var dialog = $.dialog({id: 'pop_level', title: '<?php echo Lang('level'); ?>'+level+'<?php echo Lang('person_distribution'); ?>', width:960});
			$.ajax({
				url: '<?php echo INDEX; ?>?m=server&c=get&v=level_stat',
				data: $('#get_submit').serialize()+ '&startlevel='+arrlevel[0]+'&endlevel='+arrlevel[1],
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						var strHtml = '';
						var width = 0;
						var max = data.max;
						for(var key in data.categories){
							strHtml = strHtml + '<tr><td>'+data.categories[key]+'</td><td style="padding-left:0;">';
							if (data.list.level[key] != undefined){
								width = Math.round(data.list.level[key] / max * 700);
								strHtml = strHtml + '<p style="float: left;background-color:#ED561B;width:'+width+'px">&nbsp;</p>'+data.list.level[key]+'<br>';
							}
							if (data.list.man[key] != undefined){
								width = Math.round(data.list.man[key] / max * 700);
								strHtml = strHtml + '<p style="float: left;background-color:#058DC7;width:'+width+'px">&nbsp;</p>'+data.list.man[key]+'<br>';
							}
							if (data.list.female[key] != undefined){
								width = Math.round(data.list.female[key] / max * 700);
								strHtml = strHtml + '<p style="float: left;background-color:#55BF3B;width:'+width+'px">&nbsp;</p>'+data.list.female[key]+'';
							}
							strHtml = strHtml + '</td><td>&nbsp;</td></tr>';
						}

						var Html = [
							'<div class="content">',
								'<table class="global" width="100%" style="max-width:900px;" cellpadding="0" cellspacing="0">',
									'<thead>',
										'<tr>',
											'<th style="width:30px;"><?php echo Lang('level'); ?></th>',
										    '<th><?php echo Lang('person_num'); ?></th>',
										    '<th>&nbsp;</th>',
										'</tr>',
									'</thead>',
									'<tbody>'+strHtml+'</tbody>',
								'</table>',
							'</div>'
						].join('');
						dialog.content(Html);
					}
				}
			});
		}
	});
});
</script>


<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('level_stat'); ?></span></a></li>
	</ul>
	<br class="clear">
	<div class="content" id="submit_area">
		<!-- Begin form elements -->
		<form name="get_submit" id="get_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=level_stat" method="post">
			<input type="hidden" name="doSubmit" value="1">
			<table class="global" width="100%" style="min-width:500px" cellpadding="0" cellspacing="0">
				<tbody>
				<tr>
					<th style="width:100px;"><?php echo Lang('company_platform'); ?></th>
					<td>
						<select name="cid" id="cid">
							<option value="0"><?php echo Lang('operation_platform') ?></option>
						</select>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th><?php echo Lang('server'); ?></th>
					<td>
						<select name="sid[]" multiple="multiple" id="sid" style="width:250px;height:200px;"></select>
					</td>	
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td> 
						<p>
						<input type="submit" id="btnsubmit" class="button" value="<?php echo Lang('submit'); ?>">
						<input type="reset" id="btnreset" class="button"  value="<?php echo Lang('reset'); ?>">
						</p>
					</td>
					<td>&nbsp;</td>
				</tr>
				</tbody>
			</table>
			<div id="op_tips" style="display: none;width:100%"><p></p></div>
	    </form>
		<!-- End form elements -->
	</div>
	<div class="content graph_wrapper" id="graph_wrapper">
		
	</div>
	<div class="content">
		<table class="global iswrap" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr id="countheader">
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="countlist">

			</tbody>
		</table>
	</div>
	<br class='clear'>
	<br class='clear'>
</div>
<script type="text/javascript" src="static/js/highcharts.js"></script>