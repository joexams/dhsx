<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var pageIndex = 1, pageCount = 0, pageSize = 20, recordNum = 0, typeid = 0, typeflag = 0, pid = "<?php echo $data['id'] ?>", list, typelist;
function getlogdeploy_startList(index){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_record&top="+index+"&recordnum="+recordNum;
	pageIndex = index;
	$( "#list_"+pid ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: $('#get_search_deploy_start_submit').serialize()+'&typeflag='+typeflag,
			success: showlogdeploy_startList
		});
	});
	return false;
}
function type_id_to_name(id){
	if (typelist != undefined){
		if (typelist != undefined && typelist.length > 0){
			for (var key in typelist){
				if (typelist[key].id == id){
					return typelist[key].name;
				}
			}
		}
	}
	return '';
}
function attr_id_to_name(id, value){
	var attrname = '';
	if (typelist != undefined){
		if (typelist.attribute != undefined && typelist.attribute.length > 0){
			for (var key in typelist.attribute){
				if (typelist.attribute[key].id == id){
					attrname =  typelist.attribute[key].name + '+'+value;
					return attrname;
				}
			}
		}
	}
	return '--';
}
function showlogdeploy_startList( data ) {
	if (data.status == 1){
		$('#list_'+pid).html(data.msg);
	}else {
		if (data.type != undefined){
			typeflag = 1;
			typelist = data.type;
			$('#deploy_starttypetpl').tmpl( data.type ).appendTo('#deploy_startget');
		}
		if (typeof data.chklist != 'undefined') {
			chklist = data.chklist;
		}
		
		recordNum = data.count;
		pageCount = Math.ceil( data.count/pageSize ), list = data.list;
		$( "#deploy_startpager" ).pager({ pagenumber: pageIndex, pagecount: pageCount, word: pageword, buttonClickCallback: getlogdeploy_startList });
		$('#list_'+pid).empty();
		if (data.count > 0){
			$( "#listtpl" ).tmpl( list ).prependTo( "#list_"+pid);
			$('#list_'+pid).stop(true,true).hide().slideDown(400);
			if (pageCount > 1){
				$('#list_'+pid).parent().parent('div.content').css('height', $('#list_'+pid).parent('table.global').css('height'));
			}
		}
	}
	return false;
}

$(document).ready(function(){
	if (typeof dialog != 'undefined'){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']).'->'; ?>"+'阵盘星记录');
	}
	
	$('.h_lib_nav').on('click', 'a.deploy_start_type', function(){
		var typeid = $(this).attr('data-id');
		if (typeid > 0) {
			if ($(this).parent('li').hasClass('hover') === true){
				$(this).parent('li').removeClass('hover');
				$('#typeid_'+typeid).remove();
			}else {
				$(this).parent('li').addClass('hover');
				$('#get_search_deploy_start_submit').append('<input type="hidden" id="typeid_'+typeid+'" name="typeid[]" value="'+typeid+'">');
			}
		}
		return false;
	});

	$('#get_search_deploy_start_submit').on('submit', function(e){
		e.preventDefault();
		recordNum = 0;
		getlogdeploy_startList( 1 );
	});
});
</script>
<script type="text/template" id="deploy_starttypetpl">
<li><a href="javascript:;" data-id="${id}" class="deploy_start_type">${name}</a><span></span></li>
</script>

<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${date('Y-m-d H:i', opt_time)}</td>
<td>{{if start_type == 1}}阵碎星{{else}}陨星{{/if}}</td>
<td>${attr_id_to_name(war_attribute_type_1, war_attribute_value_1)}</td>
<td>${attr_id_to_name(war_attribute_type_2, war_attribute_value_2)}</td>
<td>${attr_id_to_name(war_attribute_type_3, war_attribute_value_3)}</td>
<td>${type_id_to_name(opt_type)}</td>
<td>&nbsp;</td>
</tr>
</script>
<div id="bgwrap">
	<form id="get_search_deploy_start_submit" action="<?php echo INDEX; ?>?m=server&c=get&v=player_record" method="get" name="form">
	<div class="h_lib_nav">
	<ul id="deploy_startcons">
	</ul>
	<ul id="deploy_startget">
	</ul>
	</div>
	<br class="clear">
	<div class="nav singlenav">
		<ul class="nav_li">
			<?php if ($data['id'] <= 0 && empty($_GET['title'])){ ?>
			<li>
				<p>
					<?php echo Lang('player'); ?>：
					<select name="playertype">
						<option value=""><?php echo Lang('player_name'); ?></option>
						<option value="1"><?php echo Lang('player_nick'); ?></option>
					</select>
					<input type="text" name="playername">
				</p>
			</li>
			<?php } ?>
			<li>
				<p>
					<?php echo Lang('between_date'); ?>：
				<input name="starttime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00'})" type="text" value="">
					<input name="endtime" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59'})" type="text" value=""></p>
			</li>
			<li>
				<p>
					<?php echo Lang('db_table'); ?>：
					<select name="tbl" id="tbl">
						<option value="1"><?php echo Lang('new_db_table'); ?></option>
						<option value="0"><?php echo Lang('old_db_table'); ?></option>
					</select>
				</p>
			</li>
			<li class="nobg">
				<p>
					<input name="dogetSubmit" type="hidden" value="1">
					<input type="hidden" name="sid" value="<?php echo $data['sid'] ?>">
					<input type="hidden" name="id" value="<?php echo $data['id'] ?>">
					<input type="hidden" name="key" value="deploy_start">
					<input type="hidden" name="typeflag" value="1">
					<input type="submit" name="getsubmit" id="get_search_submit" value="<?php echo Lang('search'); ?>" class="button_link">
				</p>
			</li>
		</ul>
	</div>
	</form>
	<div class="content">
		<table class="global" width="100%" style="max-width:800px;min-width:600px;" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
				    <th style="width:50px;">ID</th>
				    <th style="width:120px;"><?php echo Lang('op_time') ?></th>
				    <th><?php echo Lang('deploy_start') ?></th>
				    <th><?php echo Lang('attribute') ?>1</th>
				    <th><?php echo Lang('attribute') ?>2</th>
				    <th><?php echo Lang('attribute') ?>3</th>
				    <th style="width:50px;"><?php echo Lang('open_priv') ?></th>
				    <th style="width:20%"><?php echo Lang('op_type') ?></th>
				    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="list_<?php echo $data['id'] ?>">
				
			</tbody>
		</table>
	</div>
	<div class="pagination" id="deploy_startpager"></div>
</div>
