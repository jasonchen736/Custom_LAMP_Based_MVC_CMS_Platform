<?php

	class languageController extends bAdminController {
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
		 *  Show the Language overview
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$recordOverviewAction = getPost('recordOverviewAction');
			if ($recordOverviewAction) {
				dataViewLanguage::processOverviewAction($recordOverviewAction, 'language', getPost('selected'), getPost('recordOverviewActionOption'));
			}
			$view = new dataViewLanguage;
			$records = $view->performSearch();
			$recordsFound = $view->countRecordsFound();
			$this->assignClean('_TITLE', 'Language Admin');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('search', $view->getSearchValues());
			$this->assignClean('query', $view->getQueryComponents());
			$this->assignClean('hasExport', false);
			$this->renderAdmin('language.tpl');	
		} // function index

		/**
		 *  Add new Language section
		 *  Args: none
		 *  Return: none
		 */
		public function addLanguage() {
			return $this->editLanguage();
		} // functon addLanguage

		/**
		 *  Edit Language section
		 *  Args: (mixed) Language object or Language id
		 *  Return: none
		 */
		public function editLanguage($language = false, $mode = 'add') {
			if (!$language) {
				$language = (int) getRequest('languageID');
			}
			if (!$language) {
				$language = new language;
			} else {
				if (is_numeric($language)) {
					$language = new language($language);
					$mode = 'edit';
				}
				if ($mode == 'edit' && !$language->exists()) {
					addError('That Language does not exist');
					redirect('/language()');
				}
			}
			$this->assignClean('_TITLE', 'Language Admin');
			$this->assignClean('language', $language->toArray());
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->renderAdmin('languageEdit.tpl');
		} // function editLanguage

		/**
		 *  Save a new Language record
		 *  Args: (obj) language
		 *  Return: none
		 */
		public function saveLanguage($language = false) {
			if (!$language) {
				$language = new language;
			}
			$update = $language->exists() ? true : false;
			$language->name = getPost('name');
			$language->url = getPost('url');
			$language->image = getPost('image');
			$language->default = getPost('default') ? 1 : 0;
			if ($language->default && $language->isNewValue('default')) {
				$prev = language::getObject(array('default' => 1));
				if ($prev && $prev->languageID !== $language->languageID) {
					$prev->default = 0;
					$prev->save();
				}
			}
			if ($language->save()) {
				addSuccess('Language saved successfully');
				if ($update || getRequest('submit') != 'Add Another') {
					redirect('/language/editLanguage?languageID='.$language->languageID.'&propertyMenuItem='.getRequest('propertyMenuItem'));
				} else {
					redirect('/language/addLanguage');
				}
			} else {
				addError('An error occurred while saving the Language');
				$language->updateSessionMessage();
			}
			return $this->editLanguage($language, $update ? 'edit' : 'add');
		} // function saveLanguage

		/**
		 *  Update an existing Language record
		 *  Args: none
		 *  Return: none
		 */
		public function updateLanguage() {
			$language = new language((int) getRequest('languageID'));
			if ($language->exists()) {
				return $this->saveLanguage($language);
			} else {
				addError('That Language does not exist');
				redirect('/language');
			}
		} // function updateLanguage
	} // class languageController

?>
