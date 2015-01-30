<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<form method="post" action="?in=main" name="form"  onSubmit="setSubmit('Submit')">
<table class="table">
  <tr>
    <th colspan="5">影响战争属性</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35">ID</td>
<td width="35">删除</td>
    <td width="150">标识</td>
    <td width="150" >名称</td>
    <td>&nbsp;</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除<?php echo $rs['name']?>"/><input name="id[]" type="hidden" value="<?php echo $rs['id']?>"/></td>
    <td><input name="sign[]" type="text" value="<?php echo $rs['sign']?>"  size="20"/></td>
    <td><input name="name[]" type="text" value="<?php echo $rs['name']?>"  size="20"/></td>
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
    <td><input name="sign_n" type="text" value=""  size="20"/></td>
    <td><input name="name_n" type="text" value=""  size="20"/></td>
    <td>&nbsp;</td>
  </tr>  
  <tr>
    <td colspan="5" align="center">
<input type="hidden" name="action" value="SetWarAttributeType" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/></td>
  </tr>  
</table>
</form>