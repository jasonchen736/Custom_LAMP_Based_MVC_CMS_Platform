<?php

	class adminUser extends bModel {
		// active record table
		protected $_table = 'adminUser';
		// field name of primary key
		protected $_id = 'adminUserID';
		// array of db field name => required
		//   array(field name => boolean)
		protected $_fields = array(
			'adminUserID' => false,
			'name'        => true,
			'email'       => true,
			'login'       => true,
			'password'    => true,
			'status'      => true,
			'created'     => false
		);
		public static $accessSections = array(
			'SUPERADMIN' => 'Admin Users Access',
			'CONTENT' => 'Content Access'
		);

		/**
		 *  Retrieve emails of admins designated as developers
		 *  args: none
		 *  return: (array) admin emails
		 */
		public static function getDevAdminEmails() {
			$emails = array();
			$sql = "SELECT `b`.`email` 
					FROM `adminUserAccess` `a` 
					JOIN `adminUser` `b` USING (`adminUserID`) 
					WHERE `a`.`access` = 'DEVELOPER'
					UNION 
					SELECT `c`.`email` 
					FROM `adminGroupAccess` `a` 
					JOIN `adminUserGroupMap` `b` ON (`a`.`adminGroupID` = `b`.`adminGroupID`) 
					JOIN `adminUser` `c` ON (`b`.`adminUserID` = `c`.`adminUserID`) 
					WHERE `a`.`access` = 'DEVELOPER'";
			$result = query($sql);
			if ($result->count > 0) {
				while ($row = $result->fetch()) {
					$emails[] = $row['email'];
				}
			}
			return $emails;
		} // function getDevAdminEmails

		/**
		 *  Pre save checks
		 *  Args: none
		 *  Return: (boolean) ok to save
		 */
		public function beforeSave() {
			$result = parent::beforeSave();
			if (!$this->exists()) {
				$this->adminUserID = NULL;
				$this->setRaw('created', 'NOW()');
			}
			if ($result) {
				if ($this->isNewValue('password')) {
					$hash = auth::generatePasswordHash($this->password);
					$this->password = $hash;
				}
			}
			return $result;
		} // function beforeSave

		/**
		 *  Check for duplicate admin user based on unique login
		 *  Args: none
		 *  Return: (boolean) is duplicate admin user
		 */
		public function isDuplicate() {
			$duplicate = false;
			$sql = "SELECT `adminUserID` FROM `".$this->_table."` WHERE `login` = ?";
			$result = query($sql, array($this->login));
			if ($result->count > 0) {
				$id = $this->adminUserID;
				while ($row = $result->fetch()) {
					if ($row['adminUserID'] != $id) {
						$this->addError('There is an existing admin user with the same login', 'duplicate');
						$this->addErrorField('login');
						$duplicate = true;
					}
				}
			}
			return $duplicate;
		} // function isDuplicate

		/**
		 *  Set admin user access
		 *  Args: (array) access
		 *  Return: (boolean) success without issue
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
					if (isset(self::$accessSections[$key]) && !$currentAccess[$key]) {
						$newAccess[] = $key;
					}
				}
				foreach ($currentAccess as $key => $val) {
					if (!isset($access[$key])) {
						$removeAccess[] = $key;
					}
				}
				$id = $this->adminUserID;
				if (!empty($newAccess)) {
					$sql = "INSERT INTO `adminUserAccess` (`adminUserID`, `access`) 
							VALUES ('".$id."', '".implode("'), ('".$id."', '", $newAccess)."')";
					query($sql);
				}
				if (!empty($removeAccess)) {
					$sql = "DELETE FROM `adminUserAccess` 
							WHERE `adminUserID` = '".$id."' 
							AND `access` IN ('".implode("', '", $removeAccess)."')";
					query($sql);
				}
				return true;
			}
			return false;
		} // function setAccess

		/**
		 *  Retrieve admin user accesses
		 *  Args: none
		 *  Return: (array) admin user accesses
		 */
		public function getAccess() {
			$access = array();
			foreach (self::$accessSections as $key => $val) {
				$access[$key] = false;
			}
			$sql = "SELECT `access` 
					FROM `adminUserAccess` 
					WHERE `adminUserID` = '".$this->adminUserID."'";
			$result = query($sql);
			if ($result->count > 0) {
				while ($row = $result->fetch()) {
					$access[$row['access']] = true;
				}
			}
			return $access;
		} // function getAccess

		/**
		 *  Set admin groups
		 *  Args: (array) groups
		 *  Return: (boolean) success without issue
		 */
		public function setGroups($groups) {
			if ($this->exists()) {
				if (!is_array($groups)) {
					$groups = array();
				}
				$allGroups = array();
				$result = adminGroup::find();
				foreach ($result as $group) {
					$allGroups[$group['adminGroupID']] = $group;
				}
				$currentGroups = $this->getGroups();
				$newGroups = array();
				$removeGroups = array();
				foreach ($groups as $key => $val) {
					if (isset($allGroups[$key]) && !isset($currentGroups[$key])) {
						$newGroups[] = $key;
					}
				}
				foreach ($currentGroups as $key => $val) {
					if (!isset($groups[$key])) {
						$removeGroups[] = $key;
					}
				}
				$id = $this->adminUserID;
				if (!empty($newGroups)) {
					$sql = "INSERT INTO `adminUserGroupMap` (`adminUserID`, `adminGroupID`) 
							VALUES ('".$id."', '".implode("'), ('".$id."', '", $newGroups)."')";
					query($sql);
				}
				if (!empty($removeGroups)) {
					$sql = "DELETE FROM `adminUserGroupMap` 
							WHERE `adminUserID` = '".$id."' 
							AND `adminGroupID` IN ('".implode("', '", $removeGroups)."')";
					query($sql);
				}
				return true;
			}
			return false;
		} // function setGroups

		/**
		 *  Retrieve admin user groups
		 *  Args: none
		 *  Return: (array) admin user groups
		 */
		public function getGroups() {
			$groups = array();
			$sql = "SELECT `adminGroupID` 
					FROM `adminUserGroupMap` 
					WHERE `adminUserID` = '".$this->adminUserID."'";
			$result = query($sql);
			if ($result->count > 0) {
				while ($row = $result->fetch()) {
					$groups[$row['adminGroupID']] = true;
				}
			}
			return $groups;
		} // function getGroups

		/**
		 *  Retrieve admin user group accesses
		 *  Args: none
		 *  Return: (array) admin user group accesses
		 */
		public function getGroupAccess() {
			$access = array();
			foreach (self::$accessSections as $key => $val) {
				$access[$key] = false;
			}
			$sql = "SELECT `c`.`access` 
					FROM `adminUserGroupMap` `a`
					JOIN `adminGroup` `b` ON (`a`.`adminGroupID` = `b`.`adminGroupID`) 
					JOIN `adminGroupAccess` `c` ON (`b`.`adminGroupID` = `c`.`adminGroupID`) 
					WHERE `a`.`adminUserID` = '".$this->adminUserID."'";
			$result = query($sql);
			if ($result->count > 0) {
				while ($row = $result->fetch()) {
					$access[$row['access']] = true;
				}
			}
			return $access;
		} // function getAccess

		/**
		 *  Authenticate user
		 *  Args: (str) password, (boolean) bypass authentication
		 *  Return: (array) auth info
		 */
		public function auth($pass, $bypassAuth = false) {
			if ($bypassAuth || !empty($pass)) {
				$authenticated = false;
				if (!$bypassAuth) {
					$hash = auth::generatePasswordHash($pass, substr($this->password, -6), (int) substr($this->password, -9, 3));
					$authenticated = $hash === $this->password;
				} else {
					$authenticated = true;
				}
				if ($authenticated) {
					$auth = array();
					$auth['user'] = $this->toArray();
					$auth['access'] = $this->getAccess();
					$groupAccess = $this->getGroupAccess();
					foreach ($auth['access'] as $key => $val) {
						if (!$val && isset($groupAccess[$key]) && $groupAccess[$key]) {
							$auth['access'][$key] = true;
						}
					}
					return $auth;
				}
			}
			return false;
		} // function login
	} // class adminUser

?>
