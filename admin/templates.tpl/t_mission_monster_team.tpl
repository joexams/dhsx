<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<?php if($msg) { ?>
<?php include template('msg'); ?>
<?php } ?>
<form method="post" action="?in=mission" name="form"  onSubmit="setSubmit('Submit2')" target="gopost">
<table class="table">
  <tr>
    <th colspan="17"><span class="bluetext"><?php echo $name?></span> 的怪物组</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="30" >ID</td>
    <td width="30">删除</td>
<td>权值</td>
    <td>阵法</td>
    <td>显示形象</td>
    <td>开始视频</td>
    <td>结束视频</td>
    <td>坐标X,Y</td>
    <td title="战场地图横向偏移量">偏移X,Y</td>
<td>最大回合数</td>
<td>顶住回合数</td>
    <td>复活怪物</td>
    <td>复活回合数量</td>
    <td>不能阵亡伙伴数</td>
    <td>怪物</td>
    <td>击杀BOOS ID</td>
    <td>&nbsp;</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除"/></td>
<td><input name="lock[]" type="text" value="<?php echo $rs['lock']?>" size="5"/></td>
<td>
<select name="deploy_mode_id[]" >
 <option class="select" value="0">选择阵法</option>
 
<?php if(is_array($deploy_mode_list)) { foreach($deploy_mode_list as $drs) { ?>
 <option value="<?php echo $drs['id']?>" 
<?php if($drs['id'] == $rs['deploy_mode_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $drs['name']?></option>
 
<?php } } ?>
</select>	</td>
<td>
<select name="monster_id[]" style="width:100">
 <option class="select" value="0">选择形象</option>
 
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
<select name="start_mission_video_id[]">
 <option class="select" value="NULL">开始视频</option>
 
<?php if(is_array($mission_video_list)) { foreach($mission_video_list as $mvrs) { ?>
 <option value="<?php echo $mvrs['id']?>" 
<?php if($mvrs['id'] == $rs['start_mission_video_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $mvrs['name']?></option>
 
<?php } } ?>
</select>
</td>
    <td>
<select name="end_mission_video_id[]">
 <option class="select" value="NULL">结束视频</option>
 
<?php if(is_array($mission_video_list)) { foreach($mission_video_list as $mvrs) { ?>
 <option value="<?php echo $mvrs['id']?>" 
<?php if($mvrs['id'] == $rs['end_mission_video_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $mvrs['name']?></option>
 
<?php } } ?>
</select>
</td>		
<td><input name="position_x[]" type="text" value="<?php echo $rs['position_x']?>" size="2"/> <input name="position_y[]" type="text" value="<?php echo $rs['position_y']?>" size="2"/></td>
<td><input name="map_margin_x[]" type="text" value="<?php echo $rs['map_margin_x']?>" size="1"/> <input name="map_margin_y[]" type="text" value="<?php echo $rs['map_margin_y']?>" size="1"/></td>
<td><input name="max_bout_number[]" type="text" value="<?php echo $rs['max_bout_number']?>" size="1"/></td>
<td><input name="request_bout_number[]" type="text" value="<?php echo $rs['request_bout_number']?>" size="1"/></td>
<td>
<select name="fuhuo_mission_monster_id[]"  style="width:100">
 <option class="select">复活怪物</option>
 
<?php if(is_array($rs['mission_monster'])) { foreach($rs['mission_monster'] as $mmrs) { ?>
 <option value="<?php echo $mmrs['id']?>" 
<?php if($mmrs['id'] == $rs['fuhuo_mission_monster_id']) { ?>
selected
<?php } ?>
><?php echo $mmrs['name']?></option>
 
<?php } } ?>
</select>	
    </td>
<td><input name="fuhuo_bout_number[]" type="text" value="<?php echo $rs['fuhuo_bout_number']?>" size="1"/></td>
<td><input name="attack_can_not_dead_number[]" type="text" value="<?php echo $rs['attack_can_not_dead_number']?>" size="1"/></td>
 <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallMissionMonster&id=<?php echo $rs['id']?>&deploy_mode_id=<?php echo $rs['deploy_mode_id']?>&name=<?php echo $rs['name_url']?>&type=<?php echo $type?>')" class="list_menu">怪物</a></td>
 <td><input name="kill_boss_id[]" type="text" value="<?php echo $rs['kill_boss_id']?>" size="1"/></td>
 <td>&nbsp;</td>
    </tr>
  
<?php } } ?>
  
<?php if($list_array_pages) { ?>
 
  <tr>
    <td colspan="17" class="page"><?php echo $list_array_pages?></td>
  </tr> 
  
<?php } ?>
  
  
<?php } else { ?>
  <tr>
<td colspan="17" align="center" height="100">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center">
<td colspan="2">新增记录→</td>
<td><input name="lock_n" type="text" value="" size="5"/></td>
<td>
<select name="deploy_mode_id_n" >
 <option class="select">选择阵法</option>
 
<?php if(is_array($deploy_mode_list)) { foreach($deploy_mode_list as $drs) { ?>
 <option value="<?php echo $drs['id']?>"><?php echo $drs['name']?></option>
 
<?php } } ?>
</select>	</td>
<td>
<select name="monster_id_n"  style="width:100">
 <option class="select">选择形象</option>
 
<?php if(is_array($monster_list)) { foreach($monster_list as $mrs) { ?>
 <option value="<?php echo $mrs['id']?>"><?php echo $mrs['level']?>级-<?php echo $mrs['name']?></option>
 
<?php } } ?>
</select>	
</td>
    <td>
<select name="start_mission_video_id_n">
 <option class="select" value="NULL">开始视频</option>
 
<?php if(is_array($mission_video_list)) { foreach($mission_video_list as $mvrs) { ?>
 <option value="<?php echo $mvrs['id']?>" ><?php echo $mvrs['name']?></option>
 
<?php } } ?>
</select>
</td>
    <td>
<select name="end_mission_video_id_n">
 <option class="select" value="NULL">结束视频</option>
 
<?php if(is_array($mission_video_list)) { foreach($mission_video_list as $mvrs) { ?>
 <option value="<?php echo $mvrs['id']?>"><?php echo $mvrs['name']?></option>
 
<?php } } ?>
</select>
</td>		
<td><input name="position_x_n" type="text" value="" size="2"/> <input name="position_y_n" type="text" value="" size="2"/></td>
<td><input name="map_margin_x_n" type="text" value="" size="1"/> <input name="map_margin_y_n" type="text" value="" size="1"/></td>
<td><input name="max_bout_number_n" type="text" value="0" size="1"/></td>
<td><input name="request_bout_number_n" type="text" value="0" size="1"/></td>
<td>
<select name="fuhuo_mission_monster_id_n"  style="width:100">
 <option class="select" value="0">复活怪物</option>
</select>	
    </td>
<td><input name="fuhuo_bout_number_n" type="text" value="0" size="1"/></td>
<td><input name="attack_can_not_dead_number_n" type="text" value="0" size="1"/></td>
<td><input name="kill_boss_id_n" type="text" value="0" size="1"/></td>
<td>&nbsp;</td>
    </tr>  
  <tr>
    <td colspan="17" align="center">
<input name="mission_scene_id" type="hidden" value="<?php echo $id?>"/>
<input type="hidden" name="action" value="SetMissionMonsterTeam" />
<input type="hidden" name="winid" value="<?php echo $winid?>" />
<input name="url" type="text" value="
<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>
" style="display:none;"/>
<input type="submit" id="Submit2" name="Submit2" value="执行操作" onClick="javascript: return confirm('你确定执行操作？');"  class="button"/></td>
  </tr>	
</table>
</form>