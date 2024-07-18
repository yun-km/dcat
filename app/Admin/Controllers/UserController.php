<?php

namespace App\Admin\Controllers;

// use App\Admin\Repositories\User;
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
            $show->field('avatar', __('user.fields.avatar'))->image(env('APP_URL') . '/storage/avatars');
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
            $form->display('id');
            $form->text('name');
            $form->text('email');
            $form->display('email_verified_at', trans('user.fields.email_verified_at'))->with(function ($value) {
                return date('Y-m-d H:i:s', strtotime($value));
            });
            $form->image('avatar', trans('user.fields.avatar'))->disk('avatars')->autoUpload();
            $form->text('password');
            $form->text('remember_token');
            $form->display('created_at', trans('user.fields.created_at'))->with(function ($value) {
                return date('Y-m-d H:i:s', strtotime($value));
            });
            $form->display('updated_at', trans('user.fields.updated_at'))->with(function ($value) {
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
