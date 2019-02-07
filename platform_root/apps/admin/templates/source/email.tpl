{include file="header.tpl"}

<p class="adminOption"><a href="/email/addEmail" class="greenPlus">New Email</a></p>

<form action="/email?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post" id="overviewForm">

	{include file="pagination.tpl"}

	<input type="hidden" name="runSearch" value="true" />

	<table class="recordsTable">

		<tr class="recordsHeader">
			<td colspan="3">&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><a href="/email?sortField=emailID&sortOrder={if $query.sortField == 'emailID'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">ID</a></td>
			<td><a href="/email?sortField=name&sortOrder={if $query.sortField == 'name'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Name</a></td>
			<td><a href="/email?sortField=subject&sortOrder={if $query.sortField == 'subject'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Subject</a></td>
			<td><a href="/email?sortField=fromEmail&sortOrder={if $query.sortField == 'fromEmail'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">From Email</a></td>
			<td><a href="/email?sortField=headerID&sortOrder={if $query.sortField == 'headerID'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Header</a></td>
			<td><a href="/email?sortField=footerID&sortOrder={if $query.sortField == 'footerID'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Footer</a></td>
		</tr>

		<tr class="recordSearchHeader">
			<td colspan="3">
				<input class="button" type="submit" value="Search" />
			</td>
			<td>
				<a href="javascript: void(0);" id="selectToggle" class="selectAll iconOnly" title="Select All">&nbsp;</a>
				<a href="javascript: void(0);" id="recordOverviewActionTrigger" class="forward iconOnly" title="Perform Action">&nbsp;</a>
			</td>
			<td class="idField"><input type="text" name="emailID" value="{$search.emailID.value}" /></td>
			<td>
				<input type="text" name="name" value="{$search.name.value}" />
				<input type="hidden" name="nameOperator" value="contains" />
			</td>
			<td>
				<input type="text" name="subject" value="{$search.subject.value}" />
				<input type="hidden" name="subjectOperator" value="contains" />
			</td>
			<td>
				<input type="text" name="fromEmail" value="{$search.fromEmail.value}" />
				<input type="hidden" name="fromEmailOperator" value="contains" />
			</td>
			<td>
				{html_options name=headerID options=$headers selected=$search.headerID.value}
			</td>
			<td>
				{html_options name=footerID options=$footers selected=$search.footerID.value}
			</td>
		</tr>

{section name=record loop=$records}
		<tr class="{cycle values="recordsRowAlpha,recordsRowBeta"}">
			<td align="center">
				<a href="/email/preview?emailID={$records[record].emailID}" class="viewContent iconOnly" title="Preview Email" target="_blank" onclick="window.open(this.href, 'Preview Email', 'width=600, height=400'); return false;">&nbsp;</a>
			</td>
			<td align="center">
				<a href="/email/editEmail?emailID={$records[record].emailID}" class="edit iconOnly" title="Edit Email">&nbsp;</a>
			</td>
			<td align="center">
				<a href="/email/sendEmail?emailID={$records[record].emailID}" class="forward iconOnly" title="Send Email">&nbsp;</a>
			</td>
			<td align="center">
				<input type="checkbox" name="selected[]" value="{$records[record].emailID}" />
			</td>
			<td align="right">{$records[record].emailID}</td>
			<td>{$records[record].name}</td>
			<td>{$records[record].subject}</td>
			<td>{$records[record].fromEmail}</td>
			<td>{if $records[record].headerID}{$headers[$records[record].headerID]}{/if}</td>
			<td>{if $records[record].footerID}{$footers[$records[record].footerID]}{/if}</td>
		</tr>
{/section}

		<tr class="recordsFooter">
			<td colspan="3">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>

	</table>

</form>

<div id="recordOverviewActions">
	<a href="javascript: void(0);" id="duplicateToLanguage" class="greenPlus" />Duplicate To Language</a>
	<br />
	<a href="javascript: void(0);" id="deleteSelected" class="remove" />Delete Selected</a>
	<a href="javascript: void(0);" id="recordOverviewActionsClose" class="redX">&nbsp;</a>
</div>

{include file="footer.tpl"}
