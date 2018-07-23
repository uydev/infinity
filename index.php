<?php
require __DIR__ . '/vendor/autoload.php';

use importer\Import;
use importer\Logger;

$import = new Import();
$import->import();
