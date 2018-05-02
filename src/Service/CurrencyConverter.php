<?php
namespace App\Service;

use \GuzzleHttp\Client;

class CurrencyConverter
{
    /** @var Client */
    private $api;

    /** @var string */
    private $apiKey;

    /**
     * CurrencyConverter constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->api = new Client([
            'base_uri' => 'http://data.fixer.io/api',
            'http_errors' => false,
        ]);
        $this->apiKey = $options['apiKey'];
    }

    /**
     * @return Client
     */
    protected function getApi()
    {
        return $this->api;
    }

    /**
     * @param float $amount
     * @param string $baseCur
     * @param string $targetCur
     * @return float
     * @throws \Exception
     */
    public function convert(float $amount, string $baseCur, string $targetCur): float
    {
        $apiResponse = $this->getApi()->get('/latest?' . http_build_query([
            'access_key' => $this->apiKey,
            'symbols' => $baseCur . ',' . $targetCur,
        ]));
        if (200 !== $apiResponse->getStatusCode()) {
            throw new \Exception('Error requesting fixer.io API, try again later.');
        }

        $responseData = json_decode($apiResponse->getBody(), true);
        if (empty($responseData['success'])) {
            throw new \Exception('Failure requesting fixer.io API: ' . $responseData['error']['info']);
        }

        $rates = $responseData['rates'];
        if (!isset($rates[$baseCur])) {
            throw new \InvalidArgumentException("Invalid currency $baseCur");
        }
        if (!isset($rates[$targetCur])) {
            throw new \InvalidArgumentException("Invalid currency $targetCur");
        }
        bcscale(15);
        return floatval(bcdiv(bcmul($amount, $rates[$targetCur]), $rates[$baseCur]));
    }
}
