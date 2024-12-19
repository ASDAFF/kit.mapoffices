<? global $MESS;
/**
 * Copyright (c) 7/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);
if (class_exists('kit_mapoffices')) return;

Class kit_mapoffices extends CModule
{
    var $MODULE_ID = 'kit.mapoffices';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";
    var $_878322151;

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = GetMessage('kit.mapoffices_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('kit.mapoffices_MODULE_DESC');
        $this->PARTNER_NAME = GetMessage('kit.mapoffices_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('kit.mapoffices_PARTNER_URI');
        $_220697396 = dirname(__FILE__);
        $_646427159 = str_replace($_SERVER['DOCUMENT_ROOT'], ' ', $_220697396);
        $_1856655138 = strpos($_646427159, '/local') === 0 ? '/local' : BX_ROOT;
        $this->_878322151 = $_1856655138 . '/modules/' . $this->MODULE_ID;
    }

    function AddLog()
    {
        return;
        $_1108327832 = func_get_args();
        $_1950193852 = ' ';
        foreach ($_1108327832 as $_1281494455) $_1950193852 .= print_r($_1281494455, true) . ' / ';
        $_1950193852 = date('Y-m-d H:i:s  ') . $_1950193852 . chr(13) . '---------' . chr(13);
        $_651798574 = fopen($_SERVER['DOCUMENT_ROOT'] . $this->_878322151 . '/install_' . date('Y-m-d') . '.log', 'a');
        fwrite($_651798574, $_1950193852);
        fclose($_651798574);
    }

    function convDir($_149054505 = '', $_1417335555 = false)
    {
        global $APPLICATION;
        if ($_149054505 == '' || $_149054505 == '/') return;
        $_480731680 = new CBXVirtualIo();
        $_9974310 = $_1417335555 ? $_480731680->RelativeToAbsolutePath($_149054505) : $_149054505;
        $_951753601 = $_480731680->GetDirectory($_9974310);
        $_1636257109 = $_951753601->GetChildren();
        $this->AddLog('convDir arChildren', $_1636257109);

        foreach ($_1636257109 as $_554404312) {
            if ($_554404312->IsDirectory()) {
                $this->convDir($_554404312->GetPath());
            } else {
                $_1400141241 = $_554404312->GetPathWithName();
                $_1585325890 = $APPLICATION->ConvertCharset(file_get_contents($_1400141241), 'WINDOWS-1251', 'UTF-8');
                $this->AddLog('convDir is file', $_1400141241, $_1585325890);
                $_978919704 = $_480731680->GetFile($_1400141241);
                $_978919704->PutContents($_1585325890);
            }
        }
    }

    function InstallDB($_1627882924 = array())
    {
        $this->AddLog('InstallDB', $_1627882924);

        COption::SetOptionString($this->MODULE_ID, 'iblock_type_demo', $_1627882924['INSTALL_IBLOCK_TO_TYPE']);
        if (CModule::IncludeModule('iblock') && $_1627882924['INSTALL_DEMO']) include_once(dirname(__FILE__) . '/install_iblock_demo.php');
        return true;
    }

    function UnInstallDB($_1627882924 = array())
    {
        GLOBAL $DB;
        $this->AddLog('UnInstallDB', $_1627882924);
        if (CModule::IncludeModule('iblock') && !$_1627882924['SAVE_DEMO_IBLOCK']) include_once(dirname(__FILE__) . '/uninstall_iblock_demo.php');
        return true;
    }

    function InstallFiles($_1627882924 = array())
    {
        $_1627882924['INSTALL_PATH'] = 'kit_mapoffices_demo';
        $this->AddLog('InstallFiles', $_1627882924);
        $this->AddLog('InstallFiles component', $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/components');
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . $this->_878322151 . '/install/components', $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/components', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . $this->_878322151 . '/install/js', $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/js/' . $this->MODULE_ID, true, true);
        if ($_1627882924['INSTALL_DEMO']) {
            $this->AddLog('InstallDB -> install demo files to dir = ' . $_1627882924['INSTALL_PATH']);
            $_2042719856 = $_SERVER['DOCUMENT_ROOT'] . $this->_878322151 . '/install/public';
            $_635304996 = $_SERVER['DOCUMENT_ROOT'] . '/' . $_1627882924['INSTALL_PATH'];
            CheckDirPath($_635304996);
            COption::SetOptionString($this->MODULE_ID, 'path_demo', $_1627882924['INSTALL_PATH']);
            $_480731680 = new CBXVirtualIo();
            $_480731680->Copy($_2042719856, $_635304996, false);
            if (defined('BX_UTF') && BX_UTF) $this->convDir($_635304996);
        }
        return true;
    }

    function UnInstallFiles()
    {
        $this->AddLog('UnInstallFiles', BX_ROOT . '/js/' . $this->MODULE_ID);
        DeleteDirFilesEx(BX_ROOT . '/js/' . $this->MODULE_ID);
        if (is_dir($_1714185542 = $_SERVER['DOCUMENT_ROOT'] . $this->_878322151 . '/install/components')) {
            if ($_951753601 = opendir($_1714185542)) {
                while (false !== $_1832318271 = readdir($_951753601)) {
                    if ($_1832318271 == '..' || $_1832318271 == '.' || !is_dir($_241762190 = $_1714185542 . '/' . $_1832318271)) continue;
                    $_1438675359 = opendir($_241762190);
                    while (false !== $_151953450 = readdir($_1438675359)) {
                        if ($_151953450 == '..' || $_151953450 == '.') continue;
                        DeleteDirFilesEx('/bitrix/components/' . $_1832318271 . '/' . $_151953450);
                    }
                    closedir($_1438675359);
                }
                closedir($_951753601);
            }
        }

            $_149054505 = 'kit_mapoffices_demo';
            if (trim($_149054505) != '') {
                $this->AddLog('Uninstall -> remove dir /' . $_149054505);
                DeleteDirFilesEx('/' . $_149054505);
            }

        return true;
    }

    function DoInstall()
    {
        global $APPLICATION, $step;
        $step = IntVal($step);
        $_149054505 = 'kit_mapoffices_demo';
        if ($step < 2) $APPLICATION->IncludeAdminFile(GetMessage('KIT_OFFICESMAP_INSTALL_TITLE'), dirname(__FILE__) . '/step1.php'); elseif ($step == 2) {
            $_1627882924 = array('INSTALL_DEMO' => $_REQUEST['kit_install_demo'] == 'Y', 'INSTALL_IBLOCK_TO_TYPE' => htmlspecialcharsEx(trim($_REQUEST['iblock_type'])), 'INSTALL_PATH' => $_149054505,);
            $this->InstallFiles($_1627882924);
            $this->InstallDB($_1627882924);
            RegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(GetMessage('KIT_OFFICESMAP_INSTALL_TITLE'), dirname(__FILE__) . '/step2.php');
        }
    }

    function DoUninstall()
    {
        global $APPLICATION, $step;
        $step = IntVal($step);
        if ($step < 2) $APPLICATION->IncludeAdminFile(GetMessage('KIT_OFFICESMAP_UNINSTALL_TITLE'), dirname(__FILE__) . '/unstep1.php'); elseif ($step == 2) {
            $_1627882924 = array('SAVE_DEMO_IBLOCK' => $_REQUEST['save_demo_iblock'] == 'Y', 'SAVE_DEMO_SECTION' => $_REQUEST['save_demo_section'] == 'Y',);
            $this->UnInstallDB($_1627882924);
            $this->UnInstallFiles($_1627882924);
            COption::RemoveOption($this->MODULE_ID);
            UnRegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(GetMessage('KIT_OFFICESMAP_UNINSTALL_TITLE'), dirname(__FILE__) . '/unstep2.php');
        }
    }
} ?>