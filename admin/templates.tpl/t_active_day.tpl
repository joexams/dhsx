<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<script type="text/javascript" src="include/js/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  var hiddenobj = $('#hiddenarea');

  function changedata(obj){
    var data = obj.attr('data'), id = obj.attr('data-id'), val = obj.val();
    if (id > 0){
      if (data != val){
        if ($('#day_id_'+id).html() == null){
          hiddenobj.append('<input type="hidden" id="day_id_'+id+'" name="id[]" value="'+id+'" />');
        }
      }else {
         $('#day_id_'+id).remove();
      }
    }
  }
  $('#table').on('blur', 'input.field', function(){
    changedata($(this));
  });

  $('#table').on('change', 'select.field', function(){
    changedata($(this));
  });
});
</script>
<form method="post" action="?in=active" name="form"  onSubmit="setSubmit('Submit')">
<table class="table" id="table" style="width:60%">
  <tr>
    <th colspan="6">定期活动</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35">ID</td>
  <td width="35">删除</td>
    <td width="50">标识</td>
    <td width="150" >名称</td>
    <td width="35">状态</td>
    <td>&nbsp;</td>
  </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除<?php echo $rs['name']?>"/></td>
  <td><input name="sign[<?php echo $rs['id']?>]" type="text" value="<?php echo $rs['sign']?>" data-id="<?php echo $rs['id']?>" data="<?php echo $rs['sign']?>" class="field" size="20"/></td>
  <td><input name="name[<?php echo $rs['id']?>]" type="text" value="<?php echo $rs['name']?>" data-id="<?php echo $rs['id']?>" data="<?php echo $rs['name']?>" class="field" size="20"/></td>
  <td>
      <select name="is_open[<?php echo $rs['id']?>]" data-id="<?php echo $rs['id']?>" data="<?php echo $rs['is_open']?>" class="field" >
        <option value="0"
<?php if($rs['is_open'] == 0) { ?>
 selected
<?php } ?>
>关闭</option>
        <option value="1"
<?php if($rs['is_open'] == 1) { ?>
 selected
<?php } ?>
>开启</option>
      </select>
  </td>
    <td>&nbsp;</td>
  </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="6" align="center">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="2">新增记录→</td>
    <td><input name="sign_n" type="text" value=""  size="20"/></td>
    <td><input name="name_n" type="text" value=""  size="20"/></td>
    <td>
      <select name="is_open_n">
        <option value="0">关闭</option>
        <option value="1">开启</option>
      </select>
    </td>
    <td>&nbsp;</td>
  </tr>  
  
<?php if($list_array_pages) { ?>
 
  <tr>
    <td colspan="5" class="page"><?php echo $list_array_pages?></td>
  </tr> 
  
<?php } ?>
  
  <tr>
    <td colspan="5" align="center" id="hiddenarea">
<input type="hidden" name="action" value="day_setting" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/></td>
 <td>&nbsp;</td>
  </tr>  
</table>
</form>