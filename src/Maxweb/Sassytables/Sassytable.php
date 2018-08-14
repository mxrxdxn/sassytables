<?php

namespace Maxweb\Sassytables;

use Illuminate\Support\Facades\Schema;
use Maxweb\Sassytables\SassyUrlConstructor;

class Sassytable
{
    // This is the template used for tables.
    public $template  = 'sassytables::table';

    // The URL.
    public $url;
    
    // Pagination vars.
    public $paginated = false;
    public $perPage   = 10;

    // Data relating to data output.
    public $viewData  = [];
    public $columns   = [];
    public $appends   = [];
    public $filters   = [];

    // Sort params.
    public $defaultSort     = 'id';
    public $defaultOrder    = 'asc';
    public $sort;
    public $order;
    private $sortOverrides  = [];

    // Permissions for table.
    public $canEdit   = true;
    public $canDelete = true;

    // String to display if no data is found.
    public $noDataString = 'No data found.';

    function __construct($object)
    {
        $this->object = $object;
    }

    public function setColumns(array $data = [])
    {
        $this->columns = $data;

        return $this;
    }

    public function setRoute($route)
    {
        $this->url = new SassyUrlConstructor($this, $route);

        return $this;
    }

    public function setFilters($filters)
    {
        $this->filters = $filters;

        if (! empty($this->filters)) {
            foreach ($this->filters as $filterName => $filterValue) {
                $this->appends['filters[' . $filterName . ']'] = $filterValue;
            }
        }

        return $this;
    }

    public function setCanEdit(bool $canEdit)
    {
        $this->canEdit = $canEdit;

        return $this;
    }

    public function setCanDelete(bool $canDelete)
    {
        $this->canDelete = $canDelete;

        return $this;
    }

    public function setDefaultSort(string $sort = 'id', string $order = 'asc')
    {
        $this->defaultSort  = $sort;
        $this->defaultOrder = $order;

        return $this;
    }

    public function setNoDataString(string $string)
    {
        $this->noDataString = $string;

        return $this;
    }

    public function setSortOverrides(array $sortOverrides)
    {
        $this->sortOverrides = $sortOverrides;

        return $this;
    }

    public function render()
    {
        $this->viewData['table'] = $this;

        if (request()->has('sort')) {
            $this->sort = request()->input('sort');
        } else {
            $this->sort = $this->defaultSort;
        }

        $this->appends['sort'] = $this->sort;

        if (request()->has('order')) {
            $this->order = request()->input('order');
        } else {
            $this->order = $this->defaultOrder;
        }

        $this->appends['order'] = $this->order;

        $this->finishProcessing();

        return view($this->template, $this->viewData);
    }

    private function finishProcessing()
    {
        $object = $this->object;

        if (array_key_exists($this->sort, $this->sortOverrides)) {
            $sql = preg_replace('/\{order\}/', $this->order, $this->sortOverrides[$this->sort]);
            $object = $object->orderByRaw(\DB::RAW($sql));
        } else {
            // Check if the column exists - if not we'll have to use a more advanced sorting technique.
            // This is useful for sorting by a column that doesn't exist on the target table.
            if (! Schema::hasColumn($object->getModel()->getTable(), $this->sort)) {

                $orderBy = null;

                foreach ($object->getQuery()->columns as $column) {
                    if (! is_object($column) && ($column instanceof \Illuminate\Database\Query\Expression) === false) continue;

                    if (preg_match('/(.*) as ' . $this->sort . '/i', $column->getValue(), $matches)) {
                        if (count($matches) === 2) {
                            $orderBy = $matches[1];
                            break;
                        }
                    }
                }
                
                if ($orderBy) {
                    $object = $object->orderByRaw(\DB::RAW($orderBy . " " . $this->order));
                } else {
                    // We're probably ordering by a created attribute on the table, which isn't really possible.
                    $this->sort  = $this->defaultSort;
                    $this->order = $this->defaultOrder;

                    $object = $object->orderBy($this->sort, $this->order);
                }

            } else {
                $object = $object->orderBy($this->sort, $this->order);
            }
        }

        if ($this->paginated === true) {
            $object = $object->paginate($this->perPage);
        } else {
            $object = $object->get();
        }

        $this->object = $object;
    }

    public function paginated($isPaginated = true, $perPage = 10)
    {
        $this->paginated = $isPaginated;
        $this->perPage   = $perPage;

        return $this;
    }

    public function appends(array $appends = [])
    {
        $this->appends = $appends;

        return $this;
    }
}