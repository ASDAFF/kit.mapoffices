<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="kit_map_offices_block">

	<div class="ymap">
		<div id="KIT_MapOffice_YMAP"></div>
	</div>

	<div class="map_control">
		<div class="geo_info">
			<?if($arResult["CALULATED"]["OTHER_CITY"] && $arParams["CITY"] == "Y"): ?>
				<?=GetMessage("KIT_OFFICEMAP_YOUR_CITY")?>:
				<?=$arResult["CITY"][$arResult["CALULATED"]["CITY_ID"]]['NAME'];?>
			<?elseif($arResult["CALULATED"]["POINT_ID"] > 0 && $arParams["CITY"] != "Y"):?>
				<?=GetMessage("KIT_OFFICEMAP_YOUR_OFFICE")?>:
				<?=$arResult["ITEMS"][$arResult["CALULATED"]["POINT_ID"]]['NAME'];?>
			<?endif;?>
		</div>
		<div class="links">
			<a href="#" map-action="map.setDefault"><?=GetMessage("KIT_SHOW_MAP_CENTER")?></a>
			<?if($arParams["SHOW_TRAFFIC"] == 'Y'):?>
			<a href="#" map-action="traffic.toggle"><?=GetMessage("KIT_SHOW_TRAFFIC")?></a>
			<?endif;?>
		</div>
	</div>

	<?if(is_array($arResult["CITY"]) && count($arResult["CITY"]) && $arParams["CITY"] == "Y" && $arParams["CITY_SELECTOR"] != "SELECT"):?>
		
		<div class="kit_office_city_celector">
			<?foreach($arResult["CITY"] as $arCity):?>
			<a href="#"  map-action="map.setCity" data-id="<?=$arCity['ID'];?>"><?=$arCity['NAME'];?></a>
			<?endforeach;?>
		</div>
		
	<?elseif(is_array($arResult["CITY"]) && count($arResult["CITY"]) && $arParams["CITY"] == "Y"):?>
	
		<div class="kit_office_city_celector">
			<span><?=GetMessage("KIT_MAPOFFICES_CITY")?>:</span>
			<select map-action="map.setCity">
			<?foreach($arResult["CITY"] as $arCity):?>
				<option value="<?=$arCity['ID'];?>"><?=$arCity['NAME'];?></option>
			<?endforeach;?>
			</select>
		</div>
		
	<?endif;?>

	<div class="officeList">
		<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		
		//create images
		//$arItem["IMAGE_S"] = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], array('width'=>140, 'height'=>90), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		$arItem["IMAGE_B"] = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], array('width'=>800, 'height'=>800), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		?>
		<div class="officeItem selector-office" data-city="<?=$arItem['CITY_ID']?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<div class="block">
				<h2><?echo $arItem["NAME"]?></h2>
				<?foreach($arItem["DISPLAY_PROPERTIES"] as $code => $prop):?>
					<p><? echo '<b>'.$prop['NAME'].'</b>: ';?>
                    <?if(is_array($prop["DISPLAY_VALUE"])):?>
                        <?=implode("&nbsp;/&nbsp;", $prop["DISPLAY_VALUE"]);?>
                    <?else:?>
                        <?=$prop["DISPLAY_VALUE"];?>
                    <?endif?></br></p>
				<?endforeach;?>
				
				<?if($arItem["HAVE_POSITION_ON_MAP"]):?>
				<a href="#" class="more onmap" map-action="map.setOffice" data-id="<?=$arItem["ID"]?>"><?=GetMessage("KIT_SHOW_ON_MAP")?></a>
				<?endif;?>
				
				<?if($arItem["IMAGE_B"]):?>
				<a class="more foto fancybox" target="_blank" href="<?=$arItem["IMAGE_B"]['src']?>"><?=GetMessage("KIT_SHOW_IMAGE")?></a>
				<?endif;?>	
			</div>	
			
			<?/*
			<?if($arItem["IMAGE_S"]):?>
				<a class="image fancybox" target="_blank" href="<?=$arItem["IMAGE_B"]['src']?>">
					<img src="<?=$arItem["IMAGE_S"]['src']?>" alt="<?=$arItem["NAME"]?>"/>
				</a>
			<?endif;?>
			*/?>
		</div>
		<?endforeach;?>
	</div>
	<div style="clear:both;"></div>
