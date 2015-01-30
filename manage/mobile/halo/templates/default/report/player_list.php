<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
Ha.page.recordNum = 0;
Ha.page.pageCount = 0;
Ha.page.pageSize = 20;
Ha.page.listEid = 'playerlist';
Ha.page.colspan = 8;
Ha.page.emptyMsg = '没有找到玩家数据。';
Ha.page.url = "<?php echo INDEX; ?>?m=server&c=get&v=public_player_list";
Ha.page.queryData = {sid: <?php echo $data['sid'] ?>, cid: <?php echo $data['cid'] ?>, order: ''};

var playerlist;
var dialog = typeof dialog != undefined ? null : '';
$(function(){
	Ha.page.getList(1, function(data){
		playerlist = data.list;
	});

	$('#selectOrder').on('click', 'a.order', function(){
		$('.active', $('#selectOrder')).removeClass('active');
		var order = $(this).attr('data');
		$(this).addClass('active');

		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize()+'&order='+$(this).attr('data');
		Ha.page.getList(1);
	});

	$('#get_search_submit').on('submit', function(e){
		e.preventDefault();
		Ha.page.recordNum = 0;
		Ha.page.queryData = $('#get_search_submit').serialize();
		Ha.page.getList(1);
	});


	$('#moreQuery').on('click', function(e){
		$('#moreConditions').toggle();
	});
});
</script>

<script type="text/template" id="playerlisttpl">
<tr {{if nickname != ''}}data-id="${id}"{{else}}title="未创建角色"{{/if}}>
<td class="num"><?php if (!$limit) { ?>${id}<?php } ?>&nbsp;</td>
<td>
{{if nickname != ''}}
<a href="#app=4&cpp=35&url=${encodeurl('report', 'player_info', 'player', '&sid=<?php echo $data['sid'] ?>&id=')}${id}" data-id="${id}" data-username="${username}" class="userinfo">${username}<br><span class="bluetitle">${nickname}</span></a>
{{else}}
${username}
{{/if}}

{{if vip_level > 0}}
<img class="grade lvl_img" src="<?php echo WEB_URL; ?>static/images/V${vip_level}.png">
{{/if}}

<br>
{{if last_offline_time > 0}}${date('Y-m-d H:i', last_offline_time)}<br>{{/if}}
</td>
<td>
<strong class="greentitle">${ingot}</strong>
</td>
<td>${coin}</td>
<td>${power}</td>
<td>${last_login_ip}<br>
${date('Y-m-d H:i', last_login_time)}</td>
</tr>
</tr>
</script>
<h2><span id="tt"><?php echo Lang('玩家数据'); ?>：
	<a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init') ?>"><?php echo Lang('game_server') ?></a>
	<span>&gt;</span><?php echo $data['title']; ?>
	<span>&gt;</span><?php echo Lang('player_list') ?>
</span></h2>
<div class="container" id="container">
	    <?php include template('report', 'player_top'); ?>

	<div class="toolbar">
		<div class="tool_date cf">
			<form id="get_search_submit" action="" method="get" name="form">
			<div class="title cf">	
				<div class="tool_group">
					<label><?php echo Lang('player'); ?>：<input type="text" class="ipt_txt_l" name="username"></label>
					<input name="cid" type="hidden" value="<?php echo $data['cid'] ?>">
					<input name="sid" type="hidden" value="<?php echo $data['sid'] ?>">
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="submit" class="btn_sbm" value="查询" id="query"> 
					<input type="reset" class="btn_rst" value="重置" id="reset">
				</div>
				<div class="more">
					<a href="javascript:;" id="moreQuery"><i class="i_triangle"></i>高级查询</a>
				</div>
			</div>
			<div class="control cf" id="moreConditions" style="display: ;">
				<div class="frm_cont">
					<ul>
						<li name="condition">
							<label class="frm_info">附加条件：</label>
							<select name="vip" class="ipt_select" style="width:100px;">
								 <option value="">VIP范围</option>
								 <option value="1">VIP1 以上</option>
								 <option value="2">VIP2 以上</option>
								 <option value="3">VIP3 以上</option>
								 <option value="4">VIP4 以上</option>
								 <option value="5">VIP5 以上</option>
								 <option value="6">VIP6 以上</option>
								 <option value="7">VIP7 以上</option>
								 <option value="8">VIP8 以上</option>
								 <option value="9">VIP9 以上</option>
								 <option value="10">VIP10 以上</option>
								 <option value="11">VIP11 以上</option>
								 <option value="12">VIP12 以上</option>
							</select>
						</li>
						<li name="condition" id="selectOrder">
							<label class="frm_info">排序：</label>
							<a href="javascript:;" class="order active" data=""><?php echo Lang('all') ?></a>
							<a href="javascript:;" class="order" data="vip">VIP</a>
							<a href="javascript:;" class="order" data="ingot"><?php echo Lang('ingot') ?></a>
							<a href="javascript:;" class="order" data="coin"><?php echo Lang('coins') ?></a>
						</li>
					</ul>
				</div>
			</div>
		</form>
	</div>		
</div>
<div class="column cf" id="table_column">
	<div id="dataTable">
	<table>
	<thead>
	<tr id="playerlistheader">
    	<th>&nbsp;</th>
	    <th><?php echo Lang('player_name') ?>/<?php echo Lang('player_nick') ?></th>
	    <th><?php echo Lang('over_total_ingot') ?></th>
	    <th><?php echo Lang('coins') ?></th>
	    <th><?php echo Lang('power') ?></th>
	    <th><?php echo Lang('last_login_time') ?></th>
	</tr>
	</thead>
	<tbody id="playerlist">
		   
	</tbody>
	</table>
	</div>
	<div id="pageJs" class="page">
        <div id="pager" class="page">
        </div>
	</div>
</div>
</div>