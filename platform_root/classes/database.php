<?php

	class database {
		private static $instance = false;
		// database handler
		private static $dbh = false;
		// query result reference vars
		public $error = false;
		public $errorNo = false;
		// when using SQL_CALC_FOUND_ROWS
		public $foundRows = false;
		public $count = false;
		public $insertID = false;

		/**
		 *  Converts any format date/time to sql format Y-m-d HH:MM:SS
		 *  Args: (str) any format date, (boolean) return time as well
		 *  Return: (str) sql formatted date/time
		 */
		public static function dateToSql($date, $time = false) {
			if (!$time) {
				return date('Y-m-d', strtotime($date));
			} else {
				return date('Y-m-d H:i:s', strtotime($date));
			}
		} // function dateToSql

		/**
		 *  Retrieve database schema, initialize connection
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			self::$dbh = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
			if (mysqli_connect_error()) {
				$this->error = 'Cannot connect to the database because: '.mysqli_connect_error();
				trigger_error('Database Error: '.$this->error, E_USER_ERROR);
			}
			self::$instance = &$this;
		} // function __construct

		/**
		 *  Return the current instance of the global database handler
		 *  Args: none
		 *  Return: (database) instance of database class
		 */
		public static function getInstance() {
			if (!self::$instance) {
				$dbh = new database;
			}
			return self::$instance;
		} // function getInstance

		/**
		 *  Performs database query, sets internval values error (if error), found rows (if sql_calc_found_rows)
		 *    affected rows/row count, insert id (if insert)
		 *  Args: (str) query, (array) params
		 *  Return: (result) result object
		 */
		public function query($query, $params = array()) {

			$this->error = false;
			$this->errorNo = false;
			$this->foundRows = false;
			$this->count = false;
			$this->insertID = false;
			if (substr($query, -1, 1) != ';') $query .= ';';
			$stmt = self::$dbh->prepare($query);
			if ($stmt) {
				$paramTypes = array();
				$refParams = array();
				foreach ($params as $key => $param) {
					$refParams[$key] = &$params[$key];
					if (is_int($param)) {
						$paramTypes[] = 'i';
					} elseif (is_double($param) || is_float($param)) {
						$paramTypes[] = 'd';
					} else {
						$paramTypes[] = 's';
					}
				}
				if (!empty($paramTypes)) {
					call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt, implode('', $paramTypes)), $refParams));
				}
				if ($stmt->execute()) {
					$stmt->store_result();
					$this->count = strpos($query, 'SELECT') === 0 ? $stmt->num_rows : $stmt->affected_rows;
					if (strpos($query, 'INSERT') === 0) {
						$this->insertID = $stmt->insert_id;
					}
					if (strpos($query, 'SQL_CALC_FOUND_ROWS') !== false) {
						$foundStmt = self::$dbh->prepare("SELECT FOUND_ROWS()");
						$foundStmt->execute();
						$foundStmt->bind_result($this->foundRows);
						$foundStmt->fetch();
					} else {
						$this->foundRows = $this->count;
					}
					$resultObj = new result($stmt, $this->count, $this->foundRows, $this->insertID, $this->error, $this->errorNo);
				} else {
					$this->error = $stmt->error;
					$this->errorNo = $stmt->errno;
					$params = htmlentities(print_r($params, true));
					trigger_error('Query Failed: '.$this->error.' <===> [Query] '.htmlentities($query).' [Params] '.$params, E_USER_ERROR);
					$resultObj = new result(false, $this->count, $this->foundRows, $this->insertID, $this->error, $this->errorNo);
				}
			} else {
				$this->error = self::$dbh->error;
				$this->errorNo = self::$dbh->errno;
				$params = htmlentities(print_r($params, true));
				trigger_error('Query Failed: '.$this->error.' <===> [Query] '.htmlentities($query).' [Params] '.$params, E_USER_ERROR);
				$resultObj = new result(false, $this->count, $this->foundRows, $this->insertID, $this->error, $this->errorNo);
			}
			return $resultObj;
		} // function query
	} // class database

?>
