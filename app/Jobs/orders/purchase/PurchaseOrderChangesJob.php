<?php

namespace App\Jobs\orders\purchase;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Storehouse;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderChangesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            foreach ($this->data as $response){

                $storehouse = Storehouse::where('email', $response['email'])->first();

                $product = Product::where([
                    'made' => $response['made'],
                    'product_code' => $response['product_code'],
                    'storehouse_id' => $storehouse->id
                ])->first();

                $purchaseOrder = PurchaseOrder::where([
                    'storehouse_id' => $storehouse->id,
                    'product_id' => $product->id,
                    'user_id' => $response['buyer_id'],
                ])->first();

                switch ($response['type']){
                    case 0 : $this->deletePurchaseOrder($purchaseOrder, $product);
                        break;
                    case 1 : $this->acceptPurchaseOrder($purchaseOrder, $product);
                        break;
                    case 2 : $this->rejectPurchaseOrder($purchaseOrder);
                        break;
                }

            }

        } catch (\Exception $exception) {
            echo $exception;
        }
    }

    public function deletePurchaseOrder($purchaseOrder, $product){
        if($purchaseOrder && $purchaseOrder->stage == 0)
            $product->update([
                'quantity' => $product->quantity + $purchaseOrder->quantity,
            ]);

        $purchaseOrder->delete();
    }

    public function acceptPurchaseOrder($purchaseOrder, $product){
        if($purchaseOrder && $purchaseOrder->stage === null && $product->quantity > 0) {

            $purchaseOrder->update([
                'stage' => 0,
            ]);

            $product->update([
                'quantity' => $product->quantity - $purchaseOrder->quantity,
            ]);

        }
    }

    public function rejectPurchaseOrder($purchaseOrder){
        if($purchaseOrder && $purchaseOrder->stage === null) {
            $purchaseOrder->update([
                'stage' => 1,
            ]);
        }
    }
}
