<?php

class Tree extends Prefab{
	/**
	* 生成树型结构所需要的2维数组
	* @var array
	*/
	public $arr = array();

	/**
	* 生成树型结构所需修饰符号，可以换成图片
	* @var array
	*/
	public $icon = array('┃','┣','┗');
	public $nbsp = "&nbsp;";

	/**
	* @access private
	*/
	public $ret = '';

	/**
	* 构造函数，初始化类
	* @param array 2维数组，例如：
	* array(
	*      1 => array('id'=>'1','parentid'=>0,'name'=>'一级栏目一'),
	*      2 => array('id'=>'2','parentid'=>0,'name'=>'一级栏目二'),
	*      3 => array('id'=>'3','parentid'=>1,'name'=>'二级栏目一'),
	*      4 => array('id'=>'4','parentid'=>1,'name'=>'二级栏目二'),
	*      5 => array('id'=>'5','parentid'=>2,'name'=>'二级栏目三'),
	*      6 => array('id'=>'6','parentid'=>3,'name'=>'三级栏目一'),
	*      7 => array('id'=>'7','parentid'=>3,'name'=>'三级栏目二')
	*      )
	*/
	public function init($arr=array()){
       $this->arr = $arr;
	   $this->ret = '';
	   return is_array($arr);
	}
    /**
	* 得到父级数组
	* @param int
	* @return array
	*/
	public function get_parent($myid){
		$newarr = array();
		if(!isset($this->arr[$myid])) return false;
		$pid = $this->arr[$myid]['parentid'];
		$pid = $this->arr[$pid]['parentid'];
		if(is_array($this->arr)){
			foreach($this->arr as $id => $a){
				if($a['parentid'] == $pid) $newarr[$id] = $a;
			}
		}
		return $newarr;
	}
	/**
	* 得到子级数组
	* @param int
	* @return array
	*/
	public function get_child($myid){
		$a = $newarr = array();
		if(is_array($this->arr)){
			foreach($this->arr as $id => $a){
				if($a['parentid'] == $myid) $newarr[$id] = $a;
			}
		}
		return $newarr ? $newarr : false;
	}
	/**
	 * 获得所有子级数组
	 * @param  [type] $myid [description]
	 * @return [type]       [description]
	 */
	public function get_all_child($myid){
		static $newarr = array();
		$a = array();
		if(is_array($this->arr)){
			foreach($this->arr as $id => $a){
				if($a['parentid'] == $myid) {
					$newarr[$id] = $a;
					$this->get_all_child($id);
				}
			}
		}
		return $newarr ? $newarr : false;
	}
    /**
	* 得到树型结构
	* @param int ID，表示获得这个ID下的所有子级
	* @param string 生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>"
	* @param int 被选中的ID，比如在做树型下拉框的时候需要用到
	* @return string
	*/
	public function get_tree($myid, $str, $sid = 0, $adds = '', $str_group = ''){
		$number=1;
		$child = $this->get_child($myid);
		if(is_array($child)){
		    $total = count($child);
			foreach($child as $id=>$value){
				$j=$k='';
				if($number==$total){
					$j .= $this->icon[2];
				}else{
					$j .= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer = $adds ? $adds.$j : '';
				$selected = $id==$sid? 'selected' : '';
				@extract($value);
				$parentid == 0 && $str_group ? eval("\$nstr = \"$str_group\";") : eval("\$nstr = \"$str\";");
				$this->ret .= $nstr;
				$nbsp = $this->nbsp;
				$this->get_tree($id, $str, $sid, $adds.$k.$nbsp,$str_group);
				$number++;
			}
		}
		return $this->ret;
	}
	/**
	* 得到当前位置数组
	* @param int
	* @return array
	*/
	public function get_position($myid, &$newarr){
		$a = array();
		if(!isset($this->arr[$myid])) return false;
        $newarr[] = $this->arr[$myid];
		$pid = $this->arr[$myid]['parentid'];
		if(isset($this->arr[$pid])){
		    $this->get_position($pid,$newarr);
		}
		if(is_array($newarr)){
			krsort($newarr);
			foreach($newarr as $v){
				$a[$v['id']] = $v;
			}
		}
		return $a;
	}
}