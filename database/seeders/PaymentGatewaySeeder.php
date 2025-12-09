<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    public function run()
    {
        $items = [
            'xendit',
            'duitku',
            'tripay'
        ];

        foreach ($items as $name) {
            PaymentGateway::firstOrCreate([
                'name' => $name
            ], [
                'credentials' => [],
                'is_active' => false
            ]);
        }
    }
}
