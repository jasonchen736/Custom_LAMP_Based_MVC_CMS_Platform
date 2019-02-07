<div id="languageSelect">
	<div id="currentLanguage">
		<img src="{$language['current']['image']}" /> {$language['current']['name']}
	</div>
	<div id="languageDropdown">
{foreach from=$language['list'] item=lan}
		<a href="/languageSelect?switchLanguage=1&languageID={$lan['languageID']}"><img src="{$lan['image']}" /> {$lan['name']}</a>
{/foreach}
	</div>
</div>
