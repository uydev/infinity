<?php

namespace importer;

use DateTime;
use importer\Logger;

/**
 * The class is responsible for validation 
 * of the values contained in the files
 * prior to import
 */
class Validator {

    //Assign the value
    public function __construct() {
        $this->logger = new Logger;
    }

    /**
     * Will go through all the fields to ensure they are
     * acceptable. If valid will continue to the next
     * field. Messages will be logged in the logfile.
     */
    public function validateCSVFile($array)
    {   
        if(isset($array['eventAction']) && (strlen($array['eventAction'])>=1 && strlen($array['eventAction'])<=20 && is_string($array['eventAction']))===FALSE){   
            $message ='EventAction Failed';
            $this->logger->logEntry(trim($message));
            return false;
        } 

        if(isset($array['eventValue']) ) {
            if((is_numeric($array['eventValue']))===FALSE){
                $message ='EventValue Failed';
                $this->logger->logEntry(trim($message));
                return false;
            }
           
            if(isset($array['eventCurrencyCode'])) {
                
                $eventCurrencyCode = strtoupper($array['eventCurrencyCode']);
                
                $isoCurrenyCodes = array("AED","AFN","ALL","AMD","ANG","AOA","ARS","AUD","AWG","AZN",
                "BAM","BBD","BDT","BGN","BHD","BIF","BMD","BND","BOB","BOV","BRL","BSD","BTN","BWP",
                "BYR","BZD","CAD","CDF","CHE","CHF","CHW","CLF","CLP","CNY","COP","COU","CRC","CUC",
                "CUP","CVE","CZK","DJF","DKK","DOP","DZD","EGP","ERN","ETB","EUR","FJD","FKP","GBP",
                "GEL","GHS","GIP","GMD","GNF","GTQ","GYD","HKD","HNL","HRK","HTG","HUF","IDR","ILS",
                "INR","IQD","IRR","ISK","JMD","JOD","JPY","KES","KGS","KHR","KMF","KPW","KRW","KWD",
                "KYD","KZT","LAK","LBP","LKR","LRD","LSL","LTL","LVL","LYD","MAD","MDL","MGA","MKD",
                "MMK","MNT","MOP","MRO","MUR","MVR","MWK","MXN","MXV","MYR","MZN","NAD","NGN","NIO",
                "NOK","NPR","NZD","OMR","PAB","PEN","PGK","PHP","PKR","PLN","PYG","QAR","RON","RSD",
                "RUB","RWF","SAR","SBD","SCR","SDG","SEK","SGD","SHP","SLL","SOS","SRD","SSP","STD",
                "SVC","SYP","SZL","THB","TJS","TMT","TND","TOP","TRY","TTD","TWD","TZS","UAH","UGX",
                "USD","USN","USS","UYI","UYU","UZS","VEF","VND","VUV","WST","XAF","XAG","XAU","XBA",
                "XBB","XBC","XBD","XCD","XDR","XFU","XOF","XPD","XPF","XPT","XSU","XTS","XUA","XXX",
                "YER","ZAR","ZMW","ZWL");
                
                //Will check that the eventCurrencyCode in the file is to the ISO 4217 Standard
                if (in_array($eventCurrencyCode, $isoCurrenyCodes)===FALSE)
                {
                    $message ='EventCurrencyCode Failed';
                    $this->logger->logEntry(trim($message));
                    return false;
                } 
            }
        }
       
        if(isset($array['callRef']) && (is_numeric($array['callRef']))===FALSE){
            $message ='callRef Failed';
            $this->logger->logEntry(trim($message));
            return false;
        }
        
        //Validate that eventDate time is not empty and is correct format
        if(isset($array['eventDatetime']) && $this->validateDate($array['eventDatetime'])===FALSE){
            $message ='EventDatetime Failed';
            $this->logger->logEntry(trim($message));
            return false;
        }

        //If all above checks have passed file is valid
        return true;      
    }

    //Validate the data to check that format is as expected
    public function validateDate($date, $format = 'Y-m-d H:i:s')
    { 
        $d = DateTime::createFromFormat($format, $date);
        if(($d && $d->format($format) == $date)===FALSE) {
           
            return false;
        }
        return true;
    }
}
