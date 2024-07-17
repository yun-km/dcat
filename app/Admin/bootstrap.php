<?php

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Models\Menu;

/**
 * Dcat-admin - admin builder based on Laravel.
 * @author jqh <https://github.com/jqhph>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 *
 * extend custom field:
 * Dcat\Admin\Form::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Column::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Filter::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

 if (Menu::where('title', 'custom-users')->doesntExist()) {
    Menu::create([
        'title' => 'custom-users',
        'icon'  => 'fa-users',
        'uri'   => 'custom-users',
    ]);
}
if (Menu::where('title', 'verificationMailLog')->doesntExist()) {
    Menu::create([
        'title' => 'verificationMailLog',
        'icon'  => 'fa-users',
        'uri'   => 'verificationMailLog',
    ]);
}
