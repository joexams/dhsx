<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
	$('.first_level_tab').on('click', 'a.settingtype', function(){
		$('.active').removeClass('active');
		$(this).addClass('active');
		type = $(this).attr('data-type');
		$('tbody').hide();
		switch(type) {
			case '0': $('#systemlist').show(); break;
			case '1': $('#databaselist').show(); break;
			case '3': $('#flushmemcache').show(); break;
		}
	});
	$('#flush').on('click', function() {
		if (confirm('确定清除所有Memcahe缓存吗？')) {
			$.ajax({
				url: '<?php echo INDEX; ?>?m=manage&c=cacheclear&v=memcache_flush',
				dataType: 'json',
				success: function(data) {
					var alertclassname = '', time = 2;
					switch (data.status){
						case 0:  alertclassname = 'alert_success';  break;
						case 1: alertclassname = 'alert_error'; break;
					}
					$('#op_tips').attr('class', alertclassname);
					$('#op_tips').children('p').html(data.msg);
					$('#op_tips').fadeIn();
					setTimeout( function(){
						$('#op_tips').fadeOut();
					}, ( time * 1000 ) );
				} 
			});
		}
	});
});
</script>
<div id="bgwrap">
	<ul class="dash1">
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('setting'); ?></span></a></li>
	</ul>
	<br class="clear">

	<ul class="first_level_tab">
		<li><a href="javascript:;" data-type="0" class="settingtype active"><?php echo Lang('system_config'); ?></a></li>
		<li><a href="javascript:;" data-type="1" class="settingtype"><?php echo Lang('database_config'); ?></a></li>
		<li><a href="javascript:;" data-type="2" class="settingtype"><?php echo Lang('language_pck'); ?></a></li>
		<li><a href="javascript:;" data-type="3" class="settingtype"><?php echo Lang('flush_memcache'); ?></a></li>
	</ul>
	<br class="clear">
	<div class="onecolumn">
		<div class="content">
			<table class="global" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<th style="width:30px;">序号</th>
				    <th style="width:200px;">键(KEY)</th>
				    <th style="width:200px;">值(VALUE)</th>
				    <th>&nbsp;</th>
				</thead>
				<tbody id="systemlist">
				<?php 
					$i=0; 
					foreach ($config as $key => $value) { 
						$i++;
				?>
					<tr>
						<th><?php echo $i; ?></th>
						<td><input type="text" name="key[]" value="<?php echo $key; ?>" style="width:90%;"></td>
						<td><input type="text" name="value[]" value="<?php echo $value; ?>" style="width:90%;"></td>
						<td>&nbsp;</td>
					</tr>
				<?php } ?>
				</tbody>

				<tbody id="databaselist" style="display:none">
					<?php 
						$i=0; 
						foreach ($database as $key => $value) { 
					?>
					<tr>
						<td>&nbsp;</td>
						<th colspan="2" style="text-align:left"><?php echo $key; ?></th>
						<td>&nbsp;</td>
					</tr>
						<?php 
						$i++;
						foreach ($value as $dkey => $val) { ?>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;<strong><?php echo Lang($dkey); ?></strong></td>
								<td><?php echo $val; ?></td>
								<td>&nbsp;</td>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
				<tbody id="flushmemcache" style="display:none">
					<tr>
						<td>&nbsp;</td>
						<td>清除所有Memcache缓存</td>
						<td><a href="javascript:;" id="flush">清除</a></td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="op_tips" style="display: none;"><p></p></div>
	</div>
</div>

