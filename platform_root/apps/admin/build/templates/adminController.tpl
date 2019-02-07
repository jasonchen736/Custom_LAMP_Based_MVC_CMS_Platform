<?php

	class __TABLE__Controller extends bAdminController {
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
		 *  Show the __LABEL__ overview
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$recordOverviewAction = getPost('recordOverviewAction');
			if ($recordOverviewAction) {
				dataView__UCFIRSTTABLE__::processOverviewAction($recordOverviewAction, '__TABLE__', getPost('selected'), getPost('recordOverviewActionOption'));
			}
			$view = new dataView__UCFIRSTTABLE__;
			$records = $view->performSearch();
			$recordsFound = $view->countRecordsFound();
			$this->assignClean('_TITLE', '__LABEL__ Admin');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('search', $view->getSearchValues());
			$this->assignClean('query', $view->getQueryComponents());
__SELECTOPTIONS__
			$this->assignClean('hasExport', true);
			$this->renderAdmin('__TABLE__.tpl');	
		} // function index

		/**
		 *  Add new __LABEL__ section
		 *  Args: none
		 *  Return: none
		 */
		public function add__UCFIRSTTABLE__() {
			return $this->edit__UCFIRSTTABLE__();
		} // functon add__UCFIRSTTABLE__

		/**
		 *  Edit __LABEL__ section
		 *  Args: (mixed) __LABEL__ object or __LABEL__ id
		 *  Return: none
		 */
		public function edit__UCFIRSTTABLE__($__TABLE__ = false, $mode = 'add') {
			if (!$__TABLE__) {
				$__TABLE__ = (int) getRequest('__PRIMARY__');
			}
			if (!$__TABLE__) {
				$__TABLE__ = new __TABLE__;
			} else {
				if (is_numeric($__TABLE__)) {
					$__TABLE__ = new __TABLE__($__TABLE__);
					$mode = 'edit';
				}
				if ($mode == 'edit' && !$__TABLE__->exists()) {
					addError('That __LABEL__ does not exist');
					redirect('/__TABLE__');
				}
			}
__HISTORY__
			$this->assignClean('_TITLE', '__LABEL__ Admin');
			$this->assignClean('__TABLE__', $__TABLE__->toArray());
__ASSIGNS__
__SELECTOPTIONS__
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->renderAdmin('__TABLE__Edit.tpl');
		} // function edit__UCFIRSTTABLE__

		/**
		 *  Save a new __LABEL__ record
		 *  Args: (obj) __TABLE__
		 *  Return: none
		 */
		public function save__UCFIRSTTABLE__($__TABLE__ = false) {
			if (!$__TABLE__) {
				$__TABLE__ = new __TABLE__;
			}
			$update = $__TABLE__->exists() ? true : false;
__SAVEPROCESS__
			if ($__TABLE__->save()) {
				addSuccess('__LABEL__ saved successfully');
				if ($update || getRequest('submit') != 'Add Another') {
					redirect('/__TABLE__/edit__UCFIRSTTABLE__?__PRIMARY__='.$__TABLE__->__PRIMARY__.'&propertyMenuItem='.getRequest('propertyMenuItem'));
				} else {
					redirect('/__TABLE__/add__UCFIRSTTABLE__');
				}
			} else {
				addError('An error occurred while saving the __LABEL__');
				$__TABLE__->updateSessionMessage();
			}
			return $this->edit__UCFIRSTTABLE__($__TABLE__, $update ? 'edit' : 'add');
		} // function save__UCFIRSTTABLE__

		/**
		 *  Update an existing __LABEL__ record
		 *  Args: none
		 *  Return: none
		 */
		public function update__UCFIRSTTABLE__() {
			$__TABLE__ = new __TABLE__((int) getRequest('__PRIMARY__'));
			if ($__TABLE__->exists()) {
				return $this->save__UCFIRSTTABLE__($__TABLE__);
			} else {
				addError('That __LABEL__ does not exist');
				redirect('/__TABLE__');
			}
		} // function update__UCFIRSTTABLE__
	} // class __TABLE__Controller

?>
