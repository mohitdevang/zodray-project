<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create regular users
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // Product names
        $products = [
            ['name' => 'Laptop', 'price' => 999.99],
            ['name' => 'Mouse', 'price' => 29.99],
            ['name' => 'Keyboard', 'price' => 79.99],
            ['name' => 'Monitor', 'price' => 249.99],
            ['name' => 'Headphones', 'price' => 149.99],
            ['name' => 'Webcam', 'price' => 89.99],
            ['name' => 'USB Drive', 'price' => 19.99],
            ['name' => 'External HDD', 'price' => 129.99],
        ];

        // Create orders for user1
        for ($i = 0; $i < 5; $i++) {
            $orderItems = [];
            $subtotal = 0;
            
            // Random number of items between 1 and 3
            $numItems = rand(1, 3);
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products[array_rand($products)];
                $quantity = rand(1, 2);
                $itemSubtotal = $product['price'] * $quantity;
                $subtotal += $itemSubtotal;
                
                $orderItems[] = [
                    'product_name' => $product['name'],
                    'product_price' => $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                ];
            }
            
            $tax = ($subtotal * 10) / 100;
            $shippingCharge = 50;
            $totalAmount = $subtotal + $tax + $shippingCharge;
            
            $paymentMethod = rand(0, 1) === 0 ? 'cod' : 'online';
            $statuses = ['pending', 'processing', 'completed', 'failed'];
            $status = $statuses[array_rand($statuses)];
            
            $order = Order::create([
                'user_id' => $user1->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_charge' => $shippingCharge,
                'total_amount' => $totalAmount,
                'status' => $status,
                'shipping_name' => 'John Doe',
                'shipping_address' => '123 Main St, City, Country',
                'shipping_phone' => '+1234567890',
                'shipping_email' => 'john@example.com',
                'payment_method' => $paymentMethod,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
            
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $item['product_name'],
                    'product_price' => $item['product_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
            
            $paymentStatus = ($status === 'completed' && rand(0, 1) === 1) ? 'completed' : 'pending';
            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'status' => $paymentStatus,
                'paid_at' => $paymentStatus === 'completed' ? now() : null,
            ]);
        }

        // Create orders for user2
        for ($i = 0; $i < 3; $i++) {
            $orderItems = [];
            $subtotal = 0;
            
            $numItems = rand(1, 2);
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products[array_rand($products)];
                $quantity = rand(1, 2);
                $itemSubtotal = $product['price'] * $quantity;
                $subtotal += $itemSubtotal;
                
                $orderItems[] = [
                    'product_name' => $product['name'],
                    'product_price' => $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                ];
            }
            
            $tax = ($subtotal * 10) / 100;
            $shippingCharge = 50;
            $totalAmount = $subtotal + $tax + $shippingCharge;
            
            $paymentMethod = 'online';
            $statuses = ['processing', 'completed'];
            $status = $statuses[array_rand($statuses)];
            
            $order = Order::create([
                'user_id' => $user2->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_charge' => $shippingCharge,
                'total_amount' => $totalAmount,
                'status' => $status,
                'shipping_name' => 'Jane Smith',
                'shipping_address' => '456 Oak Ave, Town, Country',
                'shipping_phone' => '+0987654321',
                'shipping_email' => 'jane@example.com',
                'payment_method' => $paymentMethod,
                'created_at' => now()->subDays(rand(1, 20)),
            ]);
            
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $item['product_name'],
                    'product_price' => $item['product_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
            
            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'status' => 'completed',
                'paid_at' => now(),
            ]);
        }
    }
}
