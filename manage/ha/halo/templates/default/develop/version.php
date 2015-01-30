<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 50;
Ha.page.listEid = 'versionlist';
Ha.page.colspan = 5;
Ha.page.emptyMsg = '没有找到数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=develop&c=version&v=ajax_setting_list";
var versionlist;

$(function(){
	Ha.page.getList(1, function(data){
		if (data.status == 0 && data.count >0) {
			versionlist = data.list;
		}
	});
	/**
	 * 提交
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		var objform = $(this);
		var url = '<?php echo INDEX; ?>?m=develop&c=version&v=setting';
		Ha.common.ajax(url, 'json', objform.serialize(), 'post', 'container', function(data){
			if (data.status == 0){
				$( "#versionlisttpl" ).tmpl( data.info ).prependTo( "#versionlist" ).fadeIn(2000, function(){
					var obj = $(this);
					obj.css('background', '#E6791C');
					setTimeout( function(){	obj.css('background', ''); }, ( 2000 ) );
				});	
			}
		});
		return false;
	});
	/**
	 * 点击修改
	 * @return {[type]} [description]
	 */
	$('#versionlist').on('click', 'span.entryline', function(){
		var eleid = $(this).parent().parent('tr').attr('id');
		var id = eleid.replace(/entry-/, '');
		if (id > 0){
			for(var key in versionlist){
				if (versionlist[key].id == id){
					$('#'+eleid).html( $('#editversiontpl').tmpl(versionlist[key]) );
					break;
				}
			}
		}
	});
	/**
	 * 保存修改 
	 * @return {[type]} [description]
	 */
	$('#versionlist').on('click', 'input.btnsave', function(){
		var objtr = $(this).parent().parent('tr')
		var eleid = objtr.attr('id');
		var id = eleid.replace(/entry-/, '');
		if (id > 0){
			var dateline = objtr.find('input[name="dateline"]').val();
			var version = objtr.find('input[name="version"]').val();
			var content = objtr.find('textarea[name="content"]').val();

			var url = '<?php echo INDEX; ?>?m=develop&c=version&v=setting';
			var queryData = {id: id, version: version, content: content, dateline: dateline, doSubmit: 1};
			Ha.common.ajax(url, 'json', queryData, 'post', 'container', function(data){
				if (data.status == 0){
					for(var key in versionlist){
						if (versionlist[key].id == id){
							versionlist[key].version = version;
							versionlist[key].content = content;
							versionlist[key].dateline = data.info.dateline;
							$('#'+eleid).html( $('#versionlisttpl').tmpl(versionlist[key]).html() );
							break;
						}
					}
				}
			});
		}
	});
	/**
	 * 取消修改
	 * @return {[type]} [description]
	 */
	$('#versionlist').on('click', 'input.btncancel', function(){
		var eleid = $(this).parent().parent('tr').attr('id');
		var id = eleid.replace(/entry-/, '');
		if (id > 0){
			for(var key in versionlist){
				if (versionlist[key].id == id){
					$('#'+eleid).html( $('#versionlisttpl').tmpl(versionlist[key]).html() );
					break;
				}
			}
		}
	});
	/**
	 * 删除
	 * 
	 * @return {[type]} [description]
	 */
	$('#versionlist').on('click', 'a.delete', function(){
		var obj    = $(this);
		var id = obj.attr('data-id');
		if (id > 0){
			var url = '<?php echo INDEX; ?>?m=develop&c=version&v=ajax_setting_delete';
			Ha.common.ajax(url, 'json', 'id='+id, 'post', 'container', function(data){
				if (data.status == 0) {
					obj.parent().parent('tr').remove();
				}
			})
		}
		return false;
	});
});
</script>

<script type="text/template" id="editversiontpl">
<td class="num">${id}</td>
<td><input type="text" name="dateline" value="${date('Y-m-d', dateline)}" class="ipt_txt_s"></td>
<td><input type="text" name="version" value="${version}" class="ipt_txt_s"></td>
<td>
<input type="text" name="content" value="${content}" class="ipt_txt_xl">
</td>
<td>
<input type="button" class="btn_sbm btnsave" value="<?php echo Lang('save'); ?>">
<input type="button" class="btn_rst btncancel" value="<?php echo Lang('cancel'); ?>">
</td>
</script>

<script type="text/template" id="versionlisttpl">
<tr id="entry-${id}">
	<td class="num">${id}</td>
	<td><span class="entryline">${date('Y-m-d', dateline)}</span></td>
	<td><span class="entryline">${version}</span></td>
	<td><span class="entryline">${content}</span></td>
	<td>
		<a href="javascript:;" data-id="${id}" class="delete"><?php echo Lang('delete') ?></a>
	</td>
</tr>
</script>

<div id="tipsBar" class="msg_tips"> 
    <i class="i_tips"></i>
    <p id="p_tips">点击各项记录值即转换可编辑状态。</p>
</div>
<h2><span id="tt"><?php echo Lang('version_update_log'); ?></span></h2>
<div class="container" id="container">
	<div class="column cf" id="table_column">
		<div class="title">
	        详细数据
	    </div>
		<div id="dataTable">
		<form name="post_submit" id="post_submit" method="post">
		<table>
		<thead>
		<tr id="dataTheadTr">
			<th>&nbsp;</th>
			<th><?php echo Lang('date'); ?></th>
			<th><?php echo Lang('version'); ?></th>
			<th><?php echo Lang('update_content'); ?></th>
			<th><?php echo Lang('operation') ?></th>
		</tr>
		</thead>
		<tbody>
			<tr>
				<td class="num"><span class="greentitle"><?php echo Lang('add_new_record'); ?></span></td>
				<td><input type="text" name="dateline" id="dateline" class="ipt_txt_s" onclick="WdatePicker()" value="<?php echo $data['today'] ?>" size="10"></td>
				<td><input type="text" name="version" id="version" class="ipt_txt_s"></td>
				<td>
					<input type="text" name="content" id="content" class="ipt_txt_xl">
				</td>
				<td>
					<input type="hidden" name="doSubmit" value="1">
					<input type="submit" id="btnsubmit" class="btn_sbm" value="提交">
					<input type="reset" class="btn_rst" value="重置">
				</td>
			</tr>
		</tbody>
		<tbody id="versionlist">
			   
		</tbody>
		</table>
		</form>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>