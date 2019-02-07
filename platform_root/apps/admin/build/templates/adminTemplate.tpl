{include file="header.tpl"}

<p class="adminOption"><a href="/__TABLE__/add__UCFIRSTTABLE__" class="greenPlus">New __LABEL__</a></p>

<form action="/__TABLE__?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post" id="overviewForm">

	{include file="pagination.tpl"}

	<input type="hidden" name="runSearch" value="true" />

	<table class="recordsTable">

		<tr class="recordsHeader">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><a href="/__TABLE__?sortField=__PRIMARY__&sortOrder={if $query.sortField == '__PRIMARY__'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">ID</a></td>
__COLUMNHEADERS__
		</tr>

		<tr class="recordSearchHeader">
			<td>
				<input class="button" type="submit" value="Search" />
			</td>
			<td>
				<a href="javascript: void(0);" id="selectToggle" class="selectAll iconOnly" title="Select All">&nbsp;</a>
				<a href="javascript: void(0);" id="recordOverviewActionTrigger" class="forward iconOnly" title="Perform Action">&nbsp;</a>
			</td>
			<td class="idField"><input type="text" name="__PRIMARY__" value="{$search.__PRIMARY__.value}" /></td>
__SEARCHINPUTS__
		</tr>

{section name=record loop=$records}
		<tr class="{cycle values="recordsRowAlpha,recordsRowBeta"}">
			<td align="center">
				<a href="/__TABLE__/edit__UCFIRSTTABLE__?__PRIMARY__={$records[record].__PRIMARY__}" class="edit iconOnly" title="Edit __LABEL__">&nbsp;</a>
			</td>
			<td align="center">
				<input type="checkbox" name="selected[]" value="{$records[record].__PRIMARY__}" />
			</td>
			<td align="right">{$records[record].__PRIMARY__}</td>
__RECORDROWS__
		</tr>
{/section}

		<tr class="recordsFooter">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
__RECORDFOOTERCELLS__
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
