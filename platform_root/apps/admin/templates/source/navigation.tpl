{include file="header.tpl"}

<p class="adminOption"><a href="/navigation/addNavigation" class="greenPlus">New Navigation</a></p>

<form action="/navigation?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post" id="overviewForm">

	{include file="pagination.tpl"}

	<input type="hidden" name="runSearch" value="true" />

	<table class="recordsTable">

		<tr class="recordsHeader">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><a href="/navigation?sortField=navigationID&sortOrder={if $query.sortField == 'navigationID'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">ID</a></td>
			<td><a href="/navigation?sortField=label&sortOrder={if $query.sortField == 'label'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Label</a></td>
			<td><a href="/navigation?sortField=url&sortOrder={if $query.sortField == 'url'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Url</a></td>
			<td align="right"><a href="/navigation?sortField=order&sortOrder={if $query.sortField == 'order'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Order</a></td>
		</tr>

		<tr class="recordSearchHeader">
			<td>
				<input class="button" type="submit" value="Search" />
			</td>
			<td>
				<a href="javascript: void(0);" id="selectToggle" class="selectAll iconOnly" title="Select All">&nbsp;</a>
				<a href="javascript: void(0);" id="recordOverviewActionTrigger" class="forward iconOnly" title="Perform Action">&nbsp;</a>
			</td>
			<td class="idField"><input type="text" name="navigationID" value="{$search.navigationID.value}" /></td>
			<td>
				<input type="text" name="label" value="{$search.label.value}" />
			</td>
			<td>
				<input type="text" name="url" value="{$search.url.value}" />
			</td>
			<td>
				<input type="text" name="order" value="{$search.order.value}" />
			</td>
		</tr>

{section name=record loop=$records}
		<tr class="{cycle values="recordsRowAlpha,recordsRowBeta"}">
			<td align="center">
				<a href="/navigation/editNavigation?navigationID={$records[record].navigationID}" class="edit iconOnly" title="Edit Navigation">&nbsp;</a>
			</td>
			<td align="center">
				<input type="checkbox" name="selected[]" value="{$records[record].navigationID}" />
			</td>
			<td align="right">{$records[record].navigationID}</td>
			<td>{$records[record].label}</td>
			<td>{$records[record].url}</td>
			<td align="right">{$records[record].order}</td>
		</tr>
{/section}

		<tr class="recordsFooter">
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
