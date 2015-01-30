<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<?php if($msg) { ?>
<?php include template('msg'); ?>
<?php } ?>
<form method="post" action="?in=mission" name="form"  onSubmit="setSubmit('Submit')" target="gopost">
<table class="table">
  <tr>
    <th colspan="7"><span class="bluetext"><?php echo $name?></span> 的房间信息</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35" >ID</td>
    <td width="35">删除</td>
    <td width="150">名称</td>
    <td width="100">房间权值</td>
    <td width="150">房间地图</td>
<td width="100">怪物组</td>
    <td>&nbsp;</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除"/></td>
    <td><input name="name[]" type="text" value="<?php echo $rs['name']?>"  size="20"/></td>
    <td><input name="lock[]" type="text" value="<?php echo $rs['lock']?>"  size="10"/></td>
    <td><input name="map[]" type="text" value="<?php echo $rs['map']?>"  size="20"/></td>
<td><a href="javascript:void(0)" onclick="pmwin('open','t_call.php?action=CallMissionMonsterTeam&id=<?php echo $rs['id']?>&name=<?php echo $rs['name_url']?>&type=<?php echo $type?>')" class="list_menu">怪物组</a></td>
    <td>&nbsp;</td>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="8" align="center" height="100">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center">
<td colspan="2">新增记录→</td>
    <td><input name="name_n" type="text" value="" size="20"/></td>
    <td><input name="lock_n" type="text" value=""  size="10"/></td>
    <td><input name="map_n" type="text" value=""  size="20"/></td>
<td colspan="2">&nbsp;</td>
    </tr>  
  <tr>
    <td colspan="8" align="center">
<input name="mission_id" type="hidden" value="<?php echo $id?>"/>
<input type="hidden" name="action" value="SetMissionScene" />
<input type="hidden" name="winid" value="<?php echo $winid?>" />
<input name="url" type="text" value="
<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>
" style="display:none;"/>
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick="javascript: return confirm('你确定执行操作？');"  class="button"/>	</td>
  </tr>	
</table>
</form>