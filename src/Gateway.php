<?php

namespace Omnipay\Swish;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RequestInterface;

/**
 * Swish Class.
 *
 *
 * The Swish API uses a REST-like structure. Certificates are used
 * as the API Authorization framework. Request and response payloads are formatted as JSON.
 *
 * ### Test Mode
 *
 * In order to use the test mode you need to download the test certificate and convert it to PEM files.
 * Download the certificate from https://www.getswish.se/handel/ under the section "Testa din uppkoppling"
 * In the zip you will find the .p12 that needs to be converted with openssl (or similar);
 *
 * $ openssl pkcs12 -in Swish\ Merchant\ Test\ Certificate\ 1231181189.p12 -nocerts -out certificate.key
 * $ openssl pkcs12 -in Swish\ Merchant\ Test\ Certificate\ 1231181189.p12 -nokeys -out certificate.pem
 *
 * More info about converting PKCS#12 files with openssl at https://www.openssl.org/docs/manmaster/apps/pkcs12.html
 *
 * ### Example
 *
 * #### Initialize Gateway
 *
 * <code>
 *   // Create a gateway for the Swish gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('Swish');
 *
 *   // Initialize the gateway
 *   $gateway->initialize(array(
 *               'cert' => 'certificate.pem',
 *               'privateKey' => 'certificate.key', // Or array('certificate.key', 'password')
 *               'caCert' => 'root_cert_from_swish.pem',
 *               'testMode' => true
 *   ));
 * </code>
 *
 * #### Payment
 *
 *   $transaction = $gateway->purchase(array(
 *       'amount'                   => '10.00',
 *       'currency'                 => 'SEK',
 *       'notifyUrl'                => 'https://example.com/api/swishcb/paymentrequests',
 *       'payerAlias'               => '46701234567',
 *       'payeeAlias'               => '1234760039',
 *       'message'                  => 'Kingston USB Flash Drive 8 GB'
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Purchase request was successfully sent!\n";
 *       $sale_id = $response->getTransactionReference();
 *       echo "Transaction reference = " . $sale_id . "\n";
 *   }
 * </code>
 *
 *
 * @link https://www.getswish.se/handel/
 * @link https://www.openssl.org/docs/manmaster/apps/pkcs12.html
 * @see Omnipay\Swish\Message\AbstractRequest
 *
 * @method RequestInterface authorize(array $options = [])
 * @method RequestInterface completeAuthorize(array $options = [])
 * @method RequestInterface capture(array $options = [])
 * @method RequestInterface completePurchase(array $options = [])
 * @method RequestInterface refund(array $options = [])
 * @method RequestInterface void(array $options = [])
 * @method RequestInterface createCard(array $options = [])
 * @method RequestInterface updateCard(array $options = [])
 * @method RequestInterface deleteCard(array $options = [])
 */
class Gateway extends AbstractGateway
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Swish';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'testMode' => false,
        ];
    }

    /**
     * @param array $parameters
     *
     * @return AbstractRequest|RequestInterface
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Swish\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return AbstractRequest
     */
    public function fetchTransaction(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Swish\Message\FetchTransactionRequest', $parameters);
    }
}
