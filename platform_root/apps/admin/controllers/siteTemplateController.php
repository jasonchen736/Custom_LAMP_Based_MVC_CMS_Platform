<?php

	class siteTemplateController extends bAdminController {
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
		 *  Edit Site Template section
		 *  Args: (mixed) Site Template object or Site Template id
		 *  Return: none
		 */
		public function index($siteTemplate = false, $mode = 'add') {
			if (!$siteTemplate) {
				$siteTemplate = siteTemplate::getObject(array('languageID' => language::getCurrent('languageID')));
				if ($siteTemplate) {
					$mode = 'edit';
				} else {
					$siteTemplate = new siteTemplate;
				}
			} else {
				if (is_numeric($siteTemplate)) {
					$siteTemplate = siteTemplate::getObject(array('siteTemplateID' => $siteTemplate, 'languageID' => language::getCurrent('languageID')));
					$mode = 'edit';
				}
				if ($mode == 'edit' && (!$siteTemplate || !$siteTemplate->exists() || $siteTemplate->languageID != language::getCurrent('languageID'))) {
					addError('That Site Template does not exist');
					redirect('/siteTemplate');
				}
			}
			$history = false;
			if ($date = getRequest('d')) {
				$dateStr = date('m/d/Y H:i:s', strtotime($date));
				if ($history = $siteTemplate->getHistory($date)) {
					$siteTemplate->loadData($history);
					addSuccess('Record on '.$dateStr.' loaded');
					addSuccess('Please review and press update to save');
				} else {
					addError('Record on '.$dateStr.' could not be found');
				}
			}
			$this->assignClean('_TITLE', 'Site Template Admin');
			$this->assignClean('siteTemplate', $siteTemplate->toArray());
			$this->assign('revisions', $siteTemplate->getHistory());
			$this->assign('content', $siteTemplate->content);
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->assign('languageID', language::getCurrent('languageID'));
			$this->renderAdmin('siteTemplateEdit.tpl');
		} // function index

		/**
		 *  Save a new Site Template record
		 *  Args: (obj) siteTemplate
		 *  Return: none
		 */
		public function saveSiteTemplate($siteTemplate = false) {
			if (!$siteTemplate) {
				$siteTemplate = new siteTemplate;
			}
			$update = $siteTemplate->exists() ? true : false;
			$siteTemplate->languageID = getPost('languageID');
			$siteTemplate->content = preg_replace('/ mce_[^=]*="[^"]*"/', '', getPost('content'));
			if ($siteTemplate->save()) {
				addSuccess('Site Template saved successfully');
				redirect('/siteTemplate?propertyMenuItem='.getRequest('propertyMenuItem'));
			} else {
				addError('An error occurred while saving the Site Template');
				$siteTemplate->updateSessionMessage();
			}
			return $this->index($siteTemplate, $update ? 'edit' : 'add');
		} // function saveSiteTemplate

		/**
		 *  Update an existing Site Template record
		 *  Args: none
		 *  Return: none
		 */
		public function updateSiteTemplate() {
			$siteTemplate = new siteTemplate((int) getRequest('siteTemplateID'));
			if ($siteTemplate->exists()) {
				return $this->saveSiteTemplate($siteTemplate);
			} else {
				addError('That Site Template does not exist');
				redirect('/siteTemplate');
			}
		} // function updateSiteTemplate
	} // class siteTemplateController

?>
