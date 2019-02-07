<?php

	class emailSection extends bModel {
		// active record table
		protected $_table = 'emailSection';
		// history table (optional)
		protected $_historyTable = 'emailSectionHistory';
		// field name of primary key
		protected $_id = 'emailSectionID';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
			'emailSectionID' => false,
			'languageID' => true,
			'type' => true,
			'name' => true,
			'html' => true,
			'text' => true,
			'dateAdded' => false,
			'lastModified' => false
		);
		// type options
		public static $typeOptions = array(
			'header' => 'header',
			'footer' => 'footer',
		);

		/**
		 *  Pre save checks
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			$result = parent::beforeSave();
			if (!$this->exists()) {
				$this->emailSectionID = NULL;
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
		 *  Return: (boolean) is duplicate content
		 */
		public function isDuplicate() {
			$duplicate = false;
			$sql = "SELECT `emailSectionID` FROM `".$this->_table."` WHERE `name` = ? AND `languageID` = ?";
			$result = query($sql, array($this->name, $this->languageID));
			if ($result->count > 0) {
				$id = $this->emailSectionID;
				while ($row = $result->fetch()) {
					if ($row['emailSectionID'] != $id) {
						$this->addError('There is an existing email template with the same name', 'duplicate');
						$this->addErrorField('name');
						$duplicate = true;
					}
				}
			}
			return $duplicate;
		} // function isDuplicate

		/**
		 *  Retrieve email sections of specified type
		 *  Args: (str) email section type
		 *  Return: (array) email sections array([id] => name, ... )
		 */
		public static function getSections($sectionType) {
			$sections = array();
			$sql = "SELECT `emailSectionID`, `name`
					FROM `emailSection`
					WHERE `type` = '".$sectionType."'
					ORDER BY `name`";
			$results = query($sql);
			if ($results->count > 0) {
				while ($row = $results->fetch()) {
					$sections[$row['emailSectionID']] = $row['name'];
				}
			}
			return $sections;
		} // function getSections

		/**
		 *  Retrieve emails associated with section
		 *  Args: none
		 *  Return: (array) associated emails
		 */
		public function getAssociatedEmails() {
			$sql = "SELECT * 
					FROM `email` 
					WHERE `".($this->type == 'footer' ? 'footerID' : 'headerID')."` = ?";
			$result = query($sql, array($this->emailSectionID));
			return $result->fetchAll();
		} // function getAssociatedEmails
	} // class emailSection

?>
