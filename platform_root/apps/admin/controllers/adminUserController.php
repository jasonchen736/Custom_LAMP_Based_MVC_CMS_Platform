<?php

	class adminUserController extends bAdminController {
		/**
		 *  Access control
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			parent::__construct();
			$this->auth->checkAccess('SUPERADMIN');
		} // function __construct

		/**
		 *  Show the admin users overview
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$dataView = new dataViewAdminUser;
			$records = $dataView->performSearch();
			$recordsFound = $dataView->countRecordsFound();
			$this->assignClean('_TITLE', 'Admin Users');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('search', $dataView->getSearchValues());
			$this->assignClean('query', $dataView->getQueryComponents());
			$this->renderAdmin('adminUser.tpl');
		} // function index

		/**
		 *  Add user section
		 *  Args: none
		 *  Return: none
		 */
		public function addUser() {
			return $this->editUser();
		} // function addUser

		/**
		 *  Edit admin user section
		 *  Args: (mixed) user object or user id, (str) mode
		 *  Return: none
		 */
		public function editUser($adminUser = false, $mode = 'add') {
			if (!$adminUser) {
				$adminUser = (int) getRequest('adminUserID');
			}
			if (!$adminUser) {
				$adminUser = new adminUser;
			} else {
				if (is_numeric($adminUser)) {
					$adminUser = new adminUser($adminUser);
					$mode = 'edit';
				}
				if ($mode == 'edit' && !$adminUser->exists()) {
					addError('That user does not exist');
					redirect('/adminUser');
				}
			}
			$dataView = new dataViewAdminUser;
			$this->assignClean('_TITLE', 'Admin Users');
			$this->assignClean('adminUser', $adminUser->toArray());
			$this->assignClean('accessSections', adminUser::$accessSections);
			$this->assignClean('adminGroups', adminGroup::find(array('ORDER BY' => '`name` ASC')));
			$this->assignClean('userAccess', $adminUser->getAccess());
			$this->assignClean('userGroups', $adminUser->getGroups());
			$this->assignClean('statusOptions', $dataView->getOptions('status'));
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->renderAdmin('adminUserEdit.tpl');
		} // function editUser

		/**
		 *  Save a new user record
		 *  Args: (obj) user
		 *  Return: none
		 */
		public function saveUser($adminUser = false) {
			if (!$adminUser) {
				$adminUser = new adminUser;
			}
			$update = $adminUser->exists() ? true : false;
			$adminUser->email = getPost('email');
			if ($update) {
				$password = getPost('password');
				if ($password) {
					$adminUser->password = $password;
				}
			} else {
				$adminUser->password = getPost('password');
			}
			$adminUser->login = getPost('login');
			$adminUser->name = getPost('name');
			$adminUser->status = getPost('status');
			if ($adminUser->save()) {
				addSuccess('User saved successfully');
				$access = getPost('access');
				$accessSet = $adminUser->setAccess($access);
				$groups = getPost('groups');
				$groupsSet = $adminUser->setGroups($groups);
				if (!$accessSet || !$groupsSet) {
					$adminUser->udpateSessionMessage();
					return $this->editUser($adminUser, 'edit');
				} else {
					if ($adminUser->adminUserID == $this->auth->getUserInfo('adminUserID')) {
						$auth = $adminUser->auth(false, true);
						$this->auth->updateAuth($auth);
					}
					if ($update || getRequest('submit') == 'Add and Edit') {
						redirect('/adminUser/editUser?adminUserID='.$adminUser->adminUserID.'&propertyMenuItem='.getRequest('propertyMenuItem'));
					} else {
						redirect('/adminUser/addUser');
					}
				}
			} else {
				addError('An error occurred while saving the user');
				$adminUser->updateSessionMessage();
			}
			return $this->editUser($adminUser, $update ? 'edit' : 'add');
		} // function saveUser

		/**
		 *  Update an existing user record
		 *  Args: none
		 *  Return: none
		 */
		public function updateUser() {
			$adminUser = new adminUser(getRequest('adminUserID'));
			if ($adminUser->exists()) {
				return $this->saveUser($adminUser);
			} else {
				addError('That user does not exist');
				redirect('/adminUser');
			}
		} // function updateUser
	} // function bAdminUserController

?>
