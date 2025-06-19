<?php

namespace App\Services;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * FILTER QUERY FOR REQUEST
 */
class FilterQuery
{
    protected $queryFilter;

    protected $requestFilter;

    protected $exceptParams;

    protected $searchable = [];

    public function __construct(Builder $query, Request $request, array $searchable)
    {
        try {
            $this->queryFilter = $query;
            $this->requestFilter = $request;
            $this->exceptParams = ['!limit', '!skip', '!sort', '!search'];
            $this->searchable = $searchable;

            $this->getWhere();
            $this->getSearch();

            return $this->queryFilter;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function getSearch()
    {
        $value = $this->requestFilter->get('!search', null);
        if ((! empty($value) || $value != null) && count($this->searchable) > 0) {
            foreach ($this->searchable as $name) {
                $this->operatorLike($name, $value);
            }
        }
    }

    protected function getWhere()
    {
        $params = $this->requestFilter->except($this->exceptParams);
        foreach ($params as $nameFilter => $value) {
            $nameExploded = explode('!', $nameFilter);
            $name = $nameExploded[0];
            $operator = isset($nameExploded[1]) ? $nameExploded[1] : null;

            if (is_null($value)) {
                continue;
            }

            switch ($operator) {
                case 'like':
                    $this->operatorLike($name, $value);
                    break;

                case 'startLike':
                    $this->operatorStartLike($name, $value);
                    break;

                case 'endLike':
                    $this->operatorEndLike($name, $value);
                    break;

                case 'in':
                    $this->operatorIn($name, $value);
                    break;

                case 'nin':
                    $this->operatorNotIn($name, $value);
                    break;

                case 'ne':
                    $this->operatorNotEqual($name, $value);
                    break;

                case 'gt':
                    $this->operatorGreaterThan($name, $value);
                    break;

                case 'lt':
                    $this->operatorLesserThan($name, $value);
                    break;

                case 'gte':
                    $this->operatorGreaterThanEqual($name, $value);
                    break;

                case 'lte':
                    $this->operatorLesserThanEqual($name, $value);
                    break;

                default:
                    $this->operatorEqual($name, $value);
                    break;
            }
        }
    }

    protected function operatorEqual(string $name, string $value)
    {
        $this->queryFilter->where($name, $value);
    }

    protected function operatorLike(string $name, string $value)
    {
        $this->queryFilter->where($name, 'like', '%' . $value . '%');
    }

    protected function operatorStartLike(string $name, string $value)
    {
        $this->queryFilter->where($name, 'like', $value . '%');
    }

    protected function operatorEndLike(string $name, string $value)
    {
        $this->queryFilter->where($name, 'like', '%' . $value);
    }

    protected function operatorIn(string $name, string $value)
    {
        $this->queryFilter->whereIn($name, $this->stringToArray($value));
    }

    protected function operatorNotIn(string $name, string $value)
    {
        $this->queryFilter->whereNotIn($name, $this->stringToArray($value));
    }

    protected function operatorNotEqual(string $name, string $value)
    {
        $this->queryFilter->whereNot($name, $value);
    }

    protected function operatorGreaterThan(string $name, string $value)
    {
        $this->queryFilter->where($name, '>', $value);
    }

    protected function operatorLesserThan(string $name, string $value)
    {
        $this->queryFilter->where($name, '<', $value);
    }

    protected function operatorGreaterThanEqual(string $name, string $value)
    {
        $this->queryFilter->where($name, '>=', $value);
    }

    protected function operatorLesserThanEqual(string $name, string $value)
    {
        $this->queryFilter->where($name, '<=', $value);
    }

    protected function stringToArray(string $value)
    {
        return explode(',', $value);
    }
}
