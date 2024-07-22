<?php

namespace App\Admin\Controllers;

// use App\Admin\Repositories\User;
use App\Admin\Renderables\Products;
use App\Models\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class UserController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('email');
            $grid->products->display(__('admin.Products.Seller Products'))->modal(Products::make());
            $grid->column('email_verified_at')->display(function ($email_verified_at) {
                if ($email_verified_at) {
                    return date('Y-m-d H:i:s', strtotime($email_verified_at));
                }
                return '';
            });
            $grid->column('avatar');
            $grid->column('password');
            $grid->column('remember_token');
            $grid->column('created_at')->display(function ($created_at) {
                return date('Y-m-d H:i:s', strtotime($created_at));
            });
            $grid->column('updated_at')->sortable()->display(function ($updated_at) {
                return date('Y-m-d H:i:s', strtotime($updated_at));
            });

            $grid->quickSearch(['id', 'name', 'email']);
            $grid->showQuickEditButton();
            $grid->enableDialogCreate();
            $grid->showColumnSelector();

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
        return Show::make($id, new User(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('email');
            $show->field('email_verified_at')->as(function ($email_verified_at) {
                return date('Y-m-d H:i:s', strtotime($email_verified_at));
            });
            $show->field('avatar')->image(env('APP_URL') . '/storage/avatars');
            $show->field('password');
            $show->field('remember_token');
            $show->field('created_at')->as(function ($created_at) {
                return date('Y-m-d H:i:s', strtotime($created_at));
            });
            $show->field('updated_at')->as(function ($updated_at) {
                return date('Y-m-d H:i:s', strtotime($updated_at));
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new User(), function (Form $form) {
            $userTable = config('admin.database.custom_users_table');
            // $connection = config('admin.database.connection');
            $connection = 'mysql';
            $id = $form->getKey();

            $form->display('id');
            $form->text('name')->required();
            $form->text('email')
                ->required()
                ->creationRules(['required', "unique:{$connection}.{$userTable}"])
                ->updateRules(['required', "unique:{$connection}.{$userTable},email,$id"]);
            $form->display('email_verified_at')
                ->with(function ($value) {
                    if($value) {
                        return date('Y-m-d H:i:s', strtotime($value));
                    }
                    return '';
                });
            $form->image('avatar')
                ->uniqueName()
                ->disk('avatars')
                ->autoUpload();

            if ($id) {
                $form->password('password')
                    ->minLength(8)
                    ->maxLength(20)
                    ->customFormat(function () {
                        return '';
                    });
            } else {
                $form->password('password')
                    ->required()
                    ->minLength(8)
                    ->maxLength(20);
            }
            $form->password('password_confirmation')->same('password');
            $form->ignore(['password_confirmation']);

            // $form->text('remember_token');
            $form->display('created_at')
                ->with(function ($value) {
                    return date('Y-m-d H:i:s', strtotime($value));
                });
            $form->display('updated_at')
                ->with(function ($value) {
                    return date('Y-m-d H:i:s', strtotime($value));
                });
        })->saving(function (Form $form) {
            if ($form->password && $form->model()->get('password') != $form->password) {
                $form->password = bcrypt($form->password);
            }

            if (! $form->password) {
                $form->deleteInput('password');
            }
        });
    }
}
