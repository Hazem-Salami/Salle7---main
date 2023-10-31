<?php

namespace App\Jobs\category;

use App\Http\Traits\Base64Trait;
use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateCategoryJob implements ShouldQueue
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

            $category = Category::where('name', $this->data['parent_name'])->first();

            $base64encode = $this->data['category_photo'];

            $extension = $base64encode[1];

            $base64encode = $base64encode[0];
            $base64decode = $this->base64Decode($base64encode);

            $path = $this->saveFile($extension, $base64decode, 'category_photo');

            Category::create([
                'name' => $this->data["name"],
                'description' => $this->data["description"],
                'category_id' => $category? $category->id: null,
                'image_path' => $path,
            ]);

        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
