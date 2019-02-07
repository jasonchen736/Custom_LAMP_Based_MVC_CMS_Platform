<?php

	/**
	 *  Active record for a database table
	 *    record values are arrays as: field name = array(value, enclose in quotes [update/insert])
	 */
	class bModel {
		// active record table
		protected $_table;
		// history table (optional)
		protected $_historyTable = false;
		// record editor information
		protected $_recordEditor = false;
		protected $_recordEditorID = false;
		// field name of primary key
		protected $_id;
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields;
		// field metadata and existing values
		//   array(field name => array(existing value, raw / enclosed))
		protected $_meta;
		// error arrays
		protected $_errors = array();
		protected $_errorFields = array();

		/**
		 *  Query for records in table
		 *    Pass criteria in array, including 'ORDER BY' AND 'LIMIT'
		 *    Ex: $obj->find(array('name' => 'john', 'ORDER BY' => '`name` ASC', 'LIMIT' => '1'));
		 *  Args: (array) criteria
		 *  Return: (array) records found
		 */
		public static function find($criteria = array()) {
			$class = get_called_class();
			$obj = new $class;
			if (isset($criteria['ORDER BY'])) {
				$order = $criteria['ORDER BY'];
				unset($criteria['ORDER BY']);
			} else {
				$order = false;
			}
			if (isset($criteria['LIMIT'])) {
				$limit = $criteria['LIMIT'];
				unset($criteria['LIMIT']);
			} else {
				$limit = false;
			}
			$where = array();
			$params = array();
			foreach ($criteria as $field => $val) {
				if (is_array($val)) {
					$where[] = "`".$field."` IN (?".(count($val) > 1 ? str_repeat(", ?", count($val) - 1) : "").")";
					$params = array_merge($params, $val);
				} else {
					$where[] = "`".$field."` = ?";
					$params[] = $val;
				}
			}
			$sql = "SELECT * FROM `".$obj->getTable()."`".($where ? " WHERE ".implode(" AND ", $where) : "").($order ? " ORDER BY ".$order : "").($limit ? " LIMIT ".$limit : "");
			$result = query($sql, $params);
			return $result->fetchAll();
		} // function find

		/**
		 *  Query for record, return object instance
		 *    Pass criteria in array
		 *    Ex: $obj->find(array('name' => 'john'));
		 *  Args: (array) criteria
		 *  Return: (mixed) object or null
		 */
		public static function getObject($criteria = array()) {
			$class = get_called_class();
			$obj = new $class;
			$where = array();
			foreach ($criteria as $field => $val) {
				$where[] = "`".$field."` = ?";
			}
			$sql = "SELECT * FROM `".$obj->getTable()."`".($where ? " WHERE ".implode(" AND ", $where) : "");
			$result = query($sql, $criteria);

			if ($result->count) {
				$row = $result->fetch();
				$obj->loadData($row);
				return $obj;
			}
			return null;
		} // function getObject

		/**
		 *  Constructor
		 *  Args: (mixed) id fields (construct new record if empty)
		 *  Return: none
		 */
		public function __construct($id = NULL) {
			if ($id) {
				$this->load($id);
			} else {
				$this->reset();
			}
			$this->initialize();
		} // function __construct

		/**
		 *  Get object table name
		 *  Args: none
		 *  Return: (str) table
		 */
		public function getTable() {
			return $this->_table;
		} // function getTable

		/**
		 *  Get object ID Field
		 *  Args: none
		 *  Return: (str) ID field
		 */
		public function getIDField() {
			return $this->_id;
		} // function getIDField

		/**
		 *  Reset field values
		 *    array(current value, original value, enclose quotes)
		 *  Args: none
		 *  Return: none
		 */
		public function reset() {
			foreach ($this->_fields as $field => $req) {
				$this->_meta[$field] = array('value' => NULL, 'raw' => false);
				$this->$field = NULL;
			}
			$this->clearErrors();
			$this->clearErrorFields();
		} // function reset

		/**
		 *  Load record by unique id
		 *  Args: (mixed) id can be array/str/int
		 *  Return: (boolean) success
		 */
		public function load($id) {
			$this->reset();
			$sql = "SELECT `".implode('`, `', array_keys($this->_fields))."`
				FROM `".$this->_table."`
				WHERE `".$this->_id."` = ?";
			$result = query($sql, array($id));
			if ($result->count > 0) {
				$row = $result->fetch();
				foreach ($this->_fields as $field => $req) {
					$this->_meta[$field] = array('value' => $row[$field], 'raw' => false);
					$this->$field = $row[$field];
				}
				return true;
			}
			return false;
		} // function load

		/**
		 *  Load record by data array
		 *  Args: (array) record data
		 *  Return: none
		 */
		public function loadData($data) {
			$this->reset();
			foreach ($data as $field => $val) {
				$this->_meta[$field] = array('value' => $val, 'raw' => false);
				$this->$field = $val;
			}
		} // function loadData

		/**
		 *  Save record
		 *  Args: none
		 *  Return: (boolean) success
		 */
		public function save() {
			if (!$this->beforeSave()) {
				return false;
			}
			if ($this->exists()) {
				return $this->update();
			}
			if ($this->isDuplicate()) {
				return false;
			}
			$insertFields = array();
			$insertValues = array();
			$insertPositions = array();
			$rawFields = array();
			$rawValues = array();
			foreach ($this->_fields as $field => $req) {
				if (isset($this->$field)) {
					if ($this->_meta[$field]['raw']) {
						$rawFields[] = $field;
						$rawValues[] = $this->$field;
					} else {
						$insertFields[] = $field;
						$insertValues[] = $this->$field;
						$insertPositions[] = '?';
					}
				}
			}
			$sql = "INSERT INTO `".$this->_table."` (`".implode('`, `', array_merge($insertFields, $rawFields))."`)
				VALUES (".implode(', ', array_merge($insertPositions, $rawValues)).")";
			$result = query($sql, $insertValues);
			if ($result->count == 1) {
				if ($result->insertID) {
					$this->{$this->_id} = $result->insertID;
				} else {
					trigger_error('Model Error: Unable to retrieve insert id from '.$this->_table.' insert [Query] '.$sql.' [Params] '.htmlentities(print_r($insertValues, true)).' [Error] '.$result->error, E_USER_ERROR);
					return false;
				}
				if ($this->load($this->{$this->_id})) {
					$this->logHistory('SAVE', 'New record');
					return true;
				}
			} else {
				trigger_error('Model Error: Save fail for '.$this->_table.' [Query] '.$sql.' [Params] '.htmlentities(print_r($insertValues, true)).' [Error] '.$result->error, E_USER_ERROR);
			}
			return false;
		} // function save

		/**
		 *  Update record
		 *  Args: none
		 *  Return: (boolean) success
		 */
		private function update() {
			$tableFields = array();
			foreach ($this->_fields as $field => $req) {
				$tableFields[] = $field;
			}
			$updateFields = array();
			$updateValues = array();
			$rawFields = array();
			$updates = array();
			foreach ($this->_fields as $field => $req) {
				if ((string) $this->$field !== (string) $this->_meta[$field]['value']) {
					$updates[$field] = $field;
					if ($this->_meta[$field]['raw']) {
						$rawFields[$field] = "`".$field."` = ".$this->$field;
					} else {
						$updateFields[$field] = "`".$field."` = ?";
						$updateValues[$field] = $this->$field;
					}
				}
			}
			if (!empty($updateFields) || !empty($rawFields)) {
				if ($this->isDuplicate()) {
					return false;
				}
				$sql = "UPDATE `".$this->_table."`
					SET ".implode(', ', array_merge($updateFields, $rawFields))."
					WHERE `".$this->_id."` = ?";
				$updateValues[] = $this->{$this->_id};
				$result = query($sql, $updateValues);
				if ($result->count > 0 || empty($result->error)) {
					if ($this->load($this->{$this->_id})) {
						$comment = 'Fields updated: '.implode(', ', array_keys($updates));
						$this->logHistory('UPDATE', $comment);
						return true;
					}
				} else {
					trigger_error('Model Error: Update fail for '.$this->_table.' [Query] '.$sql.' [Params] '.htmlentities(print_r($updateValues, true)).' [Error] '.$result->error, E_USER_ERROR);
				}
			} else {
				return true;
			}
			return false;
		} // function update

		/**
		 *  Delete record
		 *  Args: none
		 *  Return: (boolean) success
		 */
		public function delete() {
			if ($this->exists()) {
				$sql = "DELETE FROM `".$this->_table."`
					WHERE `".$this->_id."` = ?";
				$result = query($sql, array($this->{$this->_id}));
				if ($result->count > 0) {
					return true;
				} else {
					trigger_error('Model Error: Delete fail for '.$this->_table.' [Query] '.$sql.' [Params] '.htmlentities(print_r($idValues, true)).' [Error] '.$result->error, E_USER_ERROR);
				}
			}
			return false;
		} // function delete

		/**
		 *  Log to history table if applicable
		 *    history tables must have the date time fields specified in the object vars
		 *  Args: (str) save or update type logging, (str) comments
		 *  Return: (boolean) success
		 */
		public function logHistory($type, $comments) {
			if ($this->_historyTable) {
				$tableFields = array_keys($this->_fields);
				if ($type == 'UPDATE') {
					$effectiveThrough = date('Y-m-d H:i:s', strtotime('-1 second', strtotime($this->lastModified)));
					$sql = "UPDATE `".$this->_historyTable."`
						SET `effectiveThrough` = IF('".$effectiveThrough."' < `lastModified`, `lastModified`, '".$effectiveThrough."') 
						WHERE `".$this->_id."` = ? 
						AND `effectiveThrough` = '9999-12-31 23:59:59'";
					$result = query($sql, array($this->{$this->_id}));
					if ($result->count < 1) {
						trigger_error('Model History Error: History log update failed for '.$this->_table.' [Query] '.$sql.' [ID] '.$this->{$this->_id}.' [Error] '.$result->error, E_USER_WARNING);
					}
				}
				list($editorType, $editorID) = $this->getRecordEditor();
				$sql = "INSERT INTO `".$this->_historyTable."` (`".implode('`, `', $tableFields)."`, `effectiveThrough`, `recordEditor`, `recordEditorID`, `action`, `comments`)
					SELECT `".implode('`, `', $tableFields)."`, '9999-12-31 23:59:59', ?, ?, ?, ?
					FROM `".$this->_table."`
					WHERE `".$this->_id."` = ?";
				$result = query($sql, array($editorType, $editorID, $type, $comments, $this->{$this->_id}));
				if ($result->count < 1) {
					trigger_error('Model History Error: History log failed for '.$this->_table.' [Query] '.$sql.' [ID] '.$this->{$this->_id}.' [Error] '.$result->error, E_USER_WARNING);
					return false;
				}
			}
			return true;
		} // function logHistory

		/**
		 *  Set record editor values
		 *  Args: (str) editor type, (int) editor id
		 *  Return: none
		 */
		public function setRecordEditor($type, $id) {
			$this->_recordEditor = $type;
			$this->_recordEditorID = $id;
		} // function setRecordEditor

		/**
		 *  Get record editor information
		 *  Args: none
		 *  Return: (array) record editor information
		 */
		public function getRecordEditor() {
			if ($this->_recordEditor) {
				return array($this->_recordEditor, $this->_recordEditorID);
			} else {
				$auth = adminAuth::getInstance();
				if ($auth->isLoggedIn()) {
					return array('ADMIN', $auth->getUserInfo('adminUserID'));
				}
			}
			return array('SYSTEM', 0);
		} // function getRecordEditor

		/**
		 *  Retrieve history records
		 *  Args: (str) specific date
		 *  Return: (array) array of history records or data array for specific record, empty array if specific dated record not found
		 */
		public function getHistory($date = false) {
			if ($this->_historyTable) {
				$sql = "SELECT `a`.`".implode("`, `a`.`", array_keys($this->_fields))."`, `a`.`action`, `a`.`comments`, `a`.`".$this->_historyTable."ID`,
						`a`.`lastModified`, `a`.`effectiveThrough`, `b`.`name` AS `editor`
					FROM `".$this->_historyTable."` `a`
					JOIN `adminUser` `b` ON (`a`.`recordEditorID` = `b`.`adminUserID`)
					WHERE `".$this->_id."` = ?";
				if ($date) {
					$date = database::dateToSql($date, true);
					$sql .= " AND `lastModified` <= '".$date."' AND `effectiveThrough` >= '".$date."'";
				}
				$sql .= " ORDER BY `a`.`lastModified` DESC";
				$result = query($sql, array($this->_meta[$this->_id]['value']));
				if ($date) {
					if ($result->count > 0) {
						return $result->fetch();
					} else {
						return array();
					}
				}
				return $result->fetchAll();
			}
			return array();
		} // function getHistory

		/**
		 *  Set raw value
		 *  Args: (str) field name, (str) value
		 *  Return: none
		 */
		public function setRaw($field, $value) {
			if (isset($this->_fields[$field])) {
				$this->$field = $value;
				$this->_meta[$field]['raw'] = true;
			}
		} // function setRaw

		/**
		 *  Return field/value pairs for the active record
		 *  Args: none
		 *  Return: (array) active record field value pairs
		 */
		public function toArray() {
			$record = array();
			foreach ($this->_fields as $field => $req) {
				$record[$field] = $this->$field;
			}
			return $record;
		} // function toArray

		/**
		 *  Return true if record exists (id values are set)
		 *  Args: none
		 *  Return: (boolean) existing record
		 */
		public function exists() {
			return isset($this->{$this->_id});
		} // function exists

		/**
		 *  Return true if a field value has changed
		 *  Args: (str) field name
		 *  Return: (boolean) value changed
		 */
		public function isNewValue($field) {
			if (isset($this->_fields[$field])) {
				return !isset($this->$field) || $this->$field !== $this->_meta[$field]['value'];
			}
			return true;
		} // function isNewValue

		/**
		 *  Add an item to the error array
		 *  Args: (str) error message, (str) error index
		 *  Return: none
		 */
		public function addError($error, $index = false) {
			if ($index !== false) {
				$this->_errors[$index] = $error;
			} else {
				$this->_errors[] = $error;
			}
		} // function addError

		/**
		 *  Add an item from the error array by a known index
		 *  Args: (str) error index
		 *  Return: none
		 */
		public function removeError($index) {
			if (isset($this->_errors[$index])) {
				unset($this->_errors[$index]);
			}
		} // function removeError

		/**
		 *  Clear the error array
		 *  Args: none
		 *  Return: none
		 */
		public function clearErrors() {
			$this->_errors = array();
		} // function clearErrors

		/**
		 *  Retrieve error array
		 *  Args: none
		 *  Return: (array) error array
		 */
		public function getErrors() {
			return $this->_errors;
		} // function getErrors

		/**
		 *  Add a field name to the error field array
		 *  Args: (str) field name
		 *  Return: none
		 */
		public function addErrorField($field) {
			$this->_errorFields[$field] = true;
		} // function addErrorField

		/**
		 *  Remove a field name to the error field array
		 *  Args: (str) field name
		 *  Return: none
		 */
		public function removeErrorField($field) {
			if (isset($this->_errorFields[$field])) {
				unset($this->_errorFields[$field]);
			}
		} // function removeErrorField

		/**
		 *  Clear the error fields array
		 *  Args: none
		 *  Return: none
		 */
		public function clearErrorFields() {
			$this->_errorFields = array();
		} // function clearErrorFields

		/**
		 *  Retrieve error fields array
		 *  Args: none
		 *  Return: (array) error fields array
		 */
		public function getErrorFields() {
			return $this->_errorFields;
		} // function getErrorFields

		/**
		 *  Push internal error or error fields array to system messages
		 *  Args: (str) array to push
		 *  Return: none
		 */
		public function updateSessionMessage($type = false) {
			if (!$type || $type == 'errors') {
				$errors = $this->getErrors();
				foreach ($errors as $error) {
					addError($error);
				}
			}
			if (!$type || $type == 'errorFields') {
				$errorFields = $this->getErrorFields();
				foreach ($errorFields as $errorField => $val) {
					addErrorField($errorField);
				}
			}
		} // function updateSessionMessage

		/**
		 *  Set required field
		 *  Args: (str) field name, (boolean) required
		 *  Return: none
		 */
		public function setRequired($field, $required) {
			if (isset($this->_fields[$field])) {
				$this->_fields[$field] = $required;
			}
		} // function setRequired

		/**
		 *  Check that all required fields are present are met for save/update
		 *  Args: none
		 *  Return: (boolean) validation result
		 */
		public function checkRequired() {
			$errors = array();
			foreach ($this->_fields as $field => $req) {
				if ($req) {
					if (!isset($this->$field) || strlen($this->$field) == 0) {
						$pieces = preg_split('/(?=[A-Z])/', ucfirst($field));
						$errors[$field] = implode(' ', $pieces).' is required';
					}
				}
			}
			if (empty($errors)) {
				return true;
			} else {
				foreach ($errors as $field => $error) {
					$this->addErrorField($field);
					$this->addError($error);
				}
				return false;
			}
		} // function checkRequired

		/**
		 *  Perform any object setup on construct
		 *    OVERRIDE AS NEEDED
		 *  Args: none
		 *  Return: none
		 */
		public function initialize() {
		} // function initialize

		/**
		 *  Perform any processing needed before saving
		 *    OVERRIDE AS NEEDED
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			return $this->checkRequired();
		} // function beforeSave

		/**
		 *  Override with appropriate method if duplicate needs to be checked before save or update
		 *    OVERRIDE AS NEEDED
		 *  Args: none
		 *  Return: (boolean) duplicate record found
		 */
		public function isDuplicate() {
			return false;
		} // function isDuplicate
	} // class bModel

?>
