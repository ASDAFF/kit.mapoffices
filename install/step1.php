<?IncludeModuleLangFile(__FILE__);?>

<?
/**
 * Copyright (c) 7/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$arType = array();
$db_iblock_type = CIBlockType::GetList();
while($ar_iblock_type = $db_iblock_type->Fetch())
{
	if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))
	{
		$arType[$arIBType["ID"]] = $arIBType["NAME"];
	}   
}

if(is_array($GLOBALS["errors"]) && count($GLOBALS["errors"]))
    echo CAdminMessage::ShowMessage(implode("\n",$GLOBALS["errors"]));
elseif(!is_array($GLOBALS["errors"]))
    echo CAdminMessage::ShowMessage($GLOBALS["errors"]);
?>

<form action="<?=$APPLICATION->GetCurPage()?>" name="form1" id="kit_mapoffices_install_form">
	<?=bitrix_sessid_post()?>
	
	<input type="hidden" name="id" value="kit.mapoffices">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="2">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	
	<p><input type="checkbox" name="kit_install_demo" id="kit_install_demo" value="Y" checked><label for="instaldemo"><?echo GetMessage("KIT_OFFICESMAP_INSTALL_DEMO")?></label></p>
	<small class="demo_settings"><?echo GetMessage("KIT_OFFICESMAP_IBLOCK_TYPE_DESC")?></small>
	<p class="demo_settings">
		<?echo GetMessage("KIT_OFFICESMAP_IBLOCK_TYPE")?>:
		<select name="iblock_type" id="kit_iblock_type">
		<?foreach($arType as $id => $type):?>
		<option value="<?=$id?>"<? if($id == $_REQUEST['iblock_type']) echo " selected";?>><?=$type?></option>
		<?endforeach?>
		</select>
		<br/>
	</p>
    <p class="demo_settings">
        <?=GetMessage("KIT_OFFICESMAP_PATH")?>:
        <input type="text"  name="path" id="path" value="/kit_mapoffices_demo/" readonly />
        <br/>
    </p>
    <br/>
    <br/>
	<input type="submit" name="submit" value="<?echo GetMessage("MOD_INSTALL")?>">
</form>

<script>
BX.ready(function(){

    BX.bind(BX('kit_install_demo'), 'change', function(){
        var settings = BX.findChild(BX("kit_mapoffices_install_form"), {className: "demo_settings"}, true, true);
        for(i in settings){
            BX('kit_install_demo').checked ? BX.show(settings[i]) : BX.hide(settings[i]);
            }
        });


    });
</script>