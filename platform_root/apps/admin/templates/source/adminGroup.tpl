{include file="header.tpl"}

<p class="adminOption"><a href="/adminGroup/addGroup" class="greenPlus">New Admin Group</a></p>

{include file="pagination.tpl"}

<form action="/adminGroup?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post" id="overviewForm">
	<input type="hidden" name="runSearch" value="true" />

	<table class="recordsTable">

		<tr class="recordsHeader">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><a href="/adminGroup?sortField=adminGroupID&sortOrder={if $query.sortField == 'adminGroupID'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Group ID</a></td>
			<td><a href="/adminGroup?sortField=name&sortOrder={if $query.sortField == 'name'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Name</a></td>
		</tr>

		<tr class="recordSearchHeader">
			<td>
				<input class="button" type="submit" value="Search" />
			</td>
			<td>
				<a href="javascript: void(0);" id="selectToggle" class="selectAll iconOnly" title="Select All">&nbsp;</a>
				<a href="javascript: void(0);" id="recordOverviewActionTrigger" class="forward iconOnly" title="Perform Action">&nbsp;</a>
			</td>
			<td class="idField"><input type="text" name="adminGroupID" value="{$search.adminGroupID.value}" /></td>
			<td>
				<input type="text" name="name" value="{$search.name.value}" />
				<input type="hidden" name="name_operator" value="contains" />
			</td>
		</tr>

{section name=record loop=$records}
		<tr class="{cycle values="recordsRowAlpha,recordsRowBeta"}">
			<td align="center">
				<a href="/adminGroup/editGroup?adminGroupID={$records[record].adminGroupID}" class="edit iconOnly" title="Edit Group">&nbsp;</a>
			</td>
			<td align="center">
				<input type="checkbox" name="selected[]" value="{$records[record].adminGroupID}" />
			</td>
			<td align="right">{$records[record].adminGroupID}</td>
			<td>{$records[record].name}</td>
		</tr>
{/section}

		<tr class="recordsFooter">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>

	</table>

</form>

<div id="recordOverviewActions">
	<a href="javascript: void(0);" id="deleteSelected" class="remove" />Delete Selected</a>
	<a href="javascript: void(0);" id="recordOverviewActionsClose" class="redX">&nbsp;</a>
</div>

{include file="footer.tpl"}
