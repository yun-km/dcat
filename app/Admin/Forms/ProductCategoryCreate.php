<?php
namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;
use App\Models\ProductCategory;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Contracts\LazyRenderable;


class ProductCategoryCreate extends Form implements LazyRenderable
{
    use LazyWidget;

    public function handle(array $input)
    {
        $category = new ProductCategory();
        $category->parent_id = $input['parent_id'];
        $category->name = $input['name'];
        $category->is_active = 1;
        $category->save();

        return $this->response()->success('设置成功')->refresh();
    }
    public function default()
    {
        return [
            'parent_id' => $this->payload['parent_id'] ?? '',
            'is_active' => 1,
        ];
    }
    public function form()
    {
        $categories = ProductCategory::pluck('name', 'id')->toArray();
        $categories = [0 => __('admin.ProductCategory.no_parent_category')] + $categories;

        $this->select('parent_id', __('admin.ProductCategory.select_parent_category'))
            ->options($categories);
        $this->text('name');
    }
}
