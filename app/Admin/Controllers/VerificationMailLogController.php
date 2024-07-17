<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\VerificationMailLog;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class VerificationMailLogController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new VerificationMailLog(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('ip');
            $grid->column('device_id');
            $grid->column('browser');
            $grid->column('os');
            $grid->column('email');
            $grid->column('verification_code');
            $grid->column('expires_at');
            $grid->column('is_verified');
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
        return Show::make($id, new VerificationMailLog(), function (Show $show) {
            $show->field('id');
            $show->field('ip');
            $show->field('device_id');
            $show->field('browser');
            $show->field('os');
            $show->field('email');
            $show->field('verification_code');
            $show->field('expires_at');
            $show->field('is_verified');
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
        return Form::make(new VerificationMailLog(), function (Form $form) {
            $form->display('id');
            $form->text('ip');
            $form->text('device_id');
            $form->text('browser');
            $form->text('os');
            $form->text('email');
            $form->text('verification_code');
            $form->datetime('expires_at');
            $form->switch('is_verified', 'Is Verified');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
