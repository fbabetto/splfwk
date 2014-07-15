<?php

/**
 * Description of LogManager
 *
 * @author fbabetto
 */
// http://php.net/manual/en/function.error-log.php

require_once dirname(__FILE__) . '/ILogManager.php';
require_once dirname(__FILE__) . '/../exception/LogSettingErrorException.php';

if (file_exists(LOGGER_PATH)) {
    require_once LOGGER_PATH;
    Logger::configure(dirname(__FILE__) . '/../../logger-settings.xml');
}

class LogManager implements ILogManager {

    private $appBasePath;

    const FATAL = 0;
    const ERROR = 1;
    const WARN = 2;
    const INFO = 3;
    const DEBUG = 4;
    const TRACE = 5;

    public function __construct($appBasePath) {
        $this->appBasePath = $appBasePath;
//		$this->logSettings = $logSettings;
    }

    public function fatal($message) {
        $this->writeMsg($message, FATAL);
    }

    public function error($message) {
        $this->writeMsg($message, ERROR);
    }

    public function warn($message) {
        $this->writeMsg($message, WARN);
    }

    public function info($message) {
        $this->writeMsg($message, INFO);
    }

    public function debug($message) {
        $this->writeMsg($message, DEBUG);
    }

    public function trace($message) {
        $this->writeMsg($message, TRACE);
    }

    // to avoid code duplication
    private function writeMsg($message, $level) {
        if (USE_LOG4PHP === TRUE && file_exists(LOGGER_PATH)) {
            // use log4php
            $log = Logger::getLogger(__CLASS__);

            if (file_exists($this->appBasePath . '/logger-settings.xml'))
                Logger::configure($this->appBasePath . '/logger-settings.xml');
            $this->log = Logger::getLogger(__CLASS__);

            if ($level === FATAL) {
                $log->fatal($message);
            } elseif ($level === ERROR) {
                $log->error($message);
            } elseif ($level === WARN) {
                $log->warn($message);
            } elseif ($level === INFO) {
                $log->info($message);
            } elseif ($level === DEBUG) {
                $log->debug($message);
            } elseif ($level === TRACE) {
                $log->trace($message);
            } else {
                throw new WrongLogLevelException("Wrong log level passed to LogManager: $level");
            }
        } elseif (USE_LOG4PHP === FALSE) {
            // use log functions
            $actualLogLevel;
            //FIXME FORSE AGGIUNGERE TRIM?
            if (LOG_LEVEL === 'trace') {
                $actualLogLevel = 5;
            } elseif (LOG_LEVEL === 'debug') {
                $actualLogLevel = 4;
            } elseif (LOG_LEVEL === 'info') {
                $actualLogLevel = 3;
            } elseif (LOG_LEVEL === 'warn') {
                $actualLogLevel = 2;
            } elseif (LOG_LEVEL === 'error') {
                $actualLogLevel = 1;
            } elseif (LOG_LEVEL === 'fatal') {
                $actualLogLevel = 0;
            } else {
                throw new LogSettingErrorException("ERROR! Fix your constant.php configuration, log4php is enabled but is not installed in " + LOGGER_PATH);
                //die();
            }

            if ($level <= $actualLogLevel) {
                if (LOG_FILE == '') {
                    error_log($message + "\n");
                } else {
                    error_log($message + "\n", 3, LOG_FILE);
                }//FIXME LOGFILE COULD NOT BE FOUND OR PERMISSION PROBLEMS!
            }
        } elseif (USE_LOG4PHP === TRUE && !file_exists(LOGGER_PATH)) { // exit and ask for fixing constant.php
            throw new LogSettingErrorException("ERROR! Fix your constant.php configuration, log4php is enabled but is not installed in " + LOGGER_PATH);
            //die();
        } else {
            throw new LogSettingErrorException('use_log4php: wrong value: ' + USE_LOG4PHP);
            //die();
        }
    }

}

?>
