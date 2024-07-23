<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use App\Models\ProductCategory;
use App\Admin\Forms\ProductCategoryCreate;
use Dcat\Admin\Http\Controllers\AdminController;

class ProductCategoryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ProductCategory(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->name->tree();
            $grid->column('slug')->editable();

            $grid->column(__('admin.ProductCategory.create_child_category'))->display(__('admin.ProductCategory.create_child_category'))->expand(function () {
                return ProductCategoryCreate::make()->payload(['parent_id' => $this->id]);
            });

            $grid->is_active()->switch();
            // $grid->column('created_at');
            // $grid->column('updated_at')->sortable();
            $grid->quickSearch(['id', 'name']);
            $grid->showQuickEditButton();
            $grid->enableDialogCreate();

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
        return Show::make($id, new ProductCategory(), function (Show $show) {
            $show->field('id');
            $show->field('parent_id');
            $show->field('name');
            $show->field('slug');
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
        return Form::make(new ProductCategory(), function (Form $form) {
            $categories = ProductCategory::pluck('name', 'id')->toArray();
            $categories = [0 => __('admin.ProductCategory.no_parent_category')] + $categories;

            $form->display('id');
            $form->select('parent_id', __('admin.ProductCategory.select_parent_category'))
                ->options($categories)
                ->default(0);
            $form->text('name');
            $form->text('slug');

            $form->switch('is_active')
            ->customFormat(function ($v) {
                return $v == 1 ? true : false;
            })
            ->saving(function ($v) {
                return $v ? 1 : 0;
            });

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
