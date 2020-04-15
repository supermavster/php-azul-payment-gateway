<?php

// Add Composer
require_once __DIR__.'/vendor/autoload.php';

// Call Library
use Test\TestProcess;

// Variables
$tempData = [
    [
        'CustomOrderId' => 'SALE-1',
        'OrderNumber' => 'ORDER-1',
        // Value
        'Amount' => 650730,
        'Itbis' => 99264,
        // CC
        'CardNumber' => 'XXXXXXXXXXXX',
        'Expiration' => 'YYYYMM', // YYYYMM
        'CVC' => 'XXX'
    ],
    [
        'CustomOrderId' => 'SALE-2',
        'OrderNumber' => 'ORDER-2',
        // Value
        'Amount' => 100000,
        'Itbis' => 50000,
        // CC
        'CardNumber' => 'XXXXXXXXXXXX',
        'Expiration' => 'YYYYMM', // YYYYMM
        'CVC' => 'XXX'
    ],
];
$customOrdersHold = [
    [
        'CustomOrderId' => 'HOLD-1',
        'OrderNumber' => 'ORDER-3'
    ],
    [
        'CustomOrderId' => 'HOLD-2',
        'OrderNumber' => 'ORDER-4'
    ]
];

$testing = new TestProcess();

/** TEST 1 **/
$response[] = "Realizar 2 transacciones aprobadas con tarjetas y montos distintos (Sale)";
# First Payment
$response[] = $firstPayment = $testing->salePayment($tempData[0]);
# Second Payment
$response[] = $secondPayment = $testing->salePayment($tempData[1]);


/** TEST 2 **/
$response[] = "Consultar ambas transacciones aprobadas (VerifyPayment)";
# First Payment
$response[] = $checkFirstPayment =   $testing->verifyPayment($tempData[0]['CustomOrderId']);
# Second Payment
$response[] = $checkSecondPayment =   $testing->verifyPayment($tempData[1]['CustomOrderId']);


/** TEST 3 **/
$response[] = "Anular ambas transacciones aprobadas (Void)";
# First Payment
$response[] = $voidFirstPayment =   $testing->voidPayment($firstPayment->AzulOrderId);
# Second Payment
$response[] = $voidSecondPayment =   $testing->voidPayment($secondPayment->AzulOrderId);


/** TEST 4 **/
$response[] = "Realizar 2 retenciones con tarjetas y montos distintos (Hold)";
# First Payment
$customerOrder = array_replace($tempData[0], $customOrdersHold[0]);
$response[] = $holdOne =   $testing->holdPayment($customerOrder);
# Second Payment
$customerOrder = array_replace($tempData[1], $customOrdersHold[1]);
$response[] = $holdTwo =   $testing->holdPayment($customerOrder);


/** TEST 5 **/
$response[] = "Realizar el posteo de las 2 transacciones retenidas (Post)";
# First Payment
$response[] = $postOne =   $testing->postPayment([
    'Amount' => 650731,
    'Itbis' => 99265,
    'AzulOrderId'        => $holdOne->AzulOrderId
]);
# Second Payment
$response[] = $postTwo =   $testing->postPayment([
    'Amount' => 110000,
    'Itbis' => 51000,
    'AzulOrderId'        => $holdTwo->AzulOrderId
]);


/** TEST 6 **/
$response[] = "Crear 2 tokens con tarjetas (Data Vault Create)";
# First Token
$creditCard = [
    'CardNumber' => $tempData[0]['CardNumber'],
    'Expiration' => $tempData[0]['Expiration'],
    'CVC' => $tempData[0]['CVC'],
];
$tempToken =   $testing->makeToken($creditCard);
$tokenOne = $tempToken->DataVaultToken;
$response[] = $tempToken;

# Second Token
$creditCard = [
    'CardNumber' => $tempData[1]['CardNumber'],
    'Expiration' => $tempData[1]['Expiration'],
    'CVC' => $tempData[1]['CVC'],
];
$tempToken =   $testing->makeToken($creditCard);
$tokenTwo = $tempToken->DataVaultToken;
$response[] = $tempToken;

/** TEST 7 **/
$response[] = "Realizar 2 transacciones con ambos tokens (Data Vault Sale)";
# First Payment
$response[] = $firstPayment = $testing->saleDataVaultPayment([
    'Amount' => 110000,
    'Itbis' => 51000,
    'DataVaultToken'        => $tokenOne
]);
# Second Payment
$response[] = $secondPayment = $testing->saleDataVaultPayment([
    'Amount' => 110000,
    'Itbis' => 51000,
    'DataVaultToken'        => $tokenTwo
]);


/** TEST 8 **/
$response[] = "Eliminar ambos tokens creados (Data Vault Delete)";
# First Token
$response[] = $testing->removeToken($tokenOne);
# second Token
$response[] =  $testing->removeToken($tokenTwo);


// Print response
dump($response);