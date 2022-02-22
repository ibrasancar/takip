<?php

namespace App\Database\Seeds;

use App\Models\ManufacturersModel;
use App\Models\ProductCategoriesModel;
use CodeIgniter\Database\Seeder;
use App\Models\ProductsModel;
use Faker\Factory;

class ProductSeeder extends Seeder
{
    public function run()
    {
        //
        $model = new ProductsModel();
        $category_model = new ProductCategoriesModel();

        $category_model->insertBatch([
            ['name' => 'Koltuk Takımları'],
            ['name' => 'Yatak Odası Takımları'],
            ['name' => 'Yemek Odası Takımları'],
            ['name' => 'Masa & Sandalye'],
            ['name' => 'Aksesuarlar'],
            ['name' => 'Örnek Kategori'],
        ]);

        for ($i = 0; $i < 2000; $i++)
            $model->save($this->generateDummy());
    }
    private function generateDummy(): array
    {
        $faker = Factory::create('tr_TR');
        $users = new ManufacturersModel();
        $category_model = new ProductCategoriesModel();

        $manufacturers = $users->findAll();
        $categories = $category_model->findAll();

        return [
            'name' => $faker->lastName(),
            'price' => $faker->randomNumber(4),
            'image' => $faker->imageUrl(640, 480),
            'category' => $categories[array_rand($categories, 1)]['id'],
            'manufacturer_id' => $manufacturers[array_rand($manufacturers, 1)]['id'],
            'created_at' => $faker->dateTimeBetween('-4 month', '-1 hour')->format('Y-m-d H:i:s'),
        ];
    }
}