</div>

<?
$data = "";
foreach($arResult["ITEMS"] as $arItem)
{
	$prop_data = "";


	foreach($arItem["DISPLAY_BALOON_PROPERTIES"] as $code => $prop)
    {
        if(is_array($prop['DISPLAY_VALUE']))
            $prop_data .= '<b>'.$prop['NAME'].'</b>: '.implode(', ', $prop['DISPLAY_VALUE']).'</br>';
        else
            $prop_data .= '<b>'.$prop['NAME'].'</b>: '.$prop['DISPLAY_VALUE'].'</br>';
    }


	$data .= $arItem['ID'].":{".
		'name: '.CUtil::PhpToJSObject($arItem['NAME'], false, true).','.
		'city: '.$arItem["IBLOCK_SECTION_ID"].','.
		'desc: '.CUtil::PhpToJSObject($arItem['PREVIEW_TEXT'], false, true).','.
		'prop: '.CUtil::PhpToJSObject($prop_data, false, true).','.   
		'center: ['.$arItem["PROPERTIES"][$arParams['POINT_POSITION']]['VALUE'].'],'.
		'url: "'.($arItem['DETAIL_PAGE_URL'] ? '<a href=\"'.$arItem['DETAIL_PAGE_URL'].'\">'.GetMessage("KIT_OFFICEMAP_PODROBNEE").'</a>' : '').'"'.
		'},';
}
$data = rtrim($data,',');
?>


<script> 
	var data = {<?=$data?>};
	
	//configuration
	var config = {
	
		debug		: false,
		map			: 'KIT_MapOffice_YMAP',
		map_center	: [<?=$arResult["MAP_CENTER"]?>],
		map_zoom	: <?=$arResult["MAP_ZOOM"]?>,
		auto_zoom_correct: -2,
		city_id: <?=$arResult["CALULATED"]["CITY_ID"]?>,
		selector_office_block : "selector-office",
		ymap_api_error: '<?=GetMessage("KIT_YMAP_ERROR")?>',
		ymaps_ready: function(YMap, Collection, data){ 
		
			//callback, map ready
			//for example: add your controls on map

			YMap.controls
				.add('zoomControl', { right: 5, top: 45 })
				.add('typeSelector')
				.add('mapTools');

			},
		create_placemark: function(data){

			//callback, create point in collection
			//here you can change the option points
			
			var point_data = {
				<?if($arParams["MAP_POINT_PRESET_TYPE"] == 'Stretchy'):?> 
				iconContent: data['name'],
				<?endif;?>
				balloonContentHeader: data['name'],
				balloonContentBody: data['desc'] + '<br/> ' + data['prop'],
				balloonContentFooter: data['url']
				};
				
			var point_opt = {
				preset: '<?=$arParams["MAP_POINT_PRESET"]?>'
				};
			
			//please return objects data and options
			return {data: point_data, options: point_opt};
			
			},
		action_traffic: function(action, traffic_status, controls){
			
			//callback, when the display state changes traffic
			//example: change text on controls

			for(i in controls) {

				if(controls[i].getAttribute("map-action") != "traffic.toggle")
					continue;

				if(traffic_status)
					controls[i].innerHTML = "<?=GetMessage("KIT_HIDE_TRAFFIC")?>";
				else
					controls[i].innerHTML = "<?=GetMessage("KIT_SHOW_TRAFFIC")?>";
				}

			},
		action_map: function(action, traffic_status, controls){
		
			},
		select_city: function(el, city_id, location, zoom, data){
			
			//callback, when city selected
			
			},
		select_point: function(index, data){
			
			//callback, when click "show on map"
			//for example: scroll to map
			
			BX.scrollToNode('KIT_MapOffice_YMAP');
			}	

		};

var YMapController;

BX.ready(function(){
	
	YMapController = new BX.KITMapOfficesMap(data, config);
	//YMapController.ready(function(controller){
	//	controller.Action('traffic.show');
	//	});
	});

</script>

