<?php

	class siteTemplate extends bModel {
		// active record table
		protected $_table = 'siteTemplate';
		// history table (optional)
		protected $_historyTable = 'siteTemplateHistory';
		// field name of primary key
		protected $_id = 'siteTemplateID';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
			'siteTemplateID' => false,
			'languageID' => true,
			'content' => false,
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
				$this->siteTemplateID = NULL;
				$this->setRaw('dateAdded', 'NOW()');
				$this->setRaw('lastModified', 'NOW()');
			} else {
				$this->setRaw('lastModified', 'NOW()');
			}
			return $result;
		} // function beforeSave
	} // class siteTemplate

?>
