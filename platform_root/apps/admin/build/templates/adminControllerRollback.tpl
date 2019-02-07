			$history = false;
			if ($date = getRequest('d')) {
				$dateStr = date('m/d/Y H:i:s', strtotime($date));
				if ($history = $__TABLE__->getHistory($date)) {
					$__TABLE__->loadData($history);
					addSuccess('Record on '.$dateStr.' loaded');
					addSuccess('Please review and press update to save');
				} else {
					addError('Record on '.$dateStr.' could not be found');
				}
			}
