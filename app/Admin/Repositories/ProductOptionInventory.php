<?php

namespace App\Admin\Repositories;

use App\Models\ProductOptionInventory as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class ProductOptionInventory extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
