<?php

namespace Omnipay\Swish\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const API_VERSION = 'v1';

    protected $liveEndpoint = 'https://cpc.getswish.net/swish-cpcapi/api';
    protected $testEndpoint = 'https://mss.cpc.getswish.net/swish-cpcapi/api';

    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
    }

    public function getCert()
    {
        return $this->getParameter('cert');
    }

    public function setCert($value)
    {
        return $this->setParameter('cert', $value);
    }

    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }

    public function setPrivateKey($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    public function getCaCert()
    {
        return $this->getParameter('caCert');
    }

    public function setCaCert($value)
    {
        return $this->setParameter('caCert', $value);
    }

    public function getPayeePaymentReference()
    {
        return $this->getParameter('payeePaymentReference');
    }

    public function setPayeePaymentReference($value)
    {
        return $this->setParameter('payeePaymentReference', $value);
    }

    public function getPayerAlias()
    {
        return $this->getParameter('payerAlias');
    }

    public function setPayerAlias($value)
    {
        return $this->setParameter('payerAlias', $value);
    }

    public function getPayeeAlias()
    {
        return $this->getParameter('payeeAlias');
    }

    public function setPayeeAlias($value)
    {
        return $this->setParameter('payeeAlias', $value);
    }

    protected function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        $url = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;

        return $url.'/'.self::API_VERSION;
    }

    /**
     * @return array|mixed
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('notifyUrl', 'amount', 'currency', 'payeeAlias');

        $data = array(
            'callbackUrl' => $this->getNotifyUrl(),
            'amount'      => $this->getAmount(),
            'currency'    => $this->getCurrency(),
            'payerAlias'  => $this->getPayerAlias(),
            'payeeAlias'  => $this->getPayeeAlias(),
        );

        return $data;
    }

    /**
     * @param mixed $data
     *
     * @return ResponseInterface|PurchaseResponse
     */
    public function sendData($data)
    {
        $headers = [
            'Content-type' => 'application/json',
        ];

        $body = $data ? http_build_query($data, '', '&') : null;
        $httpResponse = $this->httpClient->request($this->getHttpMethod(), $this->getEndpoint(), $headers, $body);

        return $this->createResponse($httpResponse);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return PurchaseResponse
     */
    protected function createResponse(\Psr\Http\Message\ResponseInterface $response)
    {
        $data = $response->getBody();
        $data = json_decode($data, true);
        $statusCode = $response->getStatusCode();

        return $this->response = new PurchaseResponse($this, $response, $data, $statusCode);
    }
}
