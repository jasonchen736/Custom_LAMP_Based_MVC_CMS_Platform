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

<form action="/emailSection/{if $mode == 'edit'}updateEmailSection{else}saveEmailSection{/if}" method="post" id="editForm">
	<input type="hidden" name="languageID" value="{$languageID}" />
	<table id="editTable">
		<tr>
			<td id="editMenuCell">
				<a href="{$lastQuery}" class="viewContent">Return to previous search</a>
				<br /><br />
				<ul id="editMenu">
					<li class="head">&nbsp;</li>
					<li class="editMenuOption{if !$propertyMenuItem || $propertyMenuItem == 'emailSection_main'} selected{/if}" id="emailSection_main">Section Details</li>
					<li class="editMenuOption{if $propertyMenuItem == 'emailSection_content'} selected{/if}" id="emailSection_content">Content</li>
					<li class="end">&nbsp;</li>
				</ul>
				<div id="editActionContainer">
{if $mode == 'edit'}
					<input type="hidden" name="emailSectionID" value="{$editEmailSection.emailSectionID}" />
					<input class="button" type="submit" name="submit" value="Update" id="update" />
{else}
					<input class="button" type="submit" name="submit" value="Add and Edit" />
					<input class="button" type="submit" name="submit" value="Add Another" />
{/if}
				</div>
			</td>
			<td id="editPropertyCell">
				<div id="emailSection_mainContainer" class="propertyContainer{if $propertyMenuItem && $propertyMenuItem != 'emailSection_main'} hidden{/if}">
					<table>
{if $mode == 'edit'}
						<tr>
							<td><span class="normalLabel">Email Section ID:</span></td>
							<td>{$editEmailSection.emailSectionID}</td>
						</tr>
						<tr>
							<td><span class="normalLabel">Date Added:</span></td>
							<td>{$editEmailSection.dateAdded|date_format:"%m/%d/%Y %r"}</td>
						</tr>
						<tr>
							<td><span class="normalLabel">Last Modified:</span></td>
							<td>{$editEmailSection.lastModified|date_format:"%m/%d/%Y %r"}</td>
						</tr>
{/if}
						<tr>
							<td><span class="{if 'name'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Name:</span></td>
							<td><input type="text" name="name" value="{$editEmailSection.name}" style="width: 300px;" /></td>
						</td>
						<tr>
							<td><span class="{if 'type'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Type:</span></td>
							<td>{html_options name="type" options=$typeOptions selected=$editEmailSection.type}</td>
						</tr>
					</table>
				</div>
				<div id="emailSection_contentContainer" class="propertyContainer{if !$propertyMenuItem || $propertyMenuItem != 'emailSection_content'} hidden{/if}">
					<table>
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

{include file="footer.tpl"}
