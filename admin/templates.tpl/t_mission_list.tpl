<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
  <script language="javascript">
  function fill_data (army_type, text_id,id) {
  		
ele = document.getElementById(text_id);

  text_value = ele.value;
  values = text_value.split('\t');
  var l = 6;

  if (values.length != l) {
  alert('参数无效');
  ele.focus();

  return true;
  }
  
  document.getElementById(army_type +  '_require_power_' + id).value        = values[0];
  document.getElementById(army_type +  '_average_attack_' + id).value        = values[1];
  document.getElementById(army_type +  '_average_damage_' + id).value       = values[2];
  document.getElementById(army_type +  '_award_skill_' + id).value  = values[3];
  document.getElementById(army_type +  '_award_experience_' + id).value  = values[4];
  document.getElementById(army_type +  '_award_coins_' + id).value  = values[5];

  
  
  }

</script>
<table class="table">
  <tr>
    <th colspan="25"><a href="?in=mission&action=MissionList"><span class="bluetext"><?php echo $mission_section_name?></span> 副本的
<?php if($type == 1) { ?>
英雄
<?php } elseif($type == 2) { ?>
BOSS
<?php } elseif($type == 3) { ?>
爬塔
<?php } elseif($type == 4) { ?>
渡劫
<?php } elseif($type == 6) { ?>
桃子
<?php } elseif($type == 7) { ?>
生肖
<?php } elseif($type == 8) { ?>
游仙
<?php } elseif($type == 9) { ?>
光环
<?php } elseif($type == 11) { ?>
九界
<?php } elseif($type == 12) { ?>
九界神秘层
<?php } elseif($type == 13) { ?>
护送取经
<?php } elseif($type == 14) { ?>
战无双
<?php } elseif($type == 15) { ?>
八仙
<?php } elseif($type == 16) { ?>
轮回
<?php } else { ?>
普通
<?php } ?>
剧情列表</a>
<?php if($name) { ?>
 ＞ 搜索 <span class="bluetext"><?php echo $name?></span>
<?php } ?>
</th>
  </tr>
  <tr class="title_3">
    <td colspan="25">
<?php if($mission_section_list) { ?>
 
<?php $i=1 ?>
  
<?php if(is_array($mission_section_list)) { foreach($mission_section_list as $mrs) { ?>
  <a href="?in=mission&mission_section_id=<?php echo $mrs['id']?>&action=MissionList&type=<?php echo $type?>" class="
<?php if($mission_section_id == $mrs['id']) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
"><strong><?php echo $mrs['name']?></strong>
<?php if($type == 1) { ?>
-英雄
<?php } elseif($type == 2) { ?>
-BOSS
<?php } elseif($type == 3) { ?>
-爬塔
<?php } elseif($type == 4) { ?>
-渡劫
<?php } elseif($type == 6) { ?>
-桃子
<?php } elseif($type == 7) { ?>
-生肖
<?php } elseif($type == 8) { ?>
-游仙
<?php } elseif($type == 9) { ?>
-光环
<?php } elseif($type == 11) { ?>
-九界
<?php } elseif($type == 12) { ?>
-九界神秘层
<?php } elseif($type == 13) { ?>
-护送取经
<?php } elseif($type == 14) { ?>
-战无双
<?php } elseif($type == 15) { ?>
-八仙
<?php } elseif($type == 16) { ?>
-轮回
<?php } ?>
 (<?php echo $mrs['town_name']?> / 权值:<?php echo $mrs['lock']?>)</a>
  
<?php if($i % 5 == 0) { ?>
<br />
<?php } ?>
  
<?php $i++ ?>
  
  
<?php } } ?>
<?php } ?>
</td>
  </tr> 
<form method="post" action="?in=mission" name="form"  onSubmit="setSubmit('Submit')">   
  <tr align="center" class="title_2">
    <td width="35" rowspan="2">ID</td>
<td width="30" rowspan="2">删除</td>
    <td rowspan="2" >名称</td>
<td rowspan="2" >剧情权值</td>
    <td rowspan="2" >描述</td>
    <td rowspan="2" title="需求体力值">需求体力</td>
    <td colspan="2" title="评价标准" >评价标准</td>
    <td colspan="2" >评价奖励</td>
    <td colspan="2" >宝箱奖励</td>
