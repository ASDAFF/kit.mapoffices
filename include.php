<?php

GLOBAL $APPLICATION;
CModule::AddAutoloadClasses('wsm.mapoffices', array('WSMMapOfficeGeo' => 'classes/general/geo.php',));

Class WSMMapOffice
{
    function Script()
    {
        CJSCore::RegisterExt('wsm_mapoffices_manager', array('js' => '/bitrix/js/wsm.mapoffices/map_manager-min.js', 'skip_core' => false,));
        CJSCore::Init(array('wsm_mapoffices_manager'));
    }

    function YMapGeoCode($_1153776208)
    {
        if (!strlen($_1153776208)) return false;
        $_1433535450 = array('geocode' => $_1153776208, 'format' => 'json', 'results' => 1,);
        $_1658479760 = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?' . http_build_query($_1433535450)));
        if ($_1658479760->_1658479760->_1204751887->_955583312->_671097771->_64297075 > 0) {
            $_940147942 = $_1658479760->_1658479760->_1204751887->_141945661[0]->_1035064833->_1724872340->_1774171128;
            $_940147942 = explode('', $_940147942); // todo: Возможно пробел
            if ($_940147942 <> 2) return false;
            $_940147942 = $_940147942[1] . ',' . $_940147942[0];
            return $_940147942;
        }
        return false;
    }

    function GetLocation()
    {
        $_1263157265 = array();
        if (defined('BX_UTF') && BX_UTF === true) $_1263157265['charset'] = 'utf-8';
        $_75064107 = new WSMMapOfficeGeo($_1263157265);
        if (!is_object($_75064107)) return false;
        $_1588012887 = $_75064107->get_value(false, true);
        if (!is_array($_1588012887)) return false;
        return array('CITY' => $_1588012887['city'], 'LAT' => $_1588012887['lat'], 'LNG' => $_1588012887['lng'],);
    }
};