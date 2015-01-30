<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var typeflag = 0, typelist;
function getsoulList(){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_info",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&key=soul";
	$( "#info_list" ).fadeOut( "medium", function () {
		$.ajax({
			dataType: "json",
			url: query,
			data: data,
			success: function(data){
				if (data.status == 1){
					$('#info_list').html(data.msg);
				}else {
					if (data.type != undefined){
						typelist = data.type;
					}
					if (data.count > 0){
						$( "#listtpl" ).tmpl( data.list ).prependTo( "#info_list" );
						$( "#info_list" ).stop(true,true).hide().slideDown(400);
					}
				}
			}
		});
	});
	return false;
}

function soul_id_to_name(id){
	if (typelist != undefined){
		if (typelist.soul != undefined && typelist.soul.length > 0){
			for (var key in typelist.soul){
				if (typelist.soul[key].id == id){
					return typelist.soul[key].name + '('+typelist.soul[key].qualityname+')';
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
					if (typelist.attribute[key].unit < 1){
						attrname +='%';
					}
					return attrname;
				}
			}
		}
	}
	return '-';
}
function type_id_to_name(id){
	if (typelist != undefined){
		if (typelist.soul != undefined && typelist.soul.length > 0){
			for (var key in typelist.soul){
				if (typelist.soul[key].id == id){
					return typelist.soul[key].typename;
				}
			}
		}
	}
	return '';
}
$(document).ready(function(){
	if (dialog != undefined){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']); ?>"+'->玩家灵件');
	}
	getsoulList();
	typelist = null;
});
</script>
<script type="text/template" id="listtpl">
<tr>
<td>${id}</td>
<td>${type_id_to_name(soul_id)}</td>
<td>${soul_id_to_name(soul_id)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_1, soul_attribute_value_location_1)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_2, soul_attribute_value_location_2)}</td>
<td>${attr_id_to_name(soul_attribute_id_location_3, soul_attribute_value_location_3)}</td>
{{if typeof soul_attribute_id_location_4 != 'undefined'}}
<td>${attr_id_to_name(soul_attribute_id_location_4, soul_attribute_value_location_4)}</td>
{{else}}
<td>-</td>
{{/if}}
<td>${soul_pack_location}</td>
<td>${key}</td>
<td>&nbsp;</td>
</tr>
</script>

<div id="bgwrap">
	<div class="content">
		<table class="global" width="100%" style="max-width:800px;min-width:600px;" cellpadding="0" cellspacing="0">
			<thead>
			    <tr>
			    <th style="width:50px;">ID</th>
			    <th><?php echo Lang('soul'); ?></th>
			    <th><?php echo Lang('type'); ?></th>
			    <th><?php echo Lang('attribute'); ?>1</th>
			    <th><?php echo Lang('attribute'); ?>2</th>
			    <th><?php echo Lang('attribute'); ?>3</th>
			    <th><?php echo Lang('attribute'); ?>4</th>
			    <th><?php echo Lang('position'); ?></th>
			    <th><?php echo Lang('open_priv'); ?></th>
			    <th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="info_list">
				
			</tbody>
		</table>
	</div>
</div>
