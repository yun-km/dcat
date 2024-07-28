<?php
namespace App\Admin\Renderables;

use Dcat\Admin\Widgets\Table;
// use App\Models\ProductItemType;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Admin\Repositories\ProductItemType;
// use App\Admin\Actions\Grid\ProductTypeForm;
use App\Admin\Forms\ProductTypeForm;

class ProductTypes extends LazyRenderable
{
    public function render() {
        return $this->grid();
    }
    public function grid(): Grid
    {
        return Grid::make(new ProductItemType(), function (Grid $grid) {
            $productId = $this->key;
            $grid->model()->where('product_id', $productId);
            $grid->model()->with(['options']);

            $grid->column('id')->sortable();
            $grid->column('type_name',__('admin.ProductType.type_name'))->editable();

            $grid->column('options',__('admin.ProductType.options'))->display(function ($options) {
                return collect($options)->pluck('option_name')->map(function ($option_name) {
                    return $option_name;
                })->implode(', ');
            })->label();

            // $grid->column(__('admin.ProductType.create_product_type'))->display(__('admin.ProductType.create_product_type'))->modal(function () {
            //     return ProductTypeForm::make()->payload(['product_id' => $this->id, 'action' => 'create']);
            // });
            $grid->column(__('admin.ProductType.edit_product_type'))->display(__('admin.ProductType.edit_product_type'))->modal(function () {
                return ProductTypeForm::make()->payload(['type_id' => $this->id]);
            });

            $grid->disableToolbar();
            $grid->disablePagination();
            $grid->disableActions()
            ->disableRefreshButton()
            ->disableCreateButton()
            ->disableRowSelector()
            ->withBorder();

            // $grid->tools(function (Grid\Tools $tools) {
            //     $tools->append(new ProductTypeForm());
            // });
        });
    }
}
