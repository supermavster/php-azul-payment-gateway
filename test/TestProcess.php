<?php

namespace Test;

use Core\AzulPayment;

class TestProcess
{

    # Payment Data
    private $paymentMethod;

    public function __construct()
    {
        $this->paymentMethod = new AzulPayment();
    }

    /**
     * Make Sale Payment
     * @param mixed $data 
     * @return mixed|json 
     */
    public function salePayment($data)
    {
        // Complement Sale
        $data = array_merge(['TrxType' => "Sale"], $data);
        # Make Action
        return self::makeAction($data);
    }

    /**
     * Verify Payment
     * @param mixed $customOrderId 
     * @return mixed|json 
     */
    public function verifyPayment($customOrderId)
    {
        # Verify Payment
        return self::makeAction(['CustomOrderId' => $customOrderId], 'VerifyPayment');
    }

    /**
     * Void Payment
     * @param mixed $AzulOrderId 
     * @return mixed|json 
     */
    public function voidPayment($AzulOrderId)
    {
        # Void Payment
        return self::makeAction(['AzulOrderId' => $AzulOrderId], 'ProcessVoid');
    }

    /**
     * Hold Payment
     * @param mixed $data 
     * @return mixed|json 
     */
    public function holdPayment($data)
    {
        // Complement Hold
        $data = array_merge(['TrxType' => "Hold"], $data);
        # Make Action
        return self::makeAction($data);
    }

    /**
     * Post Payment
     * @param mixed $customOrder 
     * @return mixed|json 
     */
    public function postPayment($customOrder)
    {
        # Post Payment
        return self::makeAction($customOrder, 'ProcessPost');
    }

    /**
     * Make Token
     * @param mixed $creditCard 
     * @return mixed|json 
     */
    public function makeToken($creditCard)
    {
        // Complement ProcessDataVault (TOKEN)
        $data = array_merge(['TrxType' => "CREATE"], $creditCard);
        # Make Action
        return self::makeAction($data, 'ProcessDataVault');
    }

    /**
     * Sale Data Vault (Sale Token)
     * @param mixed $data 
     * @return mixed|json 
     */
    public function saleDataVaultPayment($data)
    {
        // Complement Sale
        $data = array_merge([
            'TrxType' => "Sale"
        ], $data);

        # Make Action
        return self::makeAction($data);
    }

    /**
     * Remove Token
     * @param mixed $tokenDataVault 
     * @return mixed|json 
     */
    public function removeToken($tokenDataVault)
    {
        // Complement ProcessDataVault (TOKEN)
        $data = ['TrxType' => "DELETE", 'DataVaultToken' => $tokenDataVault];
        # Make Action
        return self::makeAction($data, 'ProcessDataVault');
    }

    private function makeAction($data, string $query = '')
    {
        $response =  $this->paymentMethod->makeAction($data, $query);
        return $response;
    }
}
