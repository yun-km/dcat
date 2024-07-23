<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Dcat\Admin\Traits\AdminBuilder;

class ProductCategory extends Model
{
	use HasDateTimeFormatter, ModelTree;
    protected $table = 'product_categories';
    protected $titleColumn = 'name';


    protected $parentColumn = 'parent_id';


    protected $fillable = ['name', 'parent_id', 'slug', 'is_active'];

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function getOrderColumn()
    {
        return null;
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
}
