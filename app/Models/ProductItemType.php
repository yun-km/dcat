<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class ProductItemType extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'product_item_types';
    protected $fillable = ['type_name', 'parent_id','product_id'];

    public function parent()
    {
        return $this->belongsTo(ProductItemType::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductItemType::class, 'parent_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function options() {
        return $this->hasMany(ProductItemTypeOption::class, 'product_item_types_id');
    }

}
