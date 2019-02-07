<?php

	class language extends bModel {
		// active record table
		protected $_table = 'language';
		// field name of primary key
		protected $_id = 'languageID';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
			'languageID' => false,
			'name' => true,
			'url' => true,
			'image' => false,
			'default' => false,
			'dateAdded' => false,
		);

		/**
		 *  Set current langauge
		 *  Args: (int) language ID
		 *  Return: none
		 */
		public static function setCurrent($id) {
			$language = new language($id);
			if ($language->exists()) {
				$_SESSION['language'] = $language->toArray();
			}
		} // function setCurrent

		/**
		 *  Get current language
		 *  Args: (str) field
		 *  Return: (mixed) language info
		 */
		public static function getCurrent($field = false) {
			if ($field) {
				if (isset($_SESSION['language'][$field])) {
					return $_SESSION['language'][$field];
				} else {
					return NULL;
				}
			} else {
				return $_SESSION['language'];
			}
		} // function getCurrent

		/**
		 *  Pre save checks
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			$result = parent::beforeSave();
			if (!$this->exists()) {
				$this->languageID = NULL;
				$this->setRaw('dateAdded', 'NOW()');
			} else {
			}
			return $result;
		} // function beforeSave

		/**
		 *  Check for duplicate based on name
		 *  Args: none
		 *  Return: (boolean) is duplicate content
		 */
		public function isDuplicate() {
			$duplicate = false;
			$sql = "SELECT `languageID` FROM `".$this->_table."` WHERE `name` = ?";
			$result = query($sql, array($this->name));
			if ($result->count > 0) {
				$id = $this->languageID;
				while ($row = $result->fetch()) {
					if ($row['languageID'] != $id) {
						$this->addError('The language you are trying to save already exists', 'duplicate');
						$this->addErrorField('name');
						$duplicate = true;
					}
				}
			}
			return $duplicate;
		} // function isDuplicate
	} // class language

?>
