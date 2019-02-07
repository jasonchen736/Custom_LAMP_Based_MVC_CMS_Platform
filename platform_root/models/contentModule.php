<?php

	class contentModule extends bModel {
		// active record table
		protected $_table = 'contentModule';
		// history table (optional)
		protected $_historyTable = 'contentModuleHistory';
		// field name of primary key
		protected $_id = 'contentModuleID';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
			'contentModuleID' => false,
			'languageID' => true,
			'name' => false,
			'content' => false,
			'dateAdded' => false,
			'lastModified' => false
		);

		/**
		 *  Pre save checks
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			$result = parent::beforeSave();
			if (!$this->exists()) {
				$this->contentModuleID = NULL;
				$this->setRaw('dateAdded', 'NOW()');
				$this->setRaw('lastModified', 'NOW()');
			} else {
				$this->setRaw('lastModified', 'NOW()');
			}
			return $result;
		} // function beforeSave

		/**
		 *  Check for duplicate based on unique content name and language
		 *  Args: none
		 *  Return: (boolean) is duplicate content
		 */
		public function isDuplicate() {
			$duplicate = false;
			$sql = "SELECT `contentModuleID` FROM `".$this->_table."` WHERE `name` = ? AND `languageID` = ?";
			$result = query($sql, array($this->name, $this->languageID));
			if ($result->count > 0) {
				$id = $this->contentModuleID;
				while ($row = $result->fetch()) {
					if ($row['contentModuleID'] != $id) {
						$this->addError('There is an existing content module with the same name', 'duplicate');
						$this->addErrorField('name');
						$duplicate = true;
					}
				}
			}
			return $duplicate;
		} // function isDuplicate
	} // class contentModule

?>
