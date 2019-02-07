<?php

	class errorController extends bController {
		/**
		 *  Render error page
		 *  Args: (str) message
		 *  Return: none
		 */
		public function error($message) {
			if ($message == '404') {
				header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
				$page = page::getObject(array('name' => 'error_404'));
				if (!$page) {
					$page = new page;
					$page->title = '404 Not Found';
					$page->content = 'Default 404 message.  Create a page named "error_404" to customize.';
				}
			} else {
				header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error', true, 500);
				$page = page::getObject(array('name' => 'error_500'));
				if (!$page) {
					$page = new page;
					$page->title = '500 Internal Server Error';
					$page->content = 'Default 500 message. Create a page named "error_500" to customize.';
				}
			}
			$this->assignClean('_TITLE', $page->title);
			if (!empty($page->metaDescription)) {
				$this->addMeta('description', $page->metaDescription);
			}
			if (!empty($page->metaKeywords)) {
				$this->addMeta('keywords', $page->metaKeywords);
			}
			$this->assign('content', $page->renderContent());
			$this->renderView('page.tpl');
		}
	} // function errorController

?>
