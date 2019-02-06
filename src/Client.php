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

    /**
     * Get indexed document, if not indexed null will be returned
     *
     * @param  array  $params
     * @return array| null
     */
    public function get(array $params = []) : ?array
    {
        try {
            return $this->elasticsearch()->get($params);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get indexed document or fail
     *
     * @throws Elasticsearch\Common\Exceptions\Missing404Exception
     *
     * @return array
     */
    public function getOrFail(array $params = []) : array
    {
        return $this->elasticsearch()->get($params);
    }

    /**
     * Update a document indexed into elasticsearch
     *
     * @param array $params
     * @return array
     */
    public function update(array $params = []) : array
    {
        $params['body'] = ['doc' => $params['body']];
        return $this->elasticsearch()->update($params);
    }

    /**
     * Index or update a document into elasticsearch
     *
     * @param  array  $params
     * @return array
     */
    public function indexOrUpdate(array $params = []) : array
    {
        $query_params = collect($params)->only(['id', 'index', 'type'])->toArray();
        return $this->isIndexed($query_params) ? $this->update($params) : $this->index($params);
    }

    /**
     * Check if a document is indexed
     *
     * @param  array   $params
     * @return boolean
     */
    public function isIndexed(array $params = []) : bool
    {
        return (bool) $this->get($params);
    }
}
