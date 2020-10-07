<?
$module_id = 'wsm.mapoffices';
$INSTALL_IBLOCK_TO_TYPE = COption::GetOptionString($module_id, 'iblock_type_demo', '');

self::AddLog('InstallDB -> install_iblock_demo in iblock_type ', $INSTALL_IBLOCK_TO_TYPE);

$sites = array();

$rsSites = CSite::GetList($by="sort", $order="desc", Array("ACTIVE" => "Y"));
while ($arSite = $rsSites->Fetch())
	$sites[] = $arSite['ID'];

$arFields = Array(
	"IBLOCK_TYPE_ID" => $INSTALL_IBLOCK_TO_TYPE,
	"NAME" => GetMessage("wsm.mapoffices_IBLOCK_NAME"),
	"CODE" => '',
	"ACTIVE" => 'Y',
	"SECTION_CHOOSER" => 'C',
	"VERSION" => 2,
	"INDEX_ELEMENT" => 'N',
	"INDEX_SECTION" => 'N',
	"DETAIL_PAGE_URL" => '',
	"SITE_ID" => $sites,
	"SORT" => 10,
	"DESCRIPTION" => GetMessage("wsm.mapoffices_IBLOCK_DESC"),
	"DESCRIPTION_TYPE" => 'text',
	'ELEMENT_NAME' => GetMessage("wsm.mapoffices_IBLOCK_ELEMENT_NAME_DESC"),
	'ELEMENTS_NAME' => GetMessage("wsm.mapoffices_IBLOCK_ELEMENTS_NAME_DESC"),
	'SECTION_NAME' => GetMessage("wsm.mapoffices_IBLOCK_SECTION_NAME_DESC"),
	'SECTIONS_NAME' => GetMessage("wsm.mapoffices_IBLOCK_SECTIONS_NAME_DESC"),
	);

$ib = new CIBlock;		
$ID = $ib->Add($arFields);
$res = ($ID>0);

if(!$res)
	self::AddLog("InstallDB -> install_iblock_demo -> create iblock -> error", $ib->LAST_ERROR);

global $USER;
	
if($res)
{
	CIBlock::SetPermission($ID, Array("1"=>"X", "2"=>"R"));
	
	COption::SetOptionString($module_id, 'iblock_demo', $ID);

    self::AddLog("InstallDB -> install_iblock_demo -> new iblock id = ", $ID);

    self::AddLog("InstallDB -> install_iblock_demo -> create prop = yandex map");

	$arFields = Array(
		"IBLOCK_ID" => $ID,
		"NAME" => GetMessage("wsm.mapoffices_IBLOCK_PROP_YMAP_DESC"),
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "YMAP",
		"PROPERTY_TYPE" => "S",
		"USER_TYPE" => "map_yandex",
	);

    $ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);

    self::AddLog("InstallDB -> install_iblock_demo -> create prop = work time");

	$arFields = Array(
		"NAME" => GetMessage("wsm.mapoffices_IBLOCK_PROP_WORK_TIME_DESC"),
		"ACTIVE" => "Y",
		"SORT" => "300",
		"CODE" => "WORK_TIME",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $ID
	);
	$PropID = $ibp->Add($arFields);

    self::AddLog("InstallDB -> install_iblock_demo -> create prop = adres");

	$arFields = Array(
		"NAME" => GetMessage("wsm.mapoffices_IBLOCK_PROP_ADRES_DESC"),
		"ACTIVE" => "Y",
		"SORT" => "200",
		"CODE" => "ADRES",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $ID
	);
	$PropID = $ibp->Add($arFields);

    self::AddLog("InstallDB -> install_iblock_demo -> create prop = phone");

	$arFields = Array(
		"NAME" => GetMessage("wsm.mapoffices_IBLOCK_PROP_PHONE_DESC"),
		"ACTIVE" => "Y",
		"SORT" => "200",
		"CODE" => "PHONE",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $ID
	);
	$PropID = $ibp->Add($arFields);					
	

	$el = new CIBlockElement;

    self::AddLog("InstallDB -> install_iblock_demo -> create elements ...");

	$arLoadProductArray = Array(
		"MODIFIED_BY"    => $USER->GetID(),
		"IBLOCK_SECTION_ID" => false,
		"IBLOCK_ID"      => $ID,
		"PROPERTY_VALUES"=> array(
			'YMAP' => '55.771425617673, 37.649231796295',
			'ADRES' => GetMessage("wsm.mapoffices_DEMO_EL2_ADRES"),
			'WORK_TIME' => '09.00 - 18.00',
			'PHONE'  => '(495) 123-456-789',
			),
		"NAME"           => GetMessage("wsm.mapoffices_DEMO_EL1_NAME"),
		"ACTIVE"         => "Y",
		"PREVIEW_TEXT"   => GetMessage("wsm.mapoffices_DEMO_EL1_DESCRIPTION"),
		//"DETAIL_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/image.gif")
		);

	$PRODUCT_ID = $el->Add($arLoadProductArray);

    if($PRODUCT_ID)
        self::AddLog("InstallDB -> install_iblock_demo -> element created ID=".$PRODUCT_ID);
	
	$arLoadProductArray = Array(
		"MODIFIED_BY"    => $USER->GetID(),
		"IBLOCK_SECTION_ID" => false,
		"IBLOCK_ID"      => $ID,
		"PROPERTY_VALUES"=> array(
			'YMAP' => '55.8174335281775, 37.56535144907378',
			'ADRES' => GetMessage("wsm.mapoffices_DEMO_EL2_ADRES"),
			'WORK_TIME' => '09.00 - 18.00',
			'PHONE'  => '(495) 987-654-321',
			),
		"NAME"           => GetMessage("wsm.mapoffices_DEMO_EL2_NAME"),
		"ACTIVE"         => "Y",
		"PREVIEW_TEXT"   => GetMessage("wsm.mapoffices_DEMO_EL2_DESCRIPTION"),
		);

	$PRODUCT_ID = $el->Add($arLoadProductArray);

    if($PRODUCT_ID)
        self::AddLog("InstallDB -> install_iblock_demo -> element created ID=".$PRODUCT_ID);
}
