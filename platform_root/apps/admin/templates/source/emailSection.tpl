{include file="header.tpl"}

<p class="adminOption"><a href="/emailSection/addEmailSection" class="greenPlus">New Email Section</a></p>

<form action="/emailSection?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post" id="overviewForm">

	{include file="pagination.tpl"}

	<input type="hidden" name="runSearch" value="true" />

	<table class="recordsTable">

		<tr class="recordsHeader">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><a href="/emailSection?sortField=emailSectionID&sortOrder={if $query.sortField == 'emailSectionID'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">ID</a></td>
			<td><a href="/emailSection?sortField=type&sortOrder={if $query.sortField == 'type'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Type</a></td>
			<td><a href="/emailSection?sortField=name&sortOrder={if $query.sortField == 'name'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Name</a></td>
		</tr>

		<tr class="recordSearchHeader">
			<td>
				<input class="button" type="submit" value="Search" />
			</td>
			<td>
				<a href="javascript: void(0);" id="selectToggle" class="selectAll iconOnly" title="Select All">&nbsp;</a>
				<a href="javascript: void(0);" id="recordOverviewActionTrigger" class="forward iconOnly" title="Perform Action">&nbsp;</a>
			</td>
			<td class="idField"><input type="text" name="emailSectionID" value="{$search.emailSectionID.value}" /></td>
			<td>
				<select name="type">
					<option value="">All</option>
					{html_options options=$typeOptions selected=$search.type.value}
				</select>
			</td>
			<td>
				<input type="text" name="name" value="{$search.name.value}" />
				<input type="hidden" name="nameOperator" value="contains" />
			</td>
		</tr>

{section name=record loop=$records}
		<tr class="{cycle values="recordsRowAlpha,recordsRowBeta"}">
			<td align="center">
				<a href="/emailSection/editEmailSection?emailSectionID={$records[record].emailSectionID}" class="edit iconOnly" title="Edit Email Section">&nbsp;</a>
			</td>
			<td align="center">
				<input type="checkbox" name="selected[]" value="{$records[record].emailSectionID}" />
			</td>
			<td align="right">{$records[record].emailSectionID}</td>
			<td>{$records[record].type}</td>
			<td>{$records[record].name}</td>
		</tr>
{/section}

		<tr class="recordsFooter">
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
