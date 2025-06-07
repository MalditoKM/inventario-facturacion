<?php
require 'vendor/autoload.php';
use Picqer\Barcode\BarcodeGeneratorPNG;

$codigo = isset($_GET['code']) ? $_GET['code'] : '123456789012';
$generator = new BarcodeGeneratorPNG();
header('Content-type: image/png');
echo $generator->getBarcode($codigo, $generator::TYPE_CODE_128);
exit;
?>