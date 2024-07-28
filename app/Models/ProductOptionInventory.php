<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class ProductOptionInventory extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'product_option_inventories';
    protected $fillable = ['price', 'total_quantity','product_id','product_item_type_option_id'];

    protected $casts = [
        'product_item_type_option_id' => 'json',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function option() {
        return $this->belongsTo(ProductItemTypeOption::class, 'product_item_type_option_id');
    }

    public function typeOptions()
    {
        return $this->belongsToMany(ProductItemTypeOption::class, 'product_item_type_option_id', 'option_id', 'type_id');
    }

}
