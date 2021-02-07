<?php
namespace Imdhemy\Berrylastic;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Imdhemy\Berrylastic\Client;
use Imdhemy\Berrylastic\Search;

class Search
{
    /**
     * Hold instance of Berrylastic Client
     *
     * @var Imdhemy\Berrylastic\Client
     */
    private $client;

    /**
     * Hold instance of the Eloquent model
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    private $model;

    /**
     * Search query
     *
     * @var Illuminate\Support\Collection
     */
    private $query;

    /**
     * Create new instance of Document
     *
     * @param Client $client
     */
    public function __construct(Client $client, Model $model)
    {
        $this->client = $client;
        $this->model = $model;
        $this->query = new Collection();
    }
}
