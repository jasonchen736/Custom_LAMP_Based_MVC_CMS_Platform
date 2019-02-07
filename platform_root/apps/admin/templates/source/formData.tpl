{include file="header.tpl"}

<form action="/formData?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post" id="overviewForm">

	{include file="pagination.tpl"}

	<input type="hidden" name="runSearch" value="true" />

	<table class="recordsTable">

		<tr class="recordsHeader">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><a href="/formData?sortField=formDataID&sortOrder={if $query.sortField == 'formDataID'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">ID</a></td>
			<td><a href="/formData?sortField=first&sortOrder={if $query.sortField == 'first'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">First</a></td>
			<td><a href="/formData?sortField=last&sortOrder={if $query.sortField == 'last'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Last</a></td>
			<td><a href="/formData?sortField=email&sortOrder={if $query.sortField == 'email'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Email</a></td>
			<td><a href="/formData?sortField=type&sortOrder={if $query.sortField == 'type'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Type</a></td>
			<td><a href="/formData?sortField=source&sortOrder={if $query.sortField == 'source'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Source</a></td>            
			<td><a href="/formData?sortField=date&sortOrder={if $query.sortField == 'date'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Date</a></td>
		</tr>

		<tr class="recordSearchHeader">
			<td>
				<input class="button" type="submit" value="Search" />
			</td>
			<td>
				<a href="javascript: void(0);" id="selectToggle" class="selectAll iconOnly" title="Select All">&nbsp;</a>
				<a href="javascript: void(0);" id="recordOverviewActionTrigger" class="forward iconOnly" title="Perform Action">&nbsp;</a>
			</td>
			<td class="idField"><input type="text" name="formDataID" value="{$search.formDataID.value}" /></td>
			<td>
				<input type="text" name="first" value="{$search.first.value}" />
			</td>
			<td>
				<input type="text" name="last" value="{$search.last.value}" />
			</td>
			<td>
				<input type="text" name="email" value="{$search.email.value}" />
			</td>
			<td>
				<input type="text" name="type" value="{$search.type.value}" />
			</td>
			<td>
				<input type="text" name="source" value="{$search.source.value}" />
			</td>            
			<td>
				<table class="searchSection dateSelect">
					<tr>
						<td>From:</td>
						<td>
							<input type="text" name="date[min]" id="dateMin" value="{if isset($search.date.value['min'])}{$search.date.value.min|date_format:"%m/%d/%Y"}{/if}" />
							<img src="/adminImages/calendar.png" id="dateMinButton" />
						</td>
					</tr>
					<tr>
						<td>To:</td>
						<td>
							<input type="text" name="date[max]" id="dateMax" value="{if isset($search.date.value['max'])}{$search.date.value.max|date_format:"%m/%d/%Y"}{/if}" />
							<img src="/adminImages/calendar.png" id="dateMaxButton" />
						</td>
					</tr>
				</table>
{literal}
				<script type="text/javascript">
					Calendar.setup(
						{
							inputField : "dateMin",
							ifFormat : "%m/%d/%Y",
							button : "dateMinButton"
						}
					);
					Calendar.setup(
						{
							inputField : "dateMax",
							ifFormat : "%m/%d/%Y",
							button : "dateMaxButton"
						}
					);
				</script>
{/literal}
			</td>
		</tr>

{section name=record loop=$records}
		<tr class="{cycle values="recordsRowAlpha,recordsRowBeta"}">
			<td align="center">
				<a href="/formData/viewFormData?formDataID={$records[record].formDataID}" class="viewContent iconOnly" title="View Form Data">&nbsp;</a>
			</td>
			<td align="center">
				<input type="checkbox" name="selected[]" value="{$records[record].formDataID}" />
			</td>
			<td align="right">{$records[record].formDataID}</td>
			<td>{$records[record].first}</td>
			<td>{$records[record].last}</td>
			<td>{$records[record].email}</td>
			<td>{$records[record].type}</td>
			<td>{$records[record].source}</td>            
			<td>{$records[record].date|date_format:"%D %r"}</td>
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
		</tr>

	</table>

</form>

<div id="recordOverviewActions">
	<a href="javascript: void(0);" id="deleteSelected" class="remove" />Delete Selected</a>
	<a href="javascript: void(0);" id="recordOverviewActionsClose" class="redX">&nbsp;</a>
</div>

{include file="footer.tpl"}
