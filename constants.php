<?php

//const FWK_BASEPATH = '/opt/lampp/htdocs/nbprj/simpleframework';
        const FWK_BASEPATH = '/srv/http/simpleframework';
        const FWK_SETTINGS_FILE = '/srv/http/simpleframework/settings.ini.php';
        const LOGGER_PATH = '/srv/http/simpleframework/log4php/Logger.php';
// TODO ATTENZIONE AGLI SPAZI PRIMA DEI PERCORSI! MAGARI METTERE TRIM SU CODICE
//[log]
//; use_log4php true or false
        const USE_LOG4PHP = FALSE;
//; log_file if not null a custom log file is used, else syslog is used http://php.net/manual/en/function.error-log.php
//; available log_level (identical to log4php) https://logging.apache.org/log4php/docs/introduction.html
//; 0 - 5
        const LOG_LEVEL = 'trace';
        const LOG_FILE = '/srv/http/simpleframework/error-nolog4php.log';
?>
