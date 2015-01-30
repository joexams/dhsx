<div class="table-fixed" style="width:10%">
	<table class="hasEven phone_overview">
		<thead>
			<tr class="first"><th class="col1"><span>&nbsp;</span></th>	</tr>
		</thead>
		<tbody>
			<?php if (!empty($day_list)) { ?>
			<?php foreach ($day_list as $key => $value): ?>
				<tr>
				<?php if (date('w', strtotime($value)) == 0 || date('w', strtotime($value)) == 6) { ?>
				<td><span class="redtitle"><?php echo str_replace('-', '.', $value) ?></span><br><span class="graytitle">&nbsp;</span></td>
				<?php }else { ?>
				<td><span><?php echo str_replace('-', '.', $value) ?></strong><br><span class="graytitle">&nbsp;</span></td>
				<?php } ?>
				</tr>
			<?php endforeach ?>
			<?php } ?>
		</tbody>
	</table>
</div>
<div class="table-data" style="width:90%">
<div class="mask">
	<table class="hasEven" id="phone_overview">
		<thead>
			<tr>
			<?php if (!empty($hour_list)) { ?>
			<?php foreach ($hour_list as $key => $value): ?>
				<th><?php echo $value ?></th>
			<?php endforeach ?>
			<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($day_list as $day): ?>
				<tr>
				<?php foreach ($hour_list as $hour): ?>
				<td><?php echo $online[$day][$hour] ?><br><span class="graytitle"><?php echo $pay[$day][$hour] ?>&nbsp;</span></td>
				<?php endforeach ?>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
</div>