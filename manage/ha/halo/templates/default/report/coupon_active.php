<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'couponlist';
Ha.page.colspan = 5;
Ha.page.emptyMsg = '没有找到数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=report&c=coupon&v=active";

$(function() {
	/**
	 * 运营平台
	 */
	if (typeof global_companylist != 'undefined') {
		$('#global_companylisttpl').tmpl(global_companylist).appendTo('#cid');
	}


	$('#cid').on('change', function(){
		var cid = $(this).val();
		if (cid > 0 && typeof global_serverlist != 'undefined'){
			$('#sid option[value!="0"]').remove();
			$('#global_serverlisttpl').tmpl(getServerByCid(cid)).appendTo('#sid');
		}else {
			$('#sid option[value!="0"]').remove();
		}
	});

	/**
	 * 搜索
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	$('#get_search_submit').on('submit', function(e) {
		e.preventDefault();
		var objform = $(this);
		var codeid = $('#codeid').val();
		if (codeid != '0'){
			Ha.page.queryData = $('#get_search_submit').serialize();
			Ha.page.recordNum = 0;
			Ha.page.getList();
		}else {
			Ha.notify.show('请选择活动', '', 'error');
		}
	});
	
	/**
	 * 导出
	 */
	$('#exportFile').on('click', function(e) {
		var codeid = $('#codeid').val();
		if (codeid != '0'){
			var url = "<?php echo INDEX; ?>?m=report&c=coupon&v=export_active&"+$('#get_search_submit').serialize();
			location.href = url;
		}else {
			Ha.notify.show('请选择活动', '', 'error');
		}
	});

	$('#codeid').on('change', function() {
		var code = $(this).val(), times = $('option:selected', $(this)).attr('data');
		var str = '';
		if (code != '0') {
			times = isNaN(parseInt(times)) ? 1 : parseInt(times);
			times = Math.max(times, 1);
			for(var i=1; i<=times; i++) {
				str += '<option value="'+i+'">'+i+'</option>';
			}
		}
		$('#times option[value!="0"]').remove();
		$('#times').append(str);
	});

	$('#moreQuery').on('click', function(e){
		$('#moreConditions').toggle();
	});
});
</script>

<script type="text/template" id="couponlisttpl">
<tr>
<td class="num">${id}</td>
<td>${cid_to_name(cid)}${sid_to_name(sid)}</td>
<td>${code}</td>
<td>${ctime}</td>
<td>{{if player_id > 0}}${username}(${nickname}){{else}}-{{/if}}</td>
</tr>
</script>


<h2>
	<span id="moreBar" class="more"><a id="exportFile" style="" href="javascript:void(0);">导出兑换券<i class="i_down"></i></a></span>
	<span id="tt"><?php echo Lang('active_coupon'); ?></span>
</h2>
<div class="container" id="container">
	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">	
				<div class="tool_group">
	            	<select name="table_name" id="codeid" class="table_name ipt_select" style="width:100px;">
	            		<option value="0"><?php echo Lang('select_active'); ?></option>
	            		<?php foreach ($activelist as $key => $value) {
	            			echo '<option value="'.$value['table_name'].'" data="'.$value['times'].'">'.$value['table_comment'].'</option>';
	            		} ?>
	            	</select>
	            	<select name="usetype" id="usetype" class="ipt_select" style="width:100px;">
	            		<option value="0">是否使用</option>
	            		<option value="1">使用</option>
	            		<option value="2">未使用</option>
	            	</select>

					<label><?php echo Lang('coupon'); ?>：<input type="text" class="ipt_txt" name="code"></label>
					<label><?php echo Lang('player_name'); ?>：<input type="text" class="ipt_txt" name="playername"></label>
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="submit" class="btn_sbm" value="<?php echo Lang('search'); ?>" id="query"> 
				</div>
				<div class="more">
					<a href="javascript:;" id="moreQuery"><i class="i_triangle"></i>高级查询</a>
				</div>
			</div>
			<div class="control cf" id="moreConditions" style="display: none;">
				<div class="frm_cont">
					<ul>
						<li name="condition">
							<label class="frm_info">附加条件：</label>
							<select name="cid" id="cid" class="ipt_select" style="width:100px;">
							<option value="0"><?php echo Lang('operation_platform') ?></option>
							</select>
							<select name="sid" id="sid" class="ipt_select" style="width:120px;">
							<option value="0"><?php echo Lang('all_server') ?></option>
							</select>
							<select name="times" id="times" class="ipt_select" style="width:100px;">
							<option value="0">批次</option>
							</select> 
						</li>
					</ul>
				</div>
			</div>
			</form>
		</div>		
	</div>
	<div class="column cf" id="table_column">
		<div class="title">详细数据</div>
		<div id="dataTable">
		<table>
		<thead>
		<tr id="dataTheadTr">
			<th>&nbsp;</th>
			<th><?php echo Lang('server'); ?></th>
			<th><?php echo Lang('coupon'); ?></th>
			<th><?php echo Lang('receive_time'); ?></th>
			<th><?php echo Lang('receive_user'); ?></th>
		</tr>
		</thead>
		<tbody id="couponlist">
			<tr><td colspan="5" style="text-align: left">请先选择活动进行查询。</td></tr>
		</tbody>
		</table>
		</div>
		<div id="pageJs" class="page">
	        <div id="pager" class="page">
	        </div>
		</div>
	</div>
</div>