<?php

	require_once dirname(__DIR__).'/conf/init.php';

	$_table = trim(readline('Table name: '));
	$_label = trim(readline('Table display label: '));

	$buildPath = APP_ROOT.'build/';
	$templatePath = $buildPath.'templates/';

	$settings = array(
		'table'            => $_table,
		'ucFirstTable'     => ucfirst($_table),
		'label'            => $_label
	);
	$settings['primary'] = $settings['table'].'ID';

	$hasText = false;
	$fields = array();
	$sql = "DESC `".$settings['table']."`";
	$result = query($sql);
	if ($result->count > 0) {
		while ($row = $result->fetch()) {
			$fields[$row['Field']] = $row;
			if (preg_match('/^enum\(/', $row['Type'])) {
				$values = explode(',', rtrim(ltrim($row['Type'], 'enum('), ')'));
				$values = preg_replace('/\'/', '', $values);
				$options = array();
				foreach ($values as $key => $val) {
					$options[$val] = $val;
				}
				$fields[$row['Field']]['_options'] = $options;
			}
			if ($row['Key'] == 'PRI') {
				$settings['primary'] = $row['Field'];
			}
			if ($row['Type'] == 'text') {
				$hasText = true;
			}
		}
	}

	$_generateModel = strtolower(trim(readline('Generate model for '.$_table.' (Y/N): ')));
	if ($_generateModel == 'y') {
		$_history = strtolower(trim(readline('Has history table (Y/N): ')));
		// model
		$data = file_get_contents($templatePath.'model.tpl');
		foreach ($settings as $key => $val) {
			$data = str_replace('__'.strtoupper($key).'__', $val, $data);
		}
		$modelFields = '';
		$enumOptions = '';
		$saveActions = '';
		$updateActions = '';
		foreach ($fields as $key => $vals) {
			if (isset($vals['_options'])) {
				$enumOptions .= '		// '.$vals['Field'].' options'."\n";
				$enumOptions .= '		public static $'.$vals['Field'].'Options = array('."\n";
				foreach ($vals['_options'] as $k => $v) {
					$enumOptions .= '			\''.$v.'\' => \''.$v.'\','."\n";
				}
				$enumOptions .= '		);'."\n";
			}
			if ($vals['Field'] == 'dateAdded') {
				$saveActions .= '				$this->setRaw(\''.$vals['Field'].'\', \'NOW()\');'."\n";
			}
			if ($vals['Field'] == 'lastModified') {
				$saveActions .= '				$this->setRaw(\''.$vals['Field'].'\', \'NOW()\');'."\n";
				$updateActions .= '				$this->setRaw(\''.$vals['Field'].'\', \'NOW()\');'."\n";
			}
			$modelFields .= '			\''.$vals['Field'].'\' => false,'."\n";
		}
		$historyTable = '';
		if ($_history == 'y') {
			$historyTable = '		// history table (optional)'."\n";
			$historyTable .= '		protected $_historyTable = \''.$settings['table'].'History\';'."\n";
		}
		$data = preg_replace('/\n__HISTORYTABLE__/', preg_replace('/\n$/', '', !empty($historyTable) ? "\n".$historyTable : $historyTable), $data);
		$data = preg_replace('/\n__FIELDS__/', preg_replace('/\n$/', '', !empty($modelFields) ? "\n".$modelFields : $modelFields), $data);
		$data = preg_replace('/\n__ENUMOPTIONS__/', preg_replace('/\n$/', '', !empty($enumOptions) ? "\n".$enumOptions : $enumOptions), $data);
		$data = preg_replace('/\n__SAVEACTIONS__/', preg_replace('/\n$/', '', !empty($saveActions) ? "\n".$saveActions : $saveActions), $data);
		$data = preg_replace('/\n__UPDATEACTIONS__/', preg_replace('/\n$/', '', !empty($updateActions) ? "\n".$updateActions : $updateActions), $data);
		$file = SYSTEM_ROOT.'models/'.$settings['table'].'.php';
		$fh = fopen($file, 'w');
		fwrite($fh, str_replace("\r", '', $data));
		fclose($fh);
		echo 'Model Generated: '.$file."\n";
	}

	$_generateViews = strtolower(trim(readline('Generate views for '.$_table.' (Y/N): ')));
	if ($_generateViews == 'y') {
		if (!isset($_history)) {
			$_history = strtolower(trim(readline('Include revisions section? (Y/N): ')));
		}

		// data view
		$data = file_get_contents($templatePath.'dataView.tpl');
		foreach ($settings as $key => $val) {
			$data = str_replace('__'.strtoupper($key).'__', $val, $data);
		}
		$file = SYSTEM_ROOT.'dataViews/dataView'.$settings['ucFirstTable'].'.php';
		$fh = fopen($file, 'w');
		fwrite($fh, str_replace("\r", '', $data));
		fclose($fh);
		echo 'Data View Generated: '.$file."\n";

		// admin template
		$data = file_get_contents($templatePath.'adminTemplate.tpl');
		foreach ($settings as $key => $val) {
			$data = str_replace('__'.strtoupper($key).'__', $val, $data);
		}
		$columnHeaders = '';
		$searchInputs = '';
		$recordRows = '';
		$recordFooterCells = '';
		foreach ($fields as $key => $vals) {
			if ($vals['Key'] != 'PRI') {
				$columnHeaders .= '			<td><a href="/'.$settings['table'].'?sortField='.$vals['Field'].'&sortOrder={if $query.sortField == \''.$vals['Field'].'\'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">'.trim(implode(' ', preg_split('/(?=[A-Z])/', ucfirst($vals['Field'])))).'</a></td>'."\n";
				if (isset($vals['_options'])) {
					$searchInputs .= '			<td>'."\n";
					$searchInputs .= '				<select name="'.$vals['Field'].'">'."\n";
					$searchInputs .= '					<option value="">All</option>'."\n";
					$searchInputs .= '					{html_options options=$'.$vals['Field'].'Options selected=$search.'.$vals['Field'].'.value}'."\n";
					$searchInputs .= '				</select>'."\n";
					$searchInputs .= '			</td>'."\n";
					$recordRows .= '			<td>{$records[record].'.$vals['Field'].'}</td>'."\n";
				} else {
					switch ($vals['Type']) {
						case 'datetime':
						case 'timestamp':
						case 'date':
							$searchInputs .= '			<td>'."\n";
							$searchInputs .= '				<table class="searchSection dateSelect">'."\n";
							$searchInputs .= '					<tr>'."\n";
							$searchInputs .= '						<td>From:</td>'."\n";
							$searchInputs .= '						<td>'."\n";
							$searchInputs .= '							<input type="text" name="'.$vals['Field'].'[min]" id="'.$vals['Field'].'Min" value="{if isset($search.'.$vals['Field'].'.value[\'min\'])}{$search.'.$vals['Field'].'.value.min|date_format:"%m/%d/%Y"}{/if}" />'."\n";
							$searchInputs .= '							<img src="/adminImages/calendar.png" id="'.$vals['Field'].'MinButton" />'."\n";
							$searchInputs .= '						</td>'."\n";
							$searchInputs .= '					</tr>'."\n";
							$searchInputs .= '					<tr>'."\n";
							$searchInputs .= '						<td>To:</td>'."\n";
							$searchInputs .= '						<td>'."\n";
							$searchInputs .= '							<input type="text" name="'.$vals['Field'].'[max]" id="'.$vals['Field'].'Max" value="{if isset($search.'.$vals['Field'].'.value[\'max\'])}{$search.'.$vals['Field'].'.value.max|date_format:"%m/%d/%Y"}{/if}" />'."\n";
							$searchInputs .= '							<img src="/adminImages/calendar.png" id="'.$vals['Field'].'MaxButton" />'."\n";
							$searchInputs .= '						</td>'."\n";
							$searchInputs .= '					</tr>'."\n";
							$searchInputs .= '				</table>'."\n";
							$searchInputs .= '{literal}'."\n";
							$searchInputs .= '				<script type="text/javascript">'."\n";
							$searchInputs .= '					Calendar.setup('."\n";
							$searchInputs .= '						{'."\n";
							$searchInputs .= '							inputField : "'.$vals['Field'].'Min",'."\n";
							$searchInputs .= '							ifFormat : "%m/%d/%Y",'."\n";
							$searchInputs .= '							button : "'.$vals['Field'].'MinButton"'."\n";
							$searchInputs .= '						}'."\n";
							$searchInputs .= '					);'."\n";
							$searchInputs .= '					Calendar.setup('."\n";
							$searchInputs .= '						{'."\n";
							$searchInputs .= '							inputField : "'.$vals['Field'].'Max",'."\n";
							$searchInputs .= '							ifFormat : "%m/%d/%Y",'."\n";
							$searchInputs .= '							button : "'.$vals['Field'].'MaxButton"'."\n";
							$searchInputs .= '						}'."\n";
							$searchInputs .= '					);'."\n";
							$searchInputs .= '				</script>'."\n";
							$searchInputs .= '{/literal}'."\n";
							$searchInputs .= '			</td>'."\n";
							$recordRows .= '			<td>{$records[record].'.$vals['Field'].'|date_format:"%D %r"}</td>'."\n";
							break;
						case 'text':
							$searchInputs .= '			<td>'."\n";
							$searchInputs .= '				<input type="text" name="'.$vals['Field'].'" value="{$search.'.$vals['Field'].'.value}" />'."\n";
							$searchInputs .= '			</td>'."\n";
							$recordRows .= '			<td>{$records[record].'.$vals['Field'].'|truncate:80:"..."}</td>'."\n";
							break;
						default:
							$searchInputs .= '			<td>'."\n";
							$searchInputs .= '				<input type="text" name="'.$vals['Field'].'" value="{$search.'.$vals['Field'].'.value}" />'."\n";
							$searchInputs .= '			</td>'."\n";
							$recordRows .= '			<td>{$records[record].'.$vals['Field'].'}</td>'."\n";
							break;
					}
				}
				$recordFooterCells .= '			<td>&nbsp;</td>'."\n";
			}
		}
		$data = preg_replace('/\n__COLUMNHEADERS__/', preg_replace('/\n$/', '', !empty($columnHeaders) ? "\n".$columnHeaders : $columnHeaders), $data);
		$data = preg_replace('/\n__SEARCHINPUTS__/', preg_replace('/\n$/', '', !empty($searchInputs) ? "\n".$searchInputs : $searchInputs), $data);
		$data = preg_replace('/\n__RECORDROWS__/', preg_replace('/\n$/', '', !empty($recordRows) ? "\n".$recordRows : $recordRows), $data);
		$data = preg_replace('/\n__RECORDFOOTERCELLS__/', preg_replace('/\n$/', '', !empty($recordFooterCells) ? "\n".$recordFooterCells : $recordFooterCells), $data);
		$file = TEMPLATE_DIR.'source/'.$settings['table'].'.tpl';
		$fh = fopen($file, 'w');
		fwrite($fh, str_replace("\r", '', $data));
		fclose($fh);
		echo 'Admin Template Generated: '.$file."\n";

		// edit template
		$data = file_get_contents($templatePath.'editTemplate.tpl');
		$propertyMenu = '';
		$infoRows = '';
		$editInputs = '';
		$wysiwyg = '';
		if ($_history == 'y') {
			$revisionSection = file_get_contents($templatePath.'editTemplateRevisions.tpl');
			$data = preg_replace('/__REVISIONS__/',  preg_replace('/\n$/', '', $revisionSection), $data);
			$propertyMenu = '					<li class="editMenuOption{if $propertyMenuItem == \''.$settings['table'].'_revisions\'} selected{/if}" id="'.$settings['table'].'_revisions">Revision History</li>'."\n";
		} else {
			$data = preg_replace('/\n__REVISIONS__/', '', $data);
		}
		foreach ($settings as $key => $val) {
			$data = str_replace('__'.strtoupper($key).'__', $val, $data);
		}
		foreach ($fields as $key => $vals) {
			if ($vals['Key'] != 'PRI') {
				if (isset($vals['_options'])) {
					$editInputs .= '						<tr>'."\n";
					$editInputs .= '							<td><span class="{if \''.$vals['Field'].'\'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">'.ucfirst($vals['Field']).':</span></td>'."\n";
					$editInputs .= '							<td>{html_options name="'.$vals['Field'].'" options=$'.$vals['Field'].'Options selected=$'.$settings['table'].'.'.$vals['Field'].'}</td>'."\n";
					$editInputs .= '						</tr>'."\n";
				} else {
					switch ($vals['Type']) {
						case 'datetime':
						case 'timestamp':
						case 'date':
							$infoRows .= '						<tr>'."\n";
							$infoRows .= '							<td><span class="normalLabel">'.ucfirst($vals['Field']).':</span></td>'."\n";
							$infoRows .= '							<td>{$'.$settings['table'].'.'.$vals['Field'].'|date_format:"%m/%d/%Y %r"}</td>'."\n";
							$infoRows .= '						</tr>'."\n";
							break;
						case 'text':
							$editInputs .= '						<tr>'."\n";
							$editInputs .= '							<td colspan="2"><span class="{if \''.$vals['Field'].'\'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">'.ucfirst($vals['Field']).':</span></td>'."\n";
							$editInputs .= '						</tr>'."\n";
							$editInputs .= '						<tr>'."\n";
							$editInputs .= '							<td colspan="2">'."\n";
							$editInputs .= '								<textarea class="wysiwyg" name="'.$vals['Field'].'" cols="90" rows="20">{$'.$vals['Field'].'|htmlentities}</textarea>'."\n";
							$editInputs .= '								<br />'."\n";
							$editInputs .= '								Editor Note: SHIFT RETURN = new line, RETURN = new paragraph'."\n";
							$editInputs .= '							</td>'."\n";
							$editInputs .= '						</tr>'."\n";
							break;
						default:
							$editInputs .= '						<tr>'."\n";
							$editInputs .= '							<td><span class="{if \''.$vals['Field'].'\'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">'.ucfirst($vals['Field']).':</span></td>'."\n";
							$editInputs .= '							<td><input type="text" name="'.$vals['Field'].'" value="{$'.$settings['table'].'.'.$vals['Field'].'}" style="width: 300px;" /></td>'."\n";
							$editInputs .= '						</tr>'."\n";
							break;
					}
				}
			}
		}
		$data = preg_replace('/\n__PROPERTYMENU__/', preg_replace('/\n$/', '', !empty($propertyMenu) ? "\n".$propertyMenu : $propertyMenu), $data);
		$data = preg_replace('/\n__INFOROWS__/', preg_replace('/\n$/', '', !empty($infoRows) ? "\n".$infoRows : $infoRows), $data);
		$data = preg_replace('/\n__EDITINPUTS__/', preg_replace('/\n$/', '', !empty($editInputs) ? "\n".$editInputs : $editInputs), $data);
		if ($hasText) {
			$wysiwyg = "\n".'<script type="text/javascript">
tinyMCE.init({
	mode : "specific_textareas",
	plugins : "table, nonbreaking, advlink",
	editor_selector : "wysiwyg",
	width : "760",
	height : "500",
	theme : "advanced",
	theme_advanced_buttons1 : "newdocument, bold, italic, underline, strikethrough, sub, sup, justifyleft, justifycenter, justifyright, justifyfull, formatselect, fontselect, fontsizeselect",
	theme_advanced_buttons2 : "forecolorpicker, backcolorpicker, nonbreaking, hr, removeformat, charmap, bullist, numlist, outdent, indent, blockquote, undo, redo, link, unlink, anchor, image, cleanup, code",
	theme_advanced_buttons3 : "tablecontrols",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
	table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
	table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
	table_cell_limit : 100,
	table_row_limit : 5,
	table_col_limit : 5,
	relative_urls : false,
	convert_urls : false,
	remove_script_host : true,
	verify_html : false,
	force_p_newlines : false,
	force_root_block : false,
	document_base_url : "http://{$smarty.server.HTTP_HOST}",
	cleanup_on_startup: false,
	trim_span_elements: false,
	cleanup: false,
	convert_urls: false
});
</script>'."\n";
			$data = preg_replace('/\n__WYSIWYG__/', preg_replace('/\n$/', '', "\n".$wysiwyg), $data);
		} else {
			$data = preg_replace('/\n__WYSIWYG__/', '', $data);
		}
		$file = TEMPLATE_DIR.'source/'.$settings['table'].'Edit.tpl';
		$fh = fopen($file, 'w');
		fwrite($fh, str_replace("\r", '', $data));
		fclose($fh);
		echo 'Edit Template Generated: '.$file."\n";
	}

	$_generateController = strtolower(trim(readline('Generate controller for '.$_table.' (Y/N): ')));
	if ($_generateController == 'y') {
		if (!isset($_history)) {
			$_history = strtolower(trim(readline('Include revisions section? (Y/N): ')));
		}
		// admin controller
		$data = file_get_contents($templatePath.'adminController.tpl');
		$selectOptions = '';
		$assigns = '';
		if ($_history == 'y') {
			$assigns .= '			$this->assign(\'revisions\', $'.$settings['table'].'->getHistory());'."\n";
			$historyLoader = file_get_contents($templatePath.'adminControllerRollback.tpl');
			$data = preg_replace('/__HISTORY__/',  preg_replace('/\n$/', '', $historyLoader), $data);
		} else {
			$data = preg_replace('/\n__HISTORY__/', '', $data);
		}
		foreach ($settings as $key => $val) {
			$data = str_replace('__'.strtoupper($key).'__', $val, $data);
		}
		$saveProcess = '';
		foreach ($fields as $key => $vals) {
			if ($vals['Key'] != 'PRI') {
				switch ($vals['Type']) {
					case 'datetime':
					case 'timestamp':
					case 'date':
						break;
					default:
						if (isset($vals['_options'])) {
							$selectOptions .= '			$this->assign(\''.$vals['Field'].'Options\', '.$settings['table'].'::$'.$vals['Field'].'Options);'."\n";
						}
						if ($vals['Type'] == 'text') {
							$assigns .= '			$this->assign(\''.$vals['Field'].'\', $'.$settings['table'].'->'.$vals['Field'].');'."\n";
						}
						$saveProcess .= '			$'.$settings['table'].'->'.$vals['Field'].' = getPost(\''.$vals['Field'].'\');'."\n";
						break;
				}
			}
		}
		$data = preg_replace('/\n__SELECTOPTIONS__/', preg_replace('/\n$/', '', !empty($selectOptions) ? "\n".$selectOptions : $selectOptions), $data);
		$data = preg_replace('/\n__ASSIGNS__/', preg_replace('/\n$/', '', !empty($assigns) ? "\n".$assigns : $assigns), $data);
		$data = preg_replace('/\n__SAVEPROCESS__/', preg_replace('/\n$/', '', !empty($saveProcess) ? "\n".$saveProcess : $saveProcess), $data);
		$file = APP_ROOT.'controllers/'.$settings['table'].'Controller.php';
		$fh = fopen($file, 'w');
		fwrite($fh, str_replace("\r", '', $data));
		fclose($fh);
		echo 'Controller Generated: '.$file."\n";
	}

?>
