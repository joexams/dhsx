<?php if(!defined('IN_UCTIME')) exit('Access Denied'); ?>

<table class="table">
  <tr>
    <th colspan="<?php echo $columns_num?>"><?php echo $table_info?></th>
  </tr>
  
<?php if($column_search) { ?>
  <tr class="title_3">
    <td colspan="<?php echo $columns_num?>">	
<a href="?in=common&table=<?php echo $table?>" class="
<?php if(!$col_type) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
">所有</a>
  
<?php if(is_array($column_search)) { foreach($column_search as $crs) { ?>
  <a href="?in=common&table=<?php echo $table?>&type=<?php echo $crs['id']?>" class="
<?php if($col_type == $crs['id']) { ?>
 title_menu_on 
<?php } else { ?>
 title_menu 
<?php } ?>
"><?php echo $crs['name']?></a>
  
<?php } } ?>
</td>
  </tr>  
  
<?php } ?>
  
<?php if($search_column) { ?>
  <tr class="title_3">
    <td colspan="<?php echo $columns_num?>">
  	<form action="" method="get" name="forms" onSubmit="setSubmit('Submits')">
<input name="<?php echo $search_column?>" type="text" value="<?php echo $search_column_value?>" size="20" maxlength="20"  /> 
<input type="submit" id="Submits" name="Submits" value="搜索" class="button"/>
<input name="in" type="hidden" value="common" />
<input name="table" type="hidden" value="<?php echo $table?>" />
</form>	
</td>
  </tr>
  
<?php } ?>
  <form method="post" action="?in=common&table=<?php echo $table?>" name="form"  onSubmit="setSubmit('Submit')"  enctype="multipart/form-data">
  <tr align="center" class="title_2">
<td width="35">删除<input type="checkbox" id="checkAll" title="全部选择删除"/></td>
<?php if(is_array($table_Columns_list)) { foreach($table_Columns_list as $tclrs) { ?>
    <td width="150"><?php echo $tclrs['column_desc']?></td>
    
<?php } } ?>
    <td>&nbsp;</td>
    </tr>
  
<?php if($list) { ?>
  
<?php if(is_array($list)) { foreach($list as $key => $rs) { ?>
  
  <tr onmouseover=this.className="td3" onmouseout=this.className="td" align="center" id="<?php echo $rs['primary_val']?>">
    <td><input type="checkbox" name="id_del[]" value="<?php echo $rs['primary_val']?>" title="选择删除"/><input name="id_old[]" type="hidden" value="<?php echo $rs['primary_val']?>"/><input name="id[]" type="hidden" value="<?php echo $rs['primary_val']?>"/></td>
<?php if(is_array($table_Columns_list)) { foreach($table_Columns_list as $tclrs) { ?>
<?php if($tclrs['column_name']=='id') { ?>
<td><?php echo $rs['id']?></td>
<?php } else { ?>
<?php if($tclrs['column_type']=='select') { ?>
<td>
<select name="<?php echo $tclrs['column_name']?>[]" class="field">
<option value="<?php echo $default['0']?>"><?php echo $default['1']?></option>
<?php if(is_array($$tclrs['column_name'])) { foreach($$tclrs['column_name'] as $trs) { ?>
<option value="<?php echo $trs['id']?>" 
<?php if($trs['id'] == $rs[$tclrs['column_name']]) { ?>
selected="selected"
<?php } ?>
><?php echo $trs['name']?></option>
<?php } } ?>
</select>
</td>
<?php } elseif($tclrs['column_type']=='textarea') { ?>
<td><textarea name="<?php echo $tclrs['column_name']?>[]" ondblclick="textareasize(this)" class="field"><?php echo $rs[$tclrs['column_name']]?></textarea></td>
<?php } elseif($tclrs['column_type']=='a') { ?>
<td><?=$tclrs['type_val']?></td>
<?php } elseif($tclrs['column_type']=='radio') { ?>
<td>
<?php if(is_array($$tclrs['column_name'])) { foreach($$tclrs['column_name'] as $trs) { ?>
<input type="radio" name="<?php echo $tclrs['column_name']?><?php echo $rs['id']?>" class="field" value="<?php echo $trs['id']?>" 
<?php if($trs['id'] == $rs[$tclrs['column_name']]) { ?>
checked="checked"
<?php } ?>
><?php echo $trs['name']?>
<?php } } ?>
</td>
<?php } elseif($tclrs['column_type']=='checkbox') { ?>
<td>
<?php if(is_array($$tclrs['column_name'])) { foreach($$tclrs['column_name'] as $trs) { ?>
<input type="checkbox" name="<?php echo $tclrs['column_name']?><?php echo $rs['id']?>[]" class="field" value="<?php echo $trs['id']?>" 
<?php if(substr_count($rs[$tclrs['column_name']],$trs['id'])) { ?>
 checked="checked"
<?php } ?>
><?php echo $trs['name']?>
<?php } } ?>
</td>
<?php } else { ?>
    <td><input name="<?php echo $tclrs['column_name']?>[]" type="text" 
<?php if($tclrs['is_template'] > 0) { ?>
 value="<?php echo $chinese_text[$rs[$tclrs['column_name']]]?>" 
<?php } else { ?>
 value="<?php echo $rs[$tclrs['column_name']]?>" 
<?php } ?>
 
<?php if($tclrs['column_type_length'] > 0) { ?>
 size="<?php echo $tclrs['column_type_length']?>" 
<?php } else { ?>
 size="20" 
<?php } ?>
 class="field"/></td>
    
<?php } ?>
    
<?php } ?>
    
<?php } } ?>
    <td>&nbsp;</td>
    </tr>
  
<?php } } ?>
  
<?php } else { ?>
  <tr>
<td colspan="5" align="center">找不到相关信息</td>
  </tr>
  
<?php } ?>
 
  <tr class="td2" align="center" >
