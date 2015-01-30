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
				url: '<?php echo INDEX; ?>?m=server&c=get&v=vip_stat',
				data: objform.serialize(),
				dataType: 'json',
				success: function(data){
					if (data.status == 0 && data.list.length > 0){
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
								text: '<?php echo Lang('vip_stat'); ?>'
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
								name: '<?php echo Lang('vip_stat'); ?>',
								data: data.list
							}]
						});

						$('#countlist').empty();
						var strHtml = '';
						var percent = 0;
						for (var key in data.categories){
							percent = data.list[key] * 100/sum;
							strHtml = strHtml + '<tr><td>Lv.'+data.categories[key]+'</td><td>'+data.list[key]+'</td><td>'+percent.toFixed(2)+'%</td><td>&nbsp;</td></tr>';
						}
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
	});
});
</script>	

<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('vip_stat'); ?></span></a></li>
	</ul>
	<br class="clear">
	
	<div class="content" id="submit_area">
		<!-- Begin form elements -->
		<form name="get_submit" id="get_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=vip_stat" method="post">
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
				<tr>
					<th style="width:5%">VIP</th>
					<th style="width:5%"><?php echo Lang('person_num'); ?></th>
					<th style="width:5%"><?php echo Lang('percent'); ?></th>
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