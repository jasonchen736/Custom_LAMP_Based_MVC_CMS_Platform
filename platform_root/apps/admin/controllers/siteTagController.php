<?php

	class siteTagController extends bAdminController {
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
		 *  Show the site tags overview, default action
		 *  Args: none
		 *  Return: none
		 */
		function index() {
			$recordOverviewAction = getPost('recordOverviewAction');
			if ($recordOverviewAction) {
				dataViewSiteTag::processOverviewAction($recordOverviewAction, 'siteTag', getPost('selected'), getPost('recordOverviewActionOption'));
			}
			$view = new dataViewSiteTag;
			$view->forceSearch('languageID', language::getCurrent('languageID'));
			$records = $view->performSearch();
			$recordsFound = $view->countRecordsFound();
			$this->assignClean('_TITLE', 'Site Tags Admin');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('search', $view->getSearchValues());
			$this->assignClean('query', $view->getQueryComponents());
			$this->renderAdmin('siteTag.tpl');	
		} // function index

		/**
		 *  Add site tag section
		 *  Args: none
		 *  Return: none
		 */
		function addSiteTag() {
			return $this->editSiteTag();
		} // function addSiteTag

		/**
		 *  Edit site tag section
		 *  Args: (mixed) site tag object or site tag id, (str) mode
		 *  Return: none
		 */
		function editSiteTag($siteTag = false, $mode = 'add') {
			if (!$siteTag) {
				$siteTag = (int) getRequest('siteTagID');
			}
			if (!$siteTag) {
				$siteTag = new siteTag;
			} else {
				if (is_numeric($siteTag)) {
					$siteTag = new siteTag($siteTag);
					$mode = 'edit';
				}
				if ($mode == 'edit' && (!$siteTag->exists() || $siteTag->languageID != language::getCurrent('languageID'))) {
					addError('That site tag does not exist');
					redirect('/siteTag');
				}
			}
			$view = new dataViewSiteTag;
			$this->assignClean('_TITLE', 'Site Tags Admin');
			$this->assignClean('siteTag', $siteTag->toArray());
			$this->assignClean('placementOptions', $view->getOptions('placement'));
			$this->assignClean('matchTypeOptions', $view->getOptions('matchType'));
			$this->assignClean('statusOptions', $view->getOptions('status'));
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->assign('languageID', language::getCurrent('languageID'));
			$this->renderAdmin('siteTagEdit.tpl');
		} // function editSiteTag

		/**
		 *  Save a new site tag record
		 *  Args: (obj) site tag
		 *  Return: none
		 */
		function saveSiteTag($siteTag = false) {
			if (!$siteTag) {
				$siteTag = new siteTag;
			}
			$update = $siteTag->exists() ? true : false;
			$siteTag->languageID = getPost('languageID');
			$siteTag->referrer = getPost('referrer');
			$siteTag->description = getPost('description');
			$siteTag->matchType = getPost('matchType');
			$siteTag->matchValue = getPost('matchValue');
			$siteTag->placement = getPost('placement');
			$siteTag->weight = getPost('weight');
			$siteTag->HTTP = getPost('HTTP');
			$siteTag->HTTPS = getPost('HTTPS');
			$siteTag->status = getPost('status');
			if ($siteTag->save()) {
				addSuccess('Site tag saved successfully');
				if ($update || getRequest('submit') != 'Add Another') {
					redirect('/siteTag/editSiteTag?siteTagID='.$siteTag->siteTagID.'&propertyMenuItem='.getRequest('propertyMenuItem'));
				} else {
					redirect('/siteTag/addSiteTag');
				}
			} else {
				addError('An error occurred while saving the site tag');
				$siteTag->updateSessionMessage();
			}
			return $this->editSiteTag($siteTag, $update ? 'edit' : 'add');
		} // function saveSiteTag

		/**
		 *  Update an existing site tag record
		 *  Args: none
		 *  Return: none
		 */
		function updateSiteTag() {
			$siteTag = new siteTag(getRequest('siteTagID'));
			if ($siteTag->exists()) {
				return $this->saveSiteTag($siteTag);
			} else {
				addError('That site tag does not exist');
				redirect('/siteTag');
			}
		} // function updateSiteTag
	} // class siteTagController

?>
