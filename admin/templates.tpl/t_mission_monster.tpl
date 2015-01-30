<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<?php if($msg) { ?>
<?php include template('msg'); ?>
<?php } ?>
<form method="post" action="?in=mission" name="form"  onSubmit="setSubmit('Submit3')" target="gopost">
<table class="table">
  <tr>
    <th colspan="9"><span class="bluetext"><?php echo $name?></span> 的怪物</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35" >ID</td>
    <td width="35">删除</td>
    <td width="150">怪物</td>
    <td width="100">站位</td>
<td width="100">气势</td>
    <td>速度</td>
    <td width="100">任务物品</td>
    <td width="100">掉落物品</td>
    <td>参考</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr align="center" >
<td><?php echo $rs['id']?><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除"/></td>
    <td>
<select name="monster_id[]" >
 <option class="select">选择怪物</option>
 
<?php if(is_array($monster_list)) { foreach($monster_list as $mrs) { ?>
 <option value="<?php echo $mrs['id']?>" 
<?php if($mrs['id'] == $rs['monster_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $mrs['level']?>级-<?php echo $mrs['name']?></option>
 
<?php } } ?>
</select>
</td>
    <td>
<select name="deploy_grid_id[]" >
 <option class="select">选择站位</option>
 
<?php if(is_array($deploy_grid_list)) { foreach($deploy_grid_list as $drs) { ?>
 <option value="<?php echo $drs['id']?>" 
<?php if($drs['id'] == $rs['deploy_grid_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $drs['desc']?></option>
 
<?php } } ?>
</select>	
</td>
<td><input name="momentum[]" type="text" value="<?php echo $rs['momentum']?>"  size="10"/></td>
    <td><input name="speed[]" type="text" value="<?php echo $rs['speed']?>"  size="5"/></td>
    <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallMissionMonsterQuestItem&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>')" class="list_menu">任务物品</a></td>
    <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallMissionMonsterItem&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>')" class="list_menu">掉落物品</a></td>

    
<?php if($rs['i']==1) { ?>
<td rowspan="<?php echo $num?>">
<table width="110" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
  <tr align="center">
<td 
<?php if(in_array(1,$n)) { ?>
bgcolor="#FFFF99"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>前上</td>
<td 
<?php if(in_array(4,$n)) { ?>
bgcolor="#FFFF99"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>中上</td>
<td 
<?php if(in_array(7,$n)) { ?>
bgcolor="#FFFF99"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>后上</td>
  </tr>
  <tr align="center">
<td 
<?php if(in_array(2,$n)) { ?>
bgcolor="#FFFF99"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>前中</td>
<td 
<?php if(in_array(5,$n)) { ?>
bgcolor="#FFFF99"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>中中</td>
<td 
<?php if(in_array(8,$n)) { ?>
bgcolor="#FFFF99"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>后中</td>
  </tr>
  <tr align="center">
<td 
<?php if(in_array(3,$n)) { ?>
bgcolor="#FFFF99"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>前下</td>
<td 
<?php if(in_array(6,$n)) { ?>
bgcolor="#FFFF99"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>中下</td>
<td 
<?php if(in_array(9,$n)) { ?>
bgcolor="#FFFF99"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>后下</td>
  </tr>
</table>
</td>
<?php } ?>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="9" align="center" height="100">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center">
<td colspan="2">新增记录→</td>
    <td>
<select name="monster_id_n" >
 <option class="select">选择怪物</option>
 
<?php if(is_array($monster_list)) { foreach($monster_list as $mrs) { ?>
 <option value="<?php echo $mrs['id']?>" ><?php echo $mrs['level']?>级-<?php echo $mrs['name']?></option>
 
<?php } } ?>
</select>	
</td>
    <td>
<select name="deploy_grid_id_n" >
 <option class="select">选择站位</option>
 
<?php if(is_array($deploy_grid_list)) { foreach($deploy_grid_list as $drs) { ?>
 <option value="<?php echo $drs['id']?>"><?php echo $drs['desc']?></option>
 
<?php } } ?>
</select>
</td>
    <td><input name="momentum_n" type="text" value="50"  size="10"/></td>
    <td><input name="speed_n" type="text" value=""  size="5"/></td>
    <td colspan="3">&nbsp;</td>
    </tr>  
  <tr>
    <td colspan="10" align="center">
<input name="mission_monster_team_id" type="hidden" value="<?php echo $id?>"/>
<input type="hidden" name="action" value="SetMissionMonster" />
<input type="hidden" name="winid" value="<?php echo $winid?>" />
<input name="url" type="text" value="
<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>
" style="display:none;"/>
<input type="submit" id="Submit3" name="Submit3" value="执行操作" onClick="javascript: return confirm('你确定执行操作？');"  class="button"/></td>
  </tr>	
</table>
</form>