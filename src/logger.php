<?php
/**
 * This class is responsible for
 * logging all the messages into the logfile
 */
namespace importer;

class Logger {
   
    public function __construct()  {
        openlog("importSciptLog", LOG_PID | LOG_PERROR, LOG_LOCAL0);
        $this->access = date("Y/m/d H:i:s");
    }

    public function logEntry($message) {
        syslog(LOG_WARNING, "importer: $this->access ".$message);
    }
}