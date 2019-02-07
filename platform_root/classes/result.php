<?php

	class result {
		// database result
		private $result = false;
		private $fields = array();
		private $row = array();
		public $count;
		// when using SQL_CALC_FOUND_ROWS
		public $foundRows;
		public $insertID;
		public $error;
		public $errorNo;
		public $success;

		/**
		 *  Set database results
		 *  Args: none
		 *  Return: none
		 */
		public function __construct($result, $count, $foundRows, $insertID, $error, $errorNo) {
			$this->result = $result;
			$this->count = $count;
			$this->foundRows = $foundRows;
			$this->insertID = $insertID;
			$this->error = $error;
			$this->errorNo = $errorNo;
			$this->success = empty($errorNo);
			$meta = $this->result->result_metadata();
			if ($meta) {
				while($field = $meta->fetch_field()) {
					$this->fields[] = &$this->row[$field->name];
				}
				call_user_func_array(array($this->result, 'bind_result'), $this->fields);
			}
		} // function __construct

		/**
		 *  Return row as associative array from current result index
		 *  Args: none
		 *  Return: (array) current result row
		 */
		public function fetch() {
			if ($this->result->fetch()) {
				$row = array();
				foreach ($this->row as $key => $val) {
					$row[$key] = $val;
				}
				return $row;
			} else {
				return false;
			}
		} // function fetchRow

		/**
		 *  Return all rows from current result resource as associative array
		 *  Args: none
		 *  Return: (array) all rows from result
		 */
		public function fetchAll() {
			$rows = array();
			while($row = $this->fetch()) {
				$rows[] = $row;
			}
			return $rows;
		} // function fetchAll
	} // class result

?>
