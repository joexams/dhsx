<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<form method="post" action="?in=monster" name="form"  onSubmit="setSubmit('Submit')">
<table class="table">
  <tr>
    <th colspan="20">怪物</th>
  </tr>
  <tr align="center" class="title_2">
<td width="35">删除</td>
    <td width="100">ID</td>
    <td width="150">角色</td>
    <td width="150">名称</td>
<td width="150">角色等级</td>
    <td width="150">英文标识</td>
    <td width="150">怪物附加攻击力</td>
    <td width="150">怪物附加防御力</td>
    <td width="150">怪物附加生命值</td>
    <td width="150">总攻击力</td>
    <td width="150">总防御力</td>
    <td width="150">总生命值</td>
    <td width="150">第一技能等级</td>
    <td width="150">第二技能等级</td>
    <td width="150">第三技能等级</td>
    <td width="150">第四技能等级</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
  	<input name="id_old[]" type="hidden" value="<?php echo $rs['id']?>"/>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除<?php echo $rs['name']?>"/></td>
    <td><?php echo $rs['id']?></td>
    <td>
<select name="role_id[]" >
 <option class="select">选择角色</option>
 
<?php if(is_array($role_list)) { foreach($role_list as $rlrs) { ?>
 <option value="<?php echo $rlrs['id']?>" 
<?php if($rlrs['id'] == $rs['role_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $rlrs['name']?></option>
 
<?php } } ?>
</select>	
</td>
    <td><input name="name_text_id[]" type="text" value="<?php echo $chinese_text[$rs['name_text_id']]?>"  size="10"/></td>
    <td><input name="role_lv[]" type="text" value="<?php echo $rs['role_lv']?>"  size="5"/></td>
<td><input name="sign[]" type="text" value="<?php echo $rs['sign']?>"  size="20"/></td>
<td><input name="attack_add[]" type="text" value="<?php echo $rs['attack_add']?>"  size="5"/></td>
<td><input name="defense_add[]" type="text" value="<?php echo $rs['defense_add']?>"  size="5"/></td>
<td><input name="health_add[]" type="text" value="<?php echo $rs['health_add']?>"  size="5"/></td>
<td><?php echo $rs['attack']?></td>
<td><?php echo $rs['defense']?></td>
<td><?php echo $rs['health']?></td>
<td>
<select name="first_skill_lv_id[]" >
 <option class="select">选择第一技能等级</option>
 
<?php if(is_array($first_skill_list)) { foreach($first_skill_list as $slrs) { ?>
 
<?php if($role[$rs['role_id']]['first_skill'] == $slrs['skill_id']) { ?>
 <option value="<?php echo $slrs['id']?>" 
<?php if($slrs['id'] == $rs['first_skill_lv_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $slrs['name']?></option>
 
<?php } ?>
 
<?php } } ?>
</select>	
</td>    
    <td>
<select name="second_skill_lv_id[]" >
 <option class="select">选择第二技能等级</option>
 
<?php if(is_array($second_skill_list)) { foreach($second_skill_list as $slrs) { ?>
 
<?php if($role[$rs['role_id']]['second_skill'] == $slrs['skill_id']) { ?>
 <option value="<?php echo $slrs['id']?>" 
<?php if($slrs['id'] == $rs['second_skill_lv_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $slrs['name']?></option>
 
<?php } ?>
 
<?php } } ?>
</select>	
</td>    
    <td>
<select name="third_skill_lv_id[]" >
 <option class="select">选择第三技能等级</option>
 
<?php if(is_array($third_skill_list)) { foreach($third_skill_list as $slrs) { ?>
 
<?php if($role[$rs['role_id']]['third_skill'] == $slrs['skill_id']) { ?>
 <option value="<?php echo $slrs['id']?>" 
<?php if($slrs['id'] == $rs['third_skill_lv_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $slrs['name']?></option>
 
<?php } ?>
 
<?php } } ?>
</select>	
</td>    
    <td>
<select name="fourth_skill_lv_id[]" >
 <option class="select">选择第四技能等级</option>
 
<?php if(is_array($four_skill_list)) { foreach($four_skill_list as $slrs) { ?>
 
<?php if($role[$rs['role_id']]['fourth_skill'] == $slrs['skill_id']) { ?>
 <option value="<?php echo $slrs['id']?>" 
<?php if($slrs['id'] == $rs['fourth_skill_lv_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $slrs['name']?></option>
 
<?php } ?>
 
<?php } } ?>
</select>	
</td>    
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="20" align="center">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="2">新增记录→</td>
<td>
<select name="role_id_n" >
 <option class="select">选择角色</option>
 
<?php if(is_array($role_list)) { foreach($role_list as $rlrs) { ?>
 <option value="<?php echo $rlrs['id']?>"><?php echo $rlrs['name']?></option>
 
<?php } } ?>
</select>	
</td>
<td><input name="name_text_id_n" type="text" value=""  size="10"/></td>
<td><input name="role_lv_n" type="text" value=""  size="5"/></td>
<td><input name="sign_n" type="text" value=""  size="20"/></td>
<td><input name="attack_add_n" type="text" value=""  size="5"/></td>
<td><input name="defense_add_n" type="text" value=""  size="5"/></td>
<td><input name="health_add_n" type="text" value=""  size="5"/></td>
<td></td>
<td></td>
<td></td>
<td>
<select name="first_skill_lv_id_n" >
 <option class="select">选择第一技能等级</option>
 
<?php if(is_array($first_skill_list)) { foreach($first_skill_list as $slrs) { ?>
 <option value="<?php echo $slrs['id']?>"><?php echo $slrs['name']?></option>
 
<?php } } ?>
</select>	
</td>
<td>
<select name="second_skill_lv_id_n" >
 <option class="select">选择第二技能等级</option>
 
<?php if(is_array($second_skill_list)) { foreach($second_skill_list as $slrs) { ?>
 <option value="<?php echo $slrs['id']?>"><?php echo $slrs['name']?></option>
 
<?php } } ?>
</select>	
</td>
<td>
<select name="third_skill_lv_id_n" >
 <option class="select">选择第三技能等级</option>
 
<?php if(is_array($third_skill_list)) { foreach($third_skill_list as $slrs) { ?>
 <option value="<?php echo $slrs['id']?>"><?php echo $slrs['name']?></option>
 
<?php } } ?>
</select>	
</td>
<td>
<select name="fourth_skill_lv_id_n" >
 <option class="select">选择第四技能等级</option>
 
<?php if(is_array($four_skill_list)) { foreach($four_skill_list as $slrs) { ?>
 <option value="<?php echo $slrs['id']?>"><?php echo $slrs['name']?></option>
 
<?php } } ?>
</select>	
</td>
  </tr>  
  <tr>
    <td colspan="20" align="center">
<input type="hidden" name="action" value="SetMonster" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/></td>
  </tr>  
</table>
</form>