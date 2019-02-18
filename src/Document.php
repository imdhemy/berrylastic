<?php
namespace Imdhemy\Berrylastic;

use Illuminate\Database\Eloquent\Model;
use Imdhemy\Berrylastic\Client;

class Document
{
    /**
     * Hold instance of Berrylastic Client
     *
     * @var Imdhemy\Berrylastic\Client
     */
    private $client;

    /**
     * Hold instance of the Eloqunet model
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    private $model;

    /**
     * Create new instance of Document
     *
     * @param Client $client
     */
    public function __construct(Client $client, Model $model)
    {
        $this->client = $client;
        $this->model = $model;
    }

    /**
     * Save model document into elasticsearch
     *
     * @return array
     */
    public function save() : array
    {
        $params = $this->model->getDocumentParams();
        return $this->client->index($params);
    }

    /**
     * Delete model document from elasticsearch
     *
     * @return array
     */
    public function delete() : array
    {
        return [];
    }

    /**
     * Update model document on elasticsearch
     *
     * @return array
     */
    public function update() : array
    {
        $params = $this->model->getDocumentParams();
        return $this->client->update($params);
    }

    /**
     * Save a new document or update the previously indexed document
     *
     * @return array
     */
    public function saveOrUpdate() : array
    {
        $params = $this->model->getDocumentParams();
        return $this->client->indexOrUpdate($params);
    }

    /**
     * Check if this model document is indexed
     *
     * @return boolean
     */
    public function isIndexed() : bool
    {
        $params = collect($this->model->getDocumentParams())->only(['id', 'type', 'index'])->toArray();
        return $this->client->isIndexed($params);
    }
}
