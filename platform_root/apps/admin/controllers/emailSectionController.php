<?php

	class emailSectionController extends bAdminController {
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
		 *  Show the Email Section overview
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$recordOverviewAction = getPost('recordOverviewAction');
			if ($recordOverviewAction) {
				dataViewEmailSection::processOverviewAction($recordOverviewAction, 'emailSection', getPost('selected'), getPost('recordOverviewActionOption'));
			}
			$view = new dataViewEmailSection;
			$view->forceSearch('languageID', language::getCurrent('languageID'));
			$records = $view->performSearch();
			$recordsFound = $view->countRecordsFound();
			$this->assignClean('_TITLE', 'Email Section Admin');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('search', $view->getSearchValues());
			$this->assignClean('query', $view->getQueryComponents());
			$this->assign('typeOptions', emailSection::$typeOptions);
			$this->assignClean('hasExport', false);
			$this->renderAdmin('emailSection.tpl');	
		} // function index

		/**
		 *  Add new Email Section section
		 *  Args: none
		 *  Return: none
		 */
		public function addEmailSection() {
			return $this->editEmailSection();
		} // functon addEmailSection

		/**
		 *  Edit Email Section section
		 *  Args: (mixed) Email Section object or Email Section id
		 *  Return: none
		 */
		public function editEmailSection($editEmailSection = false, $mode = 'add') {
			if (!$editEmailSection) {
				$editEmailSection = (int) getRequest('emailSectionID');
			}
			if (!$editEmailSection) {
				$editEmailSection = new emailSection;
			} else {
				if (is_numeric($editEmailSection)) {
					$editEmailSection = new emailSection($editEmailSection);
					$mode = 'edit';
				}
				if ($mode == 'edit' && (!$editEmailSection->exists() || $editEmailSection->languageID != language::getCurrent('languageID'))) {
					addError('That Email Section does not exist');
					redirect('/emailSection');
				}
			}
			$this->assignClean('_TITLE', 'Email Section Admin');
			$this->assignClean('editEmailSection', $editEmailSection->toArray());
			$this->assign('html', $editEmailSection->html);
			$this->assign('text', $editEmailSection->text);
			$this->assign('typeOptions', emailSection::$typeOptions);
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->assign('languageID', language::getCurrent('languageID'));
			$this->renderAdmin('emailSectionEdit.tpl');
		} // function editEmailSection

		/**
		 *  Save a new Email Section record
		 *  Args: (obj) emailSection
		 *  Return: none
		 */
		public function saveEmailSection($editEmailSection = false) {
			if (!$editEmailSection) {
				$editEmailSection = new emailSection;
			}
			$update = $editEmailSection->exists() ? true : false;
			$editEmailSection->languageID = getPost('languageID');
			$editEmailSection->type = getPost('type');
			$editEmailSection->name = getPost('name');
			$editEmailSection->html = preg_replace('/ mce_[^=]*="[^"]*"/', '', getPost('html'));
			$editEmailSection->text = getPost('text');
			if ($editEmailSection->save()) {
				$this->clearCompiledTemplates($editEmailSection);
				addSuccess('Email Section saved successfully');
				if ($update || getRequest('submit') != 'Add Another') {
					redirect('/emailSection/editEmailSection?emailSectionID='.$editEmailSection->emailSectionID.'&propertyMenuItem='.getRequest('propertyMenuItem'));
				} else {
					redirect('/emailSection/addEmailSection');
				}
			} else {
				addError('An error occurred while saving the Email Section');
				$editEmailSection->updateSessionMessage();
			}
			return $this->editEmailSection($editEmailSection, $update ? 'edit' : 'add');
		} // function saveEmailSection

		/**
		 *  Update an existing Email Section record
		 *  Args: none
		 *  Return: none
		 */
		public function updateEmailSection() {
			$editEmailSection = new emailSection(getRequest('emailSectionID'));
			if ($editEmailSection->exists()) {
				return $this->saveEmailSection($editEmailSection);
			} else {
				addError('That Email Section does not exist');
				redirect('/emailSection');
			}
		} // function updateEmailSection

		/**
		 *  Clear compiled templates for all emails associated with the section
		 *  Args: none
		 *  Return: none
		 */
		protected function clearCompiledTemplates($editEmailSection) {
			$emails = $editEmailSection->getAssociatedEmails();
			if (!empty($emails)) {
				$this->registerCustomResource('email');
				foreach ($emails as $email) {
					$this->clearCompiled('email:'.$email['name'].'.subject');
					$this->clearCompiled('email:'.$email['name'].'.fromEmail');
					$this->clearCompiled('email:'.$email['name'].'.html');
					$this->clearCompiled('email:'.$email['name'].'.text');
				}
			}
		} // function clearCompiledTemplates
	} // class emailSectionController

?>
