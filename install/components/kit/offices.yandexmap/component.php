<?
/**
 * Copyright (c) 7/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$module_id = 'kit.mapoffices';
$rsModule = CModule::IncludeModuleEx($module_id);


if($rsModule == 0)
{
    ShowError(GetMessage("KIT_OFFICES_MODULE_NOT_INSTALLED"));
    return;
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
	$arParams["IBLOCK_TYPE"] = "service";
	
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["PARENT_SECTION"] = intval($arParams["PARENT_SECTION"]);
$arParams["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"]!="N";

$arParams["OFFICES_WITHOUT_SHOWING_POSITIONS"] = $arParams["OFFICES_WITHOUT_SHOWING_POSITIONS"] == "Y" ? "Y" : "N";
$arParams["CHECK_PERMISSIONS"] = $arParams["CHECK_PERMISSIONS"] == "N" ? "N" : "Y";

$arParams["SORT_CITY_BY1"] = trim($arParams["SORT_CITY_BY1"]);
if(strlen($arParams["SORT_CITY_BY1"])<=0)
	$arParams["SORT_CITY_BY1"] = "NAME";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_CITY_ORDER1"]))
	$arParams["SORT_CITY_ORDER1"]="ASC";
	
$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
	$arParams["SORT_ORDER1"]="DESC";

if(strlen($arParams["SORT_BY2"])<=0)
	$arParams["SORT_BY2"] = "SORT";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
	$arParams["SORT_ORDER2"]="ASC";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arParams["CITY"] = $arParams["CITY"] == 'Y' ? 'Y' : 'N';
$arParams["USE_GEOIP"] = $arParams["USE_GEOIP"] == 'N' ? 'N' : 'Y';

$arParams["INCLUDE_YMAP_SCRIPT"] = $arParams["INCLUDE_YMAP_SCRIPT"]!="N";
$arParams["CITY_DEF"] = intval($arParams["CITY_DEF"]);
$arParams["MAP_SET_CENTER_AUTO"] = $arParams["MAP_SET_CENTER_AUTO"] == 'N' ? 'N' : 'Y' ;

$arParams["SHOW_TRAFFIC"] = $arParams["SHOW_TRAFFIC"] == 'Y' ? 'Y' : 'N' ;

$arParams["MAP_CENTER"] = trim($arParams["MAP_CENTER"]);
if(strlen($arParams["MAP_CENTER"])<=0)
	$arParams["MAP_CENTER"] = "55.753107,37.621959";

$arParams["MAP_ZOOM"] = intval($arParams["MAP_ZOOM"]);
if($arParams["MAP_ZOOM"]<=0)
	$arParams["MAP_ZOOM"] = "12";	

$arParams["MAP_POINT_PRESET_TYPE"] = trim($arParams["MAP_POINT_PRESET_TYPE"]);

$arParams["MAP_POINT_PRESET"] = trim($arParams["MAP_POINT_PRESET"]);
if(strlen($arParams["MAP_POINT_PRESET"])<=0)
	$arParams["MAP_POINT_PRESET"] = "blue";	

$arParams["MAP_POINT_PRESET"] = 'twirl#'.$arParams["MAP_POINT_PRESET"].$arParams["MAP_POINT_PRESET_TYPE"].'Icon';

if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $key=>$val)
	if($val==="")
		unset($arParams["PROPERTY_CODE"][$key]);

$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);

$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;

$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
$arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"]!="N"; //Turn on by default
$arParams["INCLUDE_IBLOCK_INTO_CHAIN"] = $arParams["INCLUDE_IBLOCK_INTO_CHAIN"]!="N";


$arParams["PREVIEW_TRUNCATE_LEN"] = intval($arParams["PREVIEW_TRUNCATE_LEN"]);
$arParams["HIDE_LINK_WHEN_NO_DETAIL"] = $arParams["HIDE_LINK_WHEN_NO_DETAIL"]=="Y";

	


if($arParams["INCLUDE_YMAP_SCRIPT"])
    $APPLICATION->AddHeadString('<script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU"></script>', true);


$arResult["GEO"] = false;

if($arParams["USE_GEOIP"] == "Y")
{
	$arResult["GEO"] = KITMapOffice::GetLocation();
	//$arResult["GEO"] = false;
	
	if(!$arResult["GEO"])
		$arParams["USE_GEOIP"] = "N";
}

KITMapOffice::Script();

if($this->StartResultCache(false, array($arParams, $arResult["GEO"], ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $arrFilter)))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	if(is_numeric($arParams["IBLOCK_ID"]))
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"ID" => $arParams["IBLOCK_ID"],
		));
	}
	else
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"CODE" => $arParams["IBLOCK_ID"],
			"SITE_ID" => SITE_ID,
		));
	}
	
	if($arResult["IBLOCK"] = $rsIBlock->GetNext())
	{
		$arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
		
		//SELECT
		$arSelect = array(
			"ID",
			"IBLOCK_ID",
			"IBLOCK_SECTION_ID",
			"NAME",
			"DETAIL_PAGE_URL",
			"PREVIEW_TEXT",
			"PREVIEW_TEXT_TYPE",
			"PREVIEW_PICTURE",
			"DETAIL_PICTURE",
		);
		
		$bGetProperty = true;
		if($bGetProperty)
			$arSelect[]="PROPERTY_*";
		
		//=======================
		//ELEMENTS FILTER
		//=======================
		
		$arFilter = array (
			"IBLOCK_ID" => $arResult["IBLOCK"]["ID"],
			"ACTIVE" => "Y",
			"SECTION_ID" => false,
			"CHECK_PERMISSIONS" => "Y",
			"SECTION_GLOBAL_ACTIVE" => "Y",
			);

		if($arParams["CITY"] == "Y")
		{
			unset($arFilter["SECTION_ID"]);
			$arFilter["SECTION_ACTIVE"] = "Y";
			$arFilter["!SECTION_ID"] = false;
		}
		
		//set parent section
		
		$arParams["PARENT_SECTION"] = CIBlockFindTools::GetSectionID(
			$arParams["PARENT_SECTION"],
			$arParams["PARENT_SECTION_CODE"],
			array(
				"GLOBAL_ACTIVE" => "Y",
				"IBLOCK_ID" => $arResult["ID"],
			)
		);

		if($arParams["PARENT_SECTION"]>0)
		{
			$arFilter["SECTION_ID"] = $arParams["PARENT_SECTION"];
			
			//if($arParams["INCLUDE_SUBSECTIONS"])
			//	$arFilter["INCLUDE_SUBSECTIONS"] = "Y";

			$arResult["SECTION"]= array("PATH" => array());
			$rsPath = GetIBlockSectionPath($arResult["ID"], $arParams["PARENT_SECTION"]);
			$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"], $arParams["IBLOCK_URL"]);
			while($arPath=$rsPath->GetNext())
			{
				$arResult["SECTION"]["PATH"][] = $arPath;
			}
		}
		else
		{
			$arResult["SECTION"]= false;
		}
		
		//echo "<pre>".print_r($arFilter, true )."</pre>";
		
		//===============================
		//ORDER BY
		//===============================
		
		$arSort = array(
			$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
			$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
			);

		//if(!array_key_exists("ID", $arSort))
		//	$arSort["ID"] = "DESC";

		$points = array();
			
		$obParser = new CTextParser;
		$arResult["ITEMS"] = array();
		$arResult["ELEMENTS"] = array();
	
		//echo "<pre>".print_r($arParams, true)."</pre>";
		//echo "<pre>".print_r(array_merge($arFilter, $arrFilter), true)."</pre>";
		
		$rsElement = CIBlockElement::GetList($arSort, array_merge($arFilter, $arrFilter), false, false, $arSelect);
		$rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);

		while($obElement = $rsElement->GetNextElement())
		{
			$arItem = $obElement->GetFields();

			$arItem['IBLOCK_SECTION_ID'] = intval($arItem['IBLOCK_SECTION_ID']);
			
			$arButtons = CIBlock::GetPanelButtons(
				$arItem["IBLOCK_ID"],
				$arItem["ID"],
				0,
				array("SECTION_BUTTONS"=>false, "SESSID"=>false)
			);
			
			$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
			$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
			

			if($arParams["PREVIEW_TRUNCATE_LEN"] > 0)
				$arItem["PREVIEW_TEXT"] = $obParser->html_cut($arItem["PREVIEW_TEXT"], $arParams["PREVIEW_TRUNCATE_LEN"]);

			if($bGetProperty)
				$arItem["PROPERTIES"] = $obElement->GetProperties();
				
			$arItem["HAVE_POSITION_ON_MAP"] = true;
			if(strlen(trim($arItem["PROPERTIES"][$arParams['POINT_POSITION']]['VALUE'])) == 0)
			{
				$arItem["HAVE_POSITION_ON_MAP"] = false;
				if($arParams["OFFICES_WITHOUT_SHOWING_POSITIONS"] != "Y")
					continue;
			}
				
			
			
			$arItem["CITY_ID"] = $arItem["IBLOCK_SECTION_ID"];
			
			$points[] = array(
				'ID' => $arItem["ID"],
				'CITY' =>  $arItem["IBLOCK_SECTION_ID"],
				'MAP' => explode(',',$arItem["PROPERTIES"][$arParams['POINT_POSITION']]['VALUE'])
				);
			
			$arItem["DISPLAY_PROPERTIES"]=array();
			$arItem["DISPLAY_BALOON_PROPERTIES"] = array();
			
			foreach($arParams["BALOON_BODY"] as $pid)
			{
				$prop = &$arItem["PROPERTIES"][$pid];
				if(
					(is_array($prop["VALUE"]) && count($prop["VALUE"])>0)
					|| (!is_array($prop["VALUE"]) && strlen($prop["VALUE"])>0)
				)
				{
					$arItem["DISPLAY_BALOON_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "ymap");
				}
			}
			
			foreach($arParams["PROPERTIES"] as $pid)
			{
				$prop = &$arItem["PROPERTIES"][$pid];
				if(
					(is_array($prop["VALUE"]) && count($prop["VALUE"])>0)
					|| (!is_array($prop["VALUE"]) && strlen($prop["VALUE"])>0)
				)
				{
					$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "ymap");
				}
			}

			$arResult["ITEMS"][$arItem["ID"]] = $arItem;
			$arResult["ELEMENTS"][] = $arItem["ID"];
		}

		if(count($arResult["ITEMS"]) == 0)
			$arParams["MAP_CENTER"] = "55.753107,37.621959";

		$city_tmp = 0;
		$city_tmp2 = 0;
		$point_tmp = 0;
		$city_found = false;

		$arSFilter = Array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID"], 
			"ACTIVE" => "Y", 
			">ELEMENT_CNT" => 0,
			);
			
		if($arParams["PARENT_SECTION"] > 0)
			$arSFilter["SECTION_ID"] = $arParams["PARENT_SECTION"];
		 
		$arSort = Array(
			$arParams["SORT_CITY_BY1"] => $arParams["SORT_CITY_ORDER1"], 
			#"SORT"=>"ASC"
			);

		$rsSection = CIBlockSection::GetList($arSort, $arSFilter, false, array('ID', 'NAME'));
		while($res = $rsSection->GetNext())
		{	
			if($res["ELEMENT_CNT"] == 0)
				continue;
			
			if($arParams["CITY"] == 'Y' && $arParams["USE_GEOIP"] == 'Y' && $arResult["GEO"] !== false)
			{
				if(strtolower($arResult["GEO"]["CITY"]) == strtolower($res['NAME']))
					$city_tmp = $res['ID'];
			}
			
			if($arParams["CITY_DEF"] == $res['ID'])
				$city_found = true;
				
			$arResult["CITY"][$res['ID']] = array(
				'ID' => $res['ID'],
				'NAME' => $res['NAME'],
				);
		}
		
		
		if(!$city_found && $arParams["CITY_DEF"] > 0)
			$arParams["CITY_DEF"] = 0;
		elseif(!$city_found && $arParams["CITY_DEF"] == 0)
			$arParams["CITY_DEF"] = $arParams["PARENT_SECTION"];
			
		if(is_array($arResult["CITY"]) && count($arResult["CITY"]) == 0)
			$arParams["CITY"] = "N";

		//IP
		$sel_index = -1;	
		if($arParams["USE_GEOIP"] == 'Y' && $arResult["GEO"] !== false)
		{
			$distanse = false;
			foreach($points as $index => $p)
			{
				$t1 = $p['MAP'][0] - $arResult["GEO"]["LAT"];
				$t2 = $p['MAP'][1] - $arResult["GEO"]["LNG"];
				
				$t_distanse = sqrt($t1*$t1 + $t2*$t2);

				if($t_distanse < $distanse || $distanse === false)
				{
					$distanse = $t_distanse;
					$sel_index = $index;
				}
			}
		}
		
		
		$arResult["CALULATED"]["OTHER_CITY"] = $city_tmp == 0 && $sel_index >= 0 && $arParams["USE_GEOIP"] == 'Y' && $arParams["CITY"] == "Y";
		$arResult["CALULATED"]["CITY_ID"] = ($city_tmp == 0 && $sel_index == -1) ? $arParams["CITY_DEF"] : $points[$sel_index]['CITY'];

		$arResult["CALULATED"]["POINT_ID"] = $sel_index != 0 ? $points[$sel_index]['ID'] : 0 ;
		$arResult["CALULATED"]["POINT_CENTER"] = $sel_index != 0 ? $points[$sel_index]['MAP'] : false;

		$arResult["MAP_CENTER"] = $arParams['MAP_CENTER'];
		$arResult["MAP_ZOOM"] = $arParams['MAP_ZOOM'];
		
		if($arParams["CITY"] != "Y"  && $arParams["MAP_SET_CENTER_AUTO"] == "Y" && $arParams["USE_GEOIP"] != 'Y')
			$arResult["CALULATED"]["CITY_ID"] = 0;
		elseif($arParams["CITY"] != "Y")
			$arResult["CALULATED"]["CITY_ID"] = -1;
	
		//echo "<pre>".print_r($arResult, true )."</pre>";
		
		$this->SetResultCacheKeys(array(
			"ID",
			"LIST_PAGE_URL",
			"NAME",
			"SECTION",
			"ELEMENTS",
			));
			
		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->AbortResultCache();
		ShowError(GetMessage("KIT_OFFICES_MODULE_NA"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}
}
	



if(isset($arResult["ID"]))
{
	$arTitleOptions = null;
	if($USER->IsAuthorized())
	{
		if(
			$APPLICATION->GetShowIncludeAreas()
			|| (is_object($GLOBALS["INTRANET_TOOLBAR"]) && $arParams["INTRANET_TOOLBAR"]!=="N")
			|| $arParams["SET_TITLE"]
		)
		{
			if(CModule::IncludeModule("iblock"))
			{
				$arButtons = CIBlock::GetPanelButtons(
					$arResult["ID"],
					0,
					$arParams["PARENT_SECTION"],
					array("SECTION_BUTTONS"=>false)
				);

				if($APPLICATION->GetShowIncludeAreas())
					$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

				if(
					is_array($arButtons["intranet"])
					&& is_object($GLOBALS["INTRANET_TOOLBAR"])
					&& $arParams["INTRANET_TOOLBAR"]!=="N"
				)
				{
					$APPLICATION->AddHeadScript('/bitrix/js/main/utils.js');
					foreach($arButtons["intranet"] as $arButton)
						$GLOBALS["INTRANET_TOOLBAR"]->AddButton($arButton);
				}
			}
		}
	}

	$this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);
	
	
	
	return $arResult["ELEMENTS"];

}
?>