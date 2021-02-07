<?php
namespace Imdhemy\Berrylastic\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Search;

trait SearchTrait
{
    /**
     * Supported search queries
     *
     * @var array
     */
    protected $supported_queries = [
        'match',
        'multiMatch',
        'matchPhrase'
    ];

    /**
     * Hold a collection of search queries
     *
     * @var Illuminate\Support\Collection
     */
    protected $search_queries;

    /**
     * Hold raw search results
     *
     * @var array
     */
    public $raw_search_results;

    /**
     * Hold elastic search hits
     *
     * @var Illuminate\Support\Collection
     */
    public $hits;

    /**
     * Total hits count
     *
     * @var int
     */
    public $total_hits;

    /**
     * Restrict query to search results
     *
     * @param  Builder $builder
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $builder, int $skip = 0, int $take = 15)
    {
        $take = $take + 1;
        $params = $this->getSearchParams($skip, $take);
        $results = $this->client()->search($params);
        $this->setSearchResults($results);
        return $this->scopedSearchQuery($builder);
    }

    /**
     * Get elasticsearch hits
     *
     * @return Illuminate\Support\Collection
     */
    public function hits() : Collection
    {
        if (null === $this->hits) {
            $this->hits = new Collection();
        }
        return $this->hits;
    }

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
            return $this->appendDslQuery($this->getDslQuery($method, $parameters));
        }
        return parent::__call($method, $parameters);
    }

    /**
     * Get instance of ElasricsearchDSL Full-text Query
     *
     * @param  string $method
     * @param  array $parameters
     * @return  ONGR\ElasticsearchDSL\BuilderInterface
     */
    protected function getDslQuery($method, $parameters) : BuilderInterface
    {
        $class_name = ucfirst(Str::camel("{$method}Query"));
        $class = "\ONGR\ElasticsearchDSL\Query\FullText\\{$class_name}";
        return new $class(...$parameters);
    }

    /**
     * Append a DSL query to search_queries list
     *
     * @param  BuilderInterface $query
     * @return
     */
    protected function appendDslQuery(BuilderInterface $query)
    {
        $search_queries = $this->searchQueries();
        $search_queries->push($query);
        $this->search_queries = $search_queries;
        return $this;
    }

    /**
     * Get the collection of search queries
     *
     * @return Illuminate\Support\Collection
     */
    protected function searchQueries() : Collection
    {
        if (null === $this->search_queries) {
            $this->search_queries = new Collection();
        }
        return $this->search_queries;
    }

    /**
     * Get search params array
     *
     * @return array
     */
    protected function getSearchParams(int $from = 0, int $size = 15) : array
    {
        $search = new Search();
        foreach ($this->searchQueries() as $query) {
            $search->addQuery($query);
        }
        return [
            'from'  => $from,
            'size'  => $size,
            'index' => $this->getDocumentIndex(),
            'type'  => $this->getDocumentType(),
            'body' => $search->toArray()
        ];
    }

    /**
     * Set raw search results data
     *
     * @param array $results
     */
    protected function setSearchResults(array $results) : void
    {
        $this->raw_search_results = $results;
        $this->hits = new Collection($results['hits']['hits']);
        $this->total_hits = $results['hits']['total'];
    }

    /**
     * Restrict query builder to search results
     *
     * @param  Builder $builder
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function scopedSearchQuery(Builder $builder) : Builder
    {
        $identifiers = $this->hits()->pluck('_id');
        $primary_key = $this->getTable() . "." .$this->getKeyName();
        $builder = $builder->whereIn($primary_key, $identifiers);
        if ($identifiers->count()) {
            $ordered_identifiers = $identifiers->implode(',');
            $builder = $builder->orderByRaw(\DB::raw("FIELD($primary_key, $ordered_identifiers)"));
        }
        $builder->total_hits = $this->total_hits;
        return $builder;
    }
}
