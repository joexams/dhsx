<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">

$(document).ready(function(){
 	/**
 	 * 运营平台
 	 */
	if (typeof global_companylist != 'undefined') {
		$('#companyultpl').tmpl(global_companylist).appendTo('#companyul');
	}
	
 	$('#companyul').on('change', function(){
 		var cid = $('#companyul').val();
 		if (cid > 0){
 			var url = "<?php echo INDEX; ?>?m=report&c=data&v=total";
 			Ha.common.ajax(url, 'html', "cid="+cid+"&rnd="+Math.random(), 'get', 'container', function(data){
 				$('#module').html(data);
 			}, 1);
	 	}
 	});
 });
</script>

<script type="text/template" id="companyultpl">
<option value="${cid}" rel="#app=4&url=${encodeurl('<?php echo $data['url']['m']; ?>', '<?php echo $data['url']['v']; ?>', '<?php echo $data['url']['c']; ?>', '&cid=')}${cid}">${fn} - ${name}</option>
</script>

<h2><span id="tt"><?php echo Lang('data_total'); ?></span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div class="title">
			<div class="more" id="tblMore">
	            <div id="div_pop">
	                <select name="cid" id="companyul" class="ipt_select">

	                </select>
	            </div>
	        </div>
			详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
		    <th><?php echo Lang('server'); ?></th>
			<th><?php echo Lang('open_server_days'); ?></th>
			<th><?php echo Lang('register_count'); ?></th>
			<th><?php echo Lang('create_count'); ?></th>
			<th><?php echo Lang('max_online_count'); ?></th>
			<th><?php echo Lang('avg_online'); ?></th>
			<th><?php echo Lang('pay_person_num'); ?></th>
			<th><?php echo Lang('total_pay_times'); ?></th>
			<th><?php echo Lang('pay_money'); ?></th>
			<th>ARPU</th>
			<th><?php echo Lang('max_level') ?></th>
			<th><?php echo Lang('consumption'); ?></th>
		</tr>
		</thead>
		<tbody>
		  <?php if (!empty($list)) { ?>
		  	<?php foreach ($list as $key => $value) { ?>
		  		<tr>
		  			<td><a href="<?php echo $value['url'].urlencode($value['name']); ?>" target="_blank"><?php echo !empty($value['name']) ? $value['name'] : $value['o_name']; ?></a></td>
		  		    <td><?php echo $value['opendate']; ?>天</td>
		  		    <td><?php echo $value['register_count'] ? $value['register_count'] : '--' ?></td>
		  	    	<td>
		  	    		<?php if ($value['create_count'] == 0 && $value['avg_create_count'] == 0) { ?>
		  	    		--
		  	    		<?php }else { ?>
		  	    		<?php echo $value['create_count']; ?>
		  	    		<?php if ($value['avg_create_count'] > 0) { ?>
		  	    		<span class="graptitle">(<?php echo $value['avg_create_count']; ?>%)</span>
		  	    		<?php } ?>
		  	    		<?php } ?>
		  		    <td><?php echo $value['max_online_count'] ? $value['max_online_count'] :'--'; ?></td>
		  		    <td><?php echo $value['online_count'] ? $value['online_count'] : '--';?></td>
		  		    <td><?php echo $value['pay_player_count'] ? $value['pay_player_count'] : '--'; ?></td>
		  		    <td><?php echo $value['pay_num'] ? $value['pay_num'] : '--' ?></td>
		  		    <td><span class="orangetitle"><?php echo $value['pay_amount'] ? $value['pay_amount'] : '--'; ?></span></td>
		  		    <td><span class="greentitle"><?php echo $value['arpu'] ? $value['arpu'] : '--'; ?></span></td>
		  		    <td><?php echo $value['max_player_level'] > 0 ? $value['max_player_level'].'级' : '--'; ?></td>
		  		    <td><?php echo $value['consume'] ? $value['consume'] : '--';?></td>
		  		</tr>
		  	<?php } ?>
		  <?php }else { ?>
		  		<tr>
		  			<td colspan="12" style="text-align: left">没有找到数据。</td>
		  		</tr>
		  <?php } ?> 
		</tbody>
		</table>
		</div>
	</div>
</div>