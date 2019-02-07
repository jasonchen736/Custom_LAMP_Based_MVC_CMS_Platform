{if count($generalMessages) > 0}
<div id="generalMessages">
	<div id="generalMessagesContainer">
		<ul id="generalMessagesList">
{foreach from=$generalMessages item=gMessage}
			<li>{$gMessage}</li>
{/foreach}
		</ul>
	</div>
</div>
{/if}
{if count($successMessages) > 0}
<div id="successes">
	<div id="successContainer">
		<ul id="successList">
{foreach from=$successMessages item=sMessage}
			<li>{$sMessage}</li>
{/foreach}
		</ul>
	</div>
</div>
{/if}
{if count($errorMessages) > 0}
<div id="errors">
	<div id="errorContainer">
		<ul id="errorList">
{foreach from=$errorMessages item=eMessage}
			<li>{$eMessage}</li>
{/foreach}
		</ul>
	</div>
</div>
{/if}
