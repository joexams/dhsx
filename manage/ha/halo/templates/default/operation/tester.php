<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 15;
Ha.page.listEid = 'testerlist';
Ha.page.colspan = 4;
Ha.page.emptyMsg = '尚未找到游戏服测试号。';
Ha.page.url = "<?php echo INDEX; ?>?m=develop&c=server&v=public_server_list&type=list";


var pageIndex = 1, pageCount = 0, pageSize = 15, recordNum = 0, cid = -1, testerlist;
function getList(index){
	var query = "<?php echo INDEX; ?>?m=develop&c=server&v=public_server_list&type=list&cid="+cid+"&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	mask.show('container');
	$.ajax({
		dataType: "json",
		url: query,
		success: showList
	});
}

function showList( data ) {
	if (data.status == 1){
		$('#testerlist').html(data.msg);
	}else {
		if (cid == 0 && data.count == 0){
			$('option[value="0"]', $('#companyul')).remove();
			$('#companyul').change();
			return false;
		}
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), testerlist = data.list, cid = data.cid;
		$( ".pager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getList });
		if (data.count > 0){
			$( "#testerlist" ).empty().append($( "#testerlisttpl" ).tmpl( testerlist )).show();

			var arrsid = [], arrcid = [];
			for(var key in testerlist){
				arrsid[key] = testerlist[key].sid;
				arrcid[key] = testerlist[key].cid;
			}

			$.ajax({
				url: '<?php echo INDEX; ?>?m=operation&c=interactive&v=ajax_tester_list',
				data: {sid: arrsid.join(','), cid: arrcid.join(',')},
				dataType: 'json',
				success: function(datas){
					if (datas.status == 0){
						if (datas.list.length > 0){
							for (var key in datas.list){
								$('#userlisttpl').tmpl(datas.list[key].testers).appendTo($('#t_'+datas.list[key].cid+'_'+datas.list[key].sid).find('td.tester'));
							}
						}
					}
				}
			});
		}else {
			$( "#testerlist").html('<tr><td colspan="6" style="text-align: left">尚未找到游戏服测试号。</td></tr>').show();
		}
		mask.clear('container');
	}
}

$(function(){
	Ha.page.getList(1);
	/**
	 * 刷新测试号
	 * @return {[type]} [description]
	 */
	$("#testerlist").on('click', 'a.refresh', function(){
		var obj = $(this), sid = obj.attr('sid'), lcid = obj.attr('cid');
		if (sid > 0){
			var url = '<?php echo INDEX; ?>?m=operation&c=interactive&v=tester_refresh';
			Ha.common.ajax(url, 'json', {cid: lcid, sid: sid}, 'get', function(data){
				if (data.status == 0){
					obj.parent().siblings('td.tester').empty();
					$('#userlisttpl').tmpl(data.list).appendTo(obj.parent().siblings('td.tester'));
				}
			});
		}
	});
	/**
	 * 移除单个测试号
	 * @return {[type]} [description]
	 */
	$('#testerlist').on('click', 'a.remove', function(){
		var obj = $(this), id = obj.attr('data-id'), username = obj.attr('data-name'), nickname = obj.attr('data-nick');
		var cid = obj.parent().parent().find('input[name^="cid"]').val();
		var sid = obj.parent().parent().find('input[name^="sid"]').val();

		if (id > 0 && cid>0 && sid>0 && confirm('你确定删除测试号【'+nickname+'】吗？')){
			var url = '<?php echo INDEX; ?>?m=operation&c=source&v=give';
			var queryData = {id: id, key: 'set_tester', player_type: 1, tid: 5, cid: cid, sid: sid, doSubmit: 1, player: username};
			Ha.common.ajax(url, 'json', queryData, 'POST', function(data){
				if (data.status == 0){
					obj.remove();
				}
			});
		}
		return false;
	});
	/**
	 * 移除单个服务所有测试号
	 * @return {[type]} [description]
	 */
	$('#testerlist').on('click', 'a.clear', function(){
		var obj = $(this), servername = obj.attr('data-name');
		var cid = obj.parent().parent().find('input[name^="cid"]').val();
		var sid = obj.parent().parent().find('input[name^="sid"]').val();

		if (cid>0 && sid>0 && confirm('你确定清除服务器【'+servername+'】下所有测试号吗？')){
			var url = '<?php echo INDEX; ?>?m=operation&c=source&v=give_more';
			var queryData = {cid: cid, sid: sid, op_type: 2};
			Ha.common.ajax(url, 'json', queryData, 'POST', function(data){
				if (data.status == 0){
					$('#t_'+cid+'_'+sid).empty();
				}
			});
		}
		return false;
	});

	/**
	 * 选择所有
	 * @return {[type]} [description]
	 */
	$('#checkall').on('change', function(){
		if ($(this).is(':checked')){
			$('input:checkbox', $('#testerlist')).each(function(){
				$(this).attr('checked', 'checked');
			});
		}else {
			$('input:checkbox', $('#testerlist')).each(function(){
				$(this).removeAttr('checked');
			});
		}
	});

	$('#post_submit').on('submit', function(e){
		e.preventDefault();
		$('#btnsubmit').attr('disabled', 'disabled');
		var objform = $(this), op_type = $('#op_type').val();
		if (confirm('您确定执行此批量操作？')){

			var url = '<?php echo INDEX; ?>?m=operation&c=source&v=give_more';
			var queryData = objform.serialize();
			Ha.common.ajax(url, 'json', queryData, 'POST', function(data){
				if (data.status == 0){
					if (op_type == 2){
						$(':checked[name^="sid"]').each(function(){
							$(this).parent().parent().find('td.tester').empty();
						});
					}
				}
			});
		}
		return false;
	});
});
</script>
<script type="text/template" id="userlisttpl">
	<a href="javascript:;" data-id="${id}" data-name="${username}" data-nick="${nickname}" class="remove">${nickname}</a>&nbsp;&nbsp;
