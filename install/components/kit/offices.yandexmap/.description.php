<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("T_YANDEX_MAP_OFFICE_LIST"),
	"DESCRIPTION" => GetMessage("T_YANDEX_MAP_OFFICE_DESC"),
	"ICON" => "/images/offices.gif",
	"SORT" => 20,
	"SCREENSHOT" => array(
		"/images/screen-1.jpg",
	),
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "content",
	),
);

?>