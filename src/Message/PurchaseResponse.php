<?php

namespace Omnipay\Swish\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PurchaseResponse extends AbstractResponse
{
    /**
     * @var string|null
     */
    protected $statusCode;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * PurchaseResponse constructor.
     *
     * @param RequestInterface $request
     * @param $response
     * @param $data
     * @param $statusCode
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, $data, $statusCode)
    {
        parent::__construct($request, $data);
        $this->statusCode = $statusCode;
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getCode() == 200 || $this->getCode() == 201;
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        $location = $this->response->getHeader('location');

        if (empty($location)) {
            return null;
        }

        $urlParts = explode('/', $location[0]);

        return end($urlParts);
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        $token = $this->response->getHeader('PaymentRequestToken');

        if (empty($token)) {
            return null;
        }

        return $token[0];
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        if (isset($this->data[0]['errorMessage'])) {
            return $this->data[0]['errorMessage'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->statusCode;
    }
}
