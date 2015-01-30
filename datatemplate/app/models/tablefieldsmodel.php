<?php

namespace Models;

class TableFieldsModel extends Model {
    function __construct() {
    	$this->table_name = 'table_fields';
        parent::__construct();
    }
    /**
     * 获取数据表字段配置
     * @param  [type] $table_name [description]
     * @return [type]             [description]
     */
    function getFields($table_name)
    {
    	$list = array();
    	$fields = $this->find("table_name='$table_name'");
		if ($fields) {
			foreach ($fields as $key => $value) {
				$list[$value['field_name']] = $value;
			}
		}
		return $list;
    }
}