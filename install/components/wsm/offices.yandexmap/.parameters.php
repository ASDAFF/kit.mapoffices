<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$arMapPointPerset = array(
	'blue' => 'blue',
	'orange' => 'orange',
	'darkblue' => 'darkblue',
	'pink' => 'pink',
	'darkgreen' => 'darkgreen',
	'red' => 'red',
	'darkorange' => 'darkorange',
	'violet' => 'violet',
	'green' => 'green',
	'white' => 'white',
	'grey' => 'grey',
	'yellow' => 'yellow',
	'lightblue' => 'lightblue',
	'brown' => 'brown',
	'night' => 'night',
	'black' => 'black'
	); 

$arMapPointPersetType = array(
	'' => GetMessage("T_IBLOCK_DESC_POINT_PERSER_TYPE_1"),
	'Dot' => GetMessage("T_IBLOCK_DESC_POINT_PERSER_TYPE_2"),
	'Stretchy' => GetMessage("T_IBLOCK_DESC_POINT_PERSER_TYPE_3"),
	);
	
	
if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arSorts = Array("ASC"=>GetMessage("T_IBLOCK_DESC_ASC"), "DESC"=>GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = Array(
	"ID"=>GetMessage("T_IBLOCK_DESC_FID"),
	"NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
	"ACTIVE_FROM"=>GetMessage("T_IBLOCK_DESC_FACT"),
	"SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
	"TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
	);

$arSortFieldsSect = Array(
	"ID"=>GetMessage("T_IBLOCK_DESC_FID"),
	"NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
	"SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
	);
	
$arProperty_LNS = array();
$arProperty_YMAP = array();
$arSections = array(
	-1 => GetMessage("T_USE_COORDINATES"), 
	0 => GetMessage("T_IBLOCK_SECTION_ALL")
	);

$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")) && $arr["USER_TYPE"] != "map_yandex")
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
	elseif (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")) && $arr["USER_TYPE"] == "map_yandex")
	{
		$arProperty_YMAP[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$rsSection = CIBlockSection::GetList(Array("SORT"=>"­­ASC"), Array("IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"]), false);
while($res = $rsSection->GetNext()){
	$arSections[$res['ID']] = $res['NAME'];
}

$arComponentParameters = array(
	"GROUPS" => array(
		"MAP_DATA_SOURSE" => Array(
			"NAME" => GetMessage("GROUPS_MAP_DATA_SOURSE"),
			"SORT" => 140,
		),
		"MAP_SETTING" => Array(
			"NAME" => GetMessage("GROUPS_MAP_SETTING"),
			"SORT" => 120,
		),
		"CITY" => Array(
			"NAME" => GetMessage("GROUPS_CITY"),
			"SORT" => 110,
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
		),

		"POINT_POSITION" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_MAP_POSITION"),
			"TYPE" => "LIST",
			"DEFAULT" => "ACTIVE_FROM",
			"VALUES" => $arProperty_YMAP,
			"ADDITIONAL_VALUES" => "N",
		),
		
		
		"OFFICES_WITHOUT_SHOWING_POSITIONS" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_OFFICES_WITHOUT_SHOWING_POSITIONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),
		
		"CHECK_PERMISSIONS" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_CHECK_PERMISSIONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),
		

		
	
		
		
		
		"INCLUDE_YMAP_SCRIPT" => Array(
			"PARENT" => "MAP_SETTING",
			"NAME" => GetMessage("T_INCLUDE_YMAP_SCRIPT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),
		"SHOW_TRAFFIC" => Array(
			"PARENT" => "MAP_SETTING",
			"NAME" => GetMessage("T_IBLOCK_DESC_SHOW_TRAFFIC"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
		),
		
		"MAP_SET_CENTER_AUTO" => Array(
			"PARENT" => "MAP_SETTING",
			"NAME" => GetMessage("T_IBLOCK_DESC_MAP_SET_CENTER_AUTO"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
		),
		
		"CITY_DEF" => Array(
			"PARENT" => "MAP_SETTING",
			"NAME" => GetMessage("T_IBLOCK_DESC_CITY_DEF"),
			"TYPE" => "LIST",
			"DEFAULT" => "",
			"VALUES" => $arSections,
			"REFRESH" => "Y",
		),
		
		
		
		"MAP_CENTER" => Array(
			"PARENT" => "MAP_SETTING",
			"NAME" => GetMessage("T_IBLOCK_DESC_MAP_CENTER"),
			"TYPE" => "STRING",
			"DEFAULT" => '55.8124627516208, 37.525665060188324',
		),
		"MAP_ZOOM" => Array(
			"PARENT" => "MAP_SETTING",
			"NAME" => GetMessage("T_IBLOCK_DESC_MAP_ZOOM"),
			"TYPE" => "STRING",
			"DEFAULT" => '11',
		),

		"MAP_POINT_PRESET" => Array(
			"PARENT" => "MAP_SETTING",
			"NAME" => GetMessage("T_IBLOCK_DESC_MAP_POINT_PRESET"),
			"TYPE" => "LIST",
			"DEFAULT" => "red", 
			"VALUES" => $arMapPointPerset,
			"ADDITIONAL_VALUES" => "N",
		),
		
		
		
		"MAP_POINT_PRESET_TYPE" => Array(
			"PARENT" => "MAP_SETTING",
			"NAME" => GetMessage("T_IBLOCK_DESC_MAP_POINT_PRESET_TYPE"),
			"TYPE" => "LIST",
			"DEFAULT" => "", 
			"VALUES" => $arMapPointPersetType, 
		),
		

		
		
		
		
		
		"BALOON_BODY" => Array(
			"PARENT" => "MAP_SETTING",
			"NAME" => GetMessage("T_IBLOCK_DESC_BALOON_BODY"),
			"TYPE" => "LIST",
			"DEFAULT" => "ADRES",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_LNS,
			"ADDITIONAL_VALUES" => "Y",
		),
		
		"FILTER_NAME" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_FILTER"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		
		"SORT_BY1" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBORD1"),
			"TYPE" => "LIST",
			"DEFAULT" => "ACTIVE_FROM",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER1" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBBY1"),
			"TYPE" => "LIST",
			"DEFAULT" => "DESC",
			"VALUES" => $arSorts,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_BY2" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBORD2"),
			"TYPE" => "LIST",
			"DEFAULT" => "SORT",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER2" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBBY2"),
			"TYPE" => "LIST",
			"DEFAULT" => "ASC",
			"VALUES" => $arSorts,
			"ADDITIONAL_VALUES" => "Y",
		),
	
		"PROPERTIES" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_PROPERTY"),
			"TYPE" => "LIST",
			"DEFAULT" => "ADRES",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_LNS,
			"ADDITIONAL_VALUES" => "Y",
		),
		
		"DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
			"DETAIL",
			"DETAIL_URL",
			GetMessage("T_IBLOCK_DESC_DETAIL_PAGE_URL"),
			"",
			"URL_TEMPLATES"
		),
		"PREVIEW_TRUNCATE_LEN" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_PREVIEW_TRUNCATE_LEN"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		
		"PARENT_SECTION" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"PARENT_SECTION_CODE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),

		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_FILTER" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("IBLOCK_CACHE_FILTER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BNL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),

		"CITY" => Array(
			"PARENT" => "CITY",
			"NAME" => GetMessage("T_IBLOCK_DESC_CITY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			),

		"USE_GEOIP" => Array(
			"PARENT" => "CITY",
			"NAME" => GetMessage("T_IBLOCK_DESC_CITY_AUTO"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
			),
			
		"SORT_CITY_BY1" => Array(
			"PARENT" => "CITY",
			"NAME" => GetMessage("T_IBLOCK_DESC_SORT_CITY_BY1"),
			"TYPE" => "LIST",
			"DEFAULT" => "NAME",
			"VALUES" => $arSortFieldsSect,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_CITY_ORDER1" => Array(
			"PARENT" => "CITY",
			"NAME" => GetMessage("T_IBLOCK_DESC_SORT_CITY_ORDER1"),
			"TYPE" => "LIST",
			"DEFAULT" => "ASC",
			"VALUES" => $arSorts,
			"ADDITIONAL_VALUES" => "Y",
		),
		
	),
);


if($arCurrentValues["CITY"]=="Y")
{
	unset($arComponentParameters["PARAMETERS"]["MAP_SET_CENTER_AUTO"]);
	
	if($arCurrentValues["CITY_DEF"] >= 0)
	{
		unset($arComponentParameters["PARAMETERS"]["MAP_CENTER"]);
		unset($arComponentParameters["PARAMETERS"]["MAP_ZOOM"]);
	}
	
	$arCurrentValues["MAP_SET_CENTER_AUTO"] = "N";
}
else
{
	if(!$arCurrentValues["MAP_SET_CENTER_AUTO"] )
		$arCurrentValues["MAP_SET_CENTER_AUTO"] = "Y";
}

if($arCurrentValues["MAP_SET_CENTER_AUTO"] == "Y")
{
	unset($arComponentParameters["PARAMETERS"]["MAP_CENTER"]);
	unset($arComponentParameters["PARAMETERS"]["MAP_ZOOM"]);
}

if(count($arSections) == 0 || $arCurrentValues["CITY"] == 'N')
{
	unset($arComponentParameters["PARAMETERS"]["CITY_AUTO"]);
	unset($arComponentParameters["PARAMETERS"]["CITY_DEF"]);
}



?>
