<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Product;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ProductController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Product(), function (Grid $grid) {
            $grid->model()->with('user');
            $grid->column('id')->sortable();

            $grid->column('user.name', 'User')->link(function () {
                return route('dcat.admin.custom-users.index', ['id' => $this->user_id]);
            });

            $grid->column('title');
            $grid->column('summary');
            $grid->column('description');
            $grid->column('cover');
            $grid->column('pictures');
            $grid->column('product_category_id');
            $grid->column('tags');
            $grid->column('is_active');
            $grid->column('slug');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->enableDialogCreate();
            $grid->showColumnSelector();
            $grid->quickSearch(['id', 'title', 'summary']);
            $grid->showQuickEditButton();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('title');
                $filter->between('created_at')->date();
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
        return Show::make($id, new Product(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('title');
            $show->field('summary');
            $show->field('description');
            $show->field('cover');
            $show->field('pictures');
            $show->field('product_category_id');
            $show->field('tags');
            $show->field('is_active');
            $show->field('slug');
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
        return Form::make(new Product(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('title');
            $form->text('summary');
            $form->text('description');
            $form->text('cover');
            $form->text('pictures');
            $form->text('product_category_id');
            $form->text('tags');
            $form->text('is_active');
            $form->text('slug');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
