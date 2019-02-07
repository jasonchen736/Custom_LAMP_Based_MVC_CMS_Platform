<?php

	class adminGroup extends bModel {
		// active record table
		protected $_table = 'adminGroup';
		// field name of primary key
		protected $_id = 'adminGroupID';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
			'adminGroupID' => false,
			'name'         => true
		);

		/**
		 *  Pre save checks
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			$result = parent::beforeSave();
			if (!$this->exists()) {
				$this->adminGroupID = NULL;
			}
			return $result;
		} // function beforeSave

		/**
		 *  Check for duplicate admin group name
		 *  Args: none
		 *  Return: (boolean) is duplicate admin group name
		 */
		public function isDuplicate() {
			$duplicate = false;
			$sql = "SELECT `adminGroupID` FROM `".$this->_table."` WHERE `name` = ?";
			$result = query($sql, array($this->name));
			if ($result->count > 0) {
				$id = $this->adminGroupID;
				while ($row = $result->fetch()) {
					if ($row['adminGroupID'] != $id) {
						$this->addError('There is an existing admin group with the same name', 'duplicate');
						$this->addErrorField('name');
						$duplicate = true;
					}
				}
			}
			return $duplicate;
		} // function isDuplicate

		/**
		 *  Delete record, override to use removeGroup
		 *  Args: none
		 *  Return: (boolean) success
		 */
		public function delete() {
			return $this->removeGroup();
		} // function delete

		/**
		 *  Remove admin group
		 *  Args: none
		 *  Return: (boolean) success
		 */
		public function removeGroup() {
			$sql = "DELETE FROM `adminUserGroupMap` WHERE `adminGroupID` = '".$this->adminGroupID."'";
			$result = query($sql);
			if (!empty($result->error)) {
				$this->addError('There was a problem removing the group');
			} else {
				$sql = "DELETE FROM `adminGroupAccess` WHERE `adminGroupID` = '".$this->adminGroupID."'";
				$result = query($sql);
				if (!empty($result->error)) {
					$this->addError('There was a problem removing the group');
				} else {
					$sql = "DELETE FROM `".$this->_table."` WHERE `adminGroupID` = '".$this->adminGroupID."'";
					$result = query($sql);
					if (!empty($result->error)) {
						$this->addError('There was a problem removing the group');
					} else {
						return true;
					}
				}
			}
			return false;
		} // function removeGroup

		/**
		 *  Set admin group access
		 *  Args: (array) access
		 *  Return: none
		 */
		public function setAccess($access) {
			if ($this->exists()) {
				if (!is_array($access)) {
					$access = array();
				}
				$currentAccess = $this->getAccess();
				$newAccess = array();
				$removeAccess = array();
				foreach ($access as $key => $val) {
					if (isset(adminUser::$accessSections[$key]) && !$currentAccess[$key]) {
						$newAccess[] = $key;
					}
				}
				foreach ($currentAccess as $key => $val) {
					if (!isset($access[$key])) {
						$removeAccess[] = $key;
					}
				}
				$id = $this->adminGroupID;
				if (!empty($newAccess)) {
					$sql = "INSERT INTO `adminGroupAccess` (`adminGroupID`, `access`) 
							VALUES ('".$id."', '".implode("'), ('".$id."', '", $newAccess)."')";
					query($sql);
				}
				if (!empty($removeAccess)) {
					$sql = "DELETE FROM `adminGroupAccess` 
							WHERE `adminGroupID` = '".$id."' 
							AND `access` IN ('".implode("', '", $removeAccess)."')";
					query($sql);
				}
				return true;
			}
			return false;
		} // function setAccess

		/**
		 *  Retrieve admin group accesses
		 *  Args: none
		 *  Return: (array) admin user accesses
		 */
		public function getAccess() {
			$access = array();
			foreach (adminUser::$accessSections as $key => $val) {
				$access[$key] = false;
			}
			$sql = "SELECT `access` 
					FROM `adminGroupAccess` 
					WHERE `adminGroupID` = '".$this->adminGroupID."'";
			$result = query($sql);
			if ($result->count > 0) {
				while ($row = $result->fetch()) {
					$access[$row['access']] = true;
				}
			}
			return $access;
		} // function getAccess
	} // class adminUser

?>
