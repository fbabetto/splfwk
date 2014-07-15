<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HTMLPage
 *
 * @author fbabetto
 */
require_once dirname(__FILE__) . '/IHTMLPage.php';
require_once dirname(__FILE__) . '/../../constants.php';
require_once dirname(__FILE__) . '/../exception/ParseHTMLException.php';
require_once dirname(__FILE__) . '/IParsableHTML.php';
require_once dirname(__FILE__) . '/ParsableHTML.php';
require_once dirname(__FILE__) . '/ISessionManager.php';
require_once dirname(__FILE__) . '/SessionManager.php';
require_once dirname(__FILE__) . '/../SettingsParser.php';

//require_once LOGGER_PATH;
//Logger::configure(dirname(__FILE__) . '/../../logger-settings.xml');

class HTMLPage implements IHTMLPage {

    private $appBasePath;
    private $fwkBasePath;
    private $pageName;
    private $header;
    private $sidebar;
    private $content;
    private $footer;
    private $cssFilePath;
    private $sessionManager;
    private $settings;
    //private $username;
    //private $password;
    //private $authenticationSettings;
    private $log;

    public function __construct($appBasePath, $pageName) {
        $this->fwkBasePath = FWK_BASEPATH;
        $this->appBasePath = $appBasePath;
        $this->pageName = $pageName;
//		if (file_exists($appBasePath . '/logger-settings.xml'))
//			Logger::configure($appBasePath . '/logger-settings.xml');
//		$this->log = Logger::getLogger(__CLASS__);
        $this->log = new LogManager($appBasePath); //NEW
        try {
            $this->settings = new SettingsParser(FWK_SETTINGS_FILE, $appBasePath . '/settings.ini.php', $this->log);
        } catch (ErrorException $e) {
            echo 'Settings\' file not found!';
            die();
        }
//first we set the files to use (our own or overriden)
        $headerFilePath = FWK_BASEPATH . '/templates/header.html';
        $sidebarFilePath = FWK_BASEPATH . '/templates/sidebar.html';
        $contentFilePath = FWK_BASEPATH . '/templates/content.html';
        $footerFilePath = FWK_BASEPATH . '/templates/footer.html';
        $this->cssFilePath = FWK_BASEPATH . '/templates/style.css';
//then we override it if needed
        $headerFilePath = $this->overridePath('header.html', $headerFilePath);
        $sidebarFilePath = $this->overridePath('sidebar.html', $sidebarFilePath);
        $contentFilePath = $this->overridePath('content.html', $contentFilePath);
        $footerFilePath = $this->overridePath('footer.html', $footerFilePath);
        $this->cssFilePath = $this->overridePath('style.css', $this->cssFilePath);
//now we could get the html from these files (except for css)
        $this->header = $this->getCode($headerFilePath);
        $this->sidebar = $this->getCode($sidebarFilePath);
        $this->content = $this->getCode($contentFilePath);
        $this->footer = $this->getCode($footerFilePath);
    }

    private function overridePath($fileToOverride, $filePath) {
        $appPath = $this->appBasePath . '/templates/' . $fileToOverride;
        if (file_exists($appPath)) {
            $filePath = $appPath;
        } else {
            $this->log->debug('File ' . $filePath . ' not found. Using the framework one for ' . $fileToOverride);
        }
        return $filePath;
    }

    private function getCode($fileToRead) {
        $htmlCode = '';
        if (file_exists($fileToRead)) {
            $htmlCode = file_get_contents($fileToRead);
        } else {
            //echo 'file not found ' . $fileToRead;
            $errorMsg = "File $fileToRead not found.";
            $this->log->fatal($errorMsg);
            throw new TemplateFileNotFoundException($errorMsg . "\n");
        }
        return $htmlCode;
    }

    // warning: this function don't respect the content template but rewrite the content with the content passed as parameters
    public function setContent($contentTitle, $contentToSet) {
        //$this->content = '<h2>' . $contentTitle . '</h2>' . "\n" . $contentToSet;
        $this->content = "<div id=\"content\">\n<h2>$contentTitle</h2>\n$contentToSet";
        //remember to add the missing </div> when exporting
        //FIXME forse sistemare con template
    }

    public function addContent($contentToAdd) {
        $this->content = $this->content . "\n" . $contentToAdd;
//remember to add the missing </div> when exporting
    }

    public function getHTML() {
        $htmlCode = $this->header . "\n" . $this->sidebar . "\n" . $this->content . "\n" . '</div>' . "\n" . $this->footer;
        try {
            $parsablePage = new ParsableHTML($htmlCode, $this->log);
        } catch (DOMException $e) {
            $this->log->error("HTMLPage::getHTML, $e");
        }
// we fix css path if the application provides one
        if ($this->cssFilePath != (FWK_BASEPATH . '/templates/style.css')) {
            $parsablePage->setCssFilePath($this->cssFilePath);
        }
        $parsablePage->setCurrentPageOnMenu($this->pageName);
//FIX ACTION OF FORM
        $parsablePage->setLoginFormActionPath($this->pageName);
        $this->sessionManager = new SessionManager($this->log);
        $success = FALSE;
        if (isset($_POST['username']) && isset($_POST['password'])) {
            //try {
            $authenticationSettings = $this->settings->getSettings('authentication');
            $this->username = htmlspecialchars($_POST['username']);
            $this->password = htmlspecialchars($_POST['password']);
            $success = $this->sessionManager->authenticate($authenticationSettings['address'], $authenticationSettings['port'], htmlspecialchars($_POST['username']), htmlspecialchars($_POST['password']), $authenticationSettings['username_prefix']);
            //} catch (ErrorException $e) {
//TODO
            //}
            if (!$success) {
                $globalSettings = $this->settings->getSettings('global');
                $parsablePage->showLoginError($globalSettings['login_error_message']);
                unset($this->username);
                unset($this->password);
            } else {
                $username = htmlspecialchars($_POST['username']);
                $password = htmlspecialchars($_POST['password']);
                $this->userGroups = $this->sessionManager->getGroups($authenticationSettings['address'], $authenticationSettings['port'], $username, $password, $authenticationSettings['username_prefix'], $authenticationSettings['base_dn'], $username);
                $this->sessionManager->getInfo($authenticationSettings['address'], $authenticationSettings['port'], $username, $password, $authenticationSettings['username_prefix'], $authenticationSettings['base_dn'], $username);
                //FIXME
                header("Location: $this->pageName"); //FIXME ??
            }
        } else {//FIXME DA TOGLIERE!!!!
//echo 'errore username o password non inseriti';
// TODO FORSE MESSAGGIO DI ERRORE ANCHE QUI
        }
        if ($this->sessionManager->isLogged()) {
            $parsablePage->setLogged($_SESSION['logged']);
        }
        $htmlCode = $parsablePage->getHTML();
        return $htmlCode;
    }

    public function createDbManager($connectionName) {
        $connectionSettings = $this->settings->getSettings($connectionName);
        $dbmanager = null;
        if (isset($connectionSettings)) {
            switch ($connectionSettings['type']) {
                case 'oci': $dbmanager = new OCIManager($connectionSettings, $this->log);
                    break;
                case 'postgresql': $dbmanager = new PostgreSQLManager($connectionSettings, $this->log);
                    break;
                //http://www.php.net/manual/en/mysqlinfo.api.choosing.php
                case 'pdo': $dbmanager = new PDOManager($connectionSettings, $this->log);
                    break;
                default: throw new DatabaseTypeException("HTMLPage::createDbManager: the type of database you choose is not known on this framework; available ones are oci, postgresql, pdo.");
                    break;
            }
        }
        return $dbmanager;
    }

}

?>
