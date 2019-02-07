{include file="header.tpl"}

<p class="adminOption"><a href="/page/addPage" class="greenPlus">New Page</a></p>

<form action="/page?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post" id="overviewForm">

	{include file="pagination.tpl"}

	<input type="hidden" name="runSearch" value="true" />

	<table class="recordsTable">

		<tr class="recordsHeader">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><a href="{$smarty.server.PHP_SELF}?sortField=pageID&sortOrder={if $query.sortField == 'pageID'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">ID</a></td>
			<td><a href="{$smarty.server.PHP_SELF}?sortField=name&sortOrder={if $query.sortField == 'name'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Name</a></td>
			<td align="right"><a href="{$smarty.server.PHP_SELF}?sortField=type&sortOrder={if $query.sortField == 'type'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Page Type</a></td>
			<td><a href="{$smarty.server.PHP_SELF}?sortField=title&sortOrder={if $query.sortField == 'title'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Title</a></td>
			<td align="right"><a href="{$smarty.server.PHP_SELF}?sortField=status&sortOrder={if $query.sortField == 'status'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Status</a></td>
			<td align="right"><a href="{$smarty.server.PHP_SELF}?sortField=dateAdded&sortOrder={if $query.sortField == 'dateAdded'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Date Added</a></td>
			<td align="right"><a href="{$smarty.server.PHP_SELF}?sortField=lastModified&sortOrder={if $query.sortField == 'lastModified'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Last Modified</a></td>
		</tr>

		<tr class="recordSearchHeader">
			<td>
				<input class="button" type="submit" value="Search" />
			</td>
			<td>
				<a href="javascript: void(0);" id="selectToggle" class="selectAll iconOnly" title="Select All">&nbsp;</a>
				<a href="javascript: void(0);" id="recordOverviewActionTrigger" class="forward iconOnly" title="Perform Action">&nbsp;</a>
			</td>
			<td class="idField"><input type="text" name="pageID" value="{$search.pageID.value}" /></td>
			<td>
				<input type="text" name="name" value="{$search.name.value}" />
			</td>
			<td>
				<select name="type">
					<option value="">All</option>
					{html_options options=$typeOptions selected=$search.type.value}
				</select>
			</td>
			<td>
				<input type="text" name="title" value="{$search.title.value}" />
			</td>
			<td>
				<select name="status">
					<option value="">All</option>
					{html_options options=$statusOptions selected=$search.status.value}
				</select>
			</td>
			<td>
				<table class="searchSection dateSelect">
					<tr>
						<td>From: </td>
						<td>
							<input type="text" name="dateAdded[min]" id="dateAddedMin" value="{if isset($search.dateAdded.value['min'])}{$search.dateAdded.value.min|date_format:"%m/%d/%Y"}{/if}" />
							<img src="/adminImages/calendar.png" id="dateAddedMinButton" />
						</td>
					</tr>
					<tr>
						<td>To:</td>
						<td>
							<input type="text" name="dateAdded[max]" id="dateAddedMax" value="{if isset($search.dateAdded.value['max'])}{$search.dateAdded.value.max|date_format:"%m/%d/%Y"}{/if}" />
							<img src="/adminImages/calendar.png" id="dateAddedMaxButton" />
						</td>
					</tr>
				</table>
{literal}
				<script type="text/javascript">
					Calendar.setup(
						{
							inputField : "dateAddedMin",
							ifFormat : "%m/%d/%Y",
							button : "dateAddedMinButton"
						}
					);
					Calendar.setup(
						{
							inputField : "dateAddedMax",
							ifFormat : "%m/%d/%Y",
							button : "dateAddedMaxButton"
						}
					);
				</script>
{/literal}
			</td>
			<td>
				<table class="searchSection dateSelect">
					<tr>
						<td>From: </td>
						<td>
							<input type="text" name="lastModified[min]" id="lastModifiedMin" value="{if isset($search.lastModified.value['min'])}{$search.lastModified.value.min|date_format:"%m/%d/%Y"}{/if}" />
							<img src="/adminImages/calendar.png" id="lastModifiedMinButton" />
						</td>
					</tr>
					<tr>
						<td>To:</td>
						<td>
							<input type="text" name="lastModified[max]" id="lastModifiedMax" value="{if isset($search.lastModified.value['max'])}{$search.lastModified.value.max|date_format:"%m/%d/%Y"}{/if}" />
							<img src="/adminImages/calendar.png" id="lastModifiedMaxButton" />
						</td>
					</tr>
				</table>
{literal}
				<script type="text/javascript">
					Calendar.setup(
						{
							inputField : "lastModifiedMin",
							ifFormat : "%m/%d/%Y",
							button : "lastModifiedMinButton"
						}
					);
					Calendar.setup(
						{
							inputField : "lastModifiedMax",
							ifFormat : "%m/%d/%Y",
							button : "lastModifiedMaxButton"
						}
					);
				</script>
{/literal}
			</td>
		</tr>

{section name=record loop=$records}
		<tr class="{cycle values="recordsRowAlpha,recordsRowBeta"}">
			<td align="center">
				<a href="/page/editPage?pageID={$records[record].pageID}" class="edit iconOnly" title="Edit Page">&nbsp;</a>
			</td>
			<td align="center">
				<input type="checkbox" name="selected[]" value="{$records[record].pageID}" />
			</td>
			<td align="right">{$records[record].pageID}</td>
			<td>{$records[record].name}</td>
			<td>{$records[record].type}</td>
			<td>{$records[record].title}</td>
			<td>{$records[record].status}</td>
			<td align="right">{$records[record].dateAdded|date_format:"%D %r"}</td>
			<td align="right">{$records[record].lastModified|date_format:"%D %r"}</td>
		</tr>
{/section}

		<tr class="recordsFooter">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
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
