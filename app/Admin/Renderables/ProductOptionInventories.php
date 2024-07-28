<?php
namespace App\Admin\Renderables;

use Dcat\Admin\Grid;
// use App\Models\ProductItemType;
use Dcat\Admin\Widgets\Table;
use Dcat\Admin\Grid\LazyRenderable;
use App\Admin\Forms\ProductTypeForm;
// use App\Admin\Actions\Grid\ProductTypeForm;
use App\Models\ProductOptionInventory;
use App\Models\ProductItemType;
use App\Models\ProductItemTypeOption;

class ProductOptionInventories extends LazyRenderable
{
    public function render() {
        return $this->grid();
    }
    public function grid(): Grid
    {
        return Grid::make(new ProductOptionInventory(), function (Grid $grid) {
            $productId = $this->payload['product_id'];
            $grid->model()->where('product_id', $productId);

            // $grid->column('product_item_type_option_id', 'Types')->display(function ($product_item_type_option_id) {
            //     $typesArray = json_decode($product_item_type_option_id, true);
            //     $result = [];
            //     foreach ($typesArray as $type) {
            //         $typeName = ProductItemType::find($type['type_id'])->type_name;
            //         $optionName = ProductItemTypeOption::find($type['option_id'])->option_name;
            //         $result[] = "{$typeName}: {$optionName}";
            //     }
            //     return implode(', ', $result);
            // });


            // 获取特定 product_id 的所有类型
            $itemTypes = ProductItemType::where('product_id', $productId)->get();

            foreach ($itemTypes as $itemType) {
                $grid->column("type_{$itemType->id}", $itemType->type_name)->display(function ($product_item_type_option_id) use ($itemType) {
                    $typesArray = json_decode($this->product_item_type_option_id, true);
                    if (is_null($typesArray) || !is_array($typesArray)) {
                        return 'No options available';
                    }
                    $result = [];
                    foreach ($typesArray as $type) {
                        if ($type['type_id'] == $itemType->id) {
                            $optionName = ProductItemTypeOption::find($type['option_id'])->option_name ?? 'Unknown Option';
                            $result[] = $optionName;
                        }
                    }
                    return implode(', ', $result);
                });
            }

            $grid->column('price')->editable();
            $grid->column('total_quantity')->editable();

            $grid->disableToolbar();
            $grid->disablePagination();
            $grid->disableActions()
            ->disableRefreshButton()
            ->disableCreateButton()
            ->disableRowSelector()
            ->withBorder();

            // $grid->tools(function (Grid\Tools $tools) {
            //     $tools->append(new ProductTypeForm());
            // });
        });
    }
}
