<?php

namespace Core;

# Add Library

use Exception;
use stdClass;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class  AzulPayment
{

    // Variables and Const
    private $configData = './cert/Config.json';

    // POST
    private $headers;
    private $requestUri;
    private $certificatePath;
    private $keyPath;
    private $requestObject;


    public function __construct()
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0); //0=NOLIMIT
        // Config Data
        self::configEnvironments();
    }

    private function configEnvironments()
    {
        $jsonFile = file_get_contents($this->configData);
        if ($jsonFile === false) {
            throw new AzulPaymentException("The file {$this->configData} doesn't exist");
        }

        $jsonIterator = json_decode($jsonFile, TRUE);
        if ($jsonIterator === null) {
            throw new AzulPaymentException("No data in the file {$this->configData}");
        }
        // Set Data
        self::formatEnvironments($jsonIterator);
    }

    private function formatEnvironments($jsonData)
    {
        $typeEnvironment = ($jsonData['debug']) ? 'test' : 'production';
        $jsonConfig = $jsonData[$typeEnvironment];
        $jsonData = array_merge($jsonData, $jsonConfig);
        // Set Keys
        $path = $jsonData['publicPath'] ? $jsonData['publicPath'] : './';
        $this->certificatePath = "$path/cert/$typeEnvironment/azul.csr";
        $this->keyPath = "$path/cert/$typeEnvironment/azul.key";
        // Set Headers
        $this->headers = [
            'Content-type' => $jsonData['contentType'],
            'Auth1'        => $jsonData['authOne'],
            'Auth2'        => $jsonData['authTwo']
        ];
        // Set Request URL (JSON)
        $this->requestUri = "https://{$jsonData['url']}.azul.com.do/webservices/{$jsonData['typeService']}/Default.aspx";
        // Set Request Object
        $this->requestObject = self::makeBaseRequestObject($jsonData);
    }

    private function endpoint(string $query = ""): string
    {
        return $this->requestUri . (!checkIsEmpty($query) ? "?{$query}" : '');
    }

    private function makeTempObject($data)
    {
        $requestObject = self::getRequestObjectBase();
        // Data Check
        foreach ($data as $key => $value) {
            if (isset($value) && !checkIsEmpty($value)) {
                $requestObject->$key = $value;
            }
        }
        // Flag Token
        if (isset($data['DataVaultToken']) && !checkIsEmpty($data['DataVaultToken'])) {
            return self::getSpecialObject($requestObject);
        }
        return $requestObject;
    }

    private function makeBaseRequestObject($jsonData)
    {
        // Init Base elements
        $requestObject                       = new stdClass();
        $requestObject->Store                = $jsonData['store'];
        $requestObject->ECommerceUrl         = $jsonData['eCommerceUrl'];
        $requestObject->CustomerServicePhone = $jsonData['customerServicePhone'];
        $requestObject->MerchantId           = $jsonData['merchantId']; // *
        $requestObject->Channel              = 'EC';
        $requestObject->PosInputMode         = 'E-Commerce';
        $requestObject->CurrencyPosCode      = '$';
        $requestObject->Payments             = '1';
        $requestObject->Plan                 = '0';
        $requestObject->AcquirerRefData      = '1';
        // Another Data
        $requestObject->OriginalDate = "";
        $requestObject->OriginalTrxTicketNr = "";
        $requestObject->AuthorizationCode = "";
        $requestObject->ResponseCode = "";
        $requestObject->RRN = null;
        $requestObject->SaveToDataVault = "0";
        // Variable
        // $requestObject->CardNumber= "";
        // $requestObject->Expiration= "";
        // $requestObject->CVC= "";
        // $requestObject->TrxType= "";
        // $requestObject->Amount= "";
        // $requestObject->Itbis= "";
        // $requestObject->OrderNumber= "";
        // $requestObject->CustomOrderId= "";
        // $requestObject->AzulOrderId = null;

        return $requestObject;
    }

    private function getSpecialObject($tempData)
    {
        $requestObject                       = new \stdClass();
        $requestObject->Store                = $tempData->Store;
        $requestObject->MerchantId                = $tempData->MerchantId;
        $requestObject->ECommerceUrl         = $tempData->eCommerceUrl;
        $requestObject->CustomerServicePhone = $tempData->customerServicePhone;
        $requestObject->Channel = $tempData->Channel;
        $requestObject->PosInputMode = $tempData->PosInputMode;
        $requestObject->TrxType = $tempData->TrxType;
        $requestObject->Amount = $tempData->Amount;
        $requestObject->Itbis = $tempData->Itbis;
        $requestObject->DataVaultToken = $tempData->DataVaultToken;
        // Data Base
        $requestObject->CardNumber = "";
        $requestObject->Expiration = "";
        $requestObject->CVC = "";
        $requestObject->CurrencyPosCode = "$";
        $requestObject->Payments = "1";
        $requestObject->Plan = "0";
        $requestObject->OriginalDate = "";
        $requestObject->OriginalTrxTicketNr = "";
        $requestObject->AuthorizationCode = "";
        $requestObject->ResponseCode = "";
        $requestObject->AzulOrderId = null;
        $requestObject->AcquirerRefData = "1";
        $requestObject->RRN = null;
        $requestObject->OrderNumber = "";
        $requestObject->CustomOrderId = "";
        $requestObject->SaveToDataVault = "0";
        return $requestObject;
    }

    private function getRequestObjectBase()
    {
        return $this->requestObject;
    }

    /**
     * Make any action to Azul Payment
     * @param mixed $requestObject 
     * @param string $query 
     * @return json 
     */
    public function makeAction($requestObject, string $query = "")
    {
        $result = null;
        // Make Data
        $url = self::endpoint($query);
        $requestObject = self::makeTempObject($requestObject);
        $dataSend = [
            'headers' => $this->headers,
            'json'    => $requestObject,
            'cert'    => $this->certificatePath,
            'ssl_key' => $this->keyPath,
        ];
        // Send Data
        try {
            $client = new Client();
            $result = $client->post($url, $dataSend);
            $result = json_decode($result->getBody());
        } catch (Exception $e) {
            $result   = $e->getMessage();
        }
        return $result;
    }
}

/**
 * Check any value if is empty or null
 * @param mixed $value 
 * @param mixed $checkNull 
 * @return bool 
 */
function checkIsEmpty($value)
{
    // Null
    $checkNull = is_null($value);
    if ($checkNull) {
        return $checkNull;
    }
    // Array
    if (is_array($value)) {
        return (!isset($value) || empty($value) || count($value) <= 0) || $value === null;
    }
    // String
    if (is_string($value)) {
        return (!isset($value) || trim($value) === '');
    }

    return (!isset($value)) || empty($value);
}
