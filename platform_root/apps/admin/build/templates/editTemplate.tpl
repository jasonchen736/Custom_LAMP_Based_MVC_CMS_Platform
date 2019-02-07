{include file="header.tpl"}
__WYSIWYG__

<form action="/__TABLE__/{if $mode == 'edit'}update__UCFIRSTTABLE__{else}save__UCFIRSTTABLE__{/if}" method="post" id="editForm">
	<table id="editTable">
		<tr>
			<td id="editMenuCell">
				<a href="{$lastQuery}" class="viewContent">Return to previous search</a>
				<br /><br />
				<ul id="editMenu">
					<li class="head">&nbsp;</li>
					<li class="editMenuOption{if !$propertyMenuItem || $propertyMenuItem == '__TABLE___main'} selected{/if}" id="__TABLE___main">__LABEL__ Details</li>
__PROPERTYMENU__
					<li class="end">&nbsp;</li>
				</ul>
				<div id="editActionContainer">
{if $mode == 'edit'}
					<input type="hidden" name="__PRIMARY__" value="{$__TABLE__.__PRIMARY__}" />
					<input class="button" type="submit" name="submit" value="Update" id="update" />
{else}
					<input class="button" type="submit" name="submit" value="Add and Edit" />
					<input class="button" type="submit" name="submit" value="Add Another" />
{/if}
				</div>
			</td>
			<td id="editPropertyCell">
				<div id="__TABLE___mainContainer" class="propertyContainer{if $propertyMenuItem && $propertyMenuItem != '__TABLE___main'} hidden{/if}">
					<table>
{if $mode == 'edit'}
						<tr>
							<td><span class="normalLabel">__LABEL__ ID:</span></td>
							<td>{$__TABLE__.__PRIMARY__}</td>
						</tr>
__INFOROWS__
{/if}
__EDITINPUTS__
					</table>
				</div>
__REVISIONS__
			</td>
		</tr>
	</table>
	<input type="hidden" name="propertyMenuItem" id="propertyMenuItem" value="{$propertyMenuItem}" />
</form>

{include file="footer.tpl"}
