<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<?php if($msg) { ?>
<?php include template('msg'); ?>
<?php } ?>
<form method="post" action="?in=mission" name="form"  onSubmit="setSubmit('Submit4')" target="gopost">
<table class="table">
  <tr>
    <th colspan="8"><span class="bluetext"><?php echo $name?></span> 的任务物品</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35" >ID</td>
    <td width="35">删除</td>
    <td width="150">任务</td>
    <td width="100">奖励</td>
    <td width="100">概率%</td>
    <td>&nbsp;</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['mission_monster_id']?><input name="mission_monster_id[]" type="hidden" value="<?php echo $rs['mission_monster_id']?>"/></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['mission_monster_id']?>,<?php echo $rs['quest_id']?>" title="选择删除"/></td>
    <td><?php echo $rs['title']?>
<select name="quest_id[]" style="display:none;">
 <option class="select">选择任务</option>
 
<?php if(is_array($quest_list)) { foreach($quest_list as $qrs) { ?>
 <option value="<?php echo $qrs['id']?>" 
<?php if($qrs['id'] == $rs['quest_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $qrs['title']?></option>
 
<?php } } ?>
</select>
</td>
    <td>
<select name="item_id[]" >
 <option class="select">选择物品</option>
 
<?php if(is_array($item_list)) { foreach($item_list as $irs) { ?>
 <option value="<?php echo $irs['id']?>" 
<?php if($irs['id'] == $rs['item_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $irs['name']?></option>
 
<?php } } ?>
</select>
</td>
    <td><input name="probability[]" type="text" value="<?php echo $rs['probability']?>"  size="5"/></td>
    <td>&nbsp;</td>
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
<select name="quest_id_n" >
 <option class="select">选择任务</option>
 
<?php if(is_array($quest_list)) { foreach($quest_list as $qrs) { ?>
 <option value="<?php echo $qrs['id']?>" ><?php echo $qrs['title']?></option>
 
<?php } } ?>
</select>	
</td>
    <td>
<select name="item_id_n" >
 <option class="select">选择物品</option>
 
<?php if(is_array($item_list)) { foreach($item_list as $irs) { ?>
 <option value="<?php echo $irs['id']?>" ><?php echo $irs['name']?></option>
 
<?php } } ?>
</select>
</td>
    <td><input name="probability_n" type="text" value=""  size="5"/></td>	
    <td>&nbsp;</td>
    </tr>  
  <tr>
    <td colspan="9" align="center">
<input name="mission_monster_id_n" type="hidden" value="<?php echo $id?>"/>
<input type="hidden" name="action" value="SetMissionMonsterQuestItem" />
<input type="hidden" name="winid" value="<?php echo $winid?>" />
<input name="url" type="text" value="
<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>
" style="display:none;"/>
<input type="submit" id="Submit4" name="Submit4" value="执行操作" onClick="javascript: return confirm('你确定执行操作？');"  class="button"/></td>
  </tr>	
</table>
</form>