<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var coupon = {
	pageIndex: 1,
	pageCount: 0,
	pageSize: 15,
	recordNum: 0,
	showList: function(data, obj) {
		if (data.status == 0){
			obj.recordNum = data.count;
			obj.pageCount = Math.ceil( data.count/obj.pageSize );
			$( "#couponcodepager" ).pager({ pagenumber: obj.pageIndex, pagecount: obj.pageCount, word: pageword, buttonClickCallback: obj.getList });
			$( "#couponcodelist" ).empty();

			if (data.count > 0){
				$( "#couponcodelisttpl" ).tmpl( data.list ).prependTo( "#couponcodelist" );
				$( "#couponcodelist" ).stop(true,true).hide().slideDown(400);
				if (obj.pageCount > 1){
					$( "#couponcodelist" ).parent().parent('div.content').css('height', $('#couponcodelist').parent('table.global').css('height'));
				}
			}
		}else {
			$('#couponcodelist').html(data.msg);
		}
	},
	getList: function(index) {
		var obj = this;
		var query = "<?php echo INDEX; ?>?m=report&c=coupon&v=ajax_show_list&top="+index+"&recordnum="+this.recordNum;
		this.pageIndex = index;
		$( "#couponcodelist" ).fadeOut( "medium", function () {
			$.ajax({
				dataType: "json",
				url: query,
				data: {id: '<?php echo $data['id']; ?>'},
				success: function(data) {
					obj.showList(data, obj);
				}
			});
		});
	}
};
$(function() {
	coupon.getList(1);
});
</script>

<script type="text/template" id="couponcodelisttpl">
<tr>
	<td>${id}</td>
	<td></td>
	<td>${code}</td>
	<td></td>
	<td>${ctime}</td>
	<td>{{if player_id > 0}}${username}{{if nickname != ''}}(${nickname}){{/if}}{{else}}-{{/if}}</td>
	<td></td>
	<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th style="width:50px;">ID</th>
					<th style="width:100px;"><?php echo Lang('server'); ?></th>
					<th style="width:80px;"><?php echo Lang('coupon'); ?></th>
					<th style="width:80px;"><?php echo Lang('get_content'); ?></th>
					<th style="width:120px;"><?php echo Lang('receive_time'); ?></th>
					<th style="width:120px;"><?php echo Lang('receive_user'); ?></th>
					<th style="width:120px;"><?php echo Lang('operation'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="couponcodelist">

			</tbody>
		</table>
	</div>
	<div class="pagination" id="couponcodepager"></div>
</div>