<?php

namespace Omnipay\Swish;

use GuzzleHttp\Exception\GuzzleException;
use Http\Message\RequestFactory;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Common\Http\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Client implements ClientInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;
    /**
     * @var RequestFactory
     */
    private $requestFactory;

    public function __construct(string $rootCert, string $clientCert, string $privateKey, RequestFactory $requestFactory)
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'cert' => $rootCert,
            'ssl_key' => $privateKey,
            'verify' => $clientCert,
        ]);

        $this->requestFactory = $requestFactory;
    }

    /**
     * Creates a new PSR-7 request.
     *
     * @param string $method
     * @param string|UriInterface $uri
     * @param array $headers
     * @param resource|string|StreamInterface|null $body
     * @param string $protocolVersion
     *
     * @return ResponseInterface
     * @throws NetworkException if there is an error with the network or the remote server cannot be reached.
     *
     * @throws RequestException when the HTTP client is passed a request that is invalid and cannot be sent.
     * @throws GuzzleException
     */
    public function request($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        $request = $this->requestFactory->createRequest($method, $uri, $headers, $body, $protocolVersion);

        return $this->httpClient->send($request);
    }
}
