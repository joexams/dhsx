<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<script type="text/javascript" src="include/js/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  var hiddenobj = $('#hiddenarea');

  function changedata(obj){
    var data = obj.attr('data'), id = obj.attr('data-id'), val = obj.val();
    if (id > 0){
      if (data != val){
        if ($('#as_id_'+id).html() == null){
          hiddenobj.append('<input type="hidden" id="as_id_'+id+'" name="id[]" value="'+id+'" />');
        }
      }else {
         $('#as_id_'+id).remove();
      }
    }
  }
  $('#table').on('blur', 'input.field', function(){
    changedata($(this));
  });
});
</script>

<form method="post" action="?in=main" name="form"  onSubmit="setSubmit('Submit')">
<table class="table" id="table">
  <tr>
    <th colspan="10">小助手奖励</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35">ID</td>
<td width="35">删除</td>
    <td width="150">名称</td>
    <td width="100">vip</td>
    <td width="100">次数</td>
    <td width="100">阅历</td>
    <td width="100">龙鱼令</td>
    <td width="100">牌子数量</td>
    <td>&nbsp;</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center">
<td><?php echo $rs['id']?></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除<?php echo $rs['name']?>"/></td>
    <td><input name="name[<?php echo $rs['id']?>]" type="text" value="<?php echo $rs['name']?>" class="field" data-id="<?php echo $rs['id']?>" data="<?php echo $rs['name']?>"  size="20"/></td>
    <td><input name="vip[<?php echo $rs['id']?>]" type="text" value="<?php echo $rs['vip']?>" class="field" data-id="<?php echo $rs['id']?>" data="<?php echo $rs['vip']?>"  size="10"/></td>
    <td><input name="times[<?php echo $rs['id']?>]" type="text" value="<?php echo $rs['times']?>" class="field" data-id="<?php echo $rs['id']?>" data="<?php echo $rs['times']?>"  size="10"/></td>
    <td><input name="skill[<?php echo $rs['id']?>]" type="text" value="<?php echo $rs['skill']?>" class="field" data-id="<?php echo $rs['id']?>" data="<?php echo $rs['skill']?>"  size="10"/></td>
    <td><input name="long_yu_ling[<?php echo $rs['id']?>]" type="text" value="<?php echo $rs['long_yu_ling']?>" class="field" data-id="<?php echo $rs['id']?>" data="<?php echo $rs['long_yu_ling']?>"  size="10"/></td>
    <td><input name="card_num[<?php echo $rs['id']?>]" type="text" value="<?php echo $rs['card_num']?>" class="field" data-id="<?php echo $rs['id']?>" data="<?php echo $rs['card_num']?>"  size="10"/></td>
    <td>&nbsp;</td>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="10" align="center">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="2">新增记录→</td>
    <td><input name="name_n" type="text" value=""  size="20"/></td>
    <td><input name="vip_n" type="text" value=""  size="10"/></td>
    <td><input name="times_n" type="text" value=""  size="10"/></td>
    <td><input name="skill_n" type="text" value=""  size="10"/></td>
    <td><input name="long_yu_ling_n" type="text" value=""  size="10"/></td>
    <td><input name="card_num_n" type="text" value=""  size="10"/></td>
    <td>&nbsp;</td>
  </tr>  
  <tr>
    <td colspan="10" align="center" id="hiddenarea">
<input type="hidden" name="action" value="SetAssistantAward" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/></td>
  </tr>  
</table>
</form>