<?php

namespace MindArc\FeedGenerator\Model;

use function GuzzleHttp\Psr7\build_query;

class Fixerio
    implements \MindArc\FeedGenerator\Api\FixerioInterface
{

    //both URL and Access_key should be a configs or environment variables
    const SERVICE_URL = 'http://data.fixer.io/api/latest';
    const ACCESS_KEY = '1c9ec2aa92d9bdaedeeca80046b53383';

    protected $curlClient;

    public function __construct(\Magento\Framework\HTTP\Client\Curl $curl)
    {
        $this->curlClient = $curl;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return mixed
     */
    public function connectService()
    {
        try {
            $this->curlClient->get(self::SERVICE_URL . '?' . build_query(["access_key" => self::ACCESS_KEY]));
            if ($this->curlClient->getStatus() == 200) {
                $body = $this->curlClient->getBody();
                $response = json_decode($body);
            } else {
                $response = ['success' => false, 'message' => 'Fixerio connection cannot be established'];
            }
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
        }

        return $response;
    }
}