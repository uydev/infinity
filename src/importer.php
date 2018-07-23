<?php

namespace importer;

use importer\Logger;
use importer\Database;
use PDOException;

/**
 * This class is responsible for importing CSV data
 * in the DB
 */
class Import {

    /** 
     * Initialize and assign the objects and values
     */
    public function __construct() {
        $this->conn = Database::getDatabase();
        $this->importFilepath = "uploaded/";
        $this->processedFilepath = "processed";
        $this->logger = new Logger();
        $this->validator = new Validator();  

        if(!is_dir($this->processedFilepath)) {
            mkdir($this->processedFilepath);
        } 
    }

    /**
     * Imports data from CSV files located in the uploaded directory and 
     * inserts data into the imports DB table. CSV files which have been
     * imported are moved into the processed folder.
     */

    public function import() {
        $this->createTableIfNotExists('imports');
        
        $processed = 0;

        //Prepared statement
        $stmt = $this->conn->prepare("INSERT INTO imports (eventDatetime, eventAction, callRef, eventValue, eventCurrencyCode) VALUES (:eventDatetime, :eventAction, :callRef, :eventValue, :eventCurrencyCode)");

        foreach (glob($this->importFilepath.'*.csv') as $file) {
            
            //Do headers match. If true will continue otherwise script will exit
            $this->isHeaderValid($file);

            $array = $fields = array(); $i = 0; $executedRow = 1;
            $handle = fopen($file, "r");
            if ($handle) {
                
                //Ensure data can be insert to DB table regardless of the order of the header in the file
                $rows   = array_map('str_getcsv', file($file));
                $header = array_shift($rows);
                $csv    = array();
                
                //Loop through all the rows in the CSV file
                foreach($rows as $row) {
                    if (array(null) !== $row) {
                        $csv = array_combine($header, $row);
                        $valid = $this->validator->validateCSVFile($csv, $this->logger);    
                    }
                    
                    //If validation fails skip to next row in the CSV file
                    if(!$valid) {
                      $message = "Error occurred in file:'$file' on row number ".$executedRow;
                      $message .= "=>Invalid Row Data Detected:SKIPPING ROW";
                      $message .="\n\n";
                      $this->logger->logEntry(trim($message));
                      continue;
                    } 
                    
                    //Insert row into the DB table
                    $stmt->execute(
                        ['eventDatetime' => trim($csv['eventDatetime']), 
                            'eventAction' => trim($csv['eventAction']),
                            'callRef' => trim($csv['callRef']),
                            'eventValue' => trim($csv['eventValue']),
                            'eventCurrencyCode' => trim($csv['eventCurrencyCode'])]);
                    
                    //Keep track of the number of rows executed
                    $executedRow++;
                }
                if (feof($handle)) {
                    echo "Error: unexpected fgets() fail\n";
                }
                fclose($handle);
            }
            //Move file to Processed folder when completed
            $fileInfo = pathinfo($file);
            rename($file, $this->processedFilepath. '/'.time().'_'.$fileInfo['basename']);
            
            //Keep track of the number of files processed
            $processed++;
            
            //Record in the log file
            $message = "\nFile Processed Successfully:".$file;
            $this->logger->logEntry(trim($message));
        }

        //Find if there are any remaining CSV Files or if processing is completed
        $message = $this->processStatus($processed);
        $this->logger->logEntry(trim($message));
    }
    
    /** 
     * Checks to see if there are more files to process or if the processing is completed
     */
    public function processStatus($processed) {
        if ((count(glob("$this->importFilepath/*")) === 0 ) && (($processed==0))){
            return $message = "\nThere are no files to process\n";
        } else {
            return $message = "\nProcessing Completed\n";
        }
    }

    /**
     * Check that the header from the CSV file is as expected to prevent errors
     */
    public function isHeaderValid($file) 
    {
        $requiredHeaders = array('eventDatetime', 'eventAction', 'callRef','eventValue','eventCurrencyCode'); //headers we expect
        $f = fopen($file, 'r');
        $firstLine = fgets($f);
        fclose($f);   
        $foundHeaders = str_getcsv(trim($firstLine), ',', '"');
        //Check to see if the headers from the csv file matches what is expected. If not terminate script
        if ((count(array_diff(array_merge($foundHeaders, $requiredHeaders), array_intersect($foundHeaders, $requiredHeaders))) === 0)===FALSE) {
            $message ="\nHeaders do not match\n";
            $this->logger->logEntry(trim($message));
            die;
        } 
        return true;
    }

    //Will check to see if DB table exists and create one if needed
    public function createTableIfNotExists($table) {

        try {
            $result = $this->conn->query("SELECT 1 FROM $table LIMIT 1");
            return true;
        } catch (PDOException $e){
            if($e->getCode() === '42S02') {
                $sql = "CREATE TABLE `imports` ( 
                    `eventDatetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
                    `eventAction` VARCHAR(20) NOT NULL , 
                    `callRef` BIGINT UNSIGNED NOT NULL, 
                    `eventValue` DECIMAL NULL , 
                    `eventCurrencyCode` CHAR(3) NULL ) 
                    ENGINE = InnoDB;";
                    $this->conn->exec($sql);
                    $message = "Table Imports created successfully";
                    $this->logger->logEntry(trim($message));
                return true;
            } 
        }
    }
}
