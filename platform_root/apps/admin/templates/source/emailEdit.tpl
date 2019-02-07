{include file="header.tpl"}

<script type="text/javascript">
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
	remove_script_host : true,
	verify_html : false,
	document_base_url : "http://{$smarty.server.HTTP_HOST}",
	content_css : "{$currentLanguage['url']}/css/main.css",
	cleanup_on_startup: false,
	trim_span_elements: false,
	cleanup: false,
	convert_urls: false
});
</script>

<form action="/email/{if $mode == 'edit'}updateEmail{else}saveEmail{/if}" method="post" id="editForm">
	<input type="hidden" name="languageID" value="{$languageID}" />
	<table id="editTable">
		<tr>
			<td id="editMenuCell">
				<a href="{$lastQuery}" class="viewContent">Return to previous search</a>
				<br /><br />
				<ul id="editMenu">
					<li class="head">&nbsp;</li>
					<li class="editMenuOption{if !$propertyMenuItem || $propertyMenuItem == 'email_main'} selected{/if}" id="email_main">Email Details</li>
					<li class="editMenuOption{if $propertyMenuItem == 'email_content'} selected{/if}" id="email_content">Content</li>
					<li class="end">&nbsp;</li>
				</ul>
				<div id="editActionContainer">
{if $mode == 'edit'}
					<input type="hidden" name="emailID" value="{$editEmail.emailID}" />
					<input class="button" type="submit" name="submit" value="Update" id="update" />
{else}
					<input class="button" type="submit" name="submit" value="Add and Edit" />
					<input class="button" type="submit" name="submit" value="Add Another" />
{/if}
				</div>
			</td>
			<td id="editPropertyCell">
				<div id="email_mainContainer" class="propertyContainer{if $propertyMenuItem && $propertyMenuItem != 'email_main'} hidden{/if}">
					<table>
{if $mode == 'edit'}
						<tr>
							<td><span class="normalLabel">Email ID:</span></td>
							<td>{$editEmail.emailID}</td>
						</tr>
						<tr>
							<td><span class="normalLabel">Date Added:</span></td>
							<td>{$editEmail.dateAdded|date_format:"%m/%d/%Y %r"}</td>
						</tr>
						<tr>
							<td><span class="normalLabel">Last Modified:</span></td>
							<td>{$editEmail.lastModified|date_format:"%m/%d/%Y %r"}</td>
						</tr>
{/if}
						<tr>
							<td><span class="{if 'name'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Name:</span></td>
							<td><input type="text" name="name" value="{$editEmail.name}" style="width: 300px;" /></td>
						</tr>
						<tr>
							<td><span class="{if 'headerID'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Header ID:</span></td>
							<td>{html_options name=headerID options=$headers selected=$editEmail.headerID}</td>
						</tr>
						<tr>
							<td><span class="{if 'footerID'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Footer ID:</span></td>
							<td>{html_options name=footerID options=$footers selected=$editEmail.footerID}</td>
						</tr>
						<tr>
							<td><span class="{if 'fromEmail'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">From Email:</span></td>
							<td><input type="text" name="fromEmail" value="{$editEmail.fromEmail}" style="width: 300px;" /></td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td valign="top">
								<a href="javascript: void(0);" class="greenPlus" id="addRecipient">Add Recipient</a>
							</td>
							<td valign="top">
								<table{if empty($recipients)} style="display: none"{/if} id="recipientsTable">
									<tr>
										<td colspan="2"><span class="normalLabel">Email</span></td>
										<td><span class="normalLabel">Condition</span></td>
										<td><span class="normalLabel">Value</span></td>
									</tr>
{if $recipients}
{foreach from=$recipients item=recipient}
									<tr>
										<td><a href="javascript: void(0);" class="removeRecipient redX">&nbsp;</a></td>
										<td><input type="text" name="recipient[]" value="{$recipient[0]}" /></td>
										<td><input type="text" name="condition[]" value="{$recipient[1]}" /></td>
										<td><input type="text" name="value[]" value="{$recipient[2]}" /></td>
									</tr>
{/foreach}
{/if}
								</table>
								<table{if empty($recipients)} style="display: none"{/if} id="recipientsNotes">
									<tr>
										<td colspan="4">
											<small>
												<strong>Note:</strong> Condition and value fields are optional<br />
												If a condition is entered, the email will only send if the condition form field is submitted<br />
												If a condition and value is entered, the email will only send if the condition form field matches the value<br />
											</small>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
				<div id="email_contentContainer" class="propertyContainer{if !$propertyMenuItem || $propertyMenuItem != 'email_content'} hidden{/if}">
					<table>
						<tr>
							<td><span class="{if 'subject'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Subject:</span></td>
							<td><input type="text" name="subject" value="{$editEmail.subject}" style="width: 600px;" /></td>
						</tr>
						<tr>
							<td colspan="2"><span class="{if 'text'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Text:</span></td>
						</tr>
						<tr>
							<td colspan="2">
								<textarea name="text" cols="90" rows="20">{$text}</textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2"><span class="{if 'html'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Html:</span></td>
						</tr>
						<tr>
							<td colspan="2">
								<textarea class="wysiwyg" name="html" cols="90" rows="20">{$html|htmlentities}</textarea>
								<br />
								Editor Note: SHIFT RETURN = new line, RETURN = new paragraph
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="propertyMenuItem" id="propertyMenuItem" value="{$propertyMenuItem}" />
</form>

<div id="recipientTemplate">
	<table>
		<tr>
			<td><a href="javascript: void(0);" class="removeRecipient redX">&nbsp;</a></td>
			<td><input type="text" name="recipient[]" value="" /></td>
			<td><input type="text" name="condition[]" value="" /></td>
			<td><input type="text" name="value[]" value="" /></td>
		</tr>
	</table>
</div>

{include file="footer.tpl"}
