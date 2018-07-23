<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use importer\Import;
use importer\Database;
use importer\Validator;
use importer\Logger;

final class ImportTest extends TestCase
{
    protected $import;

    public function __construct() 
    {
        parent::__construct();
        $this->import = new Import;
    }

    protected function setUp()
    {   
        $this->host = "localhost";
        $this->dbname = "infinity";
        $this->user = "uner";
        $this->pass = "pass";
        $this->charset = "UTF8MB4";
        $this->dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        $this->options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES=> false
        ];
    }

    public function IfDirectoriesExists()
    {
        $this->assertTrue(file_exists('uploaded'));
    }

    public function testStatusProcessingCompleted() {
        $import = new Import();
        $this->assertContains("Processing Completed", $import->processStatus(11));
    }

    public function testValidateDate() {
        $array = array(
            "eventDatetime"=> "2018-01-02 10:27:36",
            "eventAction"=>"sale", 
            "callRef"=>"4536",
            "eventValue"=>"100.00",
            "eventCurrencyCode"=>"GBP"
        );
          
        $object = new Validator();
        $this->assertTrue($object->validateCSVFile($array));
    }

    // public function testLogger() {
    //     $message = 'PHPUnit test';
    //     $logger = new Logger();
    //     $logger->logEntry($message);
    // }
}
