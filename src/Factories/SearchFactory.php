<?php
namespace Imdhemy\Berrylastic\Factories;

use Illuminate\Database\Eloquent\Model;
use Imdhemy\Berrylastic\Client;
use Imdhemy\Berrylastic\Search;

 // TODO: Implement the proper type of Creational Design pattern
class SearchFactory
{
    /**
     * Elasticsearch client
     *
     * @var Imdhemy\Berrylastic\Client
     */
    private $client;

    /**
     * Eloquent Model
     *
     * @var Imdhemy\Berrylastic\Client
     */
    private $model;

    public function __construct(Client $client, Model $model)
    {
        $this->client = $client;
        $this->model = $model;
    }

    public function create() : Search
    {
        return new Search($this->client, $this->model);
    }
}
