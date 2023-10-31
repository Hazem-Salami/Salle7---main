<?php

namespace App\Jobs\product;

use App\Http\Traits\Base64Trait;
use App\Models\Category;
use App\Models\Product;
use App\Models\Storehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Base64Trait;

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

            $storehouse = Storehouse::where('email', $this->data['user_email'])->first();

            $category = Category::where('name', $this->data['category_name'])->first();

            $product = Product::where(
                [
                    'made' => $this->data['old_made'],
                    'product_code' => $this->data['old_product_code'],
                    'storehouse_id' => $storehouse->id
                ])->first();

            $image_path = null;

            if ($this->data['product_photo']) {

                $extension = $this->data['product_photo'][1];
                $base64encode = $this->data['product_photo'][0];

                $base64decode = $this->base64Decode($base64encode);

                $image_path = $this->saveFile($extension, $base64decode, 'product_photo');

                $this->deleteFile($product->image_path);
            }

            $product->update(
                [
                    'name' => $this->data['name'],
                    'description' => $this->data['description'],
                    'made' => $this->data['made'],
                    'price' => $this->data['price'],
                    'product_code' => $this->data['product_code'],
                    'quantity' => $this->data['quantity'],
                    'image_path' => $image_path?: $product->image_path,
                    'category_id' => $category->id,
                ]);

        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
