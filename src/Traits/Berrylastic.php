<?php
namespace Imdhemy\Berrylastic\Traits;

use Imdhemy\Berrylastic\Client;

trait Berrylastic
{
    /**
     * Hold instance of Berrylastic Client
     *
     * @var Imdhemy\Berrylastic\Client
     */
    protected $client;

    /**
     * Boot berrlastic on model
     */
    public static function bootBerrylastic()
    {
        static::saved(function ($model) {
            $model->savedHandler();
        });

        static::deleted(function ($model) {
            $model->deletedHandler();
        });
    }

    /**
     * Get instance of Berrylastic Client
     *
     * @return Imdhemy\Berrylastic\Client
     */
    protected function client()
    {
        if (null === $this->client) {
            $this->client = new Client();
        }
        return $this->client;
    }

    /**
     * Handle model saved event
     *
     * @return
     */
    protected function savedHandler()
    {
        $this->client()->indexOrUpdate($this->getParams());
    }

    /**
     * Handle model deleted event
     *
     * @return
     */
    protected function deletedHandler()
    {
        //
    }

    /**
     * Get document params
     *
     * @return array
     */
    protected function getParams() : array
    {
        $params =  [
            'id'    => $this->getDocumentID(),
            'index' => $this->getDocumentIndex(),
            'type'  => $this->getDocumentType(),
            'body'  => $this->getDocumentBody()
        ];
        return array_filter($params);
    }

    /**
     * Get document id
     *
     * @return string
     */
    protected function getDocumentID() : string
    {
        $primary_key = $this->getKeyName();
        return $this->$primary_key;
    }

    /**
     * Get document index from config or from the model if provided
     *
     * @return string
     */
    protected function getDocumentIndex() : string
    {
        return $this->document_index ?: env('SEARCH_INDEX', 'berrylastic');
    }

    /**
     * Get document type. By default Berrylastic will use the table name as the document type.
     * You can use a custom name by adding the $document_type string property
     *
     * @return string
     */
    protected function getDocumentType() : string
    {
        return $this->document_type ?: $this->getTable();
    }

    /**
     * Get document body
     *
     * @return array
     */
    protected function getDocumentBody() : array
    {
        return $this->only($this->searchable);
    }
}
