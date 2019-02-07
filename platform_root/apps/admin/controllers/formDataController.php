<?php

	class formDataController extends bAdminController {
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
		 *  Show the Form Data overview
		 *  Args: none
		 *  Return: none
		 */
		public function index() {
			$recordOverviewAction = getPost('recordOverviewAction');
			if ($recordOverviewAction) {
				dataViewFormData::processOverviewAction($recordOverviewAction, 'formData', getPost('selected'), getPost('recordOverviewActionOption'));
			}
			$view = new dataViewFormData;
			$view->forceSearch('languageID', language::getCurrent('languageID'));
			$view->defaultSort('date', 'DESC');
			$records = $view->performSearch();
			$recordsFound = $view->countRecordsFound();
			$this->assignClean('_TITLE', 'Form Data Admin');
			$this->assignClean('records', $records);
			$this->assignClean('recordsFound', $recordsFound);
			$this->assignClean('search', $view->getSearchValues());
			$this->assignClean('query', $view->getQueryComponents());
			$this->assignClean('hasExport', true);
			$this->renderAdmin('formData.tpl');	
		} // function index

		/**
		 *  View Form Data section
		 *  Args: none
		 *  Return: none
		 */
		public function viewFormData() {
			$formData = new formData((int) getRequest('formDataID'));
			if ($formData->exists() && $formData->languageID == language::getCurrent('languageID')) {
				$this->assignClean('_TITLE', 'View Form Data');
				$this->assignClean('formData', $formData->toArray());
				$this->assignClean('data', json_decode($formData->data, true));
				$this->assignClean('propertyMenuItem', getRequest('propertyMenuItem'));
				$this->renderAdmin('formDataView.tpl');
			} else {
				addError('Record not found');
				redirect('/formData');
			}
		} // function viewFormData

		/**
		 *  Export search
		 *    OVERRIDE AS NEEDED
		 *  Args: none
		 *  Return: none
		 */
		public function export() {
			$_REQUEST['sortField'] = 'type';
			$_REQUEST['sortOrder'] = 'ASC';
			$class = preg_replace('/Controller$/', '', get_called_class());
			headers::sendExportHeaders($class.'Export.csv');
			$dataViewClass = 'dataView'.ucfirst($class);
			$view = new $dataViewClass;
			$records = $view->performSearch(true);
			if (!empty($records)) {
				$headers = array_keys(current($records));
				$headerLine = '';
				foreach ($headers as $header) {
					if ($header != 'data') {
						$headerLine .= $header.',';
					}
				}
				$dataHeader = false;
				$header_array = array();
				foreach ($records as $record) {
					if ($record['data']) {
						$data = json_decode($record['data']);
						foreach ($data as $k => $v) {
							if (!in_array($k, $header_array)) {
								$header_array[] = $k;
							}
						}
					}
				}
				$newDataHeader = implode(",", $header_array);
				echo $headerLine;
				echo implode(",", $header_array);
				foreach ($records as $record) {
					if ($record['data']) {
						$data = json_decode($record['data']);
						$newDataHeader = '';
						foreach ($data as $k => $v) {
							$newDataHeader .= $k.',';
						}
					} else {
						$dataHeader = $newDataHeader;
					}
					if ($type != $record['type'] || $dataHeader != $newDataHeader) {
						$dataHeader = $newDataHeader;
						$header = $headerLine;
						if ($newDataHeader) {
							$header .= $newDataHeader;
						}
						$type = $record['type'];
					}
					echo "\n";
					$last = count($record) - 1;
					$count = 0;
					foreach ($record as $field => $value) {
						if ($field != 'data') {
							echo '"', str_replace('"', '""', $value), '"';
							++$count;
							if ($count != $last) {
								echo ',';
							}
						}
					}
					if ($record['data']) {
						$current_row = json_decode($record['data'], true);					
						foreach ($header_array as $key) {
							if (isset($current_row[$key]) && is_array($current_row[$key])) {
								$value = implode(', ', $current_row[$key]);	
						  	} else if (isset($current_row[$key])) {
								$value = $current_row[$key];								
							} else {
								$value = "";
							}
							echo ',';
							echo '"', str_replace('"', '""', $value), '"';
						}
					}
				}
			} else {
				echo 'No records found';
			}
			exit;
		} // function export
	} // class formDataController

?>
