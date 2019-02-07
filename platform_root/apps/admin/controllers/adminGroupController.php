<?php

	class adminGroupController extends bAdminController {
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
		 *  Show the admin groups overview
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$recordOverviewAction = getPost('recordOverviewAction');
			if ($recordOverviewAction) {
				dataViewAdminGroup::processOverviewAction($recordOverviewAction, 'adminGroup', getPost('selected'), getPost('recordOverviewActionOption'));
			}
			$view = new dataViewAdminGroup;
			$records = $view->performSearch();
			$recordsFound = $view->countRecordsFound();
			$this->assignClean('_TITLE', 'Admin Groups');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('search', $view->getSearchValues());
			$this->assignClean('query', $view->getQueryComponents());
			$this->renderAdmin('adminGroup.tpl');	
		} // function index

		/**
		 *  Add group section
		 *  Args: none
		 *  Return: none
		 */
		public function addGroup() {
			return $this->editGroup();
		} // function addGroup

		/**
		 *  Edit admin group section
		 *  Args: (mixed) group object or group id, (str) mode
		 *  Return: none
		 */
		public function editGroup($adminGroup = false, $mode = 'add') {
			if (!$adminGroup) {
				$adminGroup = (int) getRequest('adminGroupID');
			}
			if (!$adminGroup) {
				$adminGroup = new adminGroup;
			} else {
				if (is_numeric($adminGroup)) {
					$adminGroup = new adminGroup($adminGroup);
					$mode = 'edit';
				}
				if ($mode == 'edit' && !$adminGroup->exists()) {
					addError('That group does not exist');
					return $this->adminGroups();
				}
			}
			$this->assignClean('_TITLE', 'Admin Groups');
			$this->assignClean('adminGroup', $adminGroup->toArray());
			$this->assignClean('accessSections', adminUser::$accessSections);
			$this->assignClean('groupAccess', $adminGroup->getAccess());
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->renderAdmin('adminGroupEdit.tpl');
		} // function editGroup

		/**
		 *  Save a new group record
		 *  Args: (obj) group
		 *  Return: none
		 */
		public function saveGroup($adminGroup = false) {
			if (!$adminGroup) {
				$adminGroup = new adminGroup;
			}
			$update = $adminGroup->exists() ? true : false;
			$adminGroup->name = getPost('name');
			if ($adminGroup->save()) {
				addSuccess('Group saved successfully');
				$access = getPost('access');
				if (!$adminGroup->setAccess($access)) {
					$adminGroup->updateSessionMessage();
					return $this->editGroup($adminGroup, 'edit');
				} elseif ($update || getRequest('submit') == 'Add and Edit') {
					redirect('/adminGroup/editGroup?adminGroupID='.$adminGroup->adminGroupID);
				} else {
					redirect('/adminGroup/addGroup');
				}
			} else {
				addError('An error occurred while saving the group');
				$adminGroup->updateSessionMessage();
			}
			return $this->editGroup($adminGroup, $update ? 'edit' : 'add');
		} // function saveGroup

		/**
		 *  Update an existing group record
		 *  Args: none
		 *  Return: none
		 */
		public function updateGroup() {
			$adminGroup = new adminGroup(getRequest('adminGroupID'));
			if ($adminGroup->exists()) {
				return $this->saveGroup($adminGroup);
			} else {
				addError('That group does not exist');
				return $this->groups();
			}
		} // function updateGroup
	} // function adminGroupController

?>
