{include file="header.tpl"}

<form action="/adminGroup/{if $mode == 'edit'}updateGroup{else}saveGroup{/if}" method="post">
	<table id="editTable">
		<tr>
			<td id="editMenuCell">
				<a href="{$lastQuery}" class="viewContent">Return to previous search</a>
				<br /><br />
				<ul id="editMenu">
					<li class="head">&nbsp;</li>
					<li class="editMenuOption{if !$propertyMenuItem || $propertyMenuItem == 'group_main'} selected{/if}" id="group_main">Group Details</li>
					<li class="editMenuOption{if $propertyMenuItem == 'group_access'} selected{/if}" id="group_access">Group Access</li>
					<li class="end">&nbsp;</li>
				</ul>
				<div id="editActionContainer">
{if $mode == 'edit'}
					<input type="hidden" name="adminGroupID" value="{$adminGroup.adminGroupID}" />
					<input class="button" type="submit" name="submit" value="Update" />
{else}
					<input class="button" type="submit" name="submit" value="Add and Edit" />
					<input class="button" type="submit" name="submit" value="Add Another" />
{/if}
				</div>
			</td>
			<td id="editPropertyCell">
				<div id="group_mainContainer" class="propertyContainer{if $propertyMenuItem && $propertyMenuItem != 'group_main'} hidden{/if}">
					<table>
{if $mode == 'edit'}
						<tr>
							<td><span class="normalLabel">Admin Group ID:</span></td>
							<td>{$adminGroup.adminGroupID}</td>
						</tr>
{/if}
						<tr>
							<td><span class="{if 'name'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Name:</span></td>
							<td><input type="text" name="name" value="{$adminGroup.name}" /></td>
						</tr>
					</table>
				</div>
				<div id="group_accessContainer" class="propertyContainer{if !$propertyMenuItem || $propertyMenuItem != 'group_access'} hidden{/if}">
					<table>
{foreach from=$accessSections key=access item=accessLabel}
						<tr>
							<td><input type="checkbox" name="access[{$access}]"{if $groupAccess[$access]} checked="checked"{/if} /></td>
							<td align="left">&nbsp;{$accessLabel}</td>
						</tr>
{/foreach}
					</table>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="propertyMenuItem" id="propertyMenuItem" value="{$propertyMenuItem}" />
</form>

{include file="footer.tpl"}
