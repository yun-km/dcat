<?php
namespace App\Admin\Renderables;

use Dcat\Admin\Grid;
// use App\Models\ProductItemType;
use Dcat\Admin\Widgets\Table;
use App\Models\ProductItemType;
use Dcat\Admin\Grid\LazyRenderable;
// use App\Admin\Actions\Grid\ProductTypeForm;
use Illuminate\Support\Facades\Log;
use App\Admin\Forms\ProductTypeForm;
use App\Models\ProductItemTypeOption;
use Dcat\Admin\Widgets\Modal;
use App\Models\ProductOptionInventory;
use App\Admin\Forms\OptionInventoryForm;

class ProductOptionInventories extends LazyRenderable
{
    public function render() {
        return $this->grid();
    }
    public function grid(): Grid
    {
        return Grid::make(new ProductOptionInventory(), function (Grid $grid) {
            $productId = $this->payload['product_id'];
            $requestParams = request()->all();
            $grid->model()->where('product_id', $productId);
            $grid->setResource('/product-option-inventories');
            $grid->column('id');

            $itemTypes = ProductItemType::where('product_id', $productId)->with('options')->get();
            Log::info("Item Types: " . json_encode($itemTypes->toArray()));
            foreach ($itemTypes as $itemType) {
                $grid->column("type_{$itemType->id}", $itemType->type_name)->display(function () use ($itemType) {
                    $typesArray = json_decode($this->product_item_type_option_id, true);
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

            $grid->filter(function (Grid\Filter $filter) use ($itemTypes) {
                $filter->panel();
                $filter->expand(false);
                $filter->padding();

                foreach ($itemTypes as $itemType) {
                    $options = $itemType->options->pluck('option_name', 'id')->toArray();
                    Log::info("Options for ItemType ID {$itemType->id}: " . json_encode($options));

                    $filter->where($itemType->type_name, function ($query) use ($itemType){
                        $inputValue = request($itemType->type_name);
                        Log::info("Input value for {$itemType->type_name}: " . $inputValue);
                        $query->whereRaw('JSON_CONTAINS(CAST(JSON_UNQUOTE(JSON_EXTRACT(product_item_type_option_id,  \'$[0]\')) AS JSON), \'{"type_id": '.$itemType->id.', "option_id": '.$inputValue.'}\', \'$\')');
                    })->select($options);
                }
            });

            $grid->tools(function (Grid\Tools $tools) use ($productId){
                $tool = Modal::make()
                    ->lg()
                    ->title(__('admin.ProductType.create_product_type'))
                    ->body(OptionInventoryForm::make()->payload(['product_id' => $productId]))
                    ->button("<button class='btn btn-primary'>" . __('admin.ProductType.create_option_inventories') . "</button>");
                $tools->append($tool);
            });
            // $grid->disableToolbar();
            // $grid->disableActions();
            $grid->disablePagination();
            $grid->disableRefreshButton()
                ->disableCreateButton()
                ->disableRowSelector()
                ->withBorder();
        });
    }

}
