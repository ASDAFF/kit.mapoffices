<?
/**
 * Copyright (c) 7/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arCityListType = array(
	'LINK' => GetMessage("T_KIT_YMAP_CITYSELECT_TYPE_LINK"),
	'SELECT' => GetMessage("T_KIT_YMAP_CITYSELECT_TYPE_SELECT"),
	);

if($arCurrentValues["CITY"]=="Y")
{
	$arTemplateParameters = array(
		
		"CITY_SELECTOR" => Array(
			"PARENT" => "CITY",
			"NAME" => GetMessage("T_KIT_YMAP_CITYSELECT_TYPE"),
			"DEFAULT" => "LINK",
			"TYPE" => "LIST",
			"VALUES" => $arCityListType,
		),
	);
}

#print_r($arCurrentValues);