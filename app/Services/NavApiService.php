<?php


namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class NavApiService {
    /**
     * @var Client
     */
    private $client;

    /**
     * @var
     */
    private $config = [];

    /**
     * NavApiService constructor.
     */
    public function __construct() {
        $this->client = new Client(['auth' => [env('HTTP_USERNAME', 'usuari'), env('HTTP_PASSWORD', '')]]);
        $this->config = [
            'auth'    => [env('HTTP_USERNAME', 'usuari'), env('HTTP_PASSWORD', ''), 'NTLM'],
            'uri'     => env('HTTP_URIBASE', ''),
            'port'    => env('HTTP_URIPORT', ''),
            'server'  => env('HTTP_URINAVSERVER', ''),
            'service' => env('HTTP_URINAVSERVICE', ''),
            'company' => env('HTTP_URINAVCOMPANY', ''),
        ];
    }

    /**
     * @param string $navNo
     * @return array
     */
    public function getCustomer($navNo = '') {
        $url = $this->config['uri'] . ':' . $this->config['port'] . '/' . $this->config['server'] . '/' . $this->config['service'] . '/CustomerCardPage';
        $url .= "('$navNo')";

        $options = [
            'auth'  => $this->config['auth'],
            'query' => [
                '$format' => 'json',
                'company' => $this->config['company'],
            ],
        ];

        return $this->makeRequest('GET', $url, $options);
    }

    /**
     * @param string $email
     * @return array
     */
    public function getCustomerByEmail($email = '') {
        $url = $this->config['uri'] . ':' . $this->config['port'] . '/' . $this->config['server'] . '/' . $this->config['service'] . '/CustomerCardPage';

        $options = [
            'auth'  => $this->config['auth'],
            'query' => [
                '$format' => 'json',
                '$filter' => "E_Mail eq '$email'",
                'company' => $this->config['company'],
            ],
        ];

        return $this->makeRequest('GET', $url, $options);
    }

    /**
     * @param string $navNo
     * @param string $eTag
     * @param array $data
     * @return array
     */
    public function updateCustomer($navNo = '', $eTag = '', $data = []) {
        $url = $this->config['uri'] . ':' . $this->config['port'] . '/' . $this->config['server'] . '/' . $this->config['service'] . '/CustomerCardPage';
        $url .= "('$navNo')";

        $options = [
            'auth'    => $this->config['auth'],
            'query'   => [
                '$format' => 'json',
                'company' => $this->config['company'],
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'If-Match'     => 'W/"\'' . $eTag . '\'"',
            ],
            'json'    => $data,
        ];

        return $this->makeRequest('PATCH', $url, $options);
    }

    /**
     * @param array $data
     * @return array
     */
    public function createCustomer($data = []) {
        $url = $this->config['uri'] . ':' . $this->config['port'] . '/' . $this->config['server'] . '/' . $this->config['service'] . '/CustomerCardPage';
        $url .= '?$format=json&company=' . $this->config['company'];

        $options = [
            'auth'    => $this->config['auth'],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json'    => $data,
        ];

        return $this->makeRequest('POST', $url, $options);
    }

    /**
     * @param array $data
     * @return array
     */
    public function createOrder($data = []) {
        $url = $this->config['uri'] . ':' . $this->config['port'] . '/' . $this->config['server'] . '/' . $this->config['service'] . '/SalesOrder';

        $options = [
            'auth'    => $this->config['auth'],
            'query'   => [
                '$format' => 'json',
                'company' => $this->config['company'],
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json'    => $data,
        ];

        return $this->makeRequest('POST', $url, $options);
    }

    /**
     * @param string $navNo
     * @return array
     */
    public function getSalesOrder($navNo = '') {
        $url = $this->config['uri'] . ':' . $this->config['port'] . '/' . $this->config['server'] . '/' . $this->config['service'] . '/SalesOrder';
        $url .= "(Document_Type='Order', No='$navNo')";

        $options = [
            'auth'  => $this->config['auth'],
            'query' => [
                '$format' => 'json',
                'company' => $this->config['company'],
            ],
        ];

        return $this->makeRequest('GET', $url, $options);
    }

    /**
     * @param string $navNo
     * @param string $eTag
     * @param array $data
     * @return array
     */
    public function updateSalesOrder($navNo = '', $eTag = '', $data = []) {
        $url = $this->config['uri'] . ':' . $this->config['port'] . '/' . $this->config['server'] . '/' . $this->config['service'] . '/SalesOrder';
        $url .= "(Document_Type='Order', No='$navNo')";

        $options = [
            'auth'    => $this->config['auth'],
            'headers' => [
                'If-Match' => 'W/"\'' . $eTag . '\'"',
            ],
            'query'   => [
                '$format' => 'json',
                'company' => $this->config['company'],
            ],
            'json'    => $data,
        ];

        return $this->makeRequest('PATCH', $url, $options);
    }

    /**
     * @param string $navNo
     * @return array
     */
    public function getSalesOrderLines($navNo = '') {
        $url = $this->config['uri'] . ':' . $this->config['port'] . '/' . $this->config['server'] . '/' . $this->config['service'] . '/SalesOrder';
        $url .= "(Document_Type='Order', No='$navNo')/SalesOrderSalesLines";

        $options = [
            'auth'  => $this->config['auth'],
            'query' => [
                '$format' => 'json',
                'company' => $this->config['company'],
            ],
        ];

        return $this->makeRequest('GET', $url, $options);
    }

    /**
     * @param string $navNo
     * @param array $data
     * @return array
     */
    public function createOrderLine($navNo = '', $data = []) {
        $url = $this->config['uri'] . ':' . $this->config['port'] . '/' . $this->config['server'] . '/' . $this->config['service'] . '/SalesOrder';
        $url .= "(Document_Type='Order', No='$navNo')/SalesOrderSalesLines";

        $options = [
            'auth'  => $this->config['auth'],
            'query' => [
                '$format' => 'json',
                'company' => $this->config['company'],
            ],
            'json'  => $data,
        ];

        return $this->makeRequest('POST', $url, $options);
    }

    /**
     * @param $method
     * @param $url
     * @param $options
     * @return array
     */
    private function makeRequest($method, $url, $options) {
        $response = ['success' => true, 'data' => null, 'error' => null];

        try {
            $res = $this->client->request($method, $url, $options);

            $body = json_decode($res->getBody(), true);
            $response['data'] = $body;
        } catch (BadResponseException $e) {
            $res = $e->getResponse()->getBody()->getContents();

            $response['success'] = false;
            $response['error'] = $res;
        }

        return $response;
    }
}
