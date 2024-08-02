<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use App\Models\ProductCategory;
use Dcat\Admin\Grid\Displayers\Actions;
use Dcat\Admin\Widgets\Modal;
use App\Admin\Repositories\Product;
use App\Admin\Forms\ProductTypeForm;
use App\Admin\Renderables\ProductTypes;
use App\Admin\Forms\OptionInventoryForm;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Admin\Renderables\ProductOptionInventories;

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
            $grid->column('category.name' ,__('product.category'))->label();
            $grid->column('tags')->label();
            $grid->column('summary');
            $grid->column('description');
            $grid->column(__('admin.ProductType.product_types'))->display(__('admin.ProductType.product_types'))->expand(function () {
                return ProductTypes::make()->payload(['product_id' => $this->id]);
            });
            // $grid->column(__('admin.ProductType.create_product_type'))->display(__('admin.ProductType.create_product_type'))->modal(function () {
            //     return ProductTypeForm::make()->payload(['product_id' => $this->id]);
            // });

            $grid->column(__('admin.ProductType.product_inventories'))->display(__('admin.ProductType.product_inventories'))->expand(function () {
                return ProductOptionInventories::make()->payload(['product_id' => $this->id]);
            });
            // $grid->column(__('admin.ProductType.create_option_inventories'))->display(__('admin.ProductType.create_option_inventories'))->modal(function () {
            //     return OptionInventoryForm::make()->payload(['product_id' => $this->id]);
            // });

            $grid->actions(function (Actions $actions) {
                $productId = $actions->getKey();
                $modal = Modal::make()
                    ->lg()
                    ->title(__('admin.ProductType.create_product_type'))
                    ->body(ProductTypeForm::make()->payload(['product_id' => $productId]))
                    ->button( __('admin.ProductType.create_product_type'));
                $actions->append($modal);

                $modal = Modal::make()
                    ->lg()
                    ->title(__('admin.ProductType.create_option_inventories'))
                    ->body(OptionInventoryForm::make()->payload(['product_id' => $productId]))
                    ->button( __('admin.ProductType.create_option_inventories'));
                $actions->append($modal);
            });
            // $grid->column('cover');
            // $grid->column('pictures');

            $grid->is_active()->switch();
            // $grid->column('slug')->editable();
            // $grid->column('created_at');
            // $grid->column('updated_at')->sortable();

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

            $form->row(function (Form\Row $row) use ($form) {
                if (!$form->isCreating()) {
                    $row->width(2)->display('id');
                }
                $row->width(10)->text('title')->required();
            });
            $form->row(function (Form\Row $form) use ($users, $categories) {
                // $form->width(4)->text('username')->required();
                $form->width(4)->select('user_id', __('product.user'))
                    ->options($users)->required();
                $form->width(4)->select('product_category_id' ,__('product.category'))
                    ->options($categories)->required();
                $form->width(4)->text('tags')->required();

            });

            $form->row(function (Form\Row $row){
                $row->text('summary')->required();
                $row->text('description')->required();
                $row->image('cover');
                $row->text('pictures');
            });

            if (!$form->isCreating()) {
                $form->row(function (Form\Row $row) {
                    $row->width(6)->display('created_at');
                    $row->width(6)->display('updated_at');
                });
            }

            // $form->switch('is_active');
            // $form->text('slug');


            // $form->display('created_at');
            // $form->display('updated_at');
        });
    }
}
