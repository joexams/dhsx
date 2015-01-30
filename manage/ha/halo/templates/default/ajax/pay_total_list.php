<div class="table-fixed">
	<table class="hasEven phone_overview">
		<thead>
			<tr class="first"><th class="col1" colspan="2"><span>&nbsp;</span></th></tr>
		</thead>
		<tbody>
			<?php foreach ($data['serverlist'] as $key => $value): ?>
				<tr>
					<td<?php if ($data['typecount'] > 1) { ?> rowspan="<?php echo $data['typecount'] ?>"<?php } ?>><span><?php echo $value['name'] ?></span></td>
					<td><span>金额</span></td>
				</tr>

				<?php if ($data['typecount'] > 1) { ?>
					<?php foreach ($type as $tk => $tv): ?>
						<?php if ($tv == 1) { ?>
						<tr><td><span><?php echo Lang('times'); ?></span></td></tr>
						<?php }else if ($tv == 2) { ?>
						<tr><td><span><?php echo Lang('person_num'); ?></span></td></tr>
						<?php }else if ($tv == 3) { ?>
						<tr><td><span>ARPU</span></td></tr>
						<?php } ?>
					<?php endforeach ?>
				<?php } ?>

			<?php endforeach ?>
		</tbody>
	</table>
</div>
<div class="table-data">
<div class="mask">
	<table class="hasEven" id="phone_overview">
		<thead>
			<tr>
			<?php if (!empty($data['datelist'])) { ?>
			<?php foreach ($data['datelist'] as $key => $value): ?>
				<?php if (date('w', strtotime($value)) == 0 || date('w', strtotime($value)) == 6) { ?>
				<th><span class="redtitle"><?php echo str_replace('-', '.', $value) ?></span></th>
				<?php }else { ?>
				<th><?php echo str_replace('-', '.', $value) ?></th>
				<?php } ?>
				
			<?php endforeach ?>
			<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($data['serverlist'] as $key => $value): ?>

			<?php $pay = $num = $player = $arpu = ''; ?>
			<?php foreach ($data['datelist'] as $dk => $dv): ?>

			<?php $pay .= '<td><span class="orangetitle">'.round($data['alllist'][$value['id']][$tv]['pay_amount'], 2).'</span></td>' ?>

			<?php if ($data['typecount'] > 1) { ?>
				<?php foreach ($type as $tk => $tv): ?>
					<?php if ($tv == 1) { ?>
					<?php $num .= '<td><span>'.intval($data['alllist'][$value['id']][$tv]['pay_num']).'</span></td>' ?>
					
					<?php }else if ($tv == 2) { ?>
					<?php $player .= '<td><span>'.intval($data['alllist'][$value['id']][$tv]['pay_player_count']).'</span></td>' ?>

					<?php }else if ($tv == 3) { ?>
					<?php $arpu .= '<td><span>'.round($data['alllist'][$value['id']][$tv]['arpu'], 2).'</span></td>' ?>

					<?php } ?>
				<?php endforeach ?>
			<?php } ?>

			<?php endforeach ?>

			<?php echo '<tr>'.$pay.'</tr>' ?>
			<?php echo !empty($num) ? '<tr>'.$num.'</tr>' : '' ?>
			<?php echo !empty($player) ? '<tr>'.$player.'</tr>' : '' ?>
			<?php echo !empty($arpu) ? '<tr>'.$arpu.'</tr>' : '' ?>

			<?php endforeach ?>
		</tbody>
	</table>
</div>
</div>