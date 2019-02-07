
{if $_ADMIN}
<div id="adminPanel">
	<a href="/adminLogout">Logout</a>
</div>
{/if}

{assign var=year value=date("Y")}

{foreach from=$_SITETAGS.footer item=_sitetag}
{if $_sitetag.siteTag}
{$_sitetag.siteTag}
{/if}
{/foreach}

</body>

</html>
