<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {

    $('.first_level_tab').on('click', 'a.trendtype', function(){
        $('.first_level_tab .active').removeClass('active');
        $(this).addClass('active');
        var obj = $(this), type = $(this).attr('data-type');
        $('div.content', $('#tab_stone_content')).hide();
        $('div.content', $('#tab_stone_content')).eq(type).show();
    });

});
</script>
<div id="bgwrap">
<br class="clear">
<ul class="first_level_tab">
    <li><a href="javascript:;" data-type="0" class="trendtype active">非法领取前仙石</a></li>
    <li><a href="javascript:;" data-type="1" class="trendtype">非法领取仙石</a></li>
    <li><a href="javascript:;" data-type="2" class="trendtype">系统扣除仙石</a></li>
    <li><a href="javascript:;" data-type="3" class="trendtype">找回礼包仙石</a></li>
    <li><a href="javascript:;" data-type="4" class="trendtype">现在仙石存量</a></li>
    <li><a href="javascript:;" data-type="5" class="trendtype">当前仙石</a></li>
</ul>
<div class="clear"></div>
    <div class="onecolumn" id="tab_stone_content" style="width: 100%">
        <div class="content">
        	<table class="global" width="100%" cellpadding="0" cellspacing="0">
    			<tr>
    			    <th style="width:250px"><?php echo Lang('仙石') ?></th>
    			    <th style="width:80px"><?php echo Lang('数量') ?></th>
    			    <th>&nbsp;</th>
    			</tr>
	        	<?php foreach ($stoneList as $key => $value) { ?>
	        	<tr>
	        		<td><?php echo $itemList[$key]; ?></td>
	        		<td><?php echo $value; ?></td>
	        		<td>&nbsp;</td>
	        	</tr>
	        	<?php } ?>
        	</table>
        </div>
        <div class="content" style="display:none">
        	<table class="global" width="100%" cellpadding="0" cellspacing="0">
    			<tr>
    			    <th style="width:250px"><?php echo Lang('仙石') ?></th>
    			    <th style="width:80px"><?php echo Lang('数量') ?></th>
    			    <th>&nbsp;</th>
    			</tr>
	        	<?php foreach ($afterList as $key => $value) { ?>
	        	<tr>
	        		<td><?php echo $itemList[$value['item_id']]; ?></td>
	        		<td><?php echo $value['value']; ?></td>
	        		<td>&nbsp;</td>
	        	</tr>
	        	<?php } ?>
        	</table>
        </div>
        <div class="content" style="display:none">
        	<table class="global" width="100%" cellpadding="0" cellspacing="0">
    			<tr>
    			    <th style="width:250px"><?php echo Lang('仙石') ?></th>
                    <th style="width:80px"><?php echo Lang('数量') ?></th>
    			    <th style="width:120px"><?php echo Lang('时间') ?></th>
    			    <th>&nbsp;</th>
    			</tr>
	        	<?php foreach ($allList as $key => $value) { ?>
	        	<tr>
	        		<td><?php echo $itemList[$key]; ?></td>
	        		<td><?php echo $value; ?></td>
                    <td>2013-03-31 04:02</td>
	        		<td>&nbsp;</td>
	        	</tr>
	        	<?php } ?>
        	</table>
        </div>
        <div class="content" style="display:none">
        	<table class="global" width="100%" cellpadding="0" cellspacing="0">
    			<tr>
    			    <th style="width:250px"><?php echo Lang('仙石') ?></th>
    			    <th style="width:80px"><?php echo Lang('数量') ?></th>
    			    <th>&nbsp;</th>
    			</tr>
	        	<?php foreach ($giftList as $key => $value) { ?>
	        	<tr>
	        		<td><?php echo $itemList[$value['item_id']]; ?></td>
	        		<td><?php echo $value['value']; ?></td>
	        		<td>&nbsp;</td>
	        	</tr>
	        	<?php } ?>
        	</table>
        </div>
        <div class="content" style="display:none">
        	<table class="global" width="100%" cellpadding="0" cellspacing="0">
    			<tr>
    			    <th style="width:250px"><?php echo Lang('仙石') ?></th>
    			    <th style="width:80px"><?php echo Lang('数量') ?></th>
    			    <th>&nbsp;</th>
    			</tr>
	        	<?php foreach ($nowList as $key => $value) { ?>
	        	<tr>
	        		<td><?php echo $itemList[$key]; ?></td>
	        		<td><?php echo $value; ?></td>
	        		<td>&nbsp;</td>
	        	</tr>
	        	<?php } ?>
        	</table>
        </div>
        <div class="content" style="display:none">
        	<table class="global" width="100%" cellpadding="0" cellspacing="0">
    			<tr>
    			    <th style="width:250px"><?php echo Lang('仙石') ?></th>
    			    <th style="width:80px"><?php echo Lang('数量') ?></th>
    			    <th>&nbsp;</th>
    			</tr>
	        	<?php foreach ($nowallList as $key => $value) { ?>
	        	<tr>
	        		<td><?php echo $itemList[$key]; ?></td>
	        		<td><?php echo $value; ?></td>
	        		<td>&nbsp;</td>
	        	</tr>
	        	<?php } ?>
        	</table>
        </div>
    </div>
</div>