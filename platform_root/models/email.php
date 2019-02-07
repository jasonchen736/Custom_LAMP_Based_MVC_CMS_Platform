<?php

	class email extends bModel {
		// active record table
		protected $_table = 'email';
		// history table (optional)
		protected $_historyTable = 'emailHistory';
		// field name of primary key
		protected $_id = 'emailID';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
			'emailID' => false,
			'languageID' => true,
			'name' => true,
			'subject' => true,
			'html' => true,
			'text' => true,
			'fromEmail' => true,
			'headerID' => false,
			'footerID' => false,
			'recipients' => false,
			'dateAdded' => false,
			'lastModified' => false,
		);

		/**
		 *  Pre save checks
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			$result = parent::beforeSave();
			if (!$this->exists()) {
				$this->emailID = NULL;
				$this->setRaw('dateAdded', 'NOW()');
				$this->setRaw('lastModified', 'NOW()');
			} else {
				$this->setRaw('lastModified', 'NOW()');
			}
			return $result;
		} // function beforeSave

		/**
		 *  Check for duplicate based on unique name and language
		 *  Args: none
		 *  Return: (boolean) is duplicate template
		 */
		public function isDuplicate() {
			$duplicate = false;
			$sql = "SELECT `emailID` FROM `".$this->_table."` WHERE `name` = ? AND `languageID` = ?";
			$result = query($sql, array($this->name, $this->languageID));
			if ($result->count > 0) {
				$id = $this->emailID;
				while ($row = $result->fetch()) {
					if ($row['emailID'] != $id) {
						$this->addError('There is an existing email template with the same name', 'duplicate');
						$this->addErrorField('name');
						$duplicate = true;
					}
				}
			}
			return $duplicate;
		} // function isDuplicate
	} // class email

?>
