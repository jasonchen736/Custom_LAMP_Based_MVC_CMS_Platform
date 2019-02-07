<?php

	class __TABLE__ extends bModel {
		// active record table
		protected $_table = '__TABLE__';
__HISTORYTABLE__
		// field name of primary key
		protected $_id = '__PRIMARY__';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
__FIELDS__
		);
__ENUMOPTIONS__

		/**
		 *  Pre save checks
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			$result = parent::beforeSave();
			if (!$this->exists()) {
				$this->__PRIMARY__ = NULL;
__SAVEACTIONS__
			} else {
__UPDATEACTIONS__
			}
			return $result;
		} // function beforeSave
	} // class __TABLE__

?>