</script>
<script type="text/template" id="testerlisttpl">
<tr id="t_${cid}_${sid}">
	<td class="num">
	<input type="checkbox" name="sid[]" value="${sid}">
	<input type="hidden" name="cid[]" value="${cid}">
	</td>
	<td>${name}-${o_name}</td>
	<td class="tester">&nbsp;</td>
	<td>
		<a href="javascript:;" class="clear" data-name="${name}"><?php echo Lang('clear').Lang('tester') ?></a>
		<a href="javascript:;" class="refresh" cid="${cid}" sid="${sid}"><?php echo Lang('refresh').Lang('tester') ?></a>
	</td>
</tr>
</script>


<h2><span id="tt"><?php echo Lang('tester'); ?></span></h2>
<div class="container" id="container">
	<form name="post_submit" id="post_submit" method="post">
	<div class="column cf" id="table_column">
		<div id="dataTable">
		<table>
			<thead>
			    <tr>
			    	<th class="num"><input type="checkbox" name="checkall" id="checkall" value="1"></th>
					<th><?php echo Lang('server'); ?></th>
					<th><?php echo Lang('tester'); ?></th>
					<th><?php echo Lang('operation'); ?></th>
			    </tr>
			</thead>
			<tbody id="testerlist">
			   
			</tbody>
		</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
	</div>

	<div class="frm_cont" id="submit_area">
	    <ul>
	        <li>
	            <span class="frm_info"><em>*</em><?php echo Lang('op_type') ?>：</span>
				<select name="op_type" id="op_type" class="ipt_select">
					<option value="1"><?php echo Lang('give') ?></option>
					<option value="2"><?php echo Lang('clear').Lang('tester') ?></option>
				</select>
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('give').Lang('ingot') ?>：</span>
				<input type="text" name="ingot" id="ingot" class="ipt_txt">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('give').Lang('coins') ?>：</span>
				<input type="text" name="coins" id="coins" class="ipt_txt">
	        </li>
	        <li>
	            <span class="frm_info"><?php echo Lang('change_vip_level') ?>：</span>
				<select name="vip_level" id="vip_level" class="ipt_select">
					 <option value="-1"><?php echo Lang('does_not_operation') ?></option>
					 <option value="0"><?php echo Lang('cancel_vip') ?></option>
					 <option value="1">VIP1</option>
					 <option value="2">VIP2</option>
					 <option value="3">VIP3</option>
					 <option value="4">VIP4</option>
					 <option value="5">VIP5</option>
					 <option value="6">VIP6</option>
					 <option value="7">VIP7</option>
					 <option value="8">VIP8</option>
					 <option value="9">VIP9</option>
					 <option value="10">VIP10</option>
					 <option value="11">VIP11</option>
					 <option value="12">VIP12</option>
				</select>
	        </li>
           <li>
                <span class="frm_info">&nbsp;</span>
                <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
    			<input type="reset" id="btnreset" class="btn_rst" value="<?php echo Lang('reset'); ?>">
            </li>	        
        </ul>
    </div>
	</form>
</div>
