<?IncludeModuleLangFile(__FILE__);
/**
 * Copyright (c) 7/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
$path = COption::GetOptionString('kit.mapoffices', 'path_demo');
?>

<?if($_REQUEST["kit_install_demo"] == 'Y'):?>
<p><?=GetMessage("GO_TODEMO_SECTION")?>: <a target="_blank" href="/<?=$path;?>/">/<?=$path;?>/</a></p>
<?endif;?>

<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
</form>
