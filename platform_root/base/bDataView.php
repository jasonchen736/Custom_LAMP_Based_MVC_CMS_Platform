<?php

	class bDataView {
		// data view for specified table
		protected $_table;
		// table fields
		protected $_fields;
		// enum fields are converted to select options
		protected $_selectOptions;
		// requests to disregard when constructing a query string
		protected $_ignore = array(
			'_m',
			'_c',
			'submit', 
			'nextPage', 
			'previousPage', 
			'sortField',
			'sortOrder',
			'selected', 
			'recordOverviewAction', 
			'recordOverviewActionOption'
		);
		// holds search criteria: array(field name => value, force)
		//   indexes:
		//     'value' search value
		//     'force' always use this value
		protected $_searchValues = array();
		// default sorting
		protected $_defaultSortField = false;
		protected $_defaultSortOrder = false;
		// stored results
		protected $_records = array();
		protected $_recordsFound = 0;
		// errors array
		protected static $_errors = array();
		protected static $_errorFields = array();

		/**
		 *  Process overview section actions
		 *  Args: (str) action, (str) model class, (array) ids, (array) additional options
		 *  Return: none
		 */
		public static function processOverviewAction($action, $class, $ids, $options) {
			switch ($action) {
				case 'deleteSelected':
					$error = false;
					foreach ($ids as $id) {
						$obj = new $class($id);
						if ($obj->exists()) {
							if (!$obj->delete()) {
								$error = true;
							}
						}
					}
					if ($error) {
						addError('One or more records failed to be deleted');
					} else {
						addSuccess('Records deleted');
					}
					break;
				case 'duplicateToLanguage':
					$languageID = isset($options['languageID']) ? $options['languageID'] : false;
					$language = new language($languageID);
					if ($language->exists()) {
						$languageID = $language->languageID;
						$success = false;
						$obj = new $class;
						$fields = $obj->toArray();
						$idField = $obj->getIDField();
						foreach ($ids as $id) {
							$obj = new $class($id);
							if ($obj->exists()) {
								$newObj = new $class;
								foreach ($fields as $field => $val) {
									if ($field != 'dateAdded' && $field != 'lastModified' && $field != $idField) {
										$newObj->$field = $obj->$field;
									}
								}
								$newObj->languageID = $languageID;
								if ($newObj->save()) {
									$success = true;
								} else {
									addError('Record ID '.$id.' could not be copied, a duplicate record may already exist');
								}
							}
						}
						if ($success) {
							addSuccess('Records duplicated');
						}
					} else {
						addError('Please select a valid language to duplicate to');
					}
					break;
				default:
					break;
			}
			redirect($_SERVER['REQUEST_URI']);
		} // function processOverviewAction

		/**
		 *  Add an item to the error array
		 *  Args: (str) error message, (str) error index
		 *  Return: none
		 */
		public static function addError($error, $index = false) {
			if ($index !== false) {
				self::$_errors[$index] = $error;
			} else {
				self::$_errors[] = $error;
			}
		} // function addError

		/**
		 *  Add an item from the error array by a known index
		 *  Args: (str) error index
		 *  Return: none
		 */
		public static function removeError($index) {
			if (isset(self::$_errors[$index])) {
				unset(self::$_errors[$index]);
			}
		} // function removeError

		/**
		 *  Clear the error array
		 *  Args: none
		 *  Return: none
		 */
		public static function clearErrors() {
			self::$_errors = array();
		} // function clearErrors

		/**
		 *  Retrieve error array
		 *  Args: none
		 *  Return: (array) error array
		 */
		public static function getErrors() {
			return self::$_errors;
		} // function getErrors

		/**
		 *  Add a field name to the error field array
		 *  Args: (str) field name
		 *  Return: none
		 */
		public static function addErrorField($fieldName) {
			self::$_errorFields[$fieldName] = true;
		} // function addErrorField

		/**
		 *  Remove a field name to the error field array
		 *  Args: (str) field name
		 *  Return: none
		 */
		public static function removeErrorField($fieldName) {
			if (isset(self::$_errorFields[$fieldName])) {
				unset(self::$_errorFields[$fieldName]);
			}
		} // function removeErrorField

		/**
		 *  Clear the error fields array
		 *  Args: none
		 *  Return: none
		 */
		public static function clearErrorFields() {
			self::$_errorFields = array();
		} // function clearErrorFields

		/**
		 *  Retrieve error fields array
		 *  Args: none
		 *  Return: (array) error fields array
		 */
		public static function getErrorFields() {
			return self::$_errorFields;
		} // function getErrorFields

		/**
		 *  Push internal error or error fields array to system messages
		 *  Args: (str) array to push
		 *  Return: none
		 */
		public static function updateSessionMessage($type = false) {
			if (!$type || $type == 'errors') {
				$errors = self::$getErrors();
				foreach ($errors as $error) {
					addError($error);
				}
			}
			if (!$type || $type == 'errorFields') {
				$errorFields = self::$getErrorFields();
				foreach ($errorFields as $errorField => $val) {
					addErrorField($errorField);
				}
			}
		} // function updateSessionMessage

		/**
		 *  Return last search made
		 *  Args: none
		 *  Return: (mixed) last query url or false
		 */
		public static function getLastQuery() {
			if (isset($_SESSION['admin']['lastQuery'])) {
				return $_SESSION['admin']['lastQuery'];
			}
			return false;
		} // function getLastQuery

		/**
		 *  Detect and set select input options for enum fields
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			$this->_fields = array();
			$this->_get = $_GET;
			$this->_post = $_POST;
			$this->_records = array();
			$this->_recordsFound = 0;
			$this->_selectOptions = array();
			$result = query('DESC `'.$this->_table.'`');
			if ($result->count > 0) {
				while ($row = $result->fetch()) {
					if (preg_match('/^enum\(/', $row['Type'])) {
						$this->_fields[$row['Field']] = 'enum';
						$values = explode(',', rtrim(ltrim($row['Type'], 'enum('), ')'));
						$values = preg_replace('/\'/', '', $values);
						$options = array();
						foreach ($values as $key => $val) {
							$options[$val] = $val;
						}
						$this->_selectOptions[$row['Field']] = $options;
					} else {
						$this->_fields[$row['Field']] = $row['Type'];
					}
				}
			}
			$this->initialize();
		} // function __construct

		/**
		 *  Perform any data view specific initialization actions
		 *    OVERRIDE AS NEEDED
		 *  Args: none
		 *  Return: none
		 */
		public function initialize() {
		} // function initialize

		/**
		 *  Get records retrieved from search
		 *  Args: none
		 *  Return: (array) retrieved records
		 */
		public function getRecords() {
			return $this->_records;
		} // function getRecords

		/**
		 *  Get number of records found in search
		 *  Args: none
		 *  Return: (int) record count
		 */
		public function getRecordCount() {
			return $this->_recordsFound;
		} // function getRecordCount

		/**
		 *  Retrieve options from an enum data field
		 *  Args: (str) field name
		 *  Return: (array) value options
		 */
		public function getOptions($field) {
			if (array_key_exists($field, $this->_selectOptions)) {
				return $this->_selectOptions[$field];
			} else {
				return NULL;
			}
		} // function getOptions

		/**
		 *  Construct a get query string from get and post requests
		 *  Args: none
		 *  Return: (str) query string
		 */
		public function retrieveQueryString($ignore = array()) {
			if (!is_array($ignore)) {
				$ignore = array();
			}
			$ignore = array_merge($ignore, $this->_ignore);
			$querystring = array();
			$request = array_merge($this->_get, $this->_post);
			foreach ($request as $key => $val) {
				if (!in_array($key, $ignore) && $val !== '') {
					if (is_array($val)) {
						foreach ($val as $k => $v) {
							if ($v !== '') {
								$querystring[] = $key.'['.$k.']='.urlencode($v);
							}
						}
					} else {
						$querystring[] = $key.'='.urlencode($val);
					}
				}
			}
			$query = implode('&', $querystring);
			$_SESSION['admin']['lastQuery'] = PAGE_URL.'?'.$query;
			return $query;
		} // function retrieveQueryString

		/**
		 *  Return the current table positon
		 *  Args: none
		 *  Return: (array) start record, number of records shown, current page
		 */
		public function getTablePosition() {
			$start = (int) getRequest('start');
			$show = (int) getRequest('show') ? (int) getRequest('show') : 100;
			$page = (int) getRequest('page');
			$search = !getRequest('search');
			$next = getRequest('nextPage');
			$prev = getRequest('previousPage');
			if (!$search || $next || $prev || $page) {
				if (empty($next) && empty($prev)) {
					$start = 0;
					if (empty($page)) {
						$page = 1;
					}
				} elseif (is_null($start)) {
					$start = 0;
				}
				if (getRequest('nextPage')) {
					$start += $show;
				} elseif (getRequest('previousPage')) {
					$start -= $show;
					if ($start < 0) {
						$start = 0;
					}
				} elseif ($page) {
					$start = ($page - 1) * $show;
				}
				$page = floor(($start + $show) / $show);
			} else {
				$page = 1;
				$start = 0;
			}
			// update request array for function retrieveQueryString
			$this->_post['page'] = $page;
			$this->_post['start'] = $start;
			$this->_post['show'] = $show;
			return array($start, $show, $page);
		} // function getTablePosition

		/**
		 *  Retrieve search sorting configuration
		 *  Args: none
		 *  Return: (array) sort field, sort order
		 */
		public function getSearchOrder() {
			$sortField = (string) getRequest('sortField');
			$sortOrder = (string) getRequest('sortOrder');
			if (empty($sortOrder) || $sortOrder != 'DESC') {
				$sortOrder = 'ASC';
			}
			if (empty($sortField) || !isset($this->_fields[$sortField])) {
				if ($this->_defaultSortField) {
					$sortField = $this->_defaultSortField;
					$sortOrder = $this->_defaultSortOrder;
				}
			}
			return array($sortField, $sortOrder);
		} // function getSearchOrder

		/**
		 *  Retrieve search query components
		 *    - table location
		 *    - search order
		 *  Args: none
		 *  Return: (array) query components
		 */
		public function getQueryComponents() {
			$query = array();
			list($start, $show, $page) = $this->getTablePosition();
			list($sortField, $sortOrder) = $this->getSearchOrder();
			$query['start'] = $start;
			$query['show'] = $show;
			$query['page'] = $page;
			$query['pages'] = ceil($this->_recordsFound / $show);
			$query['sortField'] = $sortField;
			$query['sortOrder'] = $sortOrder;
			$query['revSortOrder'] = $sortOrder == 'ASC' ? 'DESC' : 'ASC';
			$query['querystring'] = $this->retrieveQueryString();
			return $query;
		} // function getQueryComponents

		/**
		 *  Set default sorting
		 *  Args: (str) field name, (str) order
		 *  Return: none
		 */
		public function defaultSort($field, $order) {
			$this->_defaultSortField = $field;
			$this->_defaultSortOrder = $order;
		} // function defaultSort

		/**
		 *  Set default search criteria (used only when there is no search action)
		 *  Args: (str) field name, (mixed) default value - if ranged, used array with indexes 'min' and 'max'
		 *  Return: none
		 */
		public function defaultSearch($field, $value) {
			if (!isset($this->_searchValues[$field])) {
				$this->_searchValues[$field] = array();
			}
			$this->_searchValues[$field]['value'] = $value;
			$this->_searchValues[$field]['force'] = false;
		} // function defaultSearch

		/**
		 *  Impose a search criteria, will always be used
		 *  Args: (str) field name, (mixed) default value - if ranged, used array with indexes 'min' and 'max'
		 *  Return: none
		 */
		public function forceSearch($field, $value) {
			if (!isset($this->_searchValues[$field])) {
				$this->_searchValues[$field] = array();
			}
			$this->_searchValues[$field]['value'] = $value;
			$this->_searchValues[$field]['force'] = true;
		} // function forceSearch

		/**
		 *  Return array of search values
		 *  Args: none
		 *  Return: (array) search values
		 */
		public function getSearchValues() {
			$search = array();
			$searchAction = getRequest('runSearch');
			if ($searchAction) {
				$defaultSearch = false;
			} else {
				$defaultSearch = true;
			}
			foreach ($this->_fields as $field => $vals) {
				if (isset($this->_selectOptions[$field])) {
					$fieldOptions = array_merge(array('' => 'All'), $this->_selectOptions[$field]);
				} else {
					$fieldOptions = false;
				}
				$search[$field] = array();
				if ($defaultSearch) {
					if (isset($this->_searchValues[$field])) {
						if (is_array($this->_searchValues[$field]['value'])) {
							$search[$field]['value'] = array(
								'min' => isset($this->_searchValues[$field]['value']['min']) ? $this->_searchValues[$field]['value']['min'] : '',
								'max' => isset($this->_searchValues[$field]['value']['max']) ? $this->_searchValues[$field]['value']['max'] : ''
							);
						} else {
							$search[$field]['value'] = $this->_searchValues[$field]['value'];
						}
					} else {
						$search[$field]['value'] = '';
					}
				} else {
					if (isset($this->_searchValues[$field]) && $this->_searchValues[$field]['force']) {
						if (is_array($this->_searchValues[$field]['value'])) {
							$search[$field]['value'] = array(
								'min' => isset($this->_searchValues[$field]['value']['min']) ? $this->_searchValues[$field]['value']['min'] : '',
								'max' => isset($this->_searchValues[$field]['value']['max']) ? $this->_searchValues[$field]['value']['max'] : ''
							);
						} else {
							$search[$field]['value'] = $this->_searchValues[$field]['value'];
						}
					} else {
						$value = getRequest($field);
						if (is_array($value)) {
							$search[$field]['value'] = array(
								'min' => isset($value['min']) ? $value['min'] : '',
								'max' => isset($value['max']) ? $value['max'] : ''
							);
						} else {
							$search[$field]['value'] = getRequest($field);
						}
					}
				}
				$search[$field]['options'] = $fieldOptions;
			}
			return $search;
		} // function getSearchValues

		/**
		 *  Get query "where" clauses
		 *  Args: none
		 *  Return: (array) array of clauses, array of search values
		 */
		public function getSearchClauses() {
			$clauses = array();
			$values = array();
			$search = $this->getSearchValues();
			foreach ($search as $field => $vals) {
				$date = false;
				if ($this->_fields[$field] == 'date' || $this->_fields[$field] == 'datetime' || $this->_fields[$field] == 'timestamp') {
					$date = true;
				}
				if (is_array($vals['value'])) {
					if ($vals['value']['min'] && $vals['value']['max']) {
						$clauses[] = "`".$field."` BETWEEN ? AND ?";
						if ($date) {
							$values[] = database::dateToSql($vals['value']['min']);
							$values[] = database::dateToSql($vals['value']['max']);
						} else {
							$values[] = $vals['value']['min'];
							$values[] = $vals['value']['max'];
						}
					} elseif ($vals['value']['min']) {
						$clauses[] = "`".$field."` >= ?";
						if ($date) {
							$values[] = database::dateToSql($vals['value']['min']);
						} else {
							$values[] = $vals['value']['min'];
						}
					} elseif ($vals['value']['max']) {
						$clauses[] = "`".$field."` <= ?";
						if ($date) {
							$values[] = database::dateToSql($vals['value']['max']);
						} else {
							$values[] = $vals['value']['max'];
						}
					}
				} elseif ($vals['value'] !== '' && !is_null($vals['value'])) {
					if (strpos($vals['value'], '*')) {
						$vals['value'] = str_replace('*', '%', $vals['value']);
					}
					if ($date) {
						$vals['value'] = database::dateToSql($vals['value']);
					}
					$clauses[] = "`".$field."` = ?";
					$values[] = $vals['value'];
				}
			}
			return array($clauses, $values);
		} // function getSearchClauses

		/**
		 *  Return records found from a general search using $this->getSearchComponents()
		 *    Override as needed
		 *  Args: (boolean) exporting
		 *  Return: (array) found records
		 */
		public function performSearch($export = false) {
                        list($start, $show, $page) = $this->getTablePosition();
                        list($sortField, $sortOrder) = $this->getSearchOrder();
			list($searchClauses, $searchValues) = $this->getSearchClauses();
			$sql = "SELECT * 
				FROM `".$this->_table."` 
				".(!empty($searchClauses) ? "WHERE ".implode(" AND ", $searchClauses)." " : "")
				.(!empty($sortField) ? " ORDER BY `".$sortField."` ".$sortOrder : "");
			if ($this->_table == "formData") {
				$sql .= ", formDataID ASC ";
			}	
			$sql .=	($export ? '' : " LIMIT ".$start.", ".$show);
			$result = query($sql, $searchValues);
			$this->_records = $result->fetchAll();
			return $this->_records;
		} // function performSearch

		/**
		 *  Count total records found from general search
		 *    Override as needed
		 *  Args: none
		 *  Return: (int) records found
		 */
		public function countRecordsFound() {
			list($searchClauses, $searchValues) = $this->getSearchClauses();
			$sql = "SELECT COUNT(*) AS `count` 
				FROM `".$this->_table."` 
				".(!empty($searchClauses) ? "WHERE ".implode(" AND ", $searchClauses)." " : "");
			$result = query($sql, $searchValues);
			$row = $result->fetch();
			$this->_recordsFound = $row['count'];
			return $this->_recordsFound;
		} // function countRecordsFound
	} // class bDataView

?>
