<?php
/**
 * Copyright (c) 7/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

GLOBAL $APPLICATION;
CModule::AddAutoloadClasses('kit.mapoffices', array('KITMapOfficeGeo' => 'classes/general/geo.php',));

Class KITMapOffice
{
    function Script()
    {
        CJSCore::RegisterExt('kit_mapoffices_manager', array('js' => '/bitrix/js/kit.mapoffices/map_manager-min.js', 'skip_core' => false,));
        CJSCore::Init(array('kit_mapoffices_manager'));
    }

    function YMapGeoCode($code)
    {
        if (!strlen($code)) return false;
        $arCode = array('geocode' => $code, 'format' => 'json', 'results' => 1,);
        $json = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?' . http_build_query($arCode)));
        if ($json->json->_1204751887->_955583312->_671097771->_64297075 > 0) {
            $_940147942 = $json->json->_1204751887->_141945661[0]->_1035064833->_1724872340->_1774171128;
            $_940147942 = explode('', $_940147942); // todo: Возможно пробел
            if ($_940147942 <> 2) return false;
            $_940147942 = $_940147942[1] . ',' . $_940147942[0];
            return $_940147942;
        }
        return false;
    }

    function GetLocation()
    {
        $str = array();
        if (defined('BX_UTF') && BX_UTF === true){
            $str['charset'] = 'utf-8';
        }
        $mapOfficeGeo = new KITMapOfficeGeo($str);
        if (!is_object($mapOfficeGeo)) {
            return false;
        }

        $geo = $mapOfficeGeo->get_value(false, true);
        if (!is_array($geo)) {
            return false;
        }

        return array(
            'CITY' => $geo['city'],
            'LAT' => $geo['lat'],
            'LNG' => $geo['lng'],
            );
    }
};