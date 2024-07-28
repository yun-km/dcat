<?php
namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;
use App\Models\ProductItemType;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Form\NestedForm as AdminNestedForm;


class ProductTypeForm extends Form implements LazyRenderable
{
    use LazyWidget;

    public function handle(array $input)
    {
        \Log::info('Form Input:', $input);

        $productItemType = ProductItemType::updateOrCreate(
            ['id' => $input['id'] ?? null],
            [
                'type_name' => $input['type_name'],
                'product_id' => $input['product_id'],
            ]
        );

        if (isset($input['options'])) {
            foreach ($input['options'] as $option) {
                $productItemType->options()->updateOrCreate(
                    ['id' => $option['id'] ?? null],
                    ['option_name' => $option['option_name'], 'order' => $option['order']]
                );
            }
        }
        return $this->response()->success('设置成功')->refresh();
        // return $this->response()->success('设置成功');
    }
    public function default()
    {
        // 如果有傳入type_id為編輯
        $typeId = $this->payload['type_id'] ?? '';
        if ($typeId) {
            $productItemType = ProductItemType::with('options')->find($typeId);
            return $productItemType ? $productItemType->toArray() : [];
        }
        return [
            'product_id' => $this->payload['product_id'] ?? '',
        ];
    }
    public function form()
    {
        $this->hidden('id');
        $this->hidden('product_id')->value($this->default()['product_id']);
        $this->text('type_name',__('admin.ProductType.type_name'))->required();
        $this->hasMany('options', __('admin.ProductType.options'), function (AdminNestedForm $form) {
            $form->text('option_name', __('admin.ProductType.option_name'))->required();
            $form->hidden('order')->default(0);
        });
    }
}
