{include file="header.tpl"}

<form action="/siteTag/{if $mode == 'edit'}updateSiteTag{else}saveSiteTag{/if}" method="post">
	<input type="hidden" name="languageID" value="{$languageID}" />
	<table id="editTable">
		<tr>
			<td id="editMenuCell">
				<a href="{$lastQuery}" class="viewContent">Return to previous search</a>
				<br /><br />
				<ul id="editMenu">
					<li class="head">&nbsp;</li>
					<li class="editMenuOption{if !$propertyMenuItem || $propertyMenuItem == 'siteTag_main'} selected{/if}" id="siteTag_main">Site Tag Details</li>
					<li class="end">&nbsp;</li>
				</ul>
				<div id="editActionContainer">
{if $mode == 'edit'}
					<input type="hidden" name="siteTagID" value="{$siteTag.siteTagID}" />
					<input class="button" type="submit" name="submit" value="Update Tag" />
{else}
					<input class="button" type="submit" name="submit" value="Add and Edit" />
					<br class="clear" />
					<input class="button" type="submit" name="submit" value="Add Another" />
{/if}
				</div>
			</td>
			<td id="editPropertyCell">
				<div id="siteTag_mainContainer" class="propertyContainer{if $propertyMenuItem && $propertyMenuItem != 'siteTag_main'} hidden{/if}">
					<table>
{if $mode == 'edit'}
						<tr>
							<td><span class="normalLabel">Site Tag ID:</span></td>
							<td>{$siteTag.siteTagID}</td>
						</tr>
{/if}
						<tr>
							<td><span class="{if 'referrer'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Referrer:</span>&nbsp;</td>
							<td><input type="text" name="referrer" value="{$siteTag.referrer}" /></td>
						</tr>
						<tr>
							<td><span class="{if 'description'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Description:</span>&nbsp;</td>
							<td><input type="text" name="description" value="{$siteTag.description}" style="width: 600px" /></td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td><span class="{if 'placement'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Placement:</span>&nbsp;</td>
							<td>{html_options name=placement options=$placementOptions selected=$siteTag.placement}</td>
						</tr>
						<tr>
							<td colspan="2">Set to "exact match" "ALL" to place on all pages</td>
						</tr>
						<tr>
							<td><span class="{if 'matchType'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Match Type:</span>&nbsp;</td>
							<td>{html_options name=matchType options=$matchTypeOptions selected=$siteTag.matchType}</td>
						</tr>
						<tr>
							<td><span class="{if 'matchValue'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Match Value:</span>&nbsp;</td>
							<td><input type="text" name="matchValue" value="{$siteTag.matchValue}" /></td>
						</tr>
						<tr>
							<td><span class="{if 'weight'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Weight:</span>&nbsp;</td>
							<td><input type="text" name="weight" value="{$siteTag.weight}" style="width: 40px" /></td>
						</tr>
						<tr>
							<td><span class="{if 'status'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Status:</span>&nbsp;</td>
							<td>{html_options name=status options=$statusOptions selected=$siteTag.status}</td>
						</tr>
					</table>
					<table>
						<tr>
							<td>
								<span class="{if 'HTTP'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">HTTP Tag:</span><br />
								<textarea name="HTTP" cols="100" rows="14">{$siteTag.HTTP}</textarea>
							</td>
						</tr>
						<tr>
							<td>
								<span class="{if 'HTTPS'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">HTTPS Tag:</span><br />
								<textarea name="HTTPS" cols="100" rows="14">{$siteTag.HTTPS}</textarea>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="propertyMenuItem" id="propertyMenuItem" value="{$propertyMenuItem}" />
</form>

{include file="footer.tpl"}
