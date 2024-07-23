<?php

namespace App\Admin\Repositories;

use App\Models\ProductItemType as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class ProductItemType extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
