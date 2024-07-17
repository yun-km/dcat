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
            $show->field('email_verified_at');
            $show->field('password');
            $show->field('remember_token');
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
        return Form::make(new User(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('email');
            $form->text('email_verified_at');
            $form->text('password');
            $form->text('remember_token');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
