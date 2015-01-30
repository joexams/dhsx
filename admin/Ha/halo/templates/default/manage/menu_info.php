<script type="text/javascript">
$(function(){
	$('#form').on('submit', function(e){
		modify_id = '';
		e.preventDefault();
		var objform = $(this);
		var url = wh_url+'?m=manage&c=index&v=update';
		Ha.common.ajax(url, 'json', objform.serialize(), 'get');
		$.dialog({id:'shake-demo'}).close();
	});
});
</script>
<div class="column">
	<form id="form">
	<table>
		<thead>
			<tr>
				<th>ID</th><th>菜单名称</th><th>方法文件</th><th>表</th><th>参数</th><th>链接url</th><th>是否外链</th><th>父目录</th><th>是否启用</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input type="hidden" name="nid" id="nid" value="<?php echo $mid?>"><?php echo $mid?></td>
				<td><input type="text"name="ndesc" id="ndesc" value="<?php echo $info['describe']?>"></td>
				<td><input type="text" name="nfunc_file" id="nfunc_file" value="<?php echo $info["func_file"]?>"></td>
				<td><input type="text" name="ntable_name" id="ntable_name" value="<?php echo $info["table_name"]?>"></td>
				<td><input type="text" name="nparams" id="nparams" value="<?php echo $info["params"]?>"></td>
				<td><input type="text" name="nurl" id="nurl" value="<?php echo $info["url"]?>"></td>
				<td>
					<select  name="nis_link" id="nis_link">
					<option value="0" <?php if ($info["params"]==0) echo "selected";?>>否</option>
					<option value="1" <?php if ($info["params"]==1) echo "selected";?>>是</option>
					</select>
				</td>
				<td><select name="nfather_id" id="nfather_id"><?php echo $select_tree?></select></td>
				<td>
					<select name="nstatus" id="nstatus">
					<option value="1" <?php if ($info["status"]==1) echo "selected";?>>启用</option>
					<option value="0" <?php if ($info["status"]==0) echo "selected";?>>禁用</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table><br>
	<?php if ($column_list){?>
	<table>
		<thead>
			<tr>
				<th>序号</th><th>字段</th><th>字段类型</th><th>字段描述</th><th>类型</th><th>类型框长度</th><th>默认值</th><th>字段关联SQL语句</th><th>类型固定值</th><th>是否开启</th><th>排序</th><th>选择搜索</th><th>主键(必填)</th>
			</tr>
		</thead><input type="hidden" id="modify_id" name="modify_id" value="">
		<?php foreach ($column_list as $key => $value){?>
			<tr id='<?php echo $value["id"]?>'>
				<td><?php echo $key+1;?></td>
				<td id='column_name'><?php echo $value["column_name"]?></td>
				<td id='c_type'><?php echo $value["c_type"]?></td>
				<td id='column_desc'><input name='column_desc<?php echo $value["id"]?>' type='text' value='<?php echo $value["column_desc"]?>' onchange='modify(this)'></td>
				<td id='column_type'>
				<select name='column_type<?php echo $value["id"]?>' onchange='modify(this)'>
				<?php foreach ($type_value as $k => $v){?>
				<option value='<?php echo $v?>' <?php if ($v==$value["column_type"]) echo "selected"?>><?php echo $v?></option>
				<?php }?>
				</select>
				</td>
				<td id='column_type_length'><input size='5' type='text' value='<?php echo $value["column_type_length"]?>' name='column_type_length<?php echo $value["id"]?>' onchange='modify(this)'></td>
				<td id='default_val'><input type='text' value='<?php echo $value["default_val"]?>' name='default_val<?php echo $value["id"]?>' onchange='modify(this)'></td>
				<td id='type_sql'><textarea name='type_sql<?php echo $value["id"]?>' onchange='modify(this)'><?php echo $value["type_sql"]?></textarea></td>
				<td id='type_val'><textarea onchange='modify(this)' name='type_val<?php echo $value["id"]?>'><?php echo $value["type_val"]?></textarea></td>
				<td id='column_status'>
				<select name='column_status<?php echo $value["id"]?>' onchange='modify(this)'>
				<option value='1' <?php if ($value["column_status"]==1) echo "selected"?>>开启</option>
				<option value='0' <?php if ($value["column_status"]==0) echo "selected"?>>关闭</option>
				</select>
				</td>
				<td id='column_sort'><input size='5' type='text' value='<?php echo $value["column_sort"]?>' name='column_sort<?php echo $value["id"]?>' onchange='modify(this)'></td>
				<td id='column_search'><input size='5' type='text' name='column_search<?php echo $value["id"]?>' value='<?php echo $value["column_search"]?>' onchange='modify(this)'></td>
				<td id='column_key'><input size='5' type='text' name='column_key<?php echo $value["id"]?>' value='<?php echo $value["column_key"]?>' onchange='modify(this)'></td>
			</tr>
			<?php }?>
	</table>
	<?php }?>
	<div class="float_footer">    
	<div class="frm_btn"> 
	<input type="hidden" name="doSubmit" value="1">
	<input type="submit" id="btnsubmit" class="btn_sbm" value="提交">
	<input type="button" id="btnreset" class="btn_rst" value="取消" onclick="$.dialog({id:'shake-demo'}).close();">
	</div>
	</div>
	</form>
</div>