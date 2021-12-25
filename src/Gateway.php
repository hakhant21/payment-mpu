<?php

namespace Hak\PaymentMpu;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Hak\PaymentMpu\Interfaces\GatewayInterface;

class Gateway implements GatewayInterface
{
    private $merchantID;

    private $merchantSecret;

    private $sandboxMode = true;

    private $baseUrl;

    public function __construct($merchantID = null, $merchantSecret = null)
    {
        $this->setMerchantID($merchantID ?? config('services.mpu.merchantID'));
        $this->setMerchantSecret($merchantSecret ?? config('services.mpu.merchantSecret'));
        $this->setSandboxMode(config('services.mpu.sandboxMode'));
        $this->setCurrencyCode(config('services.mpu.currencyCode'));
        $this->setBaseUrl();
    }

    public function createPayment($requestPayload = [])
    {
        $requestPayload = array_merge(
            [
                'merchantID' => $this->getMerchantID(),
                'currencyCode' => $this->getCurrencyCode()
            ],
            $requestPayload
        );

        return $this->encryptData($requestPayload);
    }
    /**
     * @param array $requestPayload
     * @return string
     */
    public function encodeJWT(array $requestPayload): string
    {
        return JWT::encode($requestPayload, $this->getMerchantSecret());
    }

    /**
     * @param array $requestPayload
     * @return array|void
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Throwable
     */

    public function decodeJWT(string $data)
    {
        return JWT::decode($data, $this->getMerchantSecret(), ['HS256']);
    }

    protected function encryptData(array $requestPayload)
    {
        try {
            $jwt = $this->encodeJWT($requestPayload);

            $response = Http::acceptJson()->post($this->getUrl('paymentToken'), [
                'payload' => $jwt
            ]);

            if($response->successful()){
                $data = $response->json()['payload'];
                $decoded = $this->decodeJWT($data);
                $status = data_get($decoded, 'respCode');

                if($status === '0000') {
                    return [
                      'token' => data_get($decoded, 'paymentToken'),
                      'paymentUrl' => data_get($decoded, 'webPaymentUrl')
                    ];
                }
                else {
                   throw new \Exception(data_get($response->json(), 'respDesc') ?? 'Error.');
                }
            }

        } catch(\Throwable $e) {
            throw $e;
        }
    }

    public function getUrl($route)
    {
        $route = config('services.mpu.baseUrl');
        return $this->getBaseUrl() . '/paymentToken';
    }

    public function setBaseUrl()
    {
        if ($this->getSandboxMode() === true) {
            $this->baseUrl = config('services.mpu.baseUrl');
        } else {
            $this->baseUrl = config('services.mpu.prodUrl');
        }
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setSandboxMode($sandboxMode)
    {
        $this->sandboxMode = $sandboxMode;
    }

    public function getSandboxMode()
    {
        return $this->sandboxMode;
    }

    public function setMerchantID($merchantID)
    {
        $this->merchantID = $merchantID;
    }

    public function getMerchantID()
    {
        return $this->merchantID;
    }

    public function setMerchantSecret($merchantSecret)
    {
        $this->merchantSecret = $merchantSecret;
    }

    public function getMerchantSecret()
    {
        return $this->merchantSecret;
    }

    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }
}
