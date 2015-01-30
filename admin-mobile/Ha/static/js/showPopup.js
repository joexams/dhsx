var baseText='';
var popUp='';
function showPopup(w,h,id,desc,url,fid,status,level,table_name){
	popUp = document.getElementById("popupcontent");
	var status1,status0,level1,level2;
	popUp.style.top = "100px";
	popUp.style.left = "200px";
	popUp.style.width = w + "px";
	popUp.style.height = h + "px";
	if (baseText == null) baseText = popUp.innerHTML;
	if (status == 1){
		status1 ='selected';
	}else if (status == 0){
		status0 ='selected';
	}
	if (level == 2){
		level1 ='selected';
	}else if (level == 3){
		level2 ='selected';
	}
	baseText = '<table class="table"><tr align="center" class="td2"><td width="35">ID</td><td width="150">菜单名称</td><td width="350">链接url</td><td width="150">父目录</td><td width="150">是否启用</td></tr><tr align="center"><td width="35"><input type="hidden" id="nid" value="'+id+'">'+id+'</td><td width="150"><input type="text" id="ndesc" value="'+desc+'"></td><td width="350"><input type="text" id="nurl" value="'+url+'" size=50></td><td width="150"><select id="nlevel" name="nlevel" onchange="select_father_menu(this.value,\'nfather_id\')"><option value="1" '+level1+'>一级目录</option><option value="2" '+level2+'>二级目录</option></select><select id="nfather_id"><option value="">根目录</option>';

	$.ajax({
		dataType: 'json',
		url: document.URL+"index.php?m=manage&c=index&v=edit",
		type: 'get',
		success: function(data){
			for(var i=0;i<data.length;i++){
				if (fid == data[i].id){
					baseText += '<option value='+data[i].id+' selected>'+data[i].describe+'</option>';
				}else{
					baseText += '<option value='+data[i].id+'>'+data[i].describe+'</option>';
				}
			}
			baseText += '</select></td><td width="150"><select id="nstatus"><option value="1" '+status1+'>启用</option><option value="0" '+status0+'>关闭</option></select></td></tr></table><br><table class="table"><tr><td>序号</td><td>字段</td><td>描述</td><td>类型</td><td>类型来源表</td><td>类型来源字段</td><td>类型来源条件</td></tr>';
			var queryData = 'table_name='+table_name;
			Ha.common.ajax(document.URL+"index.php?m=manage&c=index&v=get_column_info",'',queryData,'','',function afa(da){
				var column_num = da.list.length;
				for (var i=0;i<column_num;i++){
					baseText += "<tr id='"+da.list[i].id+"'><td>"+(i+1)+"</td><td id='column_name'>"+da.list[i].column_name+"</td><td id='column_desc'><span ondblclick='modify(this)'>"+da.list[i].column_desc+"</span></td><td id='column_type'><span ondblclick='modify(this)'>"+da.list[i].column_type+"</span></td><td id='type_table'><span ondblclick='modify(this)'>"+da.list[i].type_table+"</span></td><td id='type_column'><span ondblclick='modify(this)'>"+da.list[i].type_column+"</span></td><td id='type_where'><span ondblclick='modify(this)'>"+da.list[i].type_where+"</span></td></tr>";
				}
				popUp.innerHTML = baseText + "</table><div id=\"statusbar\" style=\"height:180px; width:100px; margin:0 auto;\"><input type=\"button\" value=\"提交\" onClick=\"submitPopup();\"><input type=\"button\" value=\"关闭\" onClick=\"hidePopup();\"></div>";
			var sbar = document.getElementById("statusbar");
			sbar.style.Top = (parseInt(h)-250) + "px";
			popUp.style.visibility = "visible";
			},'1');
			
		}
	});
}
function hidePopup(){
	 $.dialog.close();
}

function modify(self){
	var content = '<input type="text" value="'+self.innerHTML+'" onblur="comp(this)">';
	self.innerHTML = content;
}
function comp (self){
	
	var tid = self.parentNode.parentNode.parentNode.id;
	var where_c = self.parentNode.parentNode.id;
	var where_v = self.value;
	self.parentNode.innerHTML=self.value;
	var aurl = document.URL+"index.php?m=manage&c=index&v=upd_column_desc";
	var queryData = 'id='+tid+'&where_c='+where_c+'&where_v='+where_v;
	Ha.common.ajax(aurl,'',queryData);
}

function submitPopup(){
	var nid = $("#nid").val();
	var ndesc = $("#ndesc").val();
	var nurl = escape($("#nurl").val());
	var nfather_id = $("#nfather_id").val();
	var nstatus = $("#nstatus").val();
	var nlevel = $("#nlevel").val();
	var aurl = document.URL+"index.php?m=manage&c=index&v=update";
	var queryData = 'id='+nid+'&describe='+ndesc+'&url='+nurl+'&father_id='+nfather_id+'&status='+nstatus+'&level='+nlevel;
	hidePopup();
	Ha.common.ajax(aurl,'',queryData);
	Ha.page.getList(1);
}