<?php

namespace Omnipay\Swish\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class PurchaseRequest extends AbstractRequest
{
    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $data = parent::getData();

        $data['message'] = $this->getDescription();

        return $data;
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint().'/paymentrequests';
    }
}
