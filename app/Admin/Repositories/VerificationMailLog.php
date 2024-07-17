<?php

namespace App\Admin\Repositories;

use App\Models\VerificationMailLog as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class VerificationMailLog extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
