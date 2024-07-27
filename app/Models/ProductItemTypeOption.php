<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class ProductItemTypeOption extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'product_item_type_options';
    protected $fillable = ['option_name', 'is_active','product_item_types_id'];

    public function type() {
        return $this->belongsTo(ProductItemType::class, 'product_item_types_id');
    }
    public function inventory()
    {
        return $this->hasOne(ProductOptionInventory::class, 'product_item_type_option_id');
    }
}