<td colspan="1">新增记录→</td>
<?php if(is_array($table_Columns_list)) { foreach($table_Columns_list as $tclrs) { ?>
<?php if($tclrs['column_key']=='auto_increment') { ?>
<td>&nbsp;</td>
<?php } else { ?>
<?php if($tclrs['column_type']=='select') { ?>
<td>
<select name="<?php echo $tclrs['column_name']?>_n">
<option value="<?php echo $default['0']?>"><?php echo $default['1']?></option>
<?php if(is_array($$tclrs['column_name'])) { foreach($$tclrs['column_name'] as $trs) { ?>
<option value="<?php echo $trs['id']?>" 
<?php if($trs['id'] == $col_type && $tclrs['column_search'] == 1) { ?>
selected="selected"
<?php } ?>
><?php echo $trs['name']?></option>
<?php } } ?>
</select>
</td>
<?php } elseif($tclrs['column_type']=='textarea') { ?>
<td><textarea name="<?php echo $tclrs['column_name']?>_n" ondblclick="textareasize(this)"></textarea></td>
<?php } elseif($tclrs['column_type']=='radio') { ?>
<td>
<?php if(is_array($$tclrs['column_name'])) { foreach($$tclrs['column_name'] as $trs) { ?>
<input type="radio" name="<?php echo $tclrs['column_name']?>_n" value="<?php echo $trs['id']?>"><?php echo $trs['name']?>
<?php } } ?>
</td>
<?php } elseif($tclrs['column_type']=='checkbox') { ?>
<td>
<?php if(is_array($$tclrs['column_name'])) { foreach($$tclrs['column_name'] as $trs) { ?>
<input type="checkbox" name="<?php echo $tclrs['column_name']?>_n[]" value="<?php echo $trs['id']?>"><?php echo $trs['name']?>
<?php } } ?>
</td>
<?php } else { ?>
    <td><input name="<?php echo $tclrs['column_name']?>_n" type="text" value=""   
<?php if($tclrs['column_type_length'] > 0) { ?>
 size="<?php echo $tclrs['column_type_length']?>" 
<?php } else { ?>
 size="20" 
<?php } ?>
 onChange="fill_data(this);"/></td>
    
<?php } ?>
    
<?php } ?>
    
<?php } } ?>
    
    <td>&nbsp;</td>
  </tr>  
  
<?php if($list_array_pages) { ?>
 
  <tr>
    <td colspan="<?php echo $columns_num?>" class="page"><?php echo $list_array_pages?></td>
  </tr> 
  
<?php } ?>
  
  <tr>
  <td colspan="<?php echo $columns_num?>" align="left">
  <div class="file-box">
  	<input type='text' name='textfield' id='textfield' class='txt' />  
 <input type='button' class='btn' value='浏览...' />
    <input type="file" name="fileField" class="file" id="fileField" size="28" onchange="document.getElementById('textfield').value=this.value" />
    </div>
  </td>
  </tr>
  <tr>
    <td colspan="<?php echo $columns_num?>" align="center">
<input type="hidden" name="action" value="SetCommon" />
<input type="hidden" id="column_str" name="column_str" value="" />
<input type="hidden" id="primary_key" name="primary_key" value="<?php echo $primary_key?>" />
<input type="submit" id="Submit" name="Submit" value="执行操作" onClick='javascript: return confirm("你确定执行操作？");'  class="button"/></td>
  </tr>  
</form>
</table>

<script language = "javaScript" src = "include/js/jquery.min.js" type="text/javascript"></script>
<script>
var jq=jQuery.noConflict();
var column_str = '';
jq(document).ready(function(){
jq(".field").change(function(){
var column_id = jq(this).parent().parent().attr('id');
column_str = column_str+column_id+'|';
jq("#column_str").val(column_str);
});
jq("#checkAll").click(function() {
jq('input[name="id_del[]"]').prop("checked",this.checked);
});
var subBox = jq('input[name="id_del[]"]');
subBox.click(function(){
jq("#checkAll").prop("checked",subBox.length == jq('input[name="id_del[]"]:checked').length ? true : false);
});
});

</script>
<style type="text/css">
.file-box{ position:relative;width:340px}
.txt{ border:1px solid #cdcdcd; width:180px;}
.btn{ background-color:#FFF; border:1px solid #CDCDCD;height:24px; width:70px;}
.file{ position:absolute; top:0; right:80px; height:24px; filter:alpha(opacity:0);opacity: 0;width:260px }
</style>