<?php

namespace App\Admin\Actions\Grid;

use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Admin\Forms\ProductTypeCreate as ProductTypeCreateForm;
use Dcat\Admin\Widgets\Modal;

class ProductTypeCreate extends RowAction
{
    /**
     * @return string
     */
	protected $title = '建立商品規格';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        dump($this->getKey());

        return $this->response()
            ->success('Processed successfully: '.$this->getKey())
            ->refresh();
    }

    public function render()
    {
        // 实例化表单类并传递自定义参数
        $form = ProductTypeCreateForm::make()->payload(['product_id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title($this->title)
            ->body($form)
            ->button($this->title);
    }

    /**
	 * @return string|array|void
	 */
	public function confirm()
	{
		// return ['Confirm?', 'contents'];
	}

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }

    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }
}
