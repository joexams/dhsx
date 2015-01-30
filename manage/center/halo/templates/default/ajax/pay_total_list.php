<div class="table-fixed" style="width:20%;">
	<table class="hasEven phone_overview">
		<thead>
			<tr class="first"><th class="col1" colspan="2"><span>&nbsp;</span></th></tr>
		</thead>
		<tbody>
			<?php foreach ($data['serverlist'] as $key => $value): ?>
				<tr>
					<td <?php if ($data['typecount'] > 1) { ?> rowspan="<?php echo $data['typecount'] ?>"<?php } ?>><span><?php echo $value['name'] ?></span></td>
					<td><span><?php echo Lang('money')?></span></td>
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
<div class="table-data" style="width:80%;">
<div class="mask">
	<table class="hasEven" id="phone_overview">
		<thead>
			<tr>
			<th><?php echo Lang('total')?></th>
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

			<?php $pay = $num = $player = $arpu = ''; $total_pay = $total_num = $total_player = $total_arpu = 0?>
			<?php foreach ($data['datelist'] as $dk => $dv): ?>
			<?php $total_pay = round($data['alllist'][$value['id']][$dv]['pay_amount'], 2)+$total_pay;?>
			<?php $pay .= '<td><span class="orangetitle">'.round($data['alllist'][$value['id']][$dv]['pay_amount'], 2).'</span></td>' ?>

			<?php if ($data['typecount'] > 1) { ?>
				<?php foreach ($type as $tk => $tv): ?>
					<?php if ($tv == 1) { ?>
					<?php $total_num = intval($data['alllist'][$value['id']][$dv]['pay_num'])+$total_num;?>
					<?php $num .= '<td><span>'.intval($data['alllist'][$value['id']][$dv]['pay_num']).'</span></td>' ?>
					
					<?php }else if ($tv == 2) { ?>
					<?php $total_player = intval($data['alllist'][$value['id']][$dv]['pay_player_count'])+$total_player;?>
					<?php $player .= '<td><span>'.intval($data['alllist'][$value['id']][$dv]['pay_player_count']).'</span></td>' ?>

					<?php }else if ($tv == 3) { ?>
					<?php $arpu .= '<td><span>'.round($data['alllist'][$value['id']][$dv]['arpu'], 2).'</span></td>' ?>

					<?php } ?>
				<?php endforeach ?>
			<?php } ?>

			<?php endforeach ?>
			
			<?php echo '<tr><td><span class="orangetitle">'.$total_pay.'</span></td>'.$pay.'</tr>' ?>
			<?php echo !empty($num) ? '<tr><td><span>'.$total_num.'</span></td>'.$num.'</tr>' : '' ?>
			<?php echo !empty($player) ? '<tr><td><span>'.$total_player.'</span></td>'.$player.'</tr>' : '' ?>
			<?php echo !empty($arpu) ? '<tr><td><span>'.round($total_pay/$total_player,2).'</span></td>'.$arpu.'</tr>' : '' ?>

			<?php endforeach ?>
		</tbody>
	</table>
</div>
</div>