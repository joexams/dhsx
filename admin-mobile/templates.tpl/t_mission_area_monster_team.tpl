<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<form method="post" action="?in=monster" name="form"  onSubmit="setSubmit('Submit')">
<table class="table">
  <tr>
    <th colspan="7">副本怪物团</th>
  </tr>
  <tr class="title_3">
    <td colspan="7">	
<a href="?in=monster" class="
<?php if(!$mission_area) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
">所有</a>
  
<?php if(is_array($mission_area_list)) { foreach($mission_area_list as $malrs) { ?>
  <a href="?in=monster&mission_area=<?php echo $malrs['id']?>" class="
<?php if($mission_area == $malrs['id']) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
"><?php echo $malrs['name']?></a>
  
<?php } } ?>
</td>
  </tr>  
  <tr align="center" class="title_2">
<td width="35">删除</td>
    <td width="100">ID</td>
    <td width="150">代表怪</td>
<td width="150">奖励副本权值</td>
    <td width="150">奖励经验</td>
    <td width="150">副本区域</td>
    <td>&nbsp;</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
  	<input name="id_old[]" type="hidden" value="<?php echo $rs['id']?>"/>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除<?php echo $rs['name']?>"/></td>
    <td><?php echo $rs['id']?></td>

    <td>
<select name="monster_id[]" >
 <option class="select">选择怪物</option>
 
<?php if(is_array($monster_list)) { foreach($monster_list as $mrs) { ?>
 <option value="<?php echo $mrs['id']?>" 
<?php if($mrs['id'] == $rs['monster_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $mrs['name']?></option>
 
<?php } } ?>
 
</select>	
</td>
<td><input name="award_mission_key[]" type="text" value="<?php echo $rs['award_mission_key']?>"  size="20"/></td>
<td><input name="award_exp[]" type="text" value="<?php echo $rs['award_exp']?>"  size="20"/></td>
<td>
<select name="mission_area_id[]" >
 <option class="select">选择副本区域</option>
 
<?php if(is_array($mission_area_list)) { foreach($mission_area_list as $mars) { ?>
 <option value="<?php echo $mars['id']?>" 
<?php if($mars['id'] == $rs['mission_area_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $mars['name']?></option>
 
<?php } } ?>
 
</select>	
</td>
    <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallMissionAreaMonster&id=<?php echo $rs['id']?>')" class="list_menu">怪物</a></td>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="7" align="center">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="2">新增记录→</td>    
    <td>
<select name="monster_id_n" >
 <option class="select">选择怪物</option>
 
<?php if(is_array($monster_list)) { foreach($monster_list as $mrs) { ?>
 <option value="<?php echo $mrs['id']?>"><?php echo $mrs['name']?></option>
 
<?php } } ?>
 
</select>	
</td>	
<td><input name="award_mission_key_n" type="text" value=""  size="20"/></td>
<td><input name="award_exp_n" type="text" value=""  size="20"/></td>
<td>
<select name="mission_area_id_n" >
 <option class="select">选择副本区域</option>
 
<?php if(is_array($mission_area_list)) { foreach($mission_area_list as $mars) { ?>
 <option value="<?php echo $mars['id']?>" 
<?php if($mars['id'] == $mission_area) { ?>
selected="selected"
<?php } ?>
><?php echo $mars['name']?></option>
 
<?php } } ?>
 
</select>	
</td>
    <td colspan="2">&nbsp;</td>
  </tr>  
  <tr>
    <td colspan="7" align="center">
<input type="hidden" name="action" value="SetMissionAreaMonsterTeam" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/></td>
  </tr>  
</table>
</form>