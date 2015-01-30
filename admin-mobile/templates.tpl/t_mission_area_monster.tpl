<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<form method="post" action="?in=monster" name="form"  onSubmit="setSubmit('Submit')" target="gopost">
<table class="table">
  <tr>
    <th colspan="5">怪物信息</th>
  </tr>
  <tr align="center" class="title_2">
  	<td width="35">删除</td>  
  	<td width="35" >ID</td>
    <td>怪物</td>
    <td>位置</td>
    <td>参考</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr align="center" >
<input name="id_old[]" type="hidden" value="<?php echo $rs['id']?>"/>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除"/></td>
    <td><?php echo $rs['id']?></td>
<td>
<select name="monster_id[]" >
 <option class="select">选择怪物</option>
 
<?php if(is_array($monster_list)) { foreach($monster_list as $mlrs) { ?>
 <option value="<?php echo $mlrs['id']?>" 
<?php if($mlrs['id'] == $rs['monster_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $mlrs['name']?></option>
 
<?php } } ?>
 
</select>	</td>
    <td><input name="pos[]" type="text" value="<?php echo $rs['pos']?>"  size="10"/></td>
    
<?php if($rs['i']==1) { ?>
    <td rowspan="<?php echo $num?>">
<table width="100" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
  <tr align="center">
<td width="25%" 
<?php if(in_array(8,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>8</td>
<td width="25%" 
<?php if(in_array(9,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>9</td>
<td width="25%" 
<?php if(in_array(10,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>10</td>
<td width="25%" 
<?php if(in_array(11,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>11</td>
  </tr>
  <tr align="center">
<td 
<?php if(in_array(5,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>5</td>
<td 
<?php if(in_array(6,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>6</td>
<td 
<?php if(in_array(7,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>7</td>
  </tr>
  <tr align="center">
<td width="25%" 
<?php if(in_array(1,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>1</td>
<td width="25%" 
<?php if(in_array(2,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>2</td>
<td width="25%" 
<?php if(in_array(3,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>3</td>
<td width="25%" 
<?php if(in_array(4,$n)) { ?>
bgcolor="#FFFF00"
<?php } else { ?>
bgcolor="#FFFFFF"
<?php } ?>
>4</td>
  </tr>
</table>	

</td>
<?php } ?>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="5" align="center" height="100">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center">
<td colspan="2">新增记录→</td>
    <td>
<select name="monster_id_n" >
 <option class="select">选择站位类型</option>
 
<?php if(is_array($monster_list)) { foreach($monster_list as $mlrs) { ?>
 <option value="<?php echo $mlrs['id']?>"><?php echo $mlrs['name']?></option>
 
<?php } } ?>
 
</select>	</td>
    <td><input name="pos_n" type="text" value="1"  size="10"/></td>
    </tr>  
  <tr>
    <td colspan="5" align="center">
<input name="mission_area_monster_team_id" type="hidden" value="<?php echo $id?>"/>
<input type="hidden" name="action" value="SetMissionAreaMonster" />
<input type="hidden" name="winid" value="<?php echo $winid?>" />
<input name="url" type="text" value="
<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>
" style="display:none;"/>
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick="javascript: return confirm('你确定执行操作？');"  class="button"/></td>
  </tr>	
</table>
</form>