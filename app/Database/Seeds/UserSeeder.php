<?php

namespace App\Database\Seeds;

use App\Controllers\Users;
use App\Models\UserMetaModel;
use CodeIgniter\Database\Seeder;
use App\Models\UsersModel;
use Faker\Factory;

class UserSeeder extends Seeder
{
    public function run()
    {
        $model = new UsersModel();
        $model->save([
            'user_type' => 'su_admin',
            'full_name' => 'Demo Demo',
            'email'     => 'demo@demo.com',
            'password'  => '123456',
            'phone'     => '+905071265236',
            'address'   => null,
        ]);
    }
    // public function run()
    // {
    //     //
    //     $model = new UsersModel();

    //     $model->save([
    //         'user_type' => 'su_admin',
    //         'level'     => 1,
    //         'full_name' => 'Demo Demo',
    //         'email'     => 'demo@demo.com',
    //         'password'  => '123456',
    //         'phone'     => '+905071265236',
    //         'address'   => null,
    //     ]);

    //     for ($i = 0; $i < 20; $i++) {
    //         $data = $this->generateUsers();
    //         $model->save($data);

    //         if ($data['user_type'] == 'salesman') {
    //             $user_id = $model->getInsertID();
    //             $userMetaModel = new UserMetaModel();
    //             $userMetaModel->save($this->generateMetaForSales($user_id));
    //         }
    //     }
    // }

    // private function generateMetaForSales(int $user_id): array
    // {
    //     $faker = Factory::create();
    //     return [
    //         'user_id' => $user_id,
    //         'meta_title' => 'sale_percentage',
    //         'meta_value' => $faker->randomFloat(2, 0.2, 3),
    //     ];
    // }
    // private function generateUsers(): array
    // {
    //     $faker = Factory::create('tr_TR');
    //     $user_type = [
    //         'technic',
    //         'salesman',
    //         'admin',
    //         'su_admin'
    //     ];

    //     return [
    //         'level' => rand(1, 4),
    //         'user_type' => $user_type[array_rand($user_type, 1)],
    //         'full_name' => $faker->name,
    //         'email' => $faker->email,
    //         'password' => "123456",
    //         'phone' => $faker->phoneNumber(),
    //         'address' => $faker->address,
    //         'created_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
    //     ];
    // }
}
