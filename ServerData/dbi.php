<?php
class DBI {
	#
	var $db_host = IP;
	var $db_port = PORT;
	var $db_name = DB;
	var $db_user = USER;
	var $db_pwd  = PWD;
	
    # scalar variable
    var $show_errors = true;
	
    # query times
    var $num_queries = 0;
    var $rows_affected = 0;
    var $insert_id = 0;
	var $fields_info = array();
    var $last_query;
    var $timer_start = 0;
    var $timer_stop  = 0;

    # object
    var $dbh;
	
	# mode
	var $mode = true;
	
	#
	var $error = '';
	var $errno = 0;

    # array
    var $last_result = array();
	
	# return Object
	var $default_return_object = false;
	var $return_object = false;

    # ======
    # constructor
    #function DBI ($db_host, $db_user, $db_pwd, $db_name) {
    #    $this->__construct($db_host, $db_user, $db_pwd, $db_name);
    #}
    function __construct ($type = false) {
		$this->default_return_object = $type;
		$this->return_object = $type;
	}
	
	function setDefault () {
		$this->return_object
			= $this->default_return_object;
	}
	
	function connect () {
        $this->dbh = mysqli_connect(
			$this->db_host,
			$this->db_user,
			$this->db_pwd,
			$this->db_name,
			$this->db_port
		);
		
        if (! $this->dbh) {
			$this->errno();
			$this->error();
			
			$debug = debug_backtrace();
        }
        $this->dbh->query("SET NAMES 'utf8'");
		
        #$this->select($this->db_name);
	}
	
	function checkConnect () {
		if (!$this->dbh || !mysqli_ping($this->dbh)) {
			$this->dbh->connect();
			return FALSE;
		}
		
		return TRUE;
	}

    # ======
    # select database
    function select ($dbname) {
        if (! mysqli_select_db($this->dbh, $dbname)) {
			$this->errno();
			$this->error();
			
			$debug = debug_backtrace();
        }
    }

    # ======
    # close connecting database
    function close () {
        mysqli_close($this->dbh);
    }

    # ======
    # escape specal string: (only) ' => \', " => \"
    function escape ($string) {
        $string = mysqli_real_escape_string($this->dbh, $string);
        return $string;
        #ereg_replace('([%;])', '\\\1', $string);
    }

    # ======
    # transform %, _, [, ]
    function search_escape ($string) {
        $string = $this->escape($string);
        $string = ereg_replace('(\\[|\\])', '\\\1', $string);
        $string = ereg_replace('_', '\\_', $string); # or \\\_
        $string = ereg_replace('%', '\\%', $string); # or \\\%
        return $string;
    }

    # ======
    # clear cached query results
    function flush () {
        $this->last_result = array();
        $this->col_info = null;
        $this->last_query = null;
    }
	
	function super_query ($query) {
        return $this->dbh->query($query);
	}

    function query ($query) {
		# initialise return
        $result = array();
        $this->flush();
		
		# Log how the function was called
		$this->func_call = "\$db->query(\"$query\")";
		
		# Keep track of the last query for debug..
        $this->last_query = $query;
		
		# Query
        if ($sets = $this->mode
			? mysqli_query($this->dbh, $query)
			: @mysqli_query($this->dbh, $query)
			) {
            ++$this->num_queries;
        }
        else {
			$this->errno();
			$this->error();
			
			$debug = debug_backtrace();
			throw new Exception($query);
        }
		
        # record query and save
        $this->rows_affected = @mysqli_affected_rows($this->dbh);
        if (preg_match('/^\s*(?:insert|delete|update|replace|create)\s+/i', $query)) {
            if (preg_match('/^\s*(?:insert|replace)\s+/i', $query)) {
                $this->insert_id = @mysqli_insert_id($this->dbh);
            }
            $result = $this->rows_affected;
        }
        else {
			$num_rows = @mysqli_num_rows($sets);
			$this->fields_info = @mysqli_fetch_fields($sets);
			
            $row = 0;
            while ($obj = ($this->return_object ? @mysqli_fetch_object($sets) : @mysqli_fetch_assoc($sets))) {
                $result[$row++] = $obj;
				if ($row == $num_rows) break;
            }
			@mysqli_free_result($sets);
            #$this->last_result = $result;
        }
		
        return $result;
    }

    # ======
    function autocommit ($mode = FALSE) {
		$this->mode = $mode;
        mysqli_autocommit($this->dbh, $mode);
    }

    # ======
    function rollback () {
        mysqli_rollback($this->dbh);
		$this->mode = true;
		$this->errno = 0;
		$this->error = '';
    }

    # ======
    function commit () {
        mysqli_commit($this->dbh);
		$this->autocommit(TRUE);
    }
	
	# ======
	function errno () {
		if ($this->errno) return;
		$this->errno = mysqli_errno($this->dbh);
	}
    
    # ======
    function error () {
		if ($this->error) return;
        $this->error =  mysqli_error($this->dbh);
    }
}
?>