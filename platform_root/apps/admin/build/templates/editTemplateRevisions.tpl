				<div id="__TABLE___revisionsContainer" class="propertyContainer{if $propertyMenuItem != '__TABLE___revisions'} hidden{/if}">
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
								<a href="{$currentLanguage['url']}/previewContent?_type=__TABLE__&_id={$revisions[record].__PRIMARY__}&_d={$revisions[record].effectiveThrough}" class="viewContent iconOnly" title="View Revision" target="_blank">&nbsp;</a>
							</td>
							<td align="center">
{if $revisions[record].effectiveThrough != '9999-12-31 23:59:59'}
								<a href="/__TABLE__/edit__UCFIRSTTABLE__?__PRIMARY__={$revisions[record].__PRIMARY__}&d={$revisions[record].effectiveThrough}" class="forward iconOnly rollback" title="Rollback">&nbsp;</a>
{/if}
							</td>
							<td>{$revisions[record].__TABLE__HistoryID}</td>
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
