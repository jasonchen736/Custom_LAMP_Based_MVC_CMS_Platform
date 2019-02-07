<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>{$_TITLE}</title>
	
{foreach from=$_META item=_meta}
{if $_meta}
	{$_meta}
{/if}
{/foreach}
	<link href="/adminImages/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" language="JavaScript" src="/js/calendar/calendar.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/calendar/lang/calendar-en.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/calendar/calendar-setup.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/jquery/jquery-1.7.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/jquery/plugins/jquery.dimensions.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/jquery/plugins/cluetip/jquery.cluetip.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/jquery/plugins/autocomplete/auto.complete.js"></script>
	<script type="text/javascript" src="/js/jquery_new/jquery-ui.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/tiny_mce_old/tiny_mce.js"></script>
	<script type="text/javascript" language="JavaScript">
		var mainURL = '{$currentLanguage['url']}';
	</script>
	<script type="text/javascript" language="JavaScript" src="/js/admin.js"></script>
{foreach from=$_SCRIPTS item=_script}
{if $_script}
	<script type="text/javascript" language="JavaScript" src="/js/{$_script}"></script>
{/if}
{/foreach}
	<link href="/js/calendar/calendar-win2k-cold-1.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="/js/jquery/plugins/cluetip/jquery.cluetip.css" />
	<link rel="stylesheet" type="text/css" href="/js/jquery/plugins/autocomplete/auto.complete.css" />
	<link rel="stylesheet" href="/css/admin.css">
{foreach from=$_STYLES item=_style}
{if $_style}
	<link rel="stylesheet" href="/css/{$_style}">
{/if}
{/foreach}
</head>

<body id="adminBody">
{if isset($admin.validated) && $admin.validated}
<div id="cmsOptionsContainer">
	<form method="POST">
		<select name="languageID">
			{html_options options=$languageSelectOptions selected=$currentLanguage['languageID']}
		</select>
		<input type="submit" value="Switch Language" name="switchLanguage" class="button" />
	</form>
</div>
{/if}
<div id="mainContainer">
	<div class="adminMenu">
		<ul>
{if isset($admin.validated) && $admin.validated}
{if $admin.access.CONTENT}
			<li>
				<a href="/language">Languages</a>
			</li>
			<li class="separator">|</li>
			<li>
				<a href="/navigation">Site Navigation</a>
			</li>
			<li class="separator">|</li>
			<li>
				<a href="/page">Site Pages<!--[if IE 7]><!--></a><!--<![endif]-->
				<!--[if lte IE 6]><table><tr><td><![endif]-->
				<ul>
					<li><a href="/contentModule">Content Modules</a></li>
					<li><a href="/siteTemplate">Site Template</a></li>
				</ul>
				<!--[if lte IE 6]></td></tr></table></a><![endif]-->
			</li>
			
			<li class="separator">|</li>
			<li>
				<a id="manageFiles" href="javascript: void(0);">Manage Files</a>
			</li>
			<li class="separator">|</li>
			<li>
				<a href="/siteTag">Site Tags</a>
			</li>
			<li class="separator">|</li>
			<li class="separator">|</li>
{/if}
{if $admin.access.SUPERADMIN}
			<li>
				<a href="/adminUser">Admin Users<!--[if IE 7]><!--></a><!--<![endif]-->
				<!--[if lte IE 6]><table><tr><td><![endif]-->
				<ul>
					<li><a href="/adminGroup">Admin Groups</a></li>
				</ul>
				<!--[if lte IE 6]></td></tr></table></a><![endif]-->
			</li>
			<li class="separator">|</li>
{/if}
			<li>
				<a href="/logout">Logout</a>
			</li>
{else}
			<li>&nbsp;</li>
{/if}
		</ul>
	</div>
	{include file="messageSection.tpl"}
	<div id="workingSection">
