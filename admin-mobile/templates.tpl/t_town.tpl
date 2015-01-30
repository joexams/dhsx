<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<form method="post" action="?in=town" name="form"  onSubmit="setSubmit('Submit')">
<table class="table">
  <tr>
        <th colspan="13">
      
<?php if($type == 1) { ?>
世界BOSS城镇
      
<?php } elseif($type == 2) { ?>
集会所
      
<?php } elseif($type == 3) { ?>
帮派战城镇
      
<?php } elseif($type == 4) { ?>
帮派BOSS城镇
      
<?php } elseif($type == 5) { ?>
魔王城镇
      
<?php } elseif($type == 6) { ?>
九界城镇
      
<?php } elseif($type == 7) { ?>
中秋城镇
      
<?php } elseif($type == 8) { ?>
仙盟争霸
      
<?php } elseif($type == 9) { ?>
圣诞城镇
      
<?php } elseif($type == 10) { ?>
跨服城镇
      
<?php } elseif($type == 11) { ?>
结婚城镇
      
<?php } elseif($type == 12) { ?>
狂战天柱
      
<?php } elseif($type == 13) { ?>
跨服世界Boss
      
<?php } elseif($type == 14) { ?>
七夕城镇
      
<?php } elseif($type == 15) { ?>
感恩节城镇
      
<?php } else { ?>
普通城镇
<?php } ?>
 
      </th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35">ID</td>
<td width="35">删除</td>
    <td width="150">名称</td>
    <td width="100">标识</td>
    <td width="100" title="城镇解锁权值">城镇权值</td>
<td width="100" title="培养所需铜钱">所需铜钱</td>
<td width="100" title="城镇起始位置X轴坐标">X轴坐标</td>
<td width="100" title="城镇起始位置Y轴坐标">Y轴坐标</td>
<td width="100">门派</td>
    <td width="220">描述</td>
    <td width="50">NPC</td>
    <td width="50">副本</td>
    <td>&nbsp;</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除<?php echo $rs['name']?>"/><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
    <td><input name="name[]" type="text" value="<?php echo $rs['name']?>"  size="20"/></td>
    <td><input name="sign[]" type="text" value="<?php echo $rs['sign']?>"  size="10"/></td>
    <td><input name="lock[]" type="text" value="<?php echo $rs['lock']?>"  size="5"/></td>
    <td><input name="training_coins[]" type="text" value="<?php echo $rs['training_coins']?>"  size="5"/></td>
    <td><input name="start_x[]" type="text" value="<?php echo $rs['start_x']?>"  size="5"/></td>
    <td><input name="start_y[]" type="text" value="<?php echo $rs['start_y']?>"  size="5"/></td>
<td>
<select name="camp_id[]">
 <option class="select">选择门派</option>
 
<?php if(is_array($camp_list)) { foreach($camp_list as $crs) { ?>
 <option value="<?php echo $crs['id']?>" 
<?php if($crs['id'] == $rs['camp_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $crs['name']?></option>
 
<?php } } ?>
</select>	
</td>
    <td><textarea name="description[]" cols="35" rows="2" ondblclick="textareasize(this)"><?php echo $rs['description']?></textarea></td>
    <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallTownNPC&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>')" class="list_menu">NPC</a></td>
    <td><a href="?in=mission&action=MissionSection&town_id=<?php echo $rs['id']?>" class="list_menu">副本</a></td>
    <td>&nbsp;</td>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="13" align="center">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="2">新增记录→<input type="hidden" name="type" value="<?php echo $type?>" /></td>
    <td><input name="name_n" type="text" value=""  size="20"/></td>
<td><input name="sign_n" type="text" value=""  size="10"/></td>
    <td><input name="lock_n" type="text" value="<?php echo $lock_n?>"  size="5"/></td>
<td><input name="training_coins_n" type="text" value=""  size="5"/></td>
    <td><input name="start_x_n" type="text" value=""  size="5"/></td>
    <td><input name="start_y_n" type="text" value=""  size="5"/></td>
<td>
<select name="camp_id_n">
 <option class="select">选择门派</option>
 
<?php if(is_array($camp_list)) { foreach($camp_list as $crs) { ?>
 <option value="<?php echo $crs['id']?>"><?php echo $crs['name']?></option>
 
<?php } } ?>
</select>	
</td>	
    <td><textarea name="description_n" cols="35" rows="2" ondblclick="textareasize(this)"></textarea></td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>  
  <tr>
    <td colspan="13" class="greentext">
<input type="hidden" name="action" value="SetTown" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/> 若删除城镇将一并删除城镇下属的副本及副本下的相关数据</strong></td>
  </tr>  
</table>
</form>