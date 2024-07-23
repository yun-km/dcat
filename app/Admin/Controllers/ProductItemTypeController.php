<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\ProductItemType;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ProductItemTypeController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ProductItemType(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('product_id');
            $grid->column('product_item_type_option_id');
            $grid->column('type_name');
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
        return Show::make($id, new ProductItemType(), function (Show $show) {
            $show->field('id');
            $show->field('product_id');
            $show->field('product_item_type_option_id');
            $show->field('type_name');
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
        return Form::make(new ProductItemType(), function (Form $form) {
            $form->display('id');
            $form->text('product_id');
            $form->text('product_item_type_option_id');
            $form->text('type_name');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
