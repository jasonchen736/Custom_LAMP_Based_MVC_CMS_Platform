{include file="header.tpl"}

<form action="/language/{if $mode == 'edit'}updateLanguage{else}saveLanguage{/if}" method="post" id="editForm">
	<table id="editTable">
		<tr>
			<td id="editMenuCell">
				<a href="{$lastQuery}" class="viewContent">Return to previous search</a>
				<br /><br />
				<ul id="editMenu">
					<li class="head">&nbsp;</li>
					<li class="editMenuOption{if !$propertyMenuItem || $propertyMenuItem == 'language_main'} selected{/if}" id="language_main">Language Details</li>
					<li class="end">&nbsp;</li>
				</ul>
				<div id="editActionContainer">
{if $mode == 'edit'}
					<input type="hidden" name="languageID" value="{$language.languageID}" />
					<input class="button" type="submit" name="submit" value="Update" id="update" />
{else}
					<input class="button" type="submit" name="submit" value="Add and Edit" />
					<input class="button" type="submit" name="submit" value="Add Another" />
{/if}
				</div>
			</td>
			<td id="editPropertyCell">
				<div id="language_mainContainer" class="propertyContainer{if $propertyMenuItem && $propertyMenuItem != 'language_main'} hidden{/if}">
					<table>
{if $mode == 'edit'}
						<tr>
							<td><span class="normalLabel">Language ID:</span></td>
							<td>{$language.languageID}</td>
						</tr>
						<tr>
							<td><span class="normalLabel">DateAdded:</span></td>
							<td>{$language.dateAdded|date_format:"%m/%d/%Y %r"}</td>
						</tr>
{/if}
						<tr>
							<td><span class="{if 'name'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Name:</span></td>
							<td><input type="text" name="name" value="{$language.name}" style="width: 300px;" /></td>
						</tr>
						<tr>
							<td><span class="{if 'url'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">URL:</span></td>
							<td><input type="text" name="url" value="{$language.url}" style="width: 300px;" /></td>
						</tr>
						<tr>
							<td><span class="{if 'image'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Image:</span></td>
							<td><input type="text" name="image" value="{$language.image}" style="width: 300px;" />&nbsp;<a href="javascript: void(0);" class="edit iconOnly kcfinderSelect" title="Select Image">&nbsp;</a></td>
						</tr>
{if $language.image}
						<tr>
							<td><span class="normalLabel">Image Preview:</span></td>
							<td><img src="{$language.image}" /></td>
						</tr>
{/if}
						<tr>
							<td><span class="{if 'default'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Default Language:</span></td>
							<td><input type="checkbox" name="default"{if $language.default} checked="checked"{/if} /></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="propertyMenuItem" id="propertyMenuItem" value="{$propertyMenuItem}" />
</form>

{include file="footer.tpl"}
