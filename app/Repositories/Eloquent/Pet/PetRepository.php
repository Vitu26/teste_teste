<?php

namespace App\Repositories\Eloquent\Pet;

use App\Repositories\Contracts\IPetRepository;
use App\Models\Pets\Pet;
use App\Repositories\Repository;

class PetRepository extends Repository
{
    public function __construct()
    {
        $this->model = new Pet();
    }

    protected function structureUpInsert($request): array
    {
        $fields = array_keys($request);
        return $this->filter_request($request, $fields);
    }
}