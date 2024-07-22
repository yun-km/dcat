<?php
namespace App\Admin\Renderables;

use App\Models\Product as ProductModel;
use Dcat\Admin\Support\LazyRenderable;
use Dcat\Admin\Widgets\Table;

class Products extends LazyRenderable
{
    public function render()
    {
        $id = $this->key;

        $data = ProductModel::where('user_id', $id)
            ->get(['title', 'product_category_id', 'summary', 'description', 'is_active', 'created_at'])
            ->map(function ($item) {
                $item->is_active = $item->is_active ? '是' : '否';
                return $item;
            })
            ->toArray();

        $titles = [
            __('admin.Products.Title'),
            __('admin.Products.Product Category ID'),
            __('admin.Products.Summary'),
            __('admin.Products.Description'),
            __('admin.Products.Is Active'),
            __('admin.Products.Created At'),
        ];

        return Table::make($titles, $data);
    }
}
