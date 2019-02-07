{include file="header.tpl"}

<p class="adminOption"><a href="/siteTag/addSiteTag" class="greenPlus">New Site Tag</a></p>

{include file="pagination.tpl"}

<form action="/siteTag?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post" id="overviewForm">
	<input type="hidden" name="runSearch" value="true" />

	<table class="recordsTable">

		<tr class="recordsHeader">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><a href="/siteTag?sortField=siteTagID&sortOrder={if $query.sortField == 'siteTagID'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Site Tag ID</a></td>
			<td><a href="/siteTag?sortField=referrer&sortOrder={if $query.sortField == 'referrer'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Referrer</a></td>
			<td><a href="/siteTag?sortField=description&sortOrder={if $query.sortField == 'description'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Description</a></td>
			<td><a href="/siteTag?sortField=matchType&sortOrder={if $query.sortField == 'matchType'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Match Type</a></td>
			<td><a href="/siteTag?sortField=matchValue&sortOrder={if $query.sortField == 'matchValue'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Match Value</a></td>
			<td><a href="/siteTag?sortField=placement&sortOrder={if $query.sortField == 'placement'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Placement</a></td>
			<td align="right"><a href="/siteTag?sortField=weight&sortOrder={if $query.sortField == 'weight'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Weight</a></td>
			<td><a href="/siteTag?sortField=status&sortOrder={if $query.sortField == 'status'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Status</a></td>
		</tr>

		<tr class="recordSearchHeader">
			<td>
				<input class="button" type="submit" value="Search" />
			</td>
			<td>
				<a href="javascript: void(0);" id="selectToggle" class="selectAll iconOnly" title="Select All">&nbsp;</a>
				<a href="javascript: void(0);" id="recordOverviewActionTrigger" class="forward iconOnly" title="Perform Action">&nbsp;</a>
			</td>
			<td class="idField"><input type="text" name="siteTagID" value="{$search.siteTagID.value}" /></td>
			<td>
				<input type="text" name="referrer" value="{$search.referrer.value}" />
				<input type="hidden" name="referrer_operator" value="contains" />
			</td>
			<td>
				<input type="text" name="description" value="{$search.description.value}" />
				<input type="hidden" name="description_operator" value="contains" />
			</td>
			<td>{html_options name=matchType options=$search.matchType.options selected=$search.matchType.value}</td>
			<td>
				<input type="text" name="matchValue" value="{$search.matchValue.value}" />
				<input type="hidden" name="matchValue_operator" value="contains" />
			</td>
			<td>{html_options name=placement options=$search.placement.options selected=$search.placement.value}</td>
			<td>
				<table class="searchSection smallRange">
					<tr>
						<td>From:&nbsp;</td>
						<td><input type="text" name="weight[min]" value="{if isset($search.weight.value['min'])}{$search.weight.value.min}{/if}" /></td>
					</tr>
					<tr>
						<td>To:</td>
						<td><input type="text" name="weight[max]" value="{if isset($search.weight.value['max'])}{$search.weight.value.max}{/if}" /></td>
					</tr>
				</table>
			</td>
			<td>{html_options name=status options=$search.status.options selected=$search.status.value}</td>
		</tr>

{section name=record loop=$records}
		<tr class="{cycle values="recordsRowAlpha,recordsRowBeta"}">
			<td align="center">
				<a href="/siteTag/editSiteTag?siteTagID={$records[record].siteTagID}" class="edit iconOnly" title="Edit Site Tag">&nbsp;</a>
			</td>
			<td align="center">
				<input type="checkbox" name="selected[]" value="{$records[record].siteTagID}" />
			</td>
			<td align="right">{$records[record].siteTagID}</td>
			<td>{$records[record].referrer}</td>
			<td>{$records[record].description}</td>
			<td>{$records[record].matchType}</td>
			<td>{$records[record].matchValue}</td>
			<td>{$records[record].placement}</td>
			<td align="right">{$records[record].weight}</td>
			<td>{$records[record].status}</td>
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
