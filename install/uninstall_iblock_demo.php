<?
/**
 * Copyright (c) 7/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$IBLOCK_ID = intval(COption::GetOptionString('kit.mapoffices', 'iblock_demo', 0));

if($IBLOCK_ID && $_REQUEST["save_demo_iblock"] != 'Y')
{
	$DB->StartTransaction();
	if(!CIBlock::Delete($IBLOCK_ID))
	{
		$strWarning .= GetMessage("IBLOCK_DELETE_ERROR");
		$DB->Rollback();
	}
	else
		$DB->Commit();

}