<td rowspan="2" >装备掉落概率</td>
<td rowspan="2" >加:武力/绝技/法术</td>
<td rowspan="2" >关联任务</td>
    <td colspan="3" title="奖励解锁权限" >解锁</td>
<td rowspan="2" >视频</td>
    <td rowspan="2" >代表怪</td>
<td rowspan="2" >是否BOSS关</td>
<td rowspan="2" >是否禁用</td>
    <td rowspan="2" >房间</td>
    <td rowspan="2" >战败提示</td>
    </tr>
  <tr align="center" class="title_2">
    <td >伤害</td>
    <td >损血</td>
    <td >阅历</td>
    <td >经验</td>
    <td >铜币</td>
    <td title="宝箱掉落物品">掉落</td>
    <td title="奖励的剧情解锁权限" >剧情</td>
    <td title="奖励功能解锁权值"  >功能</td>
    <td title="奖励角色解锁权值"  >角色</td>
  </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?><input name="completion[]" type="hidden" value="<?php echo $rs['completion']?>"  size="2"/><input name="require_level[]" type="hidden" value="<?php echo $rs['require_level']?>"  size="2"/></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除"/><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
    <td>
<?php if($type <> 0) { ?>
<input name="name[]" type="text" value="<?php echo $rs['name']?>" size="15"/><br />
<a href="?in=mission&action=MissionView&id=<?php echo $rs['id']?>&name=<?php echo $mission_section_name_url?>&type=<?php echo $type?>" class="list_menu" >查看</a>
<?php } else { ?>
<input name="name[]" type="text" value="<?php echo $rs['name']?>" /><br /><strong><?php echo $mission_section_name?></strong>(<?php echo $rs['i']?>)
<a href="?in=mission&action=MissionView&id=<?php echo $rs['id']?>&name=<?php echo $mission_section_name_url?>&type=<?php echo $type?>" class="list_menu" >查看</a>
<?php } ?>
</td>
    <td><input name="lock[]" type="text" value="<?php echo $rs['lock']?>"  size="4"/></td>
    <td><textarea name="description[]" cols="10" rows="1" ondblclick="textareasize(this)"><?php echo $rs['description']?></textarea></td>
    <td><input name="require_power[]" id="m_require_power_<?php echo $rs['id']?>" type="text" value="<?php echo $rs['require_power']?>"  size="2" onChange="fill_data('m', this.id,<?php echo $rs['id']?>);" onClick="this.select();"/></td>
    <td><input name="average_attack[]" id="m_average_attack_<?php echo $rs['id']?>" type="text" value="<?php echo $rs['average_attack']?>"  size="2" onChange="fill_data('m', this.id,<?php echo $rs['id']?>);" onClick="this.select();"/></td>
    <td><input name="average_damage[]" id="m_average_damage_<?php echo $rs['id']?>" type="text" value="<?php echo $rs['average_damage']?>"  size="2" onChange="fill_data('m', this.id,<?php echo $rs['id']?>);" onClick="this.select();"/></td>
    <td><input name="award_skill[]" id="m_award_skill_<?php echo $rs['id']?>" type="text" value="<?php echo $rs['award_skill']?>"  size="2" onChange="fill_data('m', this.id,<?php echo $rs['id']?>);" onClick="this.select();"/></td>
    <td><input name="award_experience[]" id="m_award_experience_<?php echo $rs['id']?>" type="text" value="<?php echo $rs['award_experience']?>"  size="5" onChange="fill_data('m', this.id,<?php echo $rs['id']?>);" onClick="this.select();"/></td>
    <td><input name="award_coins[]" id="m_award_coins_<?php echo $rs['id']?>" type="text" value="<?php echo $rs['award_coins']?>"  size="2" onChange="fill_data('m', this.id,<?php echo $rs['id']?>);" onClick="this.select();"/></td>
    <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallMissionItem&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>')" class="list_menu" title="副本奖励">奖励</a></td>
    <td><input name="item_probability[]" type="text" value="<?php echo $rs['item_probability']?>"  size="2"/></td>
    <td>
