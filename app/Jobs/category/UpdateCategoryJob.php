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

class UpdateCategoryJob implements ShouldQueue
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

            $parentCategory = Category::where('name', $this->data['parent_name'])->first();

            $category = Category::where('name', $this->data['old_name'])->first();

            $image_path = null;

            if ($this->data['category_photo']) {

                $extension = $this->data['category_photo'][1];
                $base64encode = $this->data['category_photo'][0];

                $base64decode = $this->base64Decode($base64encode);

                $image_path = $this->saveFile($extension, $base64decode, 'category_photo');

                $this->deleteFile($category->image_path);
            }

            $category->update(
                [
                    'name' => $this->data['name'],
                    'description' => $this->data['description'],
                    'image_path' => $image_path?: $category->image_path,
                    'category_id' => $parentCategory? $parentCategory->id : null,
                ]);

        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
