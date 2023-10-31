<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::getQuery()->delete();
        $category = Category::create([
            'name' => 'مكونات المحرك',
            'description' => 'Engine Components',
            'image_path' => '/category_photo/محرك السيارة.jpg',
        ]);
        Category::insert([
            [
                'name' => 'المحرك',
                'description' => 'محركات كاملة أو كتل المحرك',
                'image_path' => '/category_photo/msg913756966-75827.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'الأسطوانة',
                'description' => 'يشمل تجميع رأس الأسطوانة والصمامات وعمود الكام',
                'image_path' => '/category_photo/اسطوانة.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'المكابس والحلقات',
                'description' => 'المكابس وحلقات المكابس وقضبان التوصيل',
                'image_path' => '/category_photo/المكابس والحلقات.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'عمود المرفق والمحامل',
                'description' => 'عمود المرفق والمحامل الرئيسية ومحامل قضيب التوصيل',
                'image_path' => '/category_photo/عمود المرفق والمحامل.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'مكونات التوقيت',
                'description' => 'سير التوقيت أو السلاسل، ومشدات التوقيت، وتروس التوقيت',
                'image_path' => '/category_photo/مكونات التوقيت.jpg',
                'category_id' => $category->id,
            ]
        ]);

        $category = Category::create([
            'name' => 'النظام الكهربائي',
            'description' => 'Electrical System',
            'image_path' => '/category_photo/Electrical System.jpg',
        ]);
        Category::insert([
            [
                'name' => 'البطارية',
                'description' => 'بطاريات السيارات وملحقات البطارية',
                'image_path' => '/category_photo/car-battery.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'المولد',
                'description' => 'المولدات ومنظمات الجهد',
                'image_path' => '/category_photo/المولد.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'محرك البدء',
                'description' => 'محركات البدء والسلوينودات',
                'image_path' => '/category_photo/محرك البدء.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'نظام الاشعال',
                'description' => 'شمعات الإشعال وملفات الإشعال ووحدات التحكم في الإشعال',
                'image_path' => '/category_photo/نظام إشغال.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'الحساسات',
                'description' => 'حساسات متنوعة، مثل حساسات الأكسجين وحساسات MAP وحساسات الحرارة',
                'image_path' => '/category_photo/الحساسات.jpg',
                'category_id' => $category->id,
            ]
        ]);

        $category = Category::create([
            'name' => 'نظام الوقود',
            'description' => 'Fuel System',
            'image_path' => '/category_photo/نظام وقود.jpg',
        ]);

        Category::insert([
            [
                'name' => 'مضخة الوقود',
                'description' => 'مضخات الوقود الكهربائية أو الميكانيكية',
                'image_path' => '/category_photo/مضخة.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'حاقن الوقود',
                'description' => 'حاقنات الوقود وأختام الحاقن',
                'image_path' => '/category_photo/حاقن الوقود.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'الكاربوريتور',
                'description' => 'الكاربوريتورات ومجموعات إعادة بناء الكاربوريتور',
                'image_path' => '/category_photo/الكاربوريتور.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'فلاتر الوقود',
                'description' => 'فلاتر الوقود والمصافي',
                'image_path' => '/category_photo/فلاتر الوقود.jpg',
                'category_id' => $category->id,
            ],
            [
                'name' => 'خزان الوقود ومكوناته',
                'description' => 'خزانات الوقود وأغطية خزان الوقود ووحدات إرسال الوقود',
                'image_path' => '/category_photo/خزان الوقود ومكوناته.jpg',
                'category_id' => $category->id,
            ],
        ]);
    }
}
