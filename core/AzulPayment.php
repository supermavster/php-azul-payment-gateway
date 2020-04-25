<?php

namespace Core;

# Add Library
use Exception;
use stdClass;

class  AzulPayment
{
    // Variables and Const
    private $configData = './cert/Config.json'; // In server: /home/public/cert/Config.json

    // POST
    private $headers;
    private $requestUri;
    private $certificatePath;
    private $keyPath;
    private $requestObject;


    public function __construct()
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0); // 0 = NOLIMIT
        // Config Data
        try { // Set Config
            self::configEnvironments();
        } catch (Exception $ex) {
            dump($ex->getMessage());
            exit();
        }
    }

    private function configEnvironments()
    {
        $jsonFile = file_get_contents($this->configData);
        if (checkIsEmpty($jsonFile)) {
            throw new AzulPaymentException("The file {$this->configData} doesn't exist");
        }

        $jsonIterator = json_decode($jsonFile, TRUE);
        if (checkIsEmpty($jsonIterator)) {
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
        $requestObject->ECommerceUrl         = $tempData->ECommerceUrl;
        $requestObject->CustomerServicePhone = $tempData->CustomerServicePhone;
        $requestObject->Channel = $tempData->Channel;
        $requestObject->PosInputMode = $tempData->PosInputMode;
        $requestObject->TrxType = $tempData->TrxType;
        $requestObject->Amount = $tempData->Amount;
        $requestObject->DataVaultToken = $tempData->DataVaultToken;
        // Check Data
        if (isset($tempData->Itbis)) {
            $requestObject->Itbis = $tempData->Itbis;
        }
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
        // Send Data
        try {
            $headers = [
                'Content-Type: {$this->headers['Content-type']}',
                "Auth1: {$this->headers['Auth1']}",
                "Auth2: {$this->headers['Auth2']}",
            ];
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_FAILONERROR, true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => false,
                CURLOPT_CONNECTTIMEOUT => false,
                CURLOPT_POSTFIELDS => json_encode($requestObject),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_URL => trim($url),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

            $this->certificatePath && curl_setopt($ch, CURLOPT_SSLCERT, $this->certificatePath);
            $this->keyPath && curl_setopt($ch, CURLOPT_SSLKEY, $this->keyPath);
            $result = curl_exec($ch);
            $response = json_decode($result);
            // Show Error
            $errno = curl_errno($ch);
            if ($errno) {
                $message = "cURL ({$errno}): " . curl_strerror($errno) . ", " . curl_error($ch);
                curl_close($ch);
                throw new AzulPaymentException($message, $response);
            } else {
                curl_close($ch);
                // Return Result
                $result = $response;
            }
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
