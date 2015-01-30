<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<?php if($msg) { ?>
<?php include template('msg'); ?>
<?php } ?>
<form method="post" action="?in=town" name="form"  onSubmit="setSubmit('Submit')" target="gopost">
<table class="table">
  <tr>
    <th colspan="9"><span class="bluetext"><?php echo $name?></span> 的NPC</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35" >ID</td>
    <td width="35">删除</td>
    <td>NPC</td>
  <td>权值</td>
    <td>X轴坐标</td>
    <td>Y轴坐标</td>
    
    <td>灵件</td>
    <!--td>资源ID</td-->
    <td>物品</td>
  </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除"/></td>
    <td><strong><?php echo $rs['npc_name']?></strong></td>
    <td><input name="lock[]" type="text" value="<?php echo $rs['lock']?>"  size="10"/></td>
    <td><input name="position_x[]" type="text" value="<?php echo $rs['position_x']?>"  size="10"/></td>
<td><input name="position_y[]" type="text" value="<?php echo $rs['position_y']?>" size="10"/></td>
    <!--td><input name="resource_id[]" type="text" value="<?php echo $rs['resource_id']?>" size="10"/></td-->
    <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallTownNpcSoul&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>')" class="list_menu">灵件</a></td>
    <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallTownNpcItem&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>')" class="list_menu">物品</a></td>
  </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="10" align="center" height="100">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center">
<td colspan="2">新增记录→</td>
    <td>
<select name="npc_id_n" >
 <option style="color:#999; background:#ddd;">选择NPC</option>
 
<?php if(is_array($npc_list)) { foreach($npc_list as $nrs) { ?>
 <option value="<?php echo $nrs['id']?>"><?php echo $nrs['name']?></option>
 
<?php } } ?>
 
</select>	</td>  
  <td><input name="lock_n" type="text" value=""  size="10"/></td>
    <td><input name="position_x_n" type="text" value=""  size="10"/></td>
<td><input name="position_y_n" type="text" value="" size="10"/></td>
    <!--td><input name="resource_id_n" type="text" value="" size="10"/></td-->
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>  
  <tr>
    <td colspan="10" align="center">
<input name="town_id" type="hidden" value="<?php echo $id?>"/>
<input type="hidden" name="action" value="SetTownNPC" />
<input type="hidden" name="winid" value="<?php echo $winid?>" />
<input name="url" type="text" value="
<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>
" style="display:none;"/>
<input type="submit" name="Submit" id="Submit" value="执行操作" onClick="javascript: return confirm('你确定执行操作？');"  class="button"/></td>
  </tr>	
</table>
</form>