<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<table class="table">
  <tr>
    <th colspan="24"><a href="?in=item">物品装备列表</a>
<?php if($name) { ?>
 ＞ 搜索 <span class="bluetext"><?php echo $name?></span>
<?php } ?>
</th>
  </tr>
  <tr class="title_3">
    <td colspan="24">
<span class="right" style="padding:3px;">
<form action="" method="get" name="forms" onSubmit="setSubmit('Submits')">
<select name="type_id" >
 <option class="select">选择类型</option>
 
<?php if(is_array($item_type_list)) { foreach($item_type_list as $itrs) { ?>
 <option value="<?php echo $itrs['id']?>" 
<?php if($itrs['id'] == $type_id) { ?>
selected="selected"
<?php } ?>
><?php echo $itrs['name']?></option>
 
<?php } } ?>
 
</select>
<input name="name" type="text" value="<?php echo $name?>" size="20" maxlength="20"  /> 
<input type="submit" id="Submits" name="Submits" value="搜索" class="button"/>
<input name="in" type="hidden" value="item" />
</form>	
</span>	
 <a href="?in=item" class="
<?php if(!$type_id) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
">所有</a>
<?php if($item_type_list) { ?>
  
<?php if(is_array($item_type_list)) { foreach($item_type_list as $itrs) { ?>
  <a href="?in=item&type_id=<?php echo $itrs['id']?>" class="
<?php if($type_id == $itrs['id']) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
"><?php echo $itrs['name']?></a>
  
<?php } } ?>
<?php } ?>
<a href="?in=item&type_id=<?=$wqid?>&a=2" class="
<?php if($type_id == $wqid && $a == 2) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
">剑</a>
<a href="?in=item&type_id=<?=$wqid?>&a=3" class="
<?php if($type_id == $wqid && $a == 3) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
">弓</a>
<a href="?in=item&type_id=<?=$wqid?>&a=1" class="
<?php if($type_id == $wqid && $a == 1) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
">拳套</a>
<a href="?in=item&type_id=<?=$wqid?>&a=5" class="
<?php if($type_id == $wqid && $a == 5) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
">棰子</a>
<a href="?in=item&type_id=<?=$wqid?>&a=6,7" class="
<?php if($type_id == $wqid && $a == '6,7') { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
">法杖</a>
</td>
  </tr>  
<form method="post" action="?in=item" name="form"  onSubmit="setSubmit('Submit')">
  
  <tr align="center" class="title_2">
    <td width="35">ID</td>
<td width="35">删除</td>
    <td>名称</td>
    <td>标识</td>
    <td>图标</td>
    <td>类型</td>
<td>元宝级别</td>
    <td><a href="?in=item&type_id=<?php echo $type_id?>&order=price_level" title="按等级排序" 
<?php if($order == 'price_level') { ?>
style="font-weight:bold;"
<?php } ?>
>价格等级</a></td>
    <td>品质等级</td>
    <td>用途描述</td>
    <td>详细描述</td>
<td>武力/绝技/法术</td>
    <td>普通攻击/加值</td>
    <td>普通防御/加值</td>
    <td>绝技攻击/加值</td>
    <td>绝技防御/加值</td>
    <td>法术攻击/加值</td>
    <td>法术防御/加值</td>
    <td>生命/加值</td>
<td>速度/加值</td>
<td title="要求角色等级">要求等级</td>
<td>所需元宝</td>
<td>天数</td>
<td>要求职业</td>
<?php if($type_id==10005) { ?>
<td title="变身卡与怪物对应表">怪物</td>
<?php } ?>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除<?php echo $rs['name']?>"/><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
    <td><input name="name[]" type="text" value="<?php echo $rs['name']?>"  size="15"/></td>
    <td><input name="sign[]" type="text" value="<?php echo $rs['sign']?>"  size="8"/></td>
    <td><input name="icon_id[]" type="text" value="<?php echo $rs['icon_id']?>"  size="2"/></td>
    <td>
<select name="type_id[]" >
 <option class="select">类型</option>
 
<?php if(is_array($item_type_list)) { foreach($item_type_list as $itrs) { ?>
 <option value="<?php echo $itrs['id']?>"  
<?php if($itrs['id'] == $rs['type_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $itrs['name']?></option>
 
<?php } } ?>
 
</select>
</td>
    <td>
<select name="ingot_level[]" >
 <option class="select">等级</option>
 
<?php if(is_array($item_ingot_list)) { foreach($item_ingot_list as $ilrs) { ?>
 <option value="<?php echo $ilrs['id']?>"  
<?php if($ilrs['id'] == $rs['ingot_level']) { ?>
selected="selected"
<?php } ?>
><?php echo $ilrs['id']?>级</option>
 
<?php } } ?>
 
</select>
</td>	
    <td>
<select name="price_level[]" >
 <option class="select">等级</option>
 
<?php if(is_array($item_price_list)) { foreach($item_price_list as $iprs) { ?>
 <option value="<?php echo $iprs['level']?>"  
<?php if($iprs['level'] == $rs['price_level']) { ?>
selected="selected"
<?php } ?>
><?php echo $iprs['level']?>级</option>
 
<?php } } ?>
 
</select>
</td>
    <td>
<select name="quality[]" >
 <option class="select">等级</option>
 
<?php if(is_array($item_quality_list)) { foreach($item_quality_list as $iqrs) { ?>
 <option value="<?php echo $iqrs['quality']?>"  
<?php if($iqrs['quality'] == $rs['quality']) { ?>
selected="selected"
<?php } ?>
><?php echo $iqrs['quality']?>级</option>
 
<?php } } ?>
 
</select>
</td>
    <td><input name="usage[]" type="text" value="<?php echo $rs['usage']?>"  size="8"/></td>
    <td><textarea name="description[]" cols="10" rows="1" ondblclick="textareasize(this)"><?php echo $rs['description']?></textarea></td>
    <td><input name="strength[]" type="text" value="<?php echo $rs['strength']?>"   size="1"/><input name="agile[]" type="text" value="<?php echo $rs['agile']?>"    size="1"/><input name="intellect[]" type="text" value="<?php echo $rs['intellect']?>"    size="1"/></td>
    <td><input name="attack[]" type="text" value="<?php echo $rs['attack']?>"  size="2"/><input name="attack_up[]" type="text" value="<?php echo $rs['attack_up']?>"    size="1"/>	</td>
    <td><input name="defense[]" type="text" value="<?php echo $rs['defense']?>"  size="2"/><input name="defense_up[]" type="text" value="<?php echo $rs['defense_up']?>"   size="1"/>	</td>
    <td><input name="stunt_attack[]" type="text" value="<?php echo $rs['stunt_attack']?>"  size="2"/><input name="stunt_attack_up[]" type="text" value="<?php echo $rs['stunt_attack_up']?>"  size="1"/>	</td>
    <td><input name="stunt_defense[]" type="text" value="<?php echo $rs['stunt_defense']?>"  size="2"/><input name="stunt_defense_up[]" type="text" value="<?php echo $rs['stunt_defense_up']?>"   size="1" />	</td>	
    <td><input name="magic_attack[]" type="text" value="<?php echo $rs['magic_attack']?>"  size="2"/><input name="magic_attack_up[]" type="text" value="<?php echo $rs['magic_attack_up']?>"   size="1"/>	</td>	
    <td><input name="magic_defense[]" type="text" value="<?php echo $rs['magic_defense']?>"  size="2"/><input name="magic_defense_up[]" type="text" value="<?php echo $rs['magic_defense_up']?>"   size="1"/>	</td>		
    <td><input name="health[]" type="text" value="<?php echo $rs['health']?>"  size="2"/><input name="health_up[]" type="text" value="<?php echo $rs['health_up']?>"  size="1" />	</td>
    <td><input name="speed[]" type="text" value="<?php echo $rs['speed']?>"  size="2"/><input name="speed_up[]" type="text" value="<?php echo $rs['speed_up']?>"  size="1" />	</td>
    <td><input name="require_level[]" type="text" value="<?php echo $rs['require_level']?>"  size="2"/></td>	
<td><input name="ingot[]" type="text" value="<?php echo $rs['ingot']?>"   size="3"/></td>
<td><input name="day[]" type="text" value="<?php echo $rs['day']?>"   size="3"/></td>
    <td>
<a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallItemEquipJob&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>')" class="list_menu">要求职业</a>
</td>
   
<?php if($type_id==10005) { ?>
   <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallAvatarItemMonster&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>')" class="list_menu">怪物</a></td>
   
<?php } ?>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="24" align="center">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="2">新增记录→</td>
    <td><input name="name_n" type="text" value=""  size="15"/></td>
    <td><input name="sign_n" type="text" value=""  size="8"/></td>
    <td><input name="icon_id_n" type="text" value=""  size="2"/></td>
    <td>
<select name="type_id_n" >
 <option class="select">类型</option>
 
<?php if(is_array($item_type_list)) { foreach($item_type_list as $itrs) { ?>
 <option value="<?php echo $itrs['id']?>"  
<?php if($itrs['id'] == $type_id) { ?>
selected="selected"
<?php } ?>
><?php echo $itrs['name']?></option>
 
<?php } } ?>
 
</select>
</td>
    <td>
<select name="ingot_level_n" >
 <option class="select">等级</option>
 
<?php if(is_array($item_ingot_list)) { foreach($item_ingot_list as $ilrs) { ?>
 <option value="<?php echo $ilrs['id']?>"><?php echo $ilrs['id']?>级</option>
 
<?php } } ?>
 
</select>
</td>		
    <td>
<select name="price_level_n" >
 <option class="select">等级</option>
 
<?php if(is_array($item_price_list)) { foreach($item_price_list as $iprs) { ?>
 <option value="<?php echo $iprs['level']?>" ><?php echo $iprs['level']?>级</option>
 
<?php } } ?>
 
</select>
</td>
    <td>
<select name="quality_n" >
 <option class="select">等级</option>
 
<?php if(is_array($item_quality_list)) { foreach($item_quality_list as $iqrs) { ?>
 <option value="<?php echo $iqrs['quality']?>"><?php echo $iqrs['quality']?>级</option>
 
<?php } } ?>
 
</select>
</td>
    <td><input name="usage_n" type="text" value=""  size="8"/></td>
    <td><input name="description_n" type="text" value=""  size="10" ondblclick="textareasize(this)"/></td>
    <td><input name="strength_n" type="text" value=""  size="1"/><input name="agile_n" type="text" value=""  size="1"/><input name="intellect_n" type="text" value="" size="1"/></td>
    <td><input name="attack_n" type="text" value=""  size="2"/><input name="attack_up_n" type="text" value=""  size="1"/>	</td>
    <td><input name="defense_n" type="text" value=""  size="2"/><input name="defense_up_n" type="text" value="" size="1"/>	</td>
    <td><input name="stunt_attack_n" type="text" value=""  size="2"/><input name="stunt_attack_up_n" type="text" value="" size="1"/>	</td>
    <td><input name="stunt_defense_n" type="text" value=""  size="2"/><input name="stunt_defense_up_n" type="text" value="" size="1"/>	</td>	
    <td><input name="magic_attack_n" type="text" value=""  size="2"/><input name="magic_attack_up_n" type="text" value="" size="1"/>	</td>	
    <td><input name="magic_defense_n" type="text" value=""  size="2"/><input name="magic_defense_up_n" type="text" value="" size="1"/>	</td>		
    <td><input name="health_n" type="text" value=""  size="2"/><input name="health_up_n" type="text" value="" size="1"/>	</td>
    <td><input name="speed_n" type="text" value=""  size="2"/><input name="speed_up_n" type="text" value="" size="1"/>	</td>
    <td><input name="require_level_n" type="text" value=""  size="2"/></td>		
    <td><input name="ingot_n" type="text" value=""   size="3"/></td>
    <td><input name="day_n" type="text" value=""   size="3"/></td>
    <td>&nbsp;</td>	
    
<?php if($type_id==10005) { ?>
<td>&nbsp;</td>
<?php } ?>
   </tr> 
  
<?php if($list_array_pages) { ?>
 
  <tr>
    <td colspan="24" class="page"><?php echo $list_array_pages?></td>
  </tr> 
  
<?php } ?>
     
  <tr>
    <td colspan="24" align="center">
<input type="hidden" name="action" value="SetItem" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/></td>
  </tr>
</form>
</table>