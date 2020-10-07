<?IncludeModuleLangFile(__FILE__);

echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
$path = COption::GetOptionString('wsm.mapoffices', 'path_demo');
?>

<?if($_REQUEST["wsm_install_demo"] == 'Y'):?>
<p><?=GetMessage("GO_TODEMO_SECTION")?>: <a target="_blank" href="/<?=$path;?>/">/<?=$path;?>/</a></p>
<?endif;?>

<p><?=GetMessage("GO_TO_WSMART_SITE");?><a target="_blank" href="http://w-smart.ru/marketplace/wsm.mapoffices/?from=install">http://w-smart.ru/marketplace/wsm.mapoffices/</a></p>

<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
</form>
