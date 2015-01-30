<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>
<form method="post" action="?in=active" name="form"  onSubmit="setSubmit('Submit')">
<table class="table">
  <tr>
    <th colspan="9">猜谜题库表</th>
  </tr>
  <tr align="center" class="title_2">
    <td width="35">题目ID</td>
<td width="35">删除</td>
    <td width="200">题目内容</td>
    <td width="100">答案1</td>
    <td width="100">答案2</td>
    <td width="100">答案3</td>
    <td width="100">答案4</td>
    <td width="100">正确答案</td>
    <td>&nbsp;</td>
    </tr>
  
<?php if($list_array) { ?>
  
<?php if(is_array($list_array)) { foreach($list_array as $rs) { ?>
	  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" >
<td><?php echo $rs['id']?><input name="id_old[]" type="hidden" value="<?php echo $rs['id']?>" /></td>
<td><input type="checkbox" name="id_del[]" value="<?php echo $rs['id']?>" title="选择删除"/></td>
   <td><input name="question[]" type="text" value="<?php echo $rs['question']?>"  size="50"/></td>
   <td><input name="answer_1[]" type="text" value="<?php echo $rs['answer_1']?>"  size="20"/></td>
   <td><input name="answer_2[]" type="text" value="<?php echo $rs['answer_2']?>"  size="20"/></td>
   <td><input name="answer_3[]" type="text" value="<?php echo $rs['answer_3']?>"  size="20"/></td>
   <td><input name="answer_4[]" type="text" value="<?php echo $rs['answer_4']?>"  size="20"/></td>
 <td><input name="answer[]" type="text" value="<?php echo $rs['answer']?>"  size="20"/></td>
    <td>&nbsp;</td>
    </tr>
  
<?php } } ?>
   
<?php if($list_array_pages) { ?>
 
  <tr>
    <td colspan="9" class="page"><?php echo $list_array_pages?></td>
  </tr> 
  
<?php } ?>
 

  
<?php } else { ?>
  <tr>
<td colspan="9" align="center">找不到相关信息</td>
  </tr>  
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="2">新增记录→</td>
   <td><input name="question_n" type="text" value=""  size="50"/></td>
   <td><input name="answer_1_n" type="text" value=""  size="20"/></td>
   <td><input name="answer_2_n" type="text" value=""  size="20"/></td>
   <td><input name="answer_3_n" type="text" value=""  size="20"/></td>
   <td><input name="answer_4_n" type="text" value=""  size="20"/></td>
   <td><input name="answer_n" type="text" value=""  size="20"/></td>
    <td>&nbsp;</td>
  </tr>  
  <tr>
    <td colspan="9" align="center">
<input type="hidden" name="action" value="SetQuizGameQuestion" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/></td>
  </tr>  
</table>
</form>