<?php

namespace App\Http\Requests\order\purchase;

use App\Http\Requests\BaseRequest;

class SendPurchaseOrderRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'purchaseOrders' => 'required|array',
            'purchaseOrders.*' => 'required|array',
            'purchaseOrders.*.storehouse_id' => 'required|exists:storehouses,id',
            'purchaseOrders.*.product_id' => 'required|exists:products,id',
            'purchaseOrders.*.quantity' => 'required|numeric|min:1',
            'payment_method' => 'required|boolean'
        ];
    }
}
