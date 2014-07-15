<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of settingsParser
 *
 * @author fbabetto
 */
require_once dirname(__FILE__) . '/ISettingsParser.php';
require_once dirname(__FILE__) . '/exception/ParseHTMLException.php';
require_once dirname(__FILE__) . '/exception/SettingsFileNotFoundException.php';

class SettingsParser {

    //private $fileName;
    private $settings;
    private $log;

    public function __construct($fwkSettingsFile, $appSettingsFile, $log) {

        //$this->fileName = $settingsFilePath;
        //prima leggo il file del fwk poi quello dell'applicazione
        $this->log = $log;
        $this->settings = parse_ini_file($fwkSettingsFile, TRUE);
        if (!$this->settings) {
            $this->log->fatal('Framework settings file not found in ' . $fwkSettingsFile . '.');
            throw new SettingsFileNotFoundException('Framework settings file not found in ' . $fwkSettingsFile . '.');
        }
        $appSettings = array();
        if (file_exists($appSettingsFile)) {
            $appSettings = parse_ini_file($appSettingsFile, TRUE);
//			print_r($appSettings); //DEBUG
            foreach ($appSettings as $section => $entry) {
                /* if(key_exists($section, $this->settings))//OVERRIDING
                  $this->settings[$section]=$value;
                  else $this->settings[$section]=$value;//le altre non trovate le aggiungo */
                foreach ($entry as $key => $value)
                    $this->settings[$section][$key] = $value;
            }
            $this->log->debug("Using both framework settings' file in $fwkSettingsFile and application settings' file in $appSettingsFile");
        } else
            $this->log->debug('App settings\' file ' . $appSettingsFile . ' not found, using the framework one, only.');
//		print_r($this->settings);
    }

    public function getSettings($sectionName) {
        $matchingSections = array();
        //echo $sectionName;
        //print_r($this->settings);
        foreach ($this->settings as $section => $value) {
            //echo 'section '.$section.' fine section'."\n";
            if ($section === $sectionName) {
                //array_push ($matchingSections, $value);
                $matchingSections = $value;
                //	echo 'trovato match';
            }
        }
        //print_r($matchingSections);//DEBUG
        return $matchingSections;
    }

}

?>
