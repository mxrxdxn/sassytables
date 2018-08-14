<?php

namespace Maxweb\Sassytables;

class SassyUrlConstructor
{
    public $url;
    private $table;

    function __construct($table, $url)
    {
        $this->table = $table;
        $this->url   = $url;
    }

    public function sort($column)
    {
        if ($this->table->sort === $column) {
            if ($this->table->order === 'asc') {
                $order = 'desc';
            } else {
                $order = 'asc';
            }
        } else {
            $order = $this->table->order;
        }

        // Handle filters
        $filters = [];

        if (! empty($this->table->filters)) {
            foreach ($this->table->filters as $filterName => $filterValue) {
                $filters['filters[' . $filterName . ']'] = $filterValue;
            }
        }

        return route($this->url, array_merge($filters, ['sort' => $column, 'order' => $order]));
    }
}