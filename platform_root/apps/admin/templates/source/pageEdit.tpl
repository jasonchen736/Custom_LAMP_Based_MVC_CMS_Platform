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
$(function() {
	Calendar.setup(
		{
			inputField : "articleDate",
			ifFormat : "%m/%d/%Y",
			button : "articleDateButton"
		}
	);
	$('#type').change(function() {
		updateType();
	});
	updateType();
	function updateType() {
		if ($('#type').val() == 'news') {
			$('.news').show();
		} else {
			$('.news').hide();
		}
	};
});
</script>

<form action="/page/{if $mode == 'edit'}updatePage{else}savePage{/if}" method="post" id="editForm">
	<input type="hidden" name="_type" value="page" />
	<input type="hidden" name="languageID" value="{$languageID}" />
	<table id="editTable">
		<tr>
			<td id="editMenuCell">
				<a href="{$lastQuery}" class="viewContent">Back To Last Search</a>
				<br /><br />
				<ul id="editMenu">
					<li class="head"> </li>
					<li class="editMenuOption{if !$propertyMenuItem || $propertyMenuItem == 'page_main'} selected{/if}" id="page_main">Page Details</li>
					<li class="editMenuOption{if $propertyMenuItem == 'page_metadata'} selected{/if}" id="page_metadata">Metadata</li>
					<li class="editMenuOption{if $propertyMenuItem == 'page_revisions'} selected{/if}" id="page_revisions">Revision History</li>
					<li class="end"> </li>
				</ul>
				<div id="editActionContainer">
{if $mode == 'edit'}
					<input type="hidden" name="pageID" value="{$page.pageID}" />
					<input class="button" type="submit" name="submit" value="Update" id="update" />
					<input class="button" type="submit" name="preview" value="Preview" id="preview" style="display: none" />
{else}
					<input class="button" type="submit" name="submit" value="Add and Edit" />
					<input class="button" type="submit" name="submit" value="Add Another" />
{/if}
				</div>
			</td>
			<td id="editPropertyCell">
				<div id="page_mainContainer" class="propertyContainer{if $propertyMenuItem && $propertyMenuItem != 'page_main'} hidden{/if}">
					<table>
{if $mode == 'edit'}
						<tr>
							<td><span class="normalLabel">Page ID:</span></td>
							<td>{$page.pageID}</td>
						</tr>
						<tr>
							<td><span class="normalLabel">Date Added:</span></td>
							<td>{$page.dateAdded|date_format:"%m/%d/%Y %r"}</td>
						</tr>
						<tr>
							<td><span class="normalLabel">Last Modified:</span></td>
							<td>{$page.lastModified|date_format:"%m/%d/%Y %r"}</td>
						</tr>
{/if}
						<tr>
							<td><span class="{if 'status'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Status:</span></td>
							<td>{html_options name="status" options=$statusOptions selected=$page.status}</td>
						</tr>
						<tr>
							<td><span class="{if 'name'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Name:</span></td>
							<td><input type="text" name="name" value="{$page.name}" style="width: 300px;" /></td>
						</tr>
						<tr>
							<td><span class="{if 'type'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Page Type:</span></td>
							<td>{html_options name="type" id="type" options=$typeOptions selected=$page.type}</td>
						</tr>
						<tr class="news">
							<td><span class="{if 'articleDate'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Article Date:</span></td>
							<td>
								<input type="text" name="articleDate" id="articleDate" value="{$page.articleDate|date_format:"%m/%d/%Y"}" />
								<img src="/adminImages/calendar.png" id="articleDateButton" style="position: relative; top: 5px" />
								
							</td>
						</tr>
						<tr>
							<td><span class="{if 'title'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Title:</span></td>
							<td><input type="text" name="title" value="{$page.title}" style="width: 300px;" /></td>
						</tr>
						<tr class="news">
							<td colspan="2"><span class="{if 'summary'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Summary::</span></td>
						</tr>
						<tr class="news">
							<td colspan="2">
								<textarea name="summary" cols="90" rows="5">{$page.summary}</textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2"><span class="{if 'content'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Content:</span></td>
						</tr>
						<tr>
							<td colspan="2">
								<textarea class="wysiwyg" name="content" cols="90" rows="20">{$content|htmlentities}</textarea>
								<br />
								Place content modules with "[content:name of module]"
								<br /><br />
								Editor Note: SHIFT RETURN = new line, RETURN = new paragraph
							</td>
						</tr>
					</table>
				</div>
				<div id="page_metadataContainer" class="propertyContainer{if $propertyMenuItem != 'page_metadata'} hidden{/if}">
					<table>
						<tr>
							<td>
								<span class="{if 'metaDescription'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Description:</span>
								<br />
								<textarea name="metaDescription" cols="100%" rows="10">{$page.metaDescription}</textarea>
							</td>
						</tr>
						<tr>
							<td>
								<span class="{if 'metaKeywords'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Keywords:</span>
								<br />
								<textarea name="metaKeywords" cols="100%" rows="10">{$page.metaKeywords}</textarea>
							</td>
						</tr>
					</table>
				</div>
				<div id="page_revisionsContainer" class="propertyContainer{if $propertyMenuItem != 'page_revisions'} hidden{/if}">
					<table class="recordsTable">
						<tr class="recordsHeader">
							<td colspan="2">Actions</td>
							<td>Revision ID</td>
							<td>Action</td>
							<td>Editor</td>
							<td>Comments</td>
							<td>Date Modified</td>
							<td>Effective Through</td>
						</tr>
{section name=record loop=$revisions}
						<tr class="{cycle values="recordsRowAlpha,recordsRowBeta"}">
							<td align="center">
								<a href="{$currentLanguage['url']}/previewContent?_type=page&_id={$revisions[record].pageID}&_d={$revisions[record].effectiveThrough}" class="viewContent iconOnly" title="View Page Revision" target="_blank">&nbsp;</a>
							</td>
							<td align="center">
{if $revisions[record].effectiveThrough != '9999-12-31 23:59:59'}
								<a href="/page/editPage?pageID={$revisions[record].pageID}&d={$revisions[record].effectiveThrough}" class="forward iconOnly rollback" title="Rollback Page">&nbsp;</a>
{/if}
							</td>
							<td>{$revisions[record].pageHistoryID}</td>
							<td>{$revisions[record].action}</td>
							<td>{$revisions[record].editor}</td>
							<td>{$revisions[record].comments}</td>
							<td>{$revisions[record].lastModified|date_format:"%m/%d/%Y %r"}</td>
							<td>{$revisions[record].effectiveThrough|date_format:"%m/%d/%Y %r"}</td>
						</tr>
{/section}
						<tr class="recordsFooter">
							<td colspan="2">&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="propertyMenuItem" id="propertyMenuItem" value="{$propertyMenuItem}" />
</form>

{include file="footer.tpl"}
