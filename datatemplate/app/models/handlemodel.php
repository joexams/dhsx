<?php

namespace Models;
use \Models\UserModel;

class HandleModel extends Model
{
    function __construct() {
        $this->table_name = 'log_handle';
        parent::__construct();
    }
    /**
     * 创建
     * @param  array  $insertarr [description]
     * @return [type]            [description]
     */
    function create($insertarr = array())
    {
    	if (!$insertarr)	return false;

		$this->reset();
		$this->user_id = $this->base->get('SESSION.user_id');
		$this->permission = $this->base->get('PARAMS.0');
		$this->title = $insertarr['title'];
		$this->content = $insertarr['content'];
		$this->create_time = time();
		$this->save();

		return $this->id;
    }
}