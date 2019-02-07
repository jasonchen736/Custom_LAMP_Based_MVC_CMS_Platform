<?php

	class contentModuleController extends bAdminController {
		/**
		 *  Access control
		 *  Args: none
		 *  Return: none
		 */
		public function __construct() {
			parent::__construct();
			$this->auth->checkAccess('CONTENT');
		} // function __construct

		/**
		 *  Show the Content Module overview
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$recordOverviewAction = getPost('recordOverviewAction');
			if ($recordOverviewAction) {
				dataViewContentModule::processOverviewAction($recordOverviewAction, 'contentModule', getPost('selected'), getPost('recordOverviewActionOption'));
			}
			$view = new dataViewContentModule;
			$view->forceSearch('languageID', language::getCurrent('languageID'));
			$records = $view->performSearch();
			$recordsFound = $view->countRecordsFound();
			$this->assignClean('_TITLE', 'Content Module Admin');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('search', $view->getSearchValues());
			$this->assignClean('query', $view->getQueryComponents());
			$this->assignClean('hasExport', false);
			$this->renderAdmin('contentModule.tpl');	
		} // function index

		/**
		 *  Add new Content Module section
		 *  Args: none
		 *  Return: none
		 */
		public function addContentModule() {
			return $this->editContentModule();
		} // functon addContentModule

		/**
		 *  Edit Content Module section
		 *  Args: (mixed) Content Module object or Content Module id
		 *  Return: none
		 */
		public function editContentModule($contentModule = false, $mode = 'add') {
			if (!$contentModule) {
				$contentModule = (int) getRequest('contentModuleID');
			}
			if (!$contentModule) {
				$contentModule = new contentModule;
			} else {
				if (is_numeric($contentModule)) {
					$contentModule = new contentModule($contentModule);
					$mode = 'edit';
				}
				if ($mode == 'edit' && (!$contentModule->exists() || $contentModule->languageID != language::getCurrent('languageID'))) {
					addError('That Content Module does not exist');
					redirect('/contentModule');
				}
			}
			$history = false;
			if ($date = getRequest('d')) {
				$dateStr = date('m/d/Y H:i:s', strtotime($date));
				if ($history = $contentModule->getHistory($date)) {
					$contentModule->loadData($history);
					addSuccess('Record on '.$dateStr.' loaded');
					addSuccess('Please review and press update to save');
				} else {
					addError('Record on '.$dateStr.' could not be found');
				}
			}
			$this->assignClean('_TITLE', 'Content Module Admin');
			$this->assignClean('contentModule', $contentModule->toArray());
			$this->assign('revisions', $contentModule->getHistory());
			$this->assign('content', $contentModule->content);
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->assign('languageID', language::getCurrent('languageID'));
			$this->renderAdmin('contentModuleEdit.tpl');
		} // function editContentModule

		/**
		 *  Save a new Content Module record
		 *  Args: (obj) contentModule
		 *  Return: none
		 */
		public function saveContentModule($contentModule = false) {
			if (!$contentModule) {
				$contentModule = new contentModule;
			}
			$update = $contentModule->exists() ? true : false;
			$contentModule->languageID = getPost('languageID');
			$contentModule->name = getPost('name');
			$contentModule->content = preg_replace('/ mce_[^=]*="[^"]*"/', '', getPost('content'));
			if ($contentModule->save()) {
				addSuccess('Content Module saved successfully');
				if ($update || getRequest('submit') != 'Add Another') {
					redirect('/contentModule/editContentModule?contentModuleID='.$contentModule->contentModuleID.'&propertyMenuItem='.getRequest('propertyMenuItem'));
				} else {
					redirect('/contentModule/addContentModule');
				}
			} else {
				addError('An error occurred while saving the Content Module');
				$contentModule->updateSessionMessage();
			}
			return $this->editContentModule($contentModule, $update ? 'edit' : 'add');
		} // function saveContentModule

		/**
		 *  Update an existing Content Module record
		 *  Args: none
		 *  Return: none
		 */
		public function updateContentModule() {
			$contentModule = new contentModule((int) getRequest('contentModuleID'));
			if ($contentModule->exists()) {
				return $this->saveContentModule($contentModule);
			} else {
				addError('That Content Module does not exist');
				redirect('/contentModule');
			}
		} // function updateContentModule
	} // class contentModuleController

?>
