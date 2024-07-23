<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use App\Models\ProductCategory;
use App\Admin\Repositories\Product;
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
            $grid->model()->with(['user', 'category']);

            $grid->column('id')->sortable();
            $grid->column('user.name', 'User')->link(function () {
                return route('dcat.admin.custom-users.index', ['id' => $this->user_id]);
            });
            $grid->column('title');
            $grid->column('category.name' ,__('product.category'));
            $grid->column('summary');
            $grid->column('description');
            $grid->column('cover');
            $grid->column('pictures');

            $grid->column('tags');
            $grid->is_active()->switch();
            $grid->column('slug')->editable();
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            // $grid->enableDialogCreate();
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
            // $show->model()->with(['user', 'category']);
            $show->field('id');
            $show->field('user.name', __('product.user'))->as(function () {
                return $this->user ? $this->user->name : '-';
            });
            $show->field('title');
            $show->field('category.name', __('product.category'))->as(function () {
                return $this->category ? $this->category->name : '-';
            });
            $show->field('summary');
            $show->field('description');
            $show->field('cover');
            $show->field('pictures');
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
            $users = User::pluck('name', 'id')->toArray();
            $categories = ProductCategory::pluck('name', 'id')->toArray();

            $form->display('id');
            $form->select('user_id', __('product.user'))
                ->options($users)->required();
            $form->text('title')->required();
            $form->select('product_category_id' ,__('product.category'))
                ->options($categories)->required();
            $form->text('summary')->required();
            $form->text('description')->required();
            $form->image('cover')->required();
            $form->text('pictures');
            $form->text('tags')->required();
            $form->switch('is_active');
            $form->text('slug');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
