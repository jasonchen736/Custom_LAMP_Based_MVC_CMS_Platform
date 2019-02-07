<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>{$_TITLE}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{foreach from=$_META key=_metaKey item=_meta}
{if $_meta}
	<meta name="{$_metaKey}" content="{$_meta}" />
{/if}
{/foreach}
{if isset($_metaTags) && $_metaTags}
	{$_metaTags}
{/if}
	<link href="/images/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="/css/main.css">
{foreach from=$_STYLES item=_style}
{if $_style}
	<link rel="stylesheet" href="/css/{$_style}">
{/if}
{/foreach}
	<script type="text/javascript" language="JavaScript" src="/js/jquery/jquery-1.7.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/jquery/plugins/carouFredSel/jquery.carouFredSel-6.2.1-packed.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/traffic-source.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/main.js"></script>
{foreach from=$_SCRIPTS item=_script}
{if $_script}
	<script type="text/javascript" language="JavaScript" src="/js/{$_script}"></script>
{/if}
{/foreach}
{foreach from=$_SITETAGS.header item=_sitetag}
{if $_sitetag.siteTag}
{$_sitetag.siteTag}
{/if}
{/foreach}
</head>

<body>
