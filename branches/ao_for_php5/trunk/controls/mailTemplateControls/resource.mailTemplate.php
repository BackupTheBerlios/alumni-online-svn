<?
function smarty_resource_mailTemplate_source($tpl_name, &$tpl_source, &$mailTemplate)
{
	$data = split("/", $tpl_name, 2);
	
	if(sizeof($data)!=2){
		trigger_error_msg('mailTemplate handler: $tpl_name should be in format type/name');
		return false;
	}else{
		$recordSet = $mailTemplate->main->databaseConnection->Execute("SELECT * FROM {$mailTemplate->main->databaseTablePrefix}templates 
																			WHERE
																				type='{$data[0]}'
																			AND
																				name = '{$data[1]}'");
		while (!$recordSet->EOF) {
			$tpl_source = $recordSet->fields["content"];
			$recordSet->MoveNext();
		}
		
		return true;
	}
}

function smarty_resource_mailTemplate_timestamp($tpl_name, &$tpl_timestamp, &$mailTemplate)
{
	$tpl_timestamp = time ();
	return true;
}

function smarty_resource_mailTemplate_secure($tpl_name, &$mailTemplate)
{
    // assume all templates are secure
    return true;
}

function smarty_resource_mailTemplate_trusted($tpl_name, &$mailTemplate)
{
    // not used for templates
}

?>
