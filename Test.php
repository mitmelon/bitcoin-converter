<?php
use Watermelon\BitcoinConverter\Converter;
require_once __DIR__.'/vendor/autoload.php';

$convert = new Converter;              
echo $convert->toCurrency('NGN', 1.0);
echo $convert->toCurrency('USD', 1);