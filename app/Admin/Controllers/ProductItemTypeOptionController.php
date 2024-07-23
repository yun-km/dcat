<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\ProductItemTypeOption;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ProductItemTypeOptionController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ProductItemTypeOption(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('product_item_types_id');
            $grid->column('option_name');
            $grid->column('is_active');
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
        return Show::make($id, new ProductItemTypeOption(), function (Show $show) {
            $show->field('id');
            $show->field('product_item_types_id');
            $show->field('option_name');
            $show->field('is_active');
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
        return Form::make(new ProductItemTypeOption(), function (Form $form) {
            $form->display('id');
            $form->text('product_item_types_id');
            $form->text('option_name');
            $form->text('is_active');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
