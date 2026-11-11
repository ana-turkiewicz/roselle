<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::where('email', 'admin@roselle.com')->exists()) {
            return;
        }

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@roselle.com',
            'password' => 'roselle',
            'role' => 'admin',
        ]);

        $customer1 = User::factory()->create([
            'name' => 'Maria',
            'email' => 'maria@roselle.com',
            'password' => 'roselle',
        ]);

        $customer2 = User::factory()->create([
            'name' => 'João',
            'email' => 'joao@roselle.com',
            'password' => 'roselle',
        ]);

        $customer3 = User::factory()->create([
            'name' => 'Ana',
            'email' => 'ana@roselle.com',
            'password' => 'roselle',
        ]);

        $customers = collect([$customer1, $customer2, $customer3]);

        File::copyDirectory(database_path('seeders/images'), storage_path('app/public/products'));

        $categories = [
            'Maquiagem' => [
                ['Batom Matte Rosé', 'Batom de longa duração com acabamento matte.', 39.90, 25, 'batom-matte-rose'],
                ['Base Líquida Cobertura Média', 'Base leve com acabamento natural.', 79.90, 12, 'base-liquida'],
                ['Máscara de Cílios Volume', 'Máscara que alonga e dá volume aos cílios.', 49.90, 18, 'mascara-de-cilios'],
                ['Paleta de Sombras Nude', 'Paleta com 12 tons neutros.', 119.90, 0, 'paleta-de-sombras'],
            ],
            'Skincare' => [
                ['Sérum Vitamina C', 'Sérum antioxidante que uniformiza o tom da pele.', 89.90, 15, 'serum-vitamina-c'],
                ['Hidratante Facial', 'Hidratante de textura leve para uso diário.', 59.90, 20, 'hidratante-facial'],
                ['Protetor Solar FPS 50', 'Protetor facial com toque seco.', 69.90, 30, 'protetor-solar'],
                ['Gel de Limpeza Facial', 'Gel que remove impurezas sem ressecar.', 44.90, 22, 'gel-de-limpeza'],
            ],
            'Cabelos' => [
                ['Shampoo Hidratante', 'Shampoo para cabelos secos e opacos.', 34.90, 28, 'shampoo-hidratante'],
                ['Condicionador Nutritivo', 'Condicionador que desembaraça e nutre os fios.', 36.90, 26, 'condicionador-nutritivo'],
                ['Máscara Capilar Reparadora', 'Tratamento intensivo para fios danificados.', 54.90, 14, 'mascara-capilar'],
                ['Óleo Finalizador', 'Óleo leve que dá brilho e controla o frizz.', 42.90, 9, 'oleo-finalizador'],
            ],
            'Perfumes' => [
                ['Perfume Floral', 'Fragrância floral leve para o dia a dia.', 149.90, 10, 'perfume-floral'],
                ['Colônia Cítrica', 'Colônia refrescante com notas cítricas.', 99.90, 16, 'colonia-citrica'],
                ['Perfume Amadeirado', 'Fragrância marcante com notas amadeiradas.', 179.90, 8, 'perfume-amadeirado'],
                ['Body Splash Frutal', 'Body splash com fragrância frutal suave.', 59.90, 20, 'body-splash'],
            ],
        ];

        foreach ($categories as $name => $items) {
            $category = Category::create(['name' => $name, 'slug' => Str::slug($name)]);

            foreach ($items as [$title, $description, $price, $stock, $image]) {
                $category->products()->create([
                    'name' => $title,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'image' => 'products/'.$image.'.jpg',
                ]);
            $category->products()->create([
                    'name' => $title.' Mini',
                    'description' => 'Versão mini. '.$description,
                    'price' => round($price * 0.6, 2),
                    'stock' => $stock + 5,
                    'image' => 'products/'.$image.'.jpg',
                    'is_active' => $title !== 'Perfume Amadeirado',
                ]);
            }
        }

        $products = Product::where('is_active', true)->where('stock', '>', 0)->get();

        $guest = Cart::create(['session_token' => Str::random(40)]);
        $guest->cartItems()->create(['product_id' => $products[0]->id, 'quantity' => 2]);

        $cart = Cart::create(['user_id' => $customers[0]->id]);
        $cart->cartItems()->create(['product_id' => $products[1]->id, 'quantity' => 1]);
        $cart->cartItems()->create(['product_id' => $products[2]->id, 'quantity' => 3]);

        foreach (['pending', 'paid', 'shipped', 'delivered', 'cancelled'] as $i => $status) {
            $this->order($customers[$i % 3], $status, $products->slice($i * 2, 2));
        }
    }

    private function order(User $user, string $status, $products): void
    {
        $address = Address::create([
            'user_id' => $user->id,
            'recipient_name' => $user->name,
            'zip_code' => '01001-000',
            'street' => 'Praça da Sé',
            'number' => '100',
            'neighborhood' => 'Sé',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'status' => $status,
            'total' => 0,
        ]);

        $total = 0;

        foreach ($products as $product) {
            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 1,
                'unit_price' => $product->price,
            ]);

            $total += $product->price;
        }

        $shipping = $total >= 200 ? 0 : 15;

        $order->update(['shipping' => $shipping, 'total' => $total + $shipping]);

        if (in_array($status, ['paid', 'shipped', 'delivered'])) {
            $order->payment()->create([
                'payment_method' => 'simulado',
                'status' => 'paid',
                'amount' => $order->total,
                'paid_at' => now(),
            ]);
        }
    }
}