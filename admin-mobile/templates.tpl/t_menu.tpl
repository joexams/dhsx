<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<?php if($show_menu_admin) { ?>
<table class="menu_table">
<tr>
    	<th><a href=<?php echo QQ_ADMIN?> target="_blank">菜单管理</a></th>
    </tr>
</table>
<?php } ?>
<table class="menu_table">
<tr>
    	<th style="padding:0px">&nbsp;&nbsp;&nbsp;&nbsp;搜索<br>
    	<input type="text" size="18" id="autocomplete" ></input></th>
    </tr>

</table>
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
<table class="menu_table">
<tr>
    	<th><?php echo $rs['describe']?></th>
    </tr>
    
<?php if(is_array($rs['second_menu'])) { foreach($rs['second_menu'] as $srs) { ?>
    <tr class="<?php echo $rs['id']?>">
<td nowrap="nowrap" width="120"><a href="<?php echo $srs['url']?>"><?php echo $srs['describe']?></a></td>
    </tr>
    
<?php } } ?>
</table>
<?php } } ?>
