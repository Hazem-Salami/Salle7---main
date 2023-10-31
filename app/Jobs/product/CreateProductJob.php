<?php

namespace App\Jobs\product;

use App\Http\Traits\Base64Trait;
use App\Models\Category;
use App\Models\Storehouse;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateProductJob implements ShouldQueue
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

            $base64encode = $this->data['product_photo'];

            $extension = $base64encode[1];

            $base64encode = $base64encode[0];
            $base64decode = $this->base64Decode($base64encode);

            $path = $this->saveFile($extension, $base64decode, 'product_photo');

            $category->products()->create([
                'name' => $this->data['name'],
                'description' => $this->data['description'],
                'product_code' => $this->data['product_code'],
                'made' => $this->data['made'],
                'price' => $this->data['price'],
                'image_path' => $path,
                'quantity' => $this->data['quantity'],
                'storehouse_id' => $storehouse->id,
            ]);

        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
