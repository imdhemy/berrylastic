<?php
namespace Imdhemy\Berrylastic\Traits;

use Imdhemy\Berrylastic\Document;

trait DocumentTrait
{
    /**
     * Hold instance of Berrylastic Document
     *
     * @var Imdhemy\Berrylastic\Document
     */
    protected $document;

    /**
     * Get this model instance of Document
     *
     * @return Document
     */
    public function document() : Document
    {
        if (null === $this->document) {
            $this->document = new Document($this->client(), $this);
        }
        return $this->document;
    }

    /**
     * Get document params
     *
     * @return array
     */
    public function getDocumentParams() : array
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
     * Detmermine whether to auto sync documents or not
     *
     * @return bool
     */
    protected function shouldSyncDocument() : bool
    {
        if (property_exists($this, 'sync_document')) {
            return $this->sync_document;
        }
        return true;
    }

    /**
     * Get document index from model associated table name.
     * You can use a custom name by adding the $document_index string property to the model
     *
     * @return string
     */
    public function getDocumentIndex() : string
    {
        return $this->document_index ?: $this->getTable();
    }

    /**
     * Get document type. By default Berrylastic will use the table name as the document type.
     * You can use a custom name by adding the $document_type string property
     *
     * @return string
     */
    public function getDocumentType() : string
    {
        return $this->document_type ?: $this->getTable();
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
     * Get document body
     *
     * @return array
     */
    protected function getDocumentBody() : array
    {
        return $this->only($this->searchable);
    }
}
