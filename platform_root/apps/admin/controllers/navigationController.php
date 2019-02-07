<?php

	class navigationController extends bAdminController {
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
		 *  Show the Navigation overview
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$recordOverviewAction = getPost('recordOverviewAction');
			if ($recordOverviewAction) {
				switch ($recordOverviewAction) {
					case 'duplicateToLanguage':
						dataViewNavigation::duplicateToLanguage(getPost('selected'), getPost('recordOverviewActionOption'));
						break;
					case 'deleteSelected':
						dataViewNavigation::deleteSelected(getPost('selected'));
						break;
					default:
						dataViewNavigation::processOverviewAction($recordOverviewAction, 'navigation', getPost('selected'), getPost('recordOverviewActionOption'));
						break;
				}
			}
			$view = new dataViewNavigation;
			$view->forceSearch('parent', 0);
			$view->forceSearch('languageID', language::getCurrent('languageID'));
			$records = $view->performSearch();
			$recordsFound = $view->countRecordsFound();
			$this->assignClean('_TITLE', 'Navigation Admin');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('search', $view->getSearchValues());
			$this->assignClean('query', $view->getQueryComponents());
			$this->assignClean('hasExport', false);
			$this->renderAdmin('navigation.tpl');	
		} // function index

		/**
		 *  Add new Navigation section
		 *  Args: none
		 *  Return: none
		 */
		public function addNavigation() {
			return $this->editNavigation();
		} // functon addNavigation

		/**
		 *  Edit Navigation section
		 *  Args: (mixed) Navigation object or Navigation id
		 *  Return: none
		 */
		public function editNavigation($navigation = false, $mode = 'add') {
			if (!$navigation) {
				$navigation = (int) getRequest('navigationID');
			}
			if (!$navigation) {
				$navigation = new navigation;
			} else {
				if (is_numeric($navigation)) {
					$navigation = new navigation($navigation);
					$mode = 'edit';
				}
				if ($mode == 'edit' && (!$navigation->exists() || $navigation->languageID != language::getCurrent('languageID'))) {
					addError('That record does not exist');
					redirect('/navigation');
				}
			}
			$this->assignClean('_TITLE', 'Navigation Admin');
			$this->assignClean('navigation', $navigation->toArray());
			$this->assignClean('itemLevel', $navigation->getLevel());
			$this->assignClean('menuTree', navigation::getNavigation($navigation->navigationID));
			$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
			$this->assignClean('mode', $mode);
			$this->assign('languageID', language::getCurrent('languageID'));
			$this->renderAdmin('navigationEdit.tpl');
		} // function editNavigation

		/**
		 *  Save a new Navigation record
		 *  Args: (obj) navigation
		 *  Return: none
		 */
		public function saveNavigation($navigation = false) {
			if (!$navigation) {
				$navigation = new navigation;
			}
			$update = $navigation->exists() ? true : false;
			$navigation->languageID = getPost('languageID');
			$navigation->label = getPost('label');
			$navigation->url = getPost('url');
			if (!$update) {
				$navigation->parent = 0;
			} else {
				$navigation->parent = getPost('parent');
			}
			$navigation->order = getPost('order');
			if ($navigation->save()) {
				addSuccess('Navigation record saved successfully');
				$tree = navigation::getNavigation($navigation->navigationID);
				if ($update) {
					$parentID = $navigation->navigationID;
					$level = $navigation->getLevel();
					if ($level < 2) {
						$sub1 = getPost('sub1');
						if (!is_array($sub1)) {
							$sub1 = array();
						}
						$sub2 = getPost('sub2');
						if (!is_array($sub2)) {
							$sub2 = array();
						}
						foreach ($sub1 as $key => $val) {
							$new = false;
							if (strpos($key, 'n') === 0) {
								$new = true;
								$nav = new navigation;
							} else {
								$id = preg_replace('/[^\d]/', '', $key);
								$nav = new navigation($id);
							}
							if (!isset($val['delete'])) {
								$nav->languageID = $navigation->languageID;
								$nav->label = $val['label'];
								$nav->url = $val['url'];
								$nav->order = $val['order'];
								if ($new) {
									$nav->parent = $parentID;
								}
								$saved = $nav->save();
								if ($new) {
									if ($saved) {
										$newID = $nav->navigationID;
										if (isset($sub2[$key])) {
											$sub2[$newID] = $sub2[$key];
											unset($sub2[$key]);
										}
									} else {
										if (isset($sub2[$key])) {
											unset($sub2[$key]);
										}
									}
								}
							} elseif (!$new) {
								$nav->deleteNavigation();
								if (isset($sub2[$key])) {
									unset($sub2[$key]);
								}
							} elseif (isset($sub2[$key])) {
								unset($sub2[$key]);
							}
						}
					}
					if ($level < 1) {
						foreach ($sub2 as $subParent => $subChildren) {
							foreach ($subChildren as $key => $val) {
								$new = false;
								if (strpos($key, 'n') === 0) {
									$new = true;
									$nav = new navigation;
								} else {
									$id = preg_replace('/[^\d]/', '', $key);
									$nav = new navigation($id);
								}
								if (!isset($val['delete'])) {
									$nav->languageID = $navigation->languageID;
									$nav->label = $val['label'];
									$nav->url = $val['url'];
									$nav->order = $val['order'];
									if ($new) {
										$nav->parent = $subParent;
									}
									$nav->save();
								} elseif (!$new) {
									$nav->deleteNavigation();
								}
							}
						}
					}
					if (haveErrors()) {
						addError('There was an error saving the sub navigation items, please check and try again');
					}
				}
				if ($update || getRequest('submit') != 'Add Another') {
					redirect('/navigation/editNavigation?navigationID='.$navigation->navigationID.'&propertyMenuItem='.getRequest('propertyMenuItem'));
				} else {
					redirect('/navigation/addNavigation');
				}
			} else {
				addError('An error occurred while saving the navigation record');
				$navigation->updateSessionMessage();
			}
			return $this->editNavigation($navigation, $update ? 'edit' : 'add');
		} // function saveNavigation

		/**
		 *  Update an existing Navigation record
		 *  Args: none
		 *  Return: none
		 */
		public function updateNavigation() {
			$navigation = new navigation(getRequest('navigationID'));
			if ($navigation->exists()) {
				return $this->saveNavigation($navigation);
			} else {
				addError('That record does not exist');
				redirect('/navigation');
			}
		} // function updateNavigation
	} // class navigationController

?>
