<?IncludeModuleLangFile(__FILE__);?>
<?
echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
#$path = COption::GetOptionString('wsm.mapoffices', 'path_demo');
#$path = trim($path, '/');
$path = 'wsm_mapoffices_demo';
?>

<form action="<?echo $APPLICATION->GetCurPage()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="hidden" name="id" value="wsm.mapoffices">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?echo CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN"))?>
	
	<p><?echo GetMessage("MOD_UNINST_SAVE")?></p>
	
	<?/*
	<p><input type="checkbox" name="save_demo_iblock" id="save_demo_iblock" value="Y" checked><label for="save_demo_iblock"><?echo GetMessage("MOD_UNINST_SAVE_IBLOCK")?></label></p>
	*/?>
	
	<p><input type="checkbox" name="save_demo_iblock" id="save_demo_iblock" value="Y" checked/><label for="save_demo_iblock"><?echo GetMessage("MOD_UNINST_SAVE_IBLOCK")?></label></p>
	<?if($path == ""):?>
	<input type="hidden" name="save_demo_section" value="Y"/>
	<?else:?>
	<p><input type="checkbox" name="save_demo_section" id="save_demo_section" value="Y" checked/><label for="save_demo_section"><?echo GetMessage("MOD_UNINST_SAVE_SECTION")?> (/<?=$path?>/)</label></p>
	<?endif;?>

	<input type="submit" name="inst" value="<?echo GetMessage("MOD_UNINST_DEL")?>">
</form>