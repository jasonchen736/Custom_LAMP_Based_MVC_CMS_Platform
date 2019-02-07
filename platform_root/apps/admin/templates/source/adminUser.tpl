{include file="header.tpl"}

<p class="adminOption"><a href="/adminUser/addUser" class="greenPlus">New Admin User</a></p>

{include file="pagination.tpl"}

<form action="/adminUser?sortField={$query.sortField}&sortOrder={$query.sortOrder}{if $query.querystring}&{$query.querystring}{/if}" method="post">
	<input type="hidden" name="runSearch" value="true" />

	<table class="recordsTable">

		<tr class="recordsHeader">
			<td>&nbsp;</td>
			<td align="right"><a href="/adminUser?sortField=adminUserID&sortOrder={if $query.sortField == 'adminUserID'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Admin ID</a></td>
			<td><a href="/adminUser?sortField=name&sortOrder={if $query.sortField == 'name'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Name</a></td>
			<td><a href="/adminUser?sortField=login&sortOrder={if $query.sortField == 'login'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Login</a></td>
			<td><a href="/adminUser?sortField=email&sortOrder={if $query.sortField == 'email'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Email</a></td>
			<td><a href="/adminUser?sortField=status&sortOrder={if $query.sortField == 'status'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Status</a></td>
			<td align="right"><a href="/adminUser?sortField=created&sortOrder={if $query.sortField == 'created'}{$query.revSortOrder}{else}ASC{/if}{if $query.querystring}&{$query.querystring}{/if}">Created</a></td>
		</tr>

		<tr class="recordSearchHeader">
			<td>
				<input class="button" type="submit" value="Search" />
			</td>
			<td class="idField"><input type="text" name="adminUserID" value="{$search.adminUserID.value}" /></td>
			<td>
				<input type="text" name="name" value="{$search.name.value}" />
				<input type="hidden" name="nameOperator" value="contains" />
			</td>
			<td>
				<input type="text" name="login" value="{$search.login.value}" />
				<input type="hidden" name="loginOperator" value="contains" />
			</td>
			<td>
				<input type="text" name="email" value="{$search.email.value}" />
				<input type="hidden" name="emailOperator" value="contains" />
			</td>
			<td>
				{html_options name=status options=$search.status.options selected=$search.status.value}
			</td>
			<td>
				<table class="searchSection dateSelect">
					<tr>
						<td>From:&nbsp;</td>
						<td>
							<input type="text" name="created[min]" id="createdMin" value="{if isset($search.created.value['min'])}{$search.created.value.min|date_format:"%m/%d/%Y"}{/if}" />
							<img src="/adminImages/calendar.png" id="createdMinButton" />
						</td>
					</tr>
					<tr>
						<td>To:</td>
						<td>
							<input type="text" name="created[max]" id="createdMax" value="{if isset($search.created.value['max'])}{$search.created.value.max|date_format:"%m/%d/%Y"}{/if}" />
							<img src="/adminImages/calendar.png" id="createdMaxButton" />
						</td>
					</tr>
				</table>
{literal}
				<script type="text/javascript">
					Calendar.setup(
						{
							inputField : "createdMin",
							ifFormat : "%m/%d/%Y",
							button : "createdMinButton"
						}
					);
					Calendar.setup(
						{
							inputField : "createdMax",
							ifFormat : "%m/%d/%Y",
							button : "createdMaxButton"
						}
					);
				</script>
{/literal}
			</td>
		</tr>

{section name=record loop=$records}
		<tr class="{cycle values="recordsRowAlpha,recordsRowBeta"}">
			<td align="center">
				<a href="/adminUser/editUser?adminUserID={$records[record].adminUserID}" class="edit iconOnly" title="Edit User">&nbsp;</a>
			</td>
			<td align="right">{$records[record].adminUserID}</td>
			<td>{$records[record].name}</td>
			<td>{$records[record].login}</td>
			<td>{$records[record].email}</td>
			<td>{$records[record].status}</td>
			<td align="right">{$records[record].created|date_format:"%D %r"}</td>
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
		</tr>

	</table>

</form>

{include file="footer.tpl"}
