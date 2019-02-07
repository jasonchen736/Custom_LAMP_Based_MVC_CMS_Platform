<?php

	class formData extends bModel {
		// active record table
		protected $_table = 'formData';
		// field name of primary key
		protected $_id = 'formDataID';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
			'formDataID' => false,
			'languageID' => true,
			'first' => false,
			'last' => false,
			'email' => false,
			'type' => true,
			'date' => false,
			'data' => false,
			'source' => false,
		);

		/**
		 *  Pre save checks
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			$result = parent::beforeSave();
			if (!$this->exists()) {
				$this->formDataID = NULL;
				$this->setRaw('date', 'NOW()');
				$sourceCookie = getCookie('traffic_source');
				if ($sourceCookie) {
					$this->source = $sourceCookie;
		                }
			} else {
				$this->setRaw('date', 'NOW()');
			}
			return $result;
		} // function beforeSave
	} // class formData

?>
