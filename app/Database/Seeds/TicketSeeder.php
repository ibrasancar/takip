<?php

namespace App\Database\Seeds;

use App\Models\OrdersModel;
use App\Models\TicketModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;

class TicketSeeder extends Seeder
{
    public function run()
    {
        //
        $model = new TicketModel();

        for ($i = 0; $i < 50; $i++) {
            $data = $this->generateTicket();
            $model->save($data);
        }
    }
    private function generateTicket(): array
    {
        $ordersModel = new OrdersModel();
        $orders = $ordersModel->findAll();
        $faker = Factory::create('tr_TR');
        return [
            'order_id' => $orders[array_rand($orders, 1)]['id'],
            'title' => $faker->text("50"),
            'message' => $faker->text("200"),
            'estimated_solve' => $faker->dateTimeBetween('now', '+20 days')->format('Y-m-d H:i'),
        ];
    }
}
