<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        // Create regular user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        // Create categories
        $suits = Category::create([
            'name' => 'Suits',
            'slug' => 'suits',
            'description' => 'Premium tailored suits for every occasion. From classic business attire to elegant evening wear.',
            'image' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800',
        ]);

        $shirts = Category::create([
            'name' => 'Shirts',
            'slug' => 'shirts',
            'description' => 'Sophisticated dress shirts crafted from the finest fabrics. Perfect for business and formal events.',
            'image' => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800',
        ]);

        $accessories = Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => 'Complete your look with our collection of ties, pocket squares, and cufflinks.',
            'image' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800',
        ]);

        // Create suits
        $suitProducts = [
            [
                'name' => 'Classic Navy Two-Piece Suit',
                'description' => 'A timeless navy blue suit crafted from premium Italian wool. Features a slim fit silhouette with notch lapels, two-button closure, and pick-stitched edges. Perfect for business meetings and formal occasions.',
                'price' => 899.00,
                'image' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800',
                'sizes' => ['36R', '38R', '40R', '42R', '44R', '46R'],
                'colors' => ['Navy', 'Charcoal'],
                'material' => '100% Italian Wool',
                'stock' => 25,
                'featured' => true,
            ],
            [
                'name' => 'Charcoal Pinstripe Suit',
                'description' => 'Sophisticated charcoal suit with subtle pinstripe pattern. Tailored with precision from Super 120s wool for exceptional comfort and drape. A boardroom essential.',
                'price' => 1099.00,
                'image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800',
                'sizes' => ['36R', '38R', '40R', '42R', '44R'],
                'colors' => ['Charcoal'],
                'material' => 'Super 120s Wool',
                'stock' => 15,
                'featured' => true,
            ],
            [
                'name' => 'Black Tuxedo',
                'description' => 'Elegant black tuxedo with satin peak lapels. Perfect for black-tie events and formal galas. Includes matching trousers with satin stripe.',
                'price' => 1299.00,
                'image' => 'https://images.unsplash.com/photo-1555069519-127aadedf1ee?w=800',
                'sizes' => ['36R', '38R', '40R', '42R', '44R', '46R'],
                'colors' => ['Black'],
                'material' => 'Wool Blend with Satin Trim',
                'stock' => 10,
                'featured' => true,
            ],
            [
                'name' => 'Light Gray Summer Suit',
                'description' => 'Lightweight summer suit in elegant light gray. Breathable linen-wool blend keeps you cool during warmer months. Half-lined for maximum comfort.',
                'price' => 749.00,
                'image' => 'https://images.unsplash.com/photo-1617137968427-85924c800a22?w=800',
                'sizes' => ['38R', '40R', '42R', '44R'],
                'colors' => ['Light Gray'],
                'material' => 'Linen-Wool Blend',
                'stock' => 20,
                'featured' => false,
            ],
            [
                'name' => 'Burgundy Velvet Dinner Jacket',
                'description' => 'Make a statement with this luxurious burgundy velvet dinner jacket. Features shawl collar and covered buttons. Pair with black trousers for formal events.',
                'price' => 649.00,
                'image' => 'https://images.unsplash.com/photo-1592878904946-b3cd8ae243d0?w=800',
                'sizes' => ['38R', '40R', '42R', '44R'],
                'colors' => ['Burgundy'],
                'material' => 'Cotton Velvet',
                'stock' => 8,
                'featured' => true,
            ],
            [
                'name' => 'Double-Breasted Navy Blazer',
                'description' => 'Classic double-breasted blazer with gold-tone buttons. A versatile piece that works for both casual and semi-formal occasions.',
                'price' => 549.00,
                'image' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800',
                'sizes' => ['36R', '38R', '40R', '42R', '44R', '46R'],
                'colors' => ['Navy', 'Black'],
                'material' => '100% Wool',
                'stock' => 30,
                'featured' => false,
            ],
        ];

        foreach ($suitProducts as $product) {
            Product::create([
                'category_id' => $suits->id,
                'name' => $product['name'],
                'slug' => Str::slug($product['name']) . '-' . Str::random(5),
                'description' => $product['description'],
                'price' => $product['price'],
                'image' => $product['image'],
                'sizes' => $product['sizes'],
                'colors' => $product['colors'],
                'material' => $product['material'],
                'stock' => $product['stock'],
                'featured' => $product['featured'],
                'active' => true,
            ]);
        }

        // Create shirts
        $shirtProducts = [
            [
                'name' => 'White French Cuff Dress Shirt',
                'description' => 'Crisp white dress shirt with French cuffs. Made from Egyptian cotton for superior softness and durability. A wardrobe essential.',
                'price' => 189.00,
                'image' => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800',
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White'],
                'material' => '100% Egyptian Cotton',
                'stock' => 50,
                'featured' => true,
            ],
            [
                'name' => 'Light Blue Oxford Shirt',
                'description' => 'Classic light blue Oxford shirt with button-down collar. Perfect for business casual settings. Soft and breathable cotton fabric.',
                'price' => 149.00,
                'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800',
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['Light Blue', 'White', 'Pink'],
                'material' => '100% Cotton Oxford',
                'stock' => 45,
                'featured' => true,
            ],
            [
                'name' => 'Slim Fit Poplin Shirt',
                'description' => 'Modern slim fit shirt in smooth poplin weave. Features spread collar and barrel cuffs. Ideal for a contemporary professional look.',
                'price' => 129.00,
                'image' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=800',
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['White', 'Light Blue', 'Lavender'],
                'material' => '100% Cotton Poplin',
                'stock' => 60,
                'featured' => false,
            ],
            [
                'name' => 'Bengal Stripe Dress Shirt',
                'description' => 'Bold Bengal stripe pattern adds visual interest to any suit. Cutaway collar design works perfectly with wider tie knots.',
                'price' => 169.00,
                'image' => 'https://images.unsplash.com/photo-1563630423918-b58f07336ac9?w=800',
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['Blue/White', 'Pink/White'],
                'material' => '100% Cotton',
                'stock' => 35,
                'featured' => true,
            ],
            [
                'name' => 'Herringbone Weave Shirt',
                'description' => 'Subtle herringbone texture adds sophistication to this white dress shirt. Semi-spread collar provides versatility for various tie styles.',
                'price' => 179.00,
                'image' => 'https://images.unsplash.com/photo-1603252109303-2751441dd157?w=800',
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['White', 'Blue'],
                'material' => '100% Cotton Herringbone',
                'stock' => 25,
                'featured' => false,
            ],
            [
                'name' => 'Black Formal Evening Shirt',
                'description' => 'Elegant black evening shirt with pleated bib front. Designed to complement tuxedos. Wing collar and French cuffs included.',
                'price' => 199.00,
                'image' => 'https://images.unsplash.com/photo-1620012253295-c15cc3e65df4?w=800',
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['Black', 'White'],
                'material' => '100% Cotton',
                'stock' => 20,
                'featured' => false,
            ],
        ];

        foreach ($shirtProducts as $product) {
            Product::create([
                'category_id' => $shirts->id,
                'name' => $product['name'],
                'slug' => Str::slug($product['name']) . '-' . Str::random(5),
                'description' => $product['description'],
                'price' => $product['price'],
                'image' => $product['image'],
                'sizes' => $product['sizes'],
                'colors' => $product['colors'],
                'material' => $product['material'],
                'stock' => $product['stock'],
                'featured' => $product['featured'],
                'active' => true,
            ]);
        }

        // Create accessories
        $accessoryProducts = [
            [
                'name' => 'Silk Navy Tie',
                'description' => 'Luxurious silk tie in classic navy. Hand-finished with a keeper loop. Width: 3.25 inches.',
                'price' => 89.00,
                'image' => 'https://images.unsplash.com/photo-1589756823695-278bc923f962?w=800',
                'sizes' => ['One Size'],
                'colors' => ['Navy', 'Burgundy', 'Black'],
                'material' => '100% Silk',
                'stock' => 40,
                'featured' => false,
            ],
            [
                'name' => 'Silver Cufflinks Set',
                'description' => 'Elegant sterling silver cufflinks with brushed finish. Comes in presentation box. Perfect gift for the gentleman.',
                'price' => 149.00,
                'image' => 'https://images.unsplash.com/photo-1590548784585-643d2b9f2925?w=800',
                'sizes' => ['One Size'],
                'colors' => ['Silver'],
                'material' => 'Sterling Silver',
                'stock' => 15,
                'featured' => false,
            ],
            [
                'name' => 'Pocket Square Collection',
                'description' => 'Set of three silk pocket squares in complementary patterns. Includes solid, paisley, and geometric designs.',
                'price' => 79.00,
                'image' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=800',
                'sizes' => ['One Size'],
                'colors' => ['Assorted'],
                'material' => '100% Silk',
                'stock' => 30,
                'featured' => false,
            ],
        ];

        foreach ($accessoryProducts as $product) {
            Product::create([
                'category_id' => $accessories->id,
                'name' => $product['name'],
                'slug' => Str::slug($product['name']) . '-' . Str::random(5),
                'description' => $product['description'],
                'price' => $product['price'],
                'image' => $product['image'],
                'sizes' => $product['sizes'],
                'colors' => $product['colors'],
                'material' => $product['material'],
                'stock' => $product['stock'],
                'featured' => $product['featured'],
                'active' => true,
            ]);
        }
    }
}
