<?php
namespace Imdhemy\Berrylastic;

use Elasticsearch\ClientBuilder;

class Client
{
    /**
     * Hold instance of Elasticsearch client
     *
     * @var Elasticsearch\Client
     */
    private $elasticsearch;

    /**
     * Get instance of Elasticsearch client
     *
     * @return Elasticsearch\Client
     */
    private function elasticsearch()
    {
        if (null === $this->elasticsearch) {
            $this->elasticsearch = ClientBuilder::create()->build();
        }
        return $this->elasticsearch;
    }

    /**
     * Index a document into elasticsearch
     *
     * @param  array  $params
     * @return
     */
    public function index(array $params = []) : array
    {
        return $this->elasticsearch()->index($params);
    }
}
