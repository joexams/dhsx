<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<form method="post" action="?in=main" name="form"  onSubmit="setSubmit('Submit')">
<table class="table">
  <tr>
    <th colspan="5">冷却时间类别表</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35">ID</td>
<td width="35">删除</td>
    <td width="150">名称</td>
    <td width="100" title="1个元宝可以减少的冷却时间秒数">减少时间(秒)</td>
    <td>&nbsp;</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除<?php echo $rs['cd_type_name']?>"/><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
    <td><input name="cd_type_name[]" type="text" value="<?php echo $rs['cd_type_name']?>"  size="20"/></td>
    <td><input name="ingot_time_ratio[]" type="text" value="<?php echo $rs['ingot_time_ratio']?>"  size="10"/></td>
    <td>&nbsp;</td>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="5" align="center">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="2">新增记录→</td>
    <td><input name="cd_type_name_n" type="text" value=""  size="20"/></td>
    <td><input name="ingot_time_ratio_n" type="text" value=""  size="10"/></td>
    <td>&nbsp;</td>
  </tr>  
  <tr>
    <td colspan="5" align="center">
<input type="hidden" name="action" value="SetCdTimeType" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/></td>
  </tr>  
</table>
</form>