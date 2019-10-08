<?php

namespace Omnipay\Swish\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class FetchTransactionRequest extends AbstractRequest
{
    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('transactionReference');

        return [];
    }

    /**
     * @return string
     */
    protected function getHttpMethod()
    {
        return 'GET';
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return parent::getEndpoint().'/paymentrequests/'.$this->getTransactionReference();
    }
}
