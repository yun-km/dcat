<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	use HasDateTimeFormatter;

    protected $fillable = [
        "title",
        "summary",
        "description",
        "cover",
        "tags",
        "product_category_id",
        "user_id",
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
