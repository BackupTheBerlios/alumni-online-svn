<html>
	<head>
		<title>{$title}{if $tabTitle} &gt; {$tabTitle}{/if}{if $showVersion} - {$app_name} (v{$app_version}){/if}</title>
		<META http-equiv="Content-Type" content="text/html; charset={$charset}">
		{foreach key=key item=item from=$styleSheetList}
		<LINK id="{$key}" href="{$item}" type=text/css rel=stylesheet>
		{/foreach}
		{foreach key=key item=item from=$javaScriptList}
		<script language='JavaScript' src='{$key}.js'></script>
		{/foreach}
		{foreach key=key item=item from=$metaDataList}
		<META name="{$key}" content="{$item}">
		{/foreach}
	</head>
	<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	{editMenuScript}
 	{adminHeader}
	{include file=$template}	
	</body>
</html>
