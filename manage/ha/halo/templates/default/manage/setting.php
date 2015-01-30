<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	$('.first_level_tab').on('click', 'a.settingtype', function(){
		$('.active').removeClass('active');
		$(this).addClass('active');
		type = $(this).attr('data-type');
		$('tbody').hide();
		$('#systemlist'+type).show();
	});

	$('.first_level_tab').on('click', 'a.settingtype', function(){
		$('.current', $('.first_level_tab')).removeClass('current');
		$(this).parent().addClass('current');
	});

	$('#flush').on('click', function() {
		if (confirm('确定清除所有Memcahe缓存吗？')) {
			var url = '<?php echo INDEX; ?>?m=manage&c=cacheclear&v=memcache_flush';
			Ha.common.ajax(url);
		}
	});

});
</script>

<h2><span id="tt"><?php echo Lang('setting'); ?></span></h2>
<div class="container" id="container">
	<div class="speed_result">
	    <div class="mod_tab_title first_level_tab">
	    	<ul>
	    		<li class="current"><a href="javascript:;" data-type="0" class="settingtype active"><?php echo Lang('system_config'); ?></a></li>
				<li><a href="javascript:;" data-type="1" class="settingtype"><?php echo Lang('database_config'); ?></a></li>
				<li><a href="javascript:;" data-type="2" class="settingtype"><?php echo Lang('language_pck'); ?></a></li>
				<li><a href="javascript:;" data-type="3" class="settingtype"><?php echo Lang('flush_memcache'); ?></a></li>
	    	</ul>
	    </div>
	</div>
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<form id="post_submit" action="" method="post">
		<table>
		<thead>
			<th>&nbsp;</th>
		    <th>键(KEY)</th>
		    <th>值(VALUE)</th>
		</thead>
		<tbody id="systemlist0">
		<?php 
			$i=0; 
			foreach ($config as $key => $value) { 
				$i++;
		?>
			<tr>
				<td class="num"><?php echo $i; ?></td>
				<td><input type="text" name="key[]" value="<?php echo $key; ?>" class="ipt_txt"></td>
				<td><input type="text" name="value[]" value="<?php echo $value; ?>" class="ipt_txt"></td>
			</tr>
		<?php } ?>
		</tbody>

		<tbody id="systemlist1" style="display:none">
			<?php 
				$i=0; 
				foreach ($database as $key => $value) { 
			?>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2" style="text-align:left"><?php echo $key; ?></td>
			</tr>
				<?php 
				$i++;
				foreach ($value as $dkey => $val) { ?>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;<strong><?php echo Lang($dkey); ?></strong></td>
						<td><?php echo $val; ?></td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
		<tbody id="systemlist2" style="display:none">
			<tr>
				<td>&nbsp;</td>
				<td>清除所有Memcache缓存</td>
				<td><a href="javascript:;" id="flush">清除</a></td>
			</tr>
		</tbody>

		</table>
		</form>
	</div>
	</div>
</div>

