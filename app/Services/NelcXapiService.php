<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Nelc\LaravelNelcXapiIntegration\XapiIntegration;

/**
 * Custom wrapper for NELC xAPI Integration
 * Fixes headers that trigger Cloudflare WAF blocks:
 * - Removes Access-Control-Allow-Origin from request headers (it's a response-only header)
 * - Adds X-Experience-API-Version header (required by xAPI spec)
 * - Adds proper User-Agent to avoid bot detection
 * - Adds Accept header for proper content negotiation
 */
class NelcXapiService extends XapiIntegration
{
    public function __construct()
    {
        $this->url = config('lrs-nelc-xapi.endpoint');
        $this->key = config('lrs-nelc-xapi.key');
        $this->secret = config('lrs-nelc-xapi.secret');

        $this->client = new Client([
            'auth' => [$this->key, $this->secret],
            'timeout' => 30,
            'connect_timeout' => 15,
            'http_errors' => true,
        ]);

        // ✅ Fixed headers for Cloudflare WAF compatibility:
        // 1. REMOVED Access-Control-Allow-Origin (response header sent as request = WAF red flag)
        // 2. ADDED X-Experience-API-Version (required by xAPI/LRS specification)
        // 3. ADDED proper User-Agent (avoids bot detection)
        // 4. ADDED Accept header (proper content negotiation)
        $this->headers = [
            'Content-Type'              => 'application/json',
            'X-Experience-API-Version'  => '1.0.3',
            'Accept'                    => 'application/json',
            'User-Agent'                => 'NAHJ-LMS/1.0 (compatible; xAPI-Client; +https://www.nahj.com.sa)',
        ];
    }

    /**
     * Override sendXAPIRequest with better error handling
     */
    public function sendXAPIRequest($data = [])
    {
        // Validate endpoint is configured
        if (empty($this->url)) {
            return [
                'status'  => 0,
                'message' => 'Configuration Error',
                'body'    => 'LRS_ENDPOINT is not configured. Please set it in your environment variables.',
            ];
        }

        if (empty($this->key) || empty($this->secret)) {
            return [
                'status'  => 0,
                'message' => 'Configuration Error',
                'body'    => 'LRS_USERNAME or LRS_PASSWORD is not configured. Please set them in your environment variables.',
            ];
        }

        $options = [
            'json'    => $data,
            'headers' => $this->headers,
        ];

        try {
            $response = $this->client->post($this->url, $options);

            return [
                'status'  => $response->getStatusCode(),
                'message' => $response->getReasonPhrase(),
                'body'    => $response->getBody()->getContents(),
            ];
        } catch (ConnectException $e) {
            return [
                'status'  => 0,
                'message' => 'Connection Error',
                'body'    => 'Could not connect to NELC LRS: ' . $e->getMessage(),
            ];
        } catch (RequestException $e) {
            $response = $e->getResponse();

            if ($response) {
                $body = $response->getBody()->getContents();

                // Detect Cloudflare block specifically
                if ($response->getStatusCode() === 403 && str_contains($body, 'cloudflare')) {
                    return [
                        'status'  => 403,
                        'message' => 'Cloudflare WAF Block',
                        'body'    => 'Request blocked by Cloudflare firewall on NELC servers. Contact NELC to whitelist your server IP.',
                    ];
                }

                return [
                    'status'  => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase(),
                    'body'    => $body,
                ];
            }

            return [
                'status'  => 0,
                'message' => 'Request Error',
                'body'    => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 0,
                'message' => 'Unexpected Error',
                'body'    => $e->getMessage(),
            ];
        }
    }
}
