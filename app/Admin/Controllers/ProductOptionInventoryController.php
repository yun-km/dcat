<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\ProductOptionInventory;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ProductOptionInventoryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ProductOptionInventory(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('product_item_type_option_id');
            $grid->column('price');
            $grid->column('total_quantity');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new ProductOptionInventory(), function (Show $show) {
            $show->field('id');
            $show->field('product_item_type_option_id');
            $show->field('price');
            $show->field('total_quantity');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new ProductOptionInventory(), function (Form $form) {
            $form->display('id');
            $form->text('product_item_type_option_id');
            $form->text('price');
            $form->text('total_quantity');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
