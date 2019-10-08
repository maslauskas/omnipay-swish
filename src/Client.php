<?php

namespace Omnipay\Swish;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use HelmutSchneider\Swish\ValidationException;
use Http\Client\Exception;
use Http\Message\RequestFactory;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Common\Http\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
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

    /**
     * Client constructor.
     *
     * @param string $rootCert
     * @param string $clientCert
     * @param string $privateKey
     * @param RequestFactory $requestFactory
     */
    public function __construct(string $rootCert, string $clientCert, string $privateKey, RequestFactory $requestFactory)
    {
        $config = [
            'verify' => $rootCert,
            'cert' => $clientCert,
            'ssl_key' => $privateKey,
            'handler' => HandlerStack::create(new CurlHandler()),
        ];

        $this->httpClient = \Http\Adapter\Guzzle6\Client::createWithConfig($config);
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

        return $this->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws Exception
     * @throws ValidationException
     * @throws \Exception
     */
    private function sendRequest(RequestInterface $request)
    {
        try {
            return $this->httpClient->sendRequest($request);
        } catch (Exception\NetworkException $networkException) {
            throw new NetworkException($networkException->getMessage(), $request, $networkException);
        } catch (Exception\HttpException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                case 422:
                    throw new ValidationException($e->getResponse());
            }
            throw $e;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
