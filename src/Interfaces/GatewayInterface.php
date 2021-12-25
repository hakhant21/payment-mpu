<?php

namespace Hak\PaymentMpu\Interfaces;

interface GatewayInterface
{
    public function createPayment($requestPayload = []);

    public function decodeJWT(string $content);

    public function setBaseUrl();

    public function getBaseUrl();

    public function setSandboxMode($sandboxMode);

    public function getSandboxMode();

    public function setMerchantID($merchantID);

    public function getMerchantID();

    public function setMerchantSecret($merchantSecret);

    public function getMerchantSecret();

    public function setCurrencyCode($currencyCode);

    public function getCurrencyCode();

    public function getUrl($route);
}