<input name="add_strength[]" type="text" value="<?php echo $rs['add_strength']?>"  size="1"/>
<input name="add_agile[]" type="text" value="<?php echo $rs['add_agile']?>"  size="1"/>
<input name="add_intellect[]" type="text" value="<?php echo $rs['add_intellect']?>"  size="1"/>
</td>
    <td>
<select name="releate_quest_id[]">
 <option class="select">选择任务</option>
 
<?php if(is_array($quest_list)) { foreach($quest_list as $qrs) { ?>
 <option value="<?php echo $qrs['id']?>" 
<?php if($qrs['id'] == $rs['releate_quest_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $qrs['title']?> (ID:<?php echo $qrs['id']?>)</option>
 
<?php } } ?>
</select>	</td>	
    <td><input name="award_mission_key[]" type="text" value="<?php echo $rs['award_mission_key']?>"  size="4"/></td>
    <td>
<select name="award_function_key[]" style="width:120px;">
 <option class="select">功能解锁</option>
 
<?php if(is_array($game_function_list)) { foreach($game_function_list as $gfrs) { ?>
 <option value="<?php echo $gfrs['lock']?>" 
<?php if($gfrs['lock'] == $rs['award_function_key']) { ?>
selected="selected"
<?php } ?>
>(权:<?php echo $gfrs['lock']?>)<?php echo $gfrs['name']?> </option>
 
<?php } } ?>
</select>	</td>
    <td><input name="award_role_key[]" type="text" value="<?php echo $rs['award_role_key']?>"  size="2"/></td>
    <td>
<select name="mission_video_id[]">
 <option class="select" value="NULL">选择视频</option>
 
<?php if(is_array($mission_video_list)) { foreach($mission_video_list as $mvrs) { ?>
 <option value="<?php echo $mvrs['id']?>" 
<?php if($mvrs['id'] == $rs['mission_video_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $mvrs['name']?></option>
 
<?php } } ?>
</select>	</td>	
    <td>
<select name="monster_id[]" >
 <option class="select" value="NULL">选择怪物</option>
 
<?php if(is_array($monster_list)) { foreach($monster_list as $mrs) { ?>
 <option value="<?php echo $mrs['id']?>" 
<?php if($mrs['id'] == $rs['monster_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $mrs['level']?>级-<?php echo $mrs['name']?></option>
 
<?php } } ?>
</select>	</td>
    <td>
<select name="is_boss[]">
 <option value="0" 
<?php if($rs['is_boss'] == 0) { ?>
selected="selected"
<?php } ?>
 class="select">否</option>
 <option value="1" 
<?php if($rs['is_boss'] == 1) { ?>
selected="selected"
<?php } ?>
>是</option>
</select>	
</td>
    <td>
<select name="is_disable[]">
 <option value="0" 
<?php if($rs['is_disable'] == 0) { ?>
selected="selected"
<?php } ?>
 class="select">否</option>
 <option value="1" 
<?php if($rs['is_disable'] == 1) { ?>
selected="selected"
<?php } ?>
>是</option>
</select>	
</td>

    <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallMissionScene&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>&type=<?php echo $type?>')" class="list_menu">房间</a></td>
    <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallMissionFailedTips&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>')" class="list_menu">战败提示</a></td>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="25" align="center" height="100">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="2">新增记录→<input name="completion_n" type="hidden" value="1"/><input name="require_level_n" type="hidden" value="1" /><input name="type_n" type="hidden" value="<?php echo $type?>" /></td>
    <td>
<?php if($type != 0) { ?>
<input name="name_n" type="text" value="" size="15"/>
<?php } else { ?>
<input name="name_n" type="hidden" value="<?php echo $mission_section_name?>(<?php echo $newi?>)"/><strong><?php echo $mission_section_name?></strong>(<?php echo $newi?>)
<?php } ?>
</td>
    <td><input name="lock_n" type="text" value="<?php echo $lock_n?>"  size="4"/></td>
    <td>
<textarea name="description_n" cols="10" rows="1" ondblclick="textareasize(this)"></textarea>
<select name="mission_section_id_n" style="display:none;">
 <option class="select">选择副本</option>
 
<?php if(is_array($mission_section_list)) { foreach($mission_section_list as $mrs) { ?>
 <option value="<?php echo $mrs['id']?>" 
<?php if($mrs['id'] == $mission_section_id) { ?>
selected="selected"
<?php } ?>
><?php echo $mrs['name']?></option>
 
<?php } } ?>
 
</select>	</td>
    <td><input name="require_power_n" id="m_require_power_n" type="text" value=""  size="2" onChange="fill_data('m', this.id,'n');" onClick="this.select();"/></td>
    <td><input name="average_attack_n" id="m_average_attack_n" type="text" value=""  size="2" onChange="fill_data('m', this.id,'n');" onClick="this.select();"/></td>
<td><input name="average_damage_n" id="m_average_damage_n" type="text" value=""  size="2" onChange="fill_data('m', this.id,'n');" onClick="this.select();"/></td>	
    <td><input name="award_skill_n" id="m_award_skill_n" type="text" value=""  size="2" onChange="fill_data('m', this.id,'n');" onClick="this.select();"/></td>
    <td><input name="award_experience_n" id="m_award_experience_n" type="text" value=""  size="5" onChange="fill_data('m', this.id,'n');" onClick="this.select();"/></td>
    <td><input name="award_coins_n" id="m_award_coins_n" type="text" value=""  size="2" onChange="fill_data('m', this.id,'n');" onClick="this.select();"/></td>
<td>&nbsp;</td>
    <td><input name="item_probability_n" type="text" value=""  size="2"/></td>
    <td>
<input name="add_strength_n" type="text" value=""  size="1"/>
<input name="add_agile_n" type="text" value=""  size="1"/>
<input name="add_intellect_n" type="text" value=""  size="1"/>
</td>	
<td>
<select name="releate_quest_id_n">
 <option class="select">选择任务</option>
 
<?php if(is_array($quest_list)) { foreach($quest_list as $qrs) { ?>
 <option value="<?php echo $qrs['id']?>"><?php echo $qrs['title']?> (ID:<?php echo $qrs['id']?>)</option>
 
<?php } } ?>
</select>	</td>	
    <td><input name="award_mission_key_n" type="text" value=""  size="4"/></td>
    <td>
<select name="award_function_key_n" style="width:120px;">
 <option class="select">功能解锁</option>
 
<?php if(is_array($game_function_list)) { foreach($game_function_list as $gfrs) { ?>
 <option value="<?php echo $gfrs['lock']?>">(权:<?php echo $gfrs['lock']?>)<?php echo $gfrs['name']?></option>
 
<?php } } ?>
</select>	</td>
    <td><input name="award_role_key_n" type="text" value=""  size="2"/></td>
    <td>
<select name="mission_video_id_n">
 <option class="select" value="NULL">选择视频</option>
 
<?php if(is_array($mission_video_list)) { foreach($mission_video_list as $mvrs) { ?>
 <option value="<?php echo $mvrs['id']?>"><?php echo $mvrs['name']?></option>
 
<?php } } ?>
</select>	</td>	
    <td>
<select name="monster_id_n" >
 <option class="select" value="NULL">选择怪物</option>
 
<?php if(is_array($monster_list)) { foreach($monster_list as $mrs) { ?>
 <option value="<?php echo $mrs['id']?>"><?php echo $mrs['level']?>级-<?php echo $mrs['name']?></option>
 
<?php } } ?>
</select>	</td>
    <td>
<select name="is_boss_n">
 <option value="0" class="select">否</option>
 <option value="1" >是</option>
</select>		
</td>
    <td>
<select name="is_disable_n">
 <option value="0" class="select">否</option>
 <option value="1" >是</option>
</select>		
</td>	
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
  
  
<?php if($list_array_pages) { ?>
 
  <tr>
    <td colspan="25" class="page"><?php echo $list_array_pages?></td>
  </tr> 
  
<?php } ?>
  <tr>
    <td colspan="25" class="greentext">
<input type="hidden" name="action" value="SetMissionList" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/> 若删除剧情将一并删除剧情下属的房间及房间下的相关数据</td>
  </tr> 
</form>    
</table>