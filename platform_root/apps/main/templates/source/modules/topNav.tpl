<ul id="topNav">
	{foreach from=$navigation item=item name=topnav}
		<li class="topNavItem">
			<a href="{$item['url']}" class="topNav">{$item['label']}</a>
		</li>
	{if !$smarty.foreach.topnav.last}
		<li class="separator">|</li>
	{/if}
	{/foreach}
</ul>
