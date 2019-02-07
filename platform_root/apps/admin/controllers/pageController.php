<?php

	class pageController extends bAdminController {
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
		 *  Show the Page overview
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$recordOverviewAction = getPost('recordOverviewAction');
			if ($recordOverviewAction) {
				dataViewPage::processOverviewAction($recordOverviewAction, 'page', getPost('selected'), getPost('recordOverviewActionOption'));
			}
			$view = new dataViewPage;
			$view->forceSearch('languageID', language::getCurrent('languageID'));
			$view->defaultSearch('status', 'active');
			$records = $view->performSearch();
			$recordsFound = $view->countRecordsFound();
			$this->assignClean('_TITLE', 'Page Admin');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('search', $view->getSearchValues());
			$this->assignClean('query', $view->getQueryComponents());
			$this->assign('statusOptions', page::$statusOptions);
			$this->assign('typeOptions', page::$typeOptions);
			$this->assignClean('hasExport', false);
			$this->renderAdmin('page.tpl');	
		} // function index

		/**
		 *  Add new Page section
		 *  Args: none
		 *  Return: none
		 */
		public function addPage() {
			return $this->editPage();
		} // functon addPage

		/**
		 *  Edit Page section
		 *  Args: (mixed) Page object or Page id
		 *  Return: none
		 */
		public function editPage($page = false, $mode = 'add') {
			if (!$page) {
				$page = (int) getRequest('pageID');
			}
			if (!$page) {
				$page = new page;
			} else {
				if (is_numeric($page)) {
					$page = new page($page);
					$mode = 'edit';
				}
				if ($mode == 'edit' && (!$page->exists() || $page->languageID != language::getCurrent('languageID'))) {
					addError('That Page does not exist');
					redirect('/page');
				}
			}
			$history = false;
			if ($date = getRequest('d')) {
				$dateStr = date('m/d/Y H:i:s', strtotime($date));
				if ($history = $page->getHistory($date)) {
					$page->loadData($history);
					addSuccess('Record on '.$dateStr.' loaded');
					addSuccess('Please review and hit update to save');
				} else {
					addError('Record on '.$dateStr.' could not be found');
				}
			}
			$this->assignClean('_TITLE', 'Page Admin');
			$this->assignClean('page', $page->toArray());
			$this->assign('content', $page->content);
			$this->assign('revisions', $page->getHistory());
			$this->assign('statusOptions', page::$statusOptions);
			$this->assign('typeOptions', page::$typeOptions);
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->assign('languageID', language::getCurrent('languageID'));
			$this->renderAdmin('pageEdit.tpl');
		} // function editPage

		/**
		 *  Save a new Page record
		 *  Args: (obj) page
		 *  Return: none
		 */
		public function savePage($page = false) {
			if (!$page) {
				$page = new page;
			}
			$update = $page->exists() ? true : false;
			$page->languageID = getPost('languageID');
			$page->name = preg_replace('/\s+/', '_', getPost('name'));
			$page->type = getPost('type');
			$page->title = getPost('title');
			$page->content = preg_replace('/ mce_[^=]*="[^"]*"/', '', getPost('content'));
			$page->summary = getPost('summary');
			$page->articleDate = database::dateToSql(getPost('articleDate'));
			$page->metaDescription = getPost('metaDescription');
			$page->metaKeywords = getPost('metaKeywords');
			$page->status = getPost('status');
			if ($page->save()) {
				addSuccess('Page saved successfully');
				if ($update || getRequest('submit') != 'Add Another') {
					redirect('/page/editPage?pageID='.$page->pageID.'&propertyMenuItem='.getRequest('propertyMenuItem'));
				} else {
					redirect('/page/addPage');
				}
			} else {
				addError('An error occurred while saving the Page');
				$page->updateSessionMessage();
			}
			return $this->editPage($page, $update ? 'edit' : 'add');
		} // function savePage

		/**
		 *  Update an existing Page record
		 *  Args: none
		 *  Return: none
		 */
		public function updatePage() {
			$page = new page(getRequest('pageID'));
			if ($page->exists()) {
				return $this->savePage($page);
			} else {
				addError('That Page does not exist');
				redirect('/page');
			}
		} // function updatePage
	} // class pageController

?>
