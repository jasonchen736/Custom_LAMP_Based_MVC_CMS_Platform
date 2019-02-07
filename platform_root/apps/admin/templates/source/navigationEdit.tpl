{include file="header.tpl"}

{if $mode == 'edit'}
<script type="text/javascript">
	$(function() {ldelim} 
		var sub1 = 0;
		var sub2 = 0;
{literal}
		function bindSub1() {
			$('#newSub1').click(function() {
				$('tbody#subMenuTable').append($('#sub1Template table tbody').html().replace(/S1ID/g, 'n' + sub1));
				sub1++;
				bindSub2();
			});
		}
		function bindSub2() {
			$('.newSub2').unbind();
			$('.newSub2').click(function() {
				$(this).next().children('tbody').append($('#sub2Template table tbody').html().replace(/S1ID/g, $(this).attr('rel')).replace(/S2ID/g, 'n' + sub2));
				sub2++;
			});
		}
{/literal}
{if $itemLevel < 2}
		bindSub1();
{/if}
{if $itemLevel == 0}
		bindSub2();
	{rdelim});
</script>
{/if}
{/if}

<form action="/navigation/{if $mode == 'edit'}updateNavigation{else}saveNavigation{/if}" method="post" id="editForm">
	<input type="hidden" name="languageID" value="{$languageID}" />
	<table id="editTable">
		<tr>
			<td id="editMenuCell">
				<a href="{$lastQuery}" class="viewContent">Back To Last Search</a>
				<br /><br />
				<ul id="editMenu">
					<li class="head"> </li>
					<li class="editMenuOption{if !$propertyMenuItem || $propertyMenuItem == 'navigation_main'} selected{/if}" id="navigation_main">Details</li>
					<li class="end"> </li>
				</ul>
				<div id="editActionContainer">
{if $mode == 'edit'}
					<input type="hidden" name="navigationID" value="{$navigation.navigationID}" />
					<input class="button" type="submit" name="submit" value="Update" id="update" />
{else}
					<input class="button" type="submit" name="submit" value="Add and Edit" />
					<input class="button" type="submit" name="submit" value="Add Another" />
{/if}
				</div>
			</td>
			<td id="editPropertyCell">
				<div id="navigation_mainContainer" class="propertyContainer{if $propertyMenuItem && $propertyMenuItem != 'navigation_main'} hidden{/if}">
					<table>
{if $mode == 'edit'}
						<tr>
							<td><span class="normalLabel">Navigation Item ID:</span></td>
							<td>{$navigation.navigationID}</td>
						</tr>
						<tr>
							<td><span class="normalLabel">Parent Item ID:</span></td>
							<td>{$navigation.parent}</td>
						</tr>
{/if}
						<tr>
							<td><span class="{if 'label'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Label:</span></td>
							<td><input type="text" name="label" value="{$navigation.label}" style="width: 300px;" /></td>
						</tr>
						<tr>
							<td><span class="{if 'url'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">URL:</span></td>
							<td><input type="text" name="url" value="{$navigation.url}" style="width: 300px;" /></td>
						</tr>
						<tr>
							<td><span class="{if 'order'|in_array:$errorFields}errorLabel{else}normalLabel{/if}">Sort Order:</span></td>
							<td><input type="text" name="order" value="{$navigation.order}" /></td>
						</tr>
{if $mode == 'edit' && $itemLevel < 2}
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td colspan="2">
								<strong>Sub Menus:</strong>
								&nbsp;
								<a href="javascript: void(0);" class="greenPlus" id="newSub1">New Sub Menu Option</a>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<table>
									<tbody id="subMenuTable">
{if $menuTree['sub']}
{foreach from=$menuTree['sub'] key=sub1ID item=sub1}
										<tr><td>&nbsp;</td></tr>
										<tr>
											<td>
												<input type="checkbox" name="sub1[{$sub1ID}][delete]" />&nbsp;Delete&nbsp;&raquo;&nbsp;
												Label: <input type="text" name="sub1[{$sub1ID}][label]" value="{$sub1['label']}" />
												&nbsp;&raquo;&nbsp;
												URL: <input type="text" name="sub1[{$sub1ID}][url]" value="{$sub1['url']}" />
												&nbsp;&raquo;&nbsp;
												Order: <input type="text" name="sub1[{$sub1ID}][order]" value="{$sub1['order']}" />
{if $itemLevel == 0}
												&nbsp;
												<a href="javascript: void(0);" class="greenPlus newSub2" rel="{$sub1ID}">New Sub Menu Option</a>
												<table>
													<tbody>
{if $sub1['sub']}
{foreach from=$sub1['sub'] key=sub2ID item=sub2}
														<tr>
															<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
															<td>
																<input type="checkbox" name="sub2[{$sub1ID}][{$sub2ID}][delete]" />&nbsp;Delete&nbsp;&raquo;&nbsp;
																Label: <input type="text" name="sub2[{$sub1ID}][{$sub2ID}][label]" value="{$sub2['label']}" />
																&nbsp;&raquo;&nbsp;
																URL: <input type="text" name="sub2[{$sub1ID}][{$sub2ID}][url]" value="{$sub2['url']}" />
																&nbsp;&raquo;&nbsp;
																Order: <input type="text" name="sub2[{$sub1ID}][{$sub2ID}][order]" value="{$sub2['order']}" />
															</td>
														</tr>
{/foreach}
{/if}
													</tbody>
												</table>
{/if}
											</td>
										</tr>
{/foreach}
{/if}
									</tbody>
								</table>
							</td>
						</tr>
{/if}
					</table>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="propertyMenuItem" id="propertyMenuItem" value="{$propertyMenuItem}" />
</form>

<div style="display: none" id="sub1Template">
	<table>
		<tbody>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>
					<input type="checkbox" name="sub1[S1ID][delete]" />&nbsp;Delete&nbsp;&raquo;&nbsp;
					Label: <input type="text" name="sub1[S1ID][label]" value="" />
					&nbsp;&raquo;&nbsp;
					URL: <input type="text" name="sub1[S1ID][url]" value="" />
					&nbsp;&raquo;&nbsp;
					Order: <input type="text" name="sub1[S1ID][order]" value="" />
{if $itemLevel == 0}
					&nbsp;
					<a href="javascript: void(0);" class="greenPlus newSub2" rel="S1ID">New Sub Menu Option</a>
					<table>
						<tbody>
						</tbody>
					</table>
{/if}
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="display: none" id="sub2Template">
	<table>
		<tbody>
			<tr>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td>
					<input type="checkbox" name="sub2[S1ID][S2ID][delete]" />&nbsp;Delete&nbsp;&raquo;&nbsp;
					Label: <input type="text" name="sub2[S1ID][S2ID][label]" value="" />
					&nbsp;&raquo;&nbsp;
					URL: <input type="text" name="sub2[S1ID][S2ID][url]" value="" />
					&nbsp;&raquo;&nbsp;
					Order: <input type="text" name="sub2[S1ID][S2ID][order]" value="" />
				</td>
			</tr>
		</tbody>
	</table>
</div>

{include file="footer.tpl"}
