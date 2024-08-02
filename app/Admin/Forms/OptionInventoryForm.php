<?php
namespace App\Admin\Forms;

use App\Models\Product;
use Dcat\Admin\Widgets\Form;
use App\Models\ProductOptionInventory;
use Dcat\Admin\Traits\LazyWidget;
use App\Models\ProductItemTypeOption;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Form\NestedForm as AdminNestedForm;


class OptionInventoryForm extends Form implements LazyRenderable
{
    use LazyWidget;

    public function handle(array $input)
    {
        \Log::info('Form Input:', $input);

        $filteredTypes = array_map(function ($type) {
            return [
                'type_id' => $type['id'],
                'option_id' => $type['selected_option_id'],
            ];
        }, $input['types']);

        $typesJson = json_encode($filteredTypes);
        \Log::info('Filtered types json:', ['types' => $typesJson]);

        ProductOptionInventory::updateOrCreate(
            ['product_item_type_option_id' => $typesJson ?? null],
            [
                'product_id' => $input['id'],
                'product_item_type_option_id' => $typesJson,
                'price' => $input['price'],
                'total_quantity' => $input['total_quantity'],
            ]
        );


        return $this->response()->success('设置成功')->refresh();
        // return $this->response()->success('设置成功');
    }
    public function default()
    {
        $productId = $this->payload['product_id'] ?? '';
        if ($productId) {
            $productItemType = Product::with('types')->find($productId);
            return $productItemType ? $productItemType->toArray() : [];
        }
        return [
            'product_id' => $this->payload['product_id'] ?? '',
        ];
    }
    public function form()
    {
        $this->hidden('id');
        $this->hasMany('types', __('商品規格'), function (AdminNestedForm $form) {
            $form->text('type_name', __('規格名稱'))->required();
            $form->select('selected_option_id', 'Option')
                ->options(function ($id) use ($form) {
                    $typeId = $form->getKey();
                    return ProductItemTypeOption::where('product_item_types_id', $typeId)->pluck('option_name', 'id');
                })
               ->required();
        });
        $this->text('price', __('價格'))->required();
        $this->text('total_quantity', __('數量'))->required();
    }
}
