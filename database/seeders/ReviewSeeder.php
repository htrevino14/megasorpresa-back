<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::where('is_active', true)->take(6)->get();
        $users = User::all();

        if ($products->isEmpty() || $users->isEmpty()) {
            return;
        }

        $sampleReviews = [
            [
                'rating' => 5,
                'comment' => '¡Excelente producto! Mi hijo lo amó. La calidad es muy buena y llegó en perfectas condiciones.',
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'comment' => 'Muy buen juguete, tal como se describe en la página. El envío fue rápido.',
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => 'Increíble calidad. Sin duda lo recomiendo. El envoltorio de regalo fue un detalle muy especial.',
                'is_approved' => true,
            ],
            [
                'rating' => 3,
                'comment' => 'El producto está bien, pero las instrucciones venían solo en inglés. Por lo demás todo correcto.',
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'comment' => 'Perfecto para regalar. Mi sobrina quedó encantada. Definitivamente volvería a comprar.',
                'is_approved' => true,
            ],
            [
                'rating' => 2,
                'comment' => 'El color no era exactamente como en la foto, pero funcionó bien.',
                'is_approved' => false,
            ],
        ];

        foreach ($products as $index => $product) {
            $reviewData = $sampleReviews[$index] ?? $sampleReviews[0];
            $user = $users[$index % $users->count()];

            if (! Review::where('product_id', $product->id)->where('user_id', $user->id)->exists()) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => $reviewData['rating'],
                    'comment' => $reviewData['comment'],
                    'is_approved' => $reviewData['is_approved'],
                ]);
            }
        }
    }
}
