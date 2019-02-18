<?php
namespace Imdhemy\Berrylastic\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ONGR\ElasticsearchDSL\Search;

trait SearchTrait
{
    protected $supported_queries = [
        'match',
        'multiMatch',
        'matchPhrase'
    ];

    protected $search_queries;

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, $this->supported_queries)) {
            return $this->appendSearchQuery($method, $parameters);
        }
        return parent::__call($method, $parameters);
    }

    public function search()
    {
        $params = $this->getSearchParams();
        $results = $this->client()->search($params);
        $hits = new Collection($results['hits']['hits']);

        $identifiers = $hits->pluck('_id');
        $primary_key = $this->getKeyName();
        $query = $this->whereIn($primary_key, $identifiers);

        if ($identifiers->count()) {
            $ordered_identifiers = implode(',', $identifiers->toArray());
            $query = $query->orderByRaw(\DB::raw("FIELD($primary_key, $ordered_identifiers)"));
        }
        return $query;
    }

    protected function getSearchParams()
    {
        $search = new Search();
        foreach ($this->search_queries as $query) {
            $search->addQuery($query);
        }
        return [
            'index' => $this->getDocumentIndex(),
            'type'  => $this->getDocumentType(),
            'body' => $search->toArray()
        ];
    }

    protected function getDslQuery($method, $parameters)
    {
        $class_name = ucfirst(Str::camel("{$method}Query"));
        $class = "\ONGR\ElasticsearchDSL\Query\FullText\\{$class_name}";
        $query = new $class(...$parameters);
        return $query;
    }

    protected function appendSearchQuery($method, $parameters)
    {
        $query = $this->getDslQuery($method, $parameters);
        $search_queries = $this->searchQueries();
        $search_queries->push($query);
        $this->search_queries = $search_queries;
        return $this;
    }

    protected function searchQueries()
    {
        if (null === $this->search_queries) {
            $this->search_queries = new Collection();
        }
        return $this->search_queries;
    }
}
