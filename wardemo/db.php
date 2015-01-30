<?php
class Db{
    
    var $con = null;

    function Db ($Config) {
        $this->con = mysql_connect(
			$Config['mysql_server'],
			$Config['mysql_user'],
			$Config['mysql_pwd']
		) or die(mysql_error());
        
		mysql_select_db($Config['mysql_db'], $this->con) or die(mysql_error());

        mysql_query('set names utf8') or die(mysql_error());
    }

    /**
	 * 获取所有查询记录
	 */
    public function queryAll ($sql) {

		$query_id = mysql_query($sql) or die(mysql_error());

		$rows = array();
		if ($query_id) {
			while ($row = mysql_fetch_assoc($query_id)) {
				array_push($rows, $row);
			}
		}

		return $rows;
	}


    /**
	 * 获取单条记录
	 */
	public function queryOne ($sql) {
		$rows = $this->queryAll($sql);
		if (empty ($rows)) {
			return array();
		}

		return $rows[0];
	}
}
?>
