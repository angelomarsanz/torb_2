<?php

/**
 * @package PayPalCheckoutSdk/Core
 */

namespace Modules\Paypal\Services\Core\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HttpClient
{
    private $client;
    private $environment;
    private $injectors = [];

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        $this->client = new Client([
            'base_uri' => $environment->baseUrl(),
            'timeout' => 30,
            'http_errors' => false, // We'll handle errors manually
        ]);
    }

    public function addInjector(Injector $injector)
    {
        $this->injectors[] = $injector;
    }

    public function execute(HttpRequest $request)
    {
        // Apply all injectors
        foreach ($this->injectors as $injector) {
            $injector->inject($request);
        }

        // Prepare Guzzle request options
        $options = [
            'headers' => $request->headers,
        ];

        // Handle request body
        if (isset($request->body)) {
            if (isset($request->headers['Content-Type']) && 
                strpos($request->headers['Content-Type'], 'application/json') !== false) {
                $options['json'] = $request->body;
            } elseif (isset($request->headers['Content-Type']) && 
                      strpos($request->headers['Content-Type'], 'application/x-www-form-urlencoded') !== false) {
                $options['form_params'] = $request->body;
            } else {
                $options['body'] = is_string($request->body) ? $request->body : json_encode($request->body);
            }
        }

        try {
            // Make the request
            $response = $this->client->request($request->verb, $request->path, $options);
            
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $responseHeaders = $response->getHeaders();

            // Handle error status codes
            if ($statusCode >= 400) {
                $errorMessage = $responseBody;
                try {
                    $errorData = json_decode($responseBody, true);
                    if (isset($errorData['message'])) {
                        $errorMessage = $errorData['message'];
                    } elseif (isset($errorData['error_description'])) {
                        $errorMessage = $errorData['error_description'];
                    } elseif (isset($errorData['name']) && isset($errorData['message'])) {
                        $errorMessage = $errorData['name'] . ': ' . $errorData['message'];
                    }
                } catch (\Exception $e) {
                    // Use raw response body if JSON parsing fails
                }

                throw new HttpException($errorMessage, $statusCode, $responseHeaders);
            }

            // Parse response body
            $result = null;
            if (!empty($responseBody)) {
                $result = json_decode($responseBody);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // If JSON parsing fails, return raw body
                    $result = $responseBody;
                }
            }

            // Create response object compatible with original implementation
            $responseObj = new \stdClass();
            $responseObj->statusCode = $statusCode;
            $responseObj->headers = $responseHeaders;
            $responseObj->result = $result;

            return $responseObj;

        } catch (HttpException $e) {
            // Re-throw our custom HttpException
            throw $e;
        } catch (RequestException $e) {
            // Handle Guzzle request exceptions
            $statusCode = 0;
            $response = $e->getResponse();
            
            if ($response) {
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();
                $responseHeaders = $response->getHeaders();
                
                $errorMessage = $responseBody;
                try {
                    $errorData = json_decode($responseBody, true);
                    if (isset($errorData['message'])) {
                        $errorMessage = $errorData['message'];
                    } elseif (isset($errorData['error_description'])) {
                        $errorMessage = $errorData['error_description'];
                    }
                } catch (\Exception $ex) {
                    // Use raw response body if JSON parsing fails
                }
                
                throw new HttpException($errorMessage, $statusCode, $responseHeaders, $e);
            } else {
                throw new HttpException($e->getMessage(), 0, [], $e);
            }
        } catch (\Exception $e) {
            // Handle any other exceptions
            throw new HttpException($e->getMessage(), 0, [], $e);
        }
    }
}
