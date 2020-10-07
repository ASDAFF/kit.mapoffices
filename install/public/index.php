<?
/**
 * Copyright (c) 7/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Демо страница компонента "Карта офисов"');
?>
<?$IBLOCK_ID = intval(COption::GetOptionString('kit.mapoffices', 'iblock_demo', 0));?>


<?$APPLICATION->IncludeComponent("kit:offices.yandexmap", ".default", array(
	"IBLOCK_TYPE" => "",
	"IBLOCK_ID" => $IBLOCK_ID,
	"CITY" => "N",
	"USE_GEOIP" => "Y",
	"MAP_SET_CENTER_AUTO" => "Y",
	"INCLUDE_YMAP_SCRIPT" => "Y",
	"MAP_ZOOM" => "11",
	"MAP_POINT_PRESET" => "red",
	"MAP_POINT_PRESET_TYPE" => "",
	"SHOW_TRAFFIC" => "Y",
	"POINT_POSITION" => "YMAP",
	"BALOON_BODY" => array(
		0 => "ADRES",
		1 => "WORK_TIME",
		2 => "PHONE",
		3 => "",
	),
	"PROPERTIES" => array(
		0 => "ADRES",
		1 => "WORK_TIME",
		2 => "PHONE",
		3 => "",
	),
	"SORT_BY1" => "NAME",
	"SORT_ORDER1" => "ASC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"DETAIL_URL" => "",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"PARENT_SECTION" => "0",
	"PARENT_SECTION_CODE" => ""
	),
	false
);?>
<br/><br/>
<p style="color: #919191;">Описание компонента и документацию можно найти по адресу <a target="_blank" href="http://w-smart.ru/marketplace/mapoffices/">http://w-smart.ru/marketplace/mapoffices/</a></p>
<br/>
<br/>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>