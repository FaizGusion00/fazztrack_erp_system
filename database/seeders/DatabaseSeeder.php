<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Job;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default users with different roles
        $users = [
            [
                'username' => 'superadmin',
                'name' => 'Super Admin',
                'email' => 'superadmin@fazztrack.com',
                'password' => Hash::make('admin123'),
                'role' => 'SuperAdmin',
                'phase' => null,
            ],
            [
                'username' => 'admin',
                'name' => 'Admin',
                'email' => 'admin@fazztrack.com',
                'password' => Hash::make('approver123'),
                'role' => 'Admin',
                'phase' => null,
            ],
            [
                'username' => 'sales',
                'name' => 'Sales Manager',
                'email' => 'sales@fazztrack.com',
                'password' => Hash::make('sales123'),
                'role' => 'Sales Manager',
                'phase' => null,
            ],
            [
                'username' => 'designer',
                'name' => 'Designer',
                'email' => 'designer@fazztrack.com',
                'password' => Hash::make('designer123'),
                'role' => 'Designer',
                'phase' => null,
            ],
            [
                'username' => 'print',
                'name' => 'Print Staff',
                'email' => 'print@fazztrack.com',
                'password' => Hash::make('print123'),
                'role' => 'Production Staff',
                'phase' => 'PRINT',
            ],
            [
                'username' => 'press',
                'name' => 'Press Staff',
                'email' => 'press@fazztrack.com',
                'password' => Hash::make('press123'),
                'role' => 'Production Staff',
                'phase' => 'PRESS',
            ],
            [
                'username' => 'cut',
                'name' => 'Cut Staff',
                'email' => 'cut@fazztrack.com',
                'password' => Hash::make('cut123'),
                'role' => 'Production Staff',
                'phase' => 'CUT',
            ],
            [
                'username' => 'sew',
                'name' => 'Sew Staff',
                'email' => 'sew@fazztrack.com',
                'password' => Hash::make('sew123'),
                'role' => 'Production Staff',
                'phase' => 'SEW',
            ],
            [
                'username' => 'qc',
                'name' => 'QC Staff',
                'email' => 'qc@fazztrack.com',
                'password' => Hash::make('qc1234'),
                'role' => 'Production Staff',
                'phase' => 'QC',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create sample products
        $products = [
            [
                'name' => 'Basic Cotton T-Shirt',
                'description' => 'High-quality 100% cotton t-shirt perfect for printing',
                'size' => 'M',
                'stock' => 100,
                'category' => 'T-Shirt',
                'color' => 'White',
                'material' => '100% Cotton',
                'status' => 'Active',
            ],
            [
                'name' => 'Premium Polo Shirt',
                'description' => 'Professional polo shirt with collar',
                'size' => 'L',
                'stock' => 50,
                'category' => 'Polo',
                'color' => 'Navy Blue',
                'material' => 'Cotton Blend',
                'status' => 'Active',
            ],
            [
                'name' => 'Hoodie',
                'description' => 'Comfortable hoodie for casual wear',
                'size' => 'XL',
                'stock' => 30,
                'category' => 'Hoodie',
                'color' => 'Black',
                'material' => 'Fleece',
                'status' => 'Active',
            ],
            [
                'name' => 'Tank Top',
                'description' => 'Sleeveless tank top for summer',
                'size' => 'S',
                'stock' => 75,
                'category' => 'Tank Top',
                'color' => 'Gray',
                'material' => 'Cotton',
                'status' => 'Active',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create sample clients
        $clients = [
            [
                'name' => 'ABC Company',
                'phone' => '+60123456789',
                'email' => 'contact@abc.com',
                'billing_address' => '123 Business Street, Kuala Lumpur, Malaysia',
                'shipping_address' => '123 Business Street, Kuala Lumpur, Malaysia',
                'customer_type' => 'Organisation',
            ],
            [
                'name' => 'XYZ Retail',
                'phone' => '+60187654321',
                'email' => 'orders@xyz.com',
                'billing_address' => '456 Retail Avenue, Petaling Jaya, Malaysia',
                'shipping_address' => '456 Retail Avenue, Petaling Jaya, Malaysia',
                'customer_type' => 'Agent',
            ],
            [
                'name' => 'Individual Customer',
                'phone' => '+60111222333',
                'email' => 'customer@email.com',
                'billing_address' => '789 Personal Road, Subang Jaya, Malaysia',
                'shipping_address' => '789 Personal Road, Subang Jaya, Malaysia',
                'customer_type' => 'Individual',
            ],
        ];

        foreach ($clients as $clientData) {
            $client = Client::create($clientData);
            
            // Add contacts for each client
            Contact::create([
                'client_id' => $client->client_id,
                'contact_name' => 'Primary Contact',
                'contact_phone' => $client->phone,
                'contact_email' => $client->email,
            ]);
        }

        // Create sample orders with products
        $orderData = [
            [
                'order' => [
                    'client_id' => 1,
                    'job_name' => 'Company T-Shirts',
                    'delivery_method' => 'Self Collect',
                    'status' => 'Order Created',
                    'design_deposit' => 500,
                    'production_deposit' => 1000,
                    'balance_payment' => 1000,
                    'total_amount' => 2500,
                    'due_date_design' => now()->addDays(3),
                    'due_date_production' => now()->addDays(7),
                    'remarks' => '50 pieces of company branded t-shirts',
                ],
                'products' => [
                    ['product_id' => 1, 'quantity' => 50]
                ]
            ],
            [
                'order' => [
                    'client_id' => 2,
                    'job_name' => 'Event T-Shirts',
                    'delivery_method' => 'Shipping',
                    'status' => 'Order Approved',
                    'design_deposit' => 300,
                    'production_deposit' => 800,
                    'balance_payment' => 500,
                    'total_amount' => 1600,
                    'due_date_design' => now()->addDays(2),
                    'due_date_production' => now()->addDays(5),
                    'remarks' => '100 pieces for corporate event',
                ],
                'products' => [
                    ['product_id' => 2, 'quantity' => 100]
                ]
            ],
            [
                'order' => [
                    'client_id' => 3,
                    'job_name' => 'Personal T-Shirt',
                    'delivery_method' => 'Self Collect',
                    'status' => 'Design Review',
                    'design_deposit' => 100,
                    'production_deposit' => 200,
                    'balance_payment' => 100,
                    'total_amount' => 400,
                    'due_date_design' => now()->addDays(1),
                    'due_date_production' => now()->addDays(3),
                    'remarks' => 'Custom design for personal use',
                ],
                'products' => [
                    ['product_id' => 1, 'quantity' => 1]
                ]
            ],
            [
                'order' => [
                    'client_id' => 1,
                    'job_name' => 'Test Order',
                    'delivery_method' => 'Self Collect',
                    'status' => 'Job Start',
                    'design_deposit' => 100,
                    'production_deposit' => 200,
                    'balance_payment' => 100,
                    'total_amount' => 400,
                    'due_date_design' => now()->addDays(1),
                    'due_date_production' => now()->addDays(3),
                    'remarks' => 'Test order',
                ],
                'products' => [
                    ['product_id' => 3, 'quantity' => 10]
                ]
            ],
        ];

        foreach ($orderData as $data) {
            $order = Order::create($data['order']);

            // Create order products
            foreach ($data['products'] as $productData) {
                \App\Models\OrderProduct::create([
                    'order_id' => $order->order_id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                ]);
            }

            // Create jobs for each order
            $phases = ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC'];
            foreach ($phases as $phase) {
                Job::create([
                    'order_id' => $order->order_id,
                    'phase' => $phase,
                    'status' => $phase === 'PRINT' ? 'In Progress' : 'Pending',
                    'qr_code' => 'JOB-' . $order->order_id . '-' . $phase,
                    'start_quantity' => $order->job_name === 'Personal T-Shirt' ? 1 : 50,
                    'end_quantity' => 0,
                    'reject_quantity' => 0,
                    'assigned_user_id' => User::where('phase', $phase)->first()?->id,
                ]);
            }
        }
    }
}
