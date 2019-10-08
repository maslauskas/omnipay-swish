<?php

namespace Omnipay\Swish\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest as BaseRequest;
use Omnipay\Common\Message\ResponseInterface;

abstract class AbstractRequest extends BaseRequest
{
    const API_VERSION = 'v1';

    const ENDPOINT_LIVE = 'https://cpc.getswish.net/swish-cpcapi/api';
    const ENDPOINT_TEST = 'https://mss.cpc.getswish.net/swish-cpcapi/api';

    /**
     * @return string|null
     */
    public function getPayeePaymentReference(): ?string
    {
        return $this->getParameter('payeePaymentReference');
    }

    /**
     * @param string $value
     *
     * @return AbstractRequest
     */
    public function setPayeePaymentReference(string $value)
    {
        return $this->setParameter('payeePaymentReference', $value);
    }

    /**
     * @return string|null
     */
    public function getPayerAlias(): ?string
    {
        return $this->getParameter('payerAlias');
    }

    /**
     * @param string $value
     *
     * @return AbstractRequest
     */
    public function setPayerAlias(string $value)
    {
        return $this->setParameter('payerAlias', $value);
    }

    /**
     * @return string|null
     */
    public function getPayeeAlias(): ?string
    {
        return $this->getParameter('payeeAlias');
    }

    /**
     * @param string $value
     *
     * @return AbstractRequest
     */
    public function setPayeeAlias(string $value)
    {
        return $this->setParameter('payeeAlias', $value);
    }

    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('notifyUrl', 'amount', 'currency', 'payeeAlias');

        $data = [
            'callbackUrl' => $this->getNotifyUrl(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'payerAlias' => $this->getPayerAlias(),
            'payeeAlias' => $this->getPayeeAlias(),
        ];

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
            'Accept' => 'application/json',
        ];

        $body = $data ? json_encode($data) : null;
        $httpResponse = $this->httpClient->request($this->getHttpMethod(), $this->getEndpoint(), $headers, $body);

        return $this->createResponse($httpResponse);
    }

    /**
     * @return string
     */
    protected function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        $url = $this->getTestMode() ? self::ENDPOINT_TEST : self::ENDPOINT_LIVE;

        return $url . '/' . self::API_VERSION;
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
