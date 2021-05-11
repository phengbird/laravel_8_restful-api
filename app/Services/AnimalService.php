<?php

namespace App\Services;

use App\Models\Animal;

class AnimalService
{
    protected function filterAnimals($query , $filters)
    {
        //filter way
        if (isset($filters)) {
            $filtersArray = explode(',', $filters);
            foreach ($filtersArray as $key => $filter) {
                list($key, $value) = explode(':', $filter);
                $query->where($key, 'like', "%$value%");
            }
        }

        return $query;
    }

    protected function sort($query , $sorts)
    {
        //sort way
        if (isset($sorts)) {
            $sortsArray = explode(',', $sorts);
            foreach ($sortsArray as $key => $sort) {
                list($key, $value) = explode(':', $sort);
                if ($value == 'asc' || $value == 'desc') {
                    $query->orderBy($key, $value);
                }
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query;
    }

    public function getListData($request)
    {
        //giving default value
        $limit = $request->limit ?? 10;

        //create search dbs , 
        $query = Animal::query()->with('type');

        $query = $this->filterAnimals($query , $request->filters);
        $query = $this->sort($query , $request->sorts);

        //show out with desc
        $animal = $query->orderBy('id', 'desc')
            ->paginate($limit) //using page to show how many by $limit
            ->appends($request->query());

        return $animal;
    }
}