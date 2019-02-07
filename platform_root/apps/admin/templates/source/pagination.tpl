
<table class="recordsNavigation">
	<tr>
		<th>
			<form action="{$_PAGE_URL}?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post">
				<table>
					<tr>
						<td>
							<span class="mainText">Show <input type="text" name="show" value="{$query.show}" size="1" /> records</span>
						</td>
						<td>
							<input class="button" type="submit" value="Update" />
						</td>
					</tr>
				</table>
			</form>
		</th>
{if $query.start > 0}
		<th>
			<a href="{$_PAGE_URL}?previousPage=true&sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" class="button">
				<span>Previous Page</span>
			</a>
		</th>
{/if}
{if $recordsFound > $query.show}
		<th>
			<form action="{$_PAGE_URL}?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post">
				<table>
					<tr>
						<td>
							<span class="mainText">Skip to page&nbsp;</span>
							<select name="page">
{section name=pages start=1 loop=$query.pages+1 step=1}
								<option value="{$smarty.section.pages.index}"{if $smarty.section.pages.index==$query.page} selected="selected"{/if}>{$smarty.section.pages.index}</option>
{/section}
							</select>
						</td>
						<td>
							<input class="button" type="submit" value="Go" />
						</td>
					</tr>
				</table>
			</form>
		</th>
{/if}
{if $query.start + $query.show < $recordsFound}
		<th>
			<a href="{$_PAGE_URL}?nextPage=true&sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" class="button">
				<span>Next Page</span>
			</a>
		</th>
{/if}
	</tr>
</table>
{if $recordsFound > 0}
	Showing  {math equation='x + y' x=1 y=$query.start} to {if $query.start + $query.show > $recordsFound}{$recordsFound}{else}{math equation='x + y' x=$query.show y=$query.start}{/if} of {$recordsFound}
	{if $hasExport}&nbsp;<a href="{$_PAGE_URL}/export{if $query.querystring}?{$query.querystring}{/if}" class="forward">Export Search</a>{/if}
{/if}
