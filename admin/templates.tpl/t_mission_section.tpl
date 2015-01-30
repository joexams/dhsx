<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<table class="table">
  <tr>
    <th colspan="9"><a href="?in=mission">副本列表</a>
<?php if($name) { ?>
 ＞ 搜索 <span class="bluetext"><?php echo $name?></span>
<?php } ?>
</th>
  </tr>
  <tr class="title_3">
    <td colspan="9">
<span class="right" style="padding:3px;">
<form action="" method="get" name="forms" onSubmit="setSubmit('Submits')">
<select name="town_id" >
 <option value="100000">所有</option>
 
<?php if(is_array($town_list)) { foreach($town_list as $trs) { ?>
 <option value="<?php echo $trs['id']?>" 
<?php if($trs['id'] == $town_id) { ?>
selected="selected"
<?php } ?>
><?php echo $trs['name']?></option>
 
<?php } } ?>
 
</select>
<input name="section_name" type="text" value="<?php echo $section_name?>" size="20" maxlength="20"  /> 
<input type="submit" name="Submits" id="Submits" value="搜索" class="button"/>
<input name="in" type="hidden" value="mission" />
</form>	
</span>	</td>
  </tr>  
<form method="post" action="?in=mission" name="form"  onSubmit="setSubmit('Submit')">
  <tr align="center" class="title_2">
    <td width="35">ID</th>
<td width="35">删除</td>
    <td>副本名称</td>
    <td>标识</td>
    <td>副本权值</td>
    <td>所属城镇</td>
    <td title="奖励的副本解锁权限">副本解锁</td>
    <td>奖励</td>
    <td>剧情</td>
  </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除<?php echo $rs['name']?>"/><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
<td><input name="name[]" type="text" value="<?php echo $rs['name']?>"  size="15"/></td>
    <td><input name="sign[]" type="text" value="<?php echo $rs['sign']?>"  size="10"/></td>
<td><input name="lock[]" type="text" value="<?php echo $rs['lock']?>"  size="2"/></td>
<td>
<!--onclick="selectAjax('t_call.php?action=CallShowTown','town_<?php echo $rs['id']?>','',1)"-->
<select name="town_id[]" id="town_<?php echo $rs['id']?>" >
 <option class="select">选择城镇</option>
 
<?php if(is_array($town_list)) { foreach($town_list as $trs) { ?>
 <option value="<?php echo $trs['id']?>" 
<?php if($trs['id'] == $rs['town_id']) { ?>
selected="selected"
<?php } ?>
><?php echo $trs['name']?></option>
 
<?php } } ?>
</select>	</td>
<td><input name="award_section_key[]" type="text" value="<?php echo $rs['award_section_key']?>"  size="5"/></td>
    <td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallMissionSectionItem&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>')" class="list_menu" title="剧情奖励">奖励</a></td>
    <td><a href="?in=mission&action=MissionList&mission_section_id=<?php echo $rs['id']?>" class="list_menu">剧情</a></td>
  </tr>
<!--<div id="town_<?php echo $rs['id']?>_menu" class="showMsg" style="display:none;width:100px;"></div>-->
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="9" align="center" height="100">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="2">新增记录→</td>
<td><input name="name_n" type="text" value=""  size="15"/></td>
<td><input name="sign_n" type="text" value=""  size="10"/></td>
<td><input name="lock_n" type="text" value="<?php echo $lock_n?>"  size="2"/></td>
<td>
<select name="town_id_n" >
 <option class="select">选择城镇</option>
 
<?php if(is_array($town_list)) { foreach($town_list as $trs) { ?>
 <option value="<?php echo $trs['id']?>" 
<?php if($trs['id'] == $town_id) { ?>
selected="selected"
<?php } ?>
><?php echo $trs['name']?></option>
 
<?php } } ?>
 
</select>	</td>
<td><input name="award_section_key_n" type="text" value=""  size="5"/></td>
<td colspan="2">&nbsp;</td>
  </tr> 
  
<?php if($list_array_pages) { ?>
 
  <tr>
    <td colspan="9" class="page"><?php echo $list_array_pages?></td>
  </tr> 
  
<?php } ?>
  <tr>
    <td colspan="9" class="greentext">
<input type="hidden" name="action" value="SetMissionSection" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/> 若删除副本将一并删除副本下属的剧情及剧情下的相关数据</td>
  </tr> 
</form>     
</table>
