<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<?php if($msg) { ?>
<?php include template('msg'); ?>
<?php } ?>
<form method="post" action="?in=town" name="form"  onSubmit="setSubmit('Submit2')" target="gopost">
<table class="table">
  <tr>
    <th colspan="6"><span class="bluetext"><?php echo $name?></span> 的携带灵件</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35" >ID</td>
    <td width="35">删除</td>
    <td width="150">灵件</td>
    <!--td width="150" title="功能解锁KEY大于此值才可见">权值</td-->
    <td>&nbsp;</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除"/></td>
    <td><strong><?php echo $rs['soul_name']?></strong></td>
<!--td><input name="func_lock[]" type="text" size="10" value="<?php echo $rs['func_lock']?>"/></td-->
    <td>&nbsp;</td>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="7" align="center" height="100">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center">
<td colspan="2">新增记录→</td>
    <td>
<select name="soul_id_n" >
 <option class="select">选择灵件</option>
 
<?php if(is_array($soul_list)) { foreach($soul_list as $irs) { ?>
 <option value="<?php echo $irs['id']?>" ><?php echo $irs['name']?></option>
 
<?php } } ?>
</select>	</td>
<!--td><input name="func_lock_n" type="text" size="10" value=""/></td-->
    <td>&nbsp;</td>
    </tr>  
  <tr>
    <td colspan="7" align="center">
<input name="town_npc_id" type="hidden" value="<?php echo $id?>"/>
<input type="hidden" name="action" value="SetTownNpcSoul" />
<input type="hidden" name="winid" value="<?php echo $winid?>" />
<input name="url" type="text" value="
<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>
" style="display:none;"/>
<input type="submit" id="Submit2" name="Submit2" value="执行操作" onClick="javascript: return confirm('你确定执行操作？');"  class="button"/></td>
  </tr>	
</table>
</form>