<?php

namespace Imdhemy\Berrylastic\Traits;

use Imdhemy\Berrylastic\Client;

trait Berrylastic
{
    use DocumentTrait, SearchTrait;

    /**
     * Hold instance of Berrylastic Client
     *
     * @var Imdhemy\Berrylastic\Client
     */
    protected $client;

    /**
     * Boot Berrylastic on model
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
        if ($this->shouldSyncDocument()) {
            $this->client()->indexOrUpdate($this->getDocumentParams());
        }
    }

    /**
     * Handle model deleted event
     *
     * @return
     */
    protected function deletedHandler()
    {
        if ($this->shouldDeleteDocument()) {
            $this->client()->delete($this->getDocumentParams());
        }
    }
}
