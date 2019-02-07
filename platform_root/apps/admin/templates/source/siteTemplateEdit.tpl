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

<form action="/siteTemplate/{if $mode == 'edit'}updateSiteTemplate{else}saveSiteTemplate{/if}" method="post" id="editForm">
	<input type="hidden" name="_type" value="siteTemplate" />
	<input type="hidden" name="languageID" value="{$languageID}" />
	<table id="editTable">
		<tr>
			<td id="editMenuCell">
				<ul id="editMenu">
					<li class="head">&nbsp;</li>
					<li class="editMenuOption{if !$propertyMenuItem || $propertyMenuItem == 'siteTemplate_main'} selected{/if}" id="siteTemplate_main">Site Template Details</li>
					<li class="editMenuOption{if $propertyMenuItem == 'siteTemplate_revisions'} selected{/if}" id="siteTemplate_revisions">Revision History</li>
					<li class="end">&nbsp;</li>
				</ul>
				<div id="editActionContainer">
{if $mode == 'edit'}
					<input type="hidden" name="siteTemplateID" value="{$siteTemplate.siteTemplateID}" />
					<input class="button" type="submit" name="submit" value="Update" id="update" />
					<input class="button" type="submit" name="preview" value="Preview" id="preview" style="display: none" />
{else}
					<input class="button" type="submit" name="submit" value="Save" />
{/if}
				</div>
			</td>
			<td id="editPropertyCell">
				<div id="siteTemplate_mainContainer" class="propertyContainer{if $propertyMenuItem && $propertyMenuItem != 'siteTemplate_main'} hidden{/if}">
					<table>
{if $mode == 'edit'}
						<tr>
							<td><span class="normalLabel">DateAdded:</span></td>
							<td>{$siteTemplate.dateAdded|date_format:"%m/%d/%Y %r"}</td>
						</tr>
						<tr>
							<td><span class="normalLabel">LastModified:</span></td>
							<td>{$siteTemplate.lastModified|date_format:"%m/%d/%Y %r"}</td>
						</tr>
{/if}
						<tr>
							<td colspan="2">
								<textarea class="wysiwyg" name="content" cols="90" rows="20">{$content|htmlentities}</textarea>
								<br />
								Place page content with "[content]"
								<br /><br />
								Editor Note: SHIFT RETURN = new line, RETURN = new paragraph
							</td>
						</tr>
					</table>
				</div>
				<div id="siteTemplate_revisionsContainer" class="propertyContainer{if $propertyMenuItem != 'siteTemplate_revisions'} hidden{/if}">
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
								<a href="{$currentLanguage['url']}/previewContent?_type=siteTemplate&_id={$revisions[record].siteTemplateID}&_d={$revisions[record].effectiveThrough}" class="viewContent iconOnly" title="View Revision" target="_blank">&nbsp;</a>
							</td>
							<td align="center">
{if $revisions[record].effectiveThrough != '9999-12-31 23:59:59'}
								<a href="/siteTemplate?d={$revisions[record].effectiveThrough}" class="forward iconOnly rollback" title="Rollback">&nbsp;</a>
{/if}
							</td>
							<td>{$revisions[record].siteTemplateHistoryID}</td>
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
