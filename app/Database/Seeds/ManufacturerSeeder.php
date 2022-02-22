<?php

namespace App\Database\Seeds;

use App\Models\ManufacturersModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;

class ManufacturerSeeder extends Seeder
{
    public function run()
    {
        //
        $model = new ManufacturersModel();

        for ($i = 0; $i < 20000; $i++) {
            $data = $this->generateManufacturer();
            $model->save($data);
        }
    }
    private function generateManufacturer(): array
    {
        $faker = Factory::create('tr_TR');

        return [
            'name' => $faker->company(),
            'contact_name' => $faker->name,
            'email' => $faker->email,
            'phone' => $faker->phoneNumber(),
            'address' => $faker->address,
            'created_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
        ];
    }
}
