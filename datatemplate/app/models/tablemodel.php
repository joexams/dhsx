<?php

namespace Models;

class TableModel extends Model {
    function __construct() {
    	$this->table_name = 'table';
        parent::__construct();
    }
    /**
     * 创建表栏目
     * @return [type] [description]
     */
    function create()
    {
        $parentid = $this->base->get('POST.parentid');
        $depth = 0;
        if ($parentid > 0) {
            $this->load(array('id=?', $parentid));
            $depth = $this->depth + 1;
        }

        $this->db->begin();
        try{
    		$this->reset();
            $this->parentid = $parentid;
            $this->db_id = $this->base->get('POST.db_id') ?: 0;
            $this->name = $this->base->get('POST.name');
            $this->querystring = $this->base->get('POST.querystring');
            $this->comment = $this->base->get('POST.comment');
    		$this->orderby = $this->base->get('POST.orderby');
            $this->depth = $depth;
            $this->sort = $this->base->get('POST.sort') ?: 0;
    		$this->created_at = time();
    		$this->save();
            $table_id = $this->id;
            $table_name = $this->name;

            $fields = $this->base->get('POST.field');
            if ($table_id > 0 && $table_name && $fields) {

                $fieldsmodel = new \Models\TableFieldsModel();

                $sql = "INSERT INTO ".$fieldsmodel->table_name."(table_id, table_name, field_name, field_comment, tips, target_sql, display, search, required, default_value, fixed_value, input_type, input_width, connect_table) VALUES ";
                $insert_arr = array();
                foreach ($fields as $key => $value) {
                    $insert_arr[] = "($table_id, '$table_name', '".$value['field_name']."', '".$value['field_comment']."', '".$value['tips']."', '".$value['target_sql']."', '".(isset($value['display'])?$value['display']:0)."', '".(isset($value['search'])?$value['search']:0)."', '".(isset($value['required'])?$value['required']:0)."', '".$value['default_value']."', '".$value['fixed_value']."', '".$value['input_type']."', '".$value['input_width']."', '".$value['connect_table']."')";
                 }
                 $sql .= implode(',', $insert_arr);
                 $fieldsmodel->db->exec($sql);
            }

            $this->db->commit();
            return $this->id;
        }catch(Exception $e){
            $this->db->rollback();
        }
		return $this->id;
    }
    /**
     * 栏目修改
     * @return [type] [description]
     */
    function modify(){
        $tableInfo = $this->load(array('id=?', $this->base->get('POST.id')));
        if (!$tableInfo) return false;

        if ($tableInfo->parentid != $this->base->get('POST.parentid')) {
            if ($this->base->get('POST.parentid') > 0) {
                $parent_info = $this->findone(array('id=?', $this->base->get('POST.parentid')));
                $this->depth = $parent_info->depth + 1;
            }else {
                $this->depth = 0;
            }
        }

        $this->parentid = $this->base->get('POST.parentid');
        $this->name = $this->base->get('POST.name');
        $this->querystring = $this->base->get('POST.querystring');
        $this->orderby = $this->base->get('POST.orderby');
        $this->comment = $this->base->get('POST.comment');
        $this->sort = $this->base->get('POST.sort');
        $this->save();

        $table_id = $this->id;
        $fields = $this->base->get('POST.field');
        $table_name = $this->base->get('POST.name');

        if ($table_id > 0 && $table_name && $fields) {
            $fieldsmodel = new \Models\TableFieldsModel();
            $insert_arr = array();
            foreach ($fields as $key => $value) {
                if ($fieldsmodel->count("table_id='$table_id' AND field_name='".$value['field_name']."'") > 0) {
                    $fieldsmodel->db->exec("UPDATE ".$fieldsmodel->table_name." SET field_comment='".$value['field_comment']."', tips='".$value['tips']."', target_sql='".$value['target_sql']."', display='".(isset($value['display'])?$value['display']:0)."', search='".(isset($value['search'])?$value['search']:0)."', required='".(isset($value['required'])?$value['required']:0)."', default_value='".$value['default_value']."', fixed_value='".$value['fixed_value']."', input_type='".$value['input_type']."', input_width='".$value['input_width']."',connect_table='".$value['connect_table']."' WHERE table_id='$table_id' AND field_name='".$value['field_name']."'");
                }else {
                    $insert_arr[] = "($table_id, '$table_name', '".$value['field_name']."', '".$value['field_comment']."', '".$value['tips']."', '".$value['target_sql']."', '".(isset($value['display'])?$value['display']:0)."', '".(isset($value['search'])?$value['search']:0)."', '".(isset($value['required'])?$value['required']:0)."', '".$value['default_value']."', '".$value['fixed_value']."', '".$value['input_type']."', '".$value['input_width']."', '".$value['connect_table']."')";
                }
             }
             if ($insert_arr) {
                $sql = "INSERT INTO ".$fieldsmodel->table_name."(table_id, table_name, field_name, field_comment, tips, target_sql, display, search, required, default_value, fixed_value, input_type, input_width, connect_table) VALUES ";
                 $sql .= implode(',', $insert_arr);
                 $fieldsmodel->db->exec($sql);
             }
        }
        return $table_id;
    }
    /**
     * 删除
     * @return [type] [description]
     */
    function delete(){
        $table_id = $this->base->get('GET.id');
        $this->db->begin();
        try{
            $list = $this->getTableList();
            $tree = \Tree::instance();
            $tree->init($list);
            $child_arr = $tree->get_all_child($table_id);
            if ($child_arr) {
                $ids = implode(',', $child_arr);
                $ids .= ','.$table_id;
            }else {
                $ids = $table_id;
            }
            $this->db->exec('DELETE FROM dt_table WHERE id in ('.$ids.')');
            $this->db->exec('DELETE FROM dt_table_fields WHERE table_id in ('.$ids.')');
            $this->db->commit();
            return true;
        }catch(Exception $e){
            $this->db->rollback();
        }
        return false;
    }
    /**
     * 所有权限列表
     * @return [type] [description]
     */
    function getTableList(){
    	$list = $this->find();
    	$menu = array();
    	if ($list) {
    		foreach ($list as $key => $value) {
                $menu[$value->id] = array();
    			$menu[$value->id]['id'] =  $value->id;
                $menu[$value->id]['name'] =  $value->comment;
                $menu[$value->id]['parentid'] =  $value->parentid;
                $menu[$value->id]['level'] =  $value->depth;
                $menu[$value->id]['tablename'] =  $value->name;
                $menu[$value->id]['querystring'] =  $value->querystring;
    			$menu[$value->id]['parentid_node'] =  $value->parentid ? '  data-tt-parent-id="'.$value->parentid.'"' : '';
    		}
    	}
    	return $menu;
    }
}