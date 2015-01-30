<?php

namespace Models;

class Model extends \DB\SQL\Mapper
{
    protected $db = '';
	protected $base;
	protected $table_name = '';
	public  $table_prefix = 'fk_';

	public function __construct()
	{
        $this->base = \Base::instance();
        $this->db = $this->base->get('db');
        $this->table_prefix = $this->base->get('prefix') ?: $this->table_prefix;
		if ($this->table_name && $this->db) {
            $this->table_name = $this->table_prefix.$this->table_name;
			parent::__construct($this->db, $this->table_name);
		}
	}
    /**
     * 获取分页
     * @param  integer $page    [description]
     * @param  string  $filter  [description]
     * @param  array   $options [description]
     * @param  integer $perpage [description]
     * @return [type]           [description]
     */
	public function getPageList($page = 0, $filter = '', $options = array(), $perpage = 20)
	{
		$page = $page > 0 ? $page - 1 : 0;
        $list = $this->paginate($page, $perpage, $filter, $options);
        $data = array();
        if ($list && $list['total'] >0) {
            $data['total'] = $list['total'];
            $data['limit'] = $list['limit'];
            $data['count'] = $list['count'];
            $data['pos']   = $list['pos'];
            foreach ($list['subset'] as $key => $value) {
                foreach ($value->fields as $field => $val) {
                    $data['list'][$key][$field] = $value->$field;
                }
            }
        }

        return $data;
	}
}