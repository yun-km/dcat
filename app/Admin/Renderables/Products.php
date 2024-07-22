<?php
namespace App\Admin\Renderables;

use Dcat\Admin\Widgets\Table;
// use App\Models\Product;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Admin\Repositories\Product;

class Products extends LazyRenderable
{
    public function grid(): Grid
    {
        return Grid::make(new Product(), function (Grid $grid) {
            $userId = $this->key;
            $grid->model()->where('user_id', $userId);

            $grid->column('id')->sortable();
            $grid->column('title', __('admin.Products.Title'));
            $grid->column('product_category_id', __('admin.Products.Product Category ID'));
            $grid->column('summary', __('admin.Products.Summary'));
            $grid->column('description', __('admin.Products.Description'));
            $grid->column('is_active', __('admin.Products.Is Active'))->bool();

            $grid->column('created_at', __('admin.Products.Created At'));
            $grid->column('updated_at', __('admin.Products.Updated At'));

            $grid->quickSearch(['id', 'title', 'summary']);
            $grid->disableActions();
            $grid->disableRowSelector();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->padding();
                $filter->equal('title', __('admin.Products.Title'));
            });

        });
    }
}
