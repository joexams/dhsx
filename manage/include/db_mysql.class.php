<?php

/*
	[Discuz!] (C)2001-2009 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms
	数据连接类
	$Id: db_mysql.class.php 16688 2008-11-14 06:41:07Z cnteacher $
*/

if(!defined('IN_UCTIME')) {
	exit('Access Denied');
}

class mysql {

	var $version = '';
	var $querynum = 0;
	var $link = null;

	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE, $dbcharset2 = '') {

		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		if(!$this->link = @$func($dbhost, $dbuser, $dbpw, 1)) {
			$halt && $this->halt('Can not connect to SQL server');
		} else {
			if($this->version() > '4.1') {
				global $charset, $dbcharset;
				$dbcharset = $dbcharset2 ? $dbcharset2 : $dbcharset;
				$dbcharset = !$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $this->link);
			}
			$dbname && @mysql_select_db($dbname, $this->link);
		}

	}

	function select_db($dbname) {
		return mysql_select_db($dbname, $this->link);
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}

	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}

	function query($sql, $type = '') {

		global $debug, $discuz_starttime, $sqldebug, $sqlspenttimes;

		if(defined('SYS_DEBUG') && SYS_DEBUG) {
			@include_once UCTIME_ROOT.'./include/debug.func.php';
			sqldebug($sql);
		}

		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
			'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql, $this->link))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				$this->close();
				require UCTIME_ROOT.'./config.inc.php';
				$this->connect($mydbhost, $mydbuser, $mydbpw, $mydbname, $mypconnect, true, $mydbcharset);
				//$this->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
				$this->query($sql, 'RETRY'.$type);
			} elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt('SQL Query Error', $sql);
			}
		}

		$this->querynum++;
		return $query;
	}

	function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	function error() {
		return (($this->link) ? mysql_error($this->link) : mysql_error());
	}

	function errno() {
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
	}

	function result($query, $row = 0) {
		$query = @mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) {
		return mysql_fetch_field($query);
	}

	function version() {
		if(empty($this->version)) {
			$this->version = mysql_get_server_info($this->link);
		}
		return $this->version;
	}

	function close() {
		return mysql_close($this->link);
	}

	function halt($message = '', $sql = '') {
		if ($GLOBALS['adminWebType'] == 's' || $GLOBALS['adminWebName'] == '1wan') 
		{
			$s = '<br>'.$sql;
		}
		//if ($GLOBALS['adminWebType'] != 's') 
		//{
			$page = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];//当前地址
			//$dir = UCTIME_ROOT."/log/";//生成目录
			//$filename = date('Y-m-d')."_sql.txt";//文件名
			//$ccc .= "[".date('H:i:s').' | '.$message.' | '.$page."]\n".$sql."\n\n";
			SetSqlLog($sql,$page,$message);
			//writetofile($filename,$ccc,'a',$dir);//写入
		//}
		
        if(!$sql) {
			echo $message;
			exit();
        }else{
			echo $message.$s;	
			exit();
		}		
	}
}

?>