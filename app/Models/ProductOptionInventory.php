<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class ProductOptionInventory extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'product_option_inventories';
    protected $fillable = ['price', 'total_quantity'];

    public function option() {
        return $this->belongsTo(ProductItemTypeOption::class, 'product_item_type_option_id');
    }

}
