<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class FacturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = Cliente::all();
        $products = Product::limit(10)->get();
        $user = User::first();

        if ($clientes->isEmpty() || $products->isEmpty() || !$user) {
            $this->command->warn('No hay clientes, productos o usuarios para crear facturas');
            return;
        }

        foreach ($clientes as $cliente) {
            // Crear 2-3 facturas por cliente
            $numFacturas = rand(2, 3);
            
            for ($i = 0; $i < $numFacturas; $i++) {
                $factura = Factura::create([
                    'invoice_number' => Factura::generateInvoiceNumber(),
                    'cliente_id' => $cliente->id,
                    'user_id' => $user->id,
                    'fecha_emision' => now()->subDays(rand(1, 30)),
                    'fecha_vencimiento' => now()->addDays(rand(15, 45)),
                    'subtotal' => 0,
                    'tax' => 0,
                    'discount' => 0,
                    'total' => 0,
                    'status' => ['pending', 'paid', 'paid'][rand(0, 2)],
                    'payment_method' => ['cash', 'card', 'transfer'][rand(0, 2)],
                    'notes' => 'Factura de prueba',
                ]);

                // Agregar 2-4 items a cada factura
                $numItems = rand(2, 4);
                $subtotal = 0;

                for ($j = 0; $j < $numItems; $j++) {
                    $product = $products->random();
                    $quantity = rand(1, 5);
                    $price = $product->price;
                    $itemSubtotal = $quantity * $price;
                    $subtotal += $itemSubtotal;

                    FacturaItem::create([
                        'factura_id' => $factura->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $itemSubtotal,
                    ]);
                }

                // Actualizar totales de la factura
                $tax = $subtotal * 0.19; // 19% IVA
                $total = $subtotal + $tax;

                $factura->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                ]);
            }
        }

        $this->command->info('Facturas de prueba creadas exitosamente');
    }
}
