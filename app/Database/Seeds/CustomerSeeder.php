<?php

namespace App\Database\Seeds;

use App\Models\CustomersModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        //
        $model = new CustomersModel();

        for ($i = 0; $i < 500; $i++) {
            $data = $this->generateCustomer();
            $model->save($data);
        }
    }
    private function generateCustomer(): array
    {
        $faker = Factory::create('tr_TR');

        return [
            'name' => $faker->name(),
            'email' => $faker->email,
            'phone' => $faker->phoneNumber(),
            'address' => $faker->address,
            'birthday' => $faker->dateTimeThisCentury()->format('Y-m-d'),
            'created_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
        ];
    }
}
