<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@keminexports.com'],
            [
                'name' => 'Kemin Admin',
                'password' => Hash::make('KeminAdmin2026!#Secure'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Settings & Presenter PIN
        // Default PIN: 8391 (4 digits, non-trivial, verified)
        $defaultPin = '8391';
        $pinHash = Hash::make($defaultPin);

        $settings = [
            'presenter_pin_hash' => $pinHash,
            'pin_length' => '4',
            'lockout_max_attempts' => '5',
            'lockout_window_minutes' => '15',
            'lockout_minutes' => '15',
            'global_lockout_threshold' => '30',
            'presenter_session_hours' => '8',
            'presenter_session_version' => '1',
            'hero_line_1' => 'PRECISION',
            'hero_line_2' => 'Meets Craftsmanship',
            'tagline' => 'Packaging & Print Manufacturer, Sri Lanka',
            'stats_json' => json_encode([
                ['label' => 'Years of Excellence', 'value' => '15+'],
                ['label' => 'Global Brand Partners', 'value' => '120+'],
                ['label' => 'Export Destinations', 'value' => '25+'],
                ['label' => 'Quality Certifications', 'value' => 'ISO & FSC'],
            ]),
        ];

        foreach ($settings as $key => $val) {
            Setting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        // 3. Categories
        $categoriesData = [
            ['name' => 'Rigid Luxury Boxes', 'sort_order' => 1],
            ['name' => 'Corrugated Cartons', 'sort_order' => 2],
            ['name' => 'Garment & Apparel Hangtags', 'sort_order' => 3],
            ['name' => 'Eco-Friendly Kraft Packaging', 'sort_order' => 4],
            ['name' => 'Premium Tea & Food Cartons', 'sort_order' => 5],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                ['name' => $cat['name'], 'sort_order' => $cat['sort_order']]
            );
        }

        // 4. Companies (Trust Logos)
        $companiesData = [
            [
                'name' => 'Spa Ceylon Luxury Ayurveda',
                'description' => 'World’s largest luxury Ayurveda chain with retail stores across Europe, Asia, and North America. Relies on Kemin for rigid embossed gift boxes and soft-touch sleeves.',
                'website' => 'https://spaceylon.com',
                'country' => 'Sri Lanka',
                'industry' => 'Luxury Cosmetics & Wellness',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Dilmah Ceylon Tea',
                'description' => 'Globally acclaimed single-origin tea brand exported to over 100 countries. Kemin provides gold-foiled caddies and export shipper packaging.',
                'website' => 'https://www.dilmahtea.com',
                'country' => 'Sri Lanka',
                'industry' => 'Tea & Beverage',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'MAS Holdings',
                'description' => 'South Asia’s largest design-to-delivery apparel solutions provider, producing for Victoria’s Secret, Nike, and Lululemon. Uses Kemin certified hangtags and RFID swing tags.',
                'website' => 'https://www.masholdings.com',
                'country' => 'Sri Lanka',
                'industry' => 'Apparel & Textiles',
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Brandix Apparel',
                'description' => 'Pioneer in eco-friendly garment manufacturing with net-zero carbon facilities. Partnered with Kemin for FSC-certified biodegradable packaging.',
                'website' => 'https://brandix.com',
                'country' => 'Sri Lanka',
                'industry' => 'Apparel & Fashion',
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Akbar Brothers',
                'description' => 'Sri Lanka’s leading tea exporter for over 30 consecutive years, shipping premium bulk and consumer packs worldwide.',
                'website' => 'https://akbar.com',
                'country' => 'Sri Lanka',
                'industry' => 'Global Tea Exports',
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Ceylon Biscuits Limited (Munchee)',
                'description' => 'Leading FMCG conglomerate in South Asia with major confectionery brands exported across 65+ countries.',
                'website' => 'https://cbllk.com',
                'country' => 'Sri Lanka',
                'industry' => 'Confectionery & FMCG',
                'is_featured' => false,
                'sort_order' => 6,
            ],
        ];

        $companies = [];
        foreach ($companiesData as $comp) {
            $companies[$comp['name']] = Company::firstOrCreate(
                ['slug' => Str::slug($comp['name'])],
                array_merge($comp, [
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ])
            );
        }

        // 5. Products
        $productsData = [
            [
                'category' => 'Rigid Luxury Boxes',
                'name' => 'Matte Black Magnetic Closure Gift Box',
                'short_description' => '1500gsm rigid board with deep matte lamination, concealed magnetic snap, and custom velvet eva foam insert.',
                'description' => '<p>Crafted for ultra-premium cosmetics, luxury watches, and executive gifting. Features <strong>1500gsm heavy greyboard</strong> wrapped in 157gsm coated art paper with anti-scratch matte soft-touch lamination.</p><ul><li>Concealed neodymium magnetic closure</li><li>Gold hot-foil stamping on top lid</li><li>Velvet-flocked laser cut EVA insert</li><li>FSC-certified board</li></ul>',
                'specs' => [
                    ['key' => 'Board Thickness', 'value' => '1500gsm Rigid Greyboard'],
                    ['key' => 'Wrap Paper', 'value' => '157gsm Coated Art Paper'],
                    ['key' => 'Finishing', 'value' => 'Anti-Scratch Soft-Touch Matte + Gold Foil'],
                    ['key' => 'Closure', 'value' => 'Dual Neodymium Magnetic Snaps'],
                    ['key' => 'Lead Time', 'value' => '14-21 Business Days'],
                ],
                'is_featured' => true,
                'sort_order' => 1,
                'companies' => [
                    ['name' => 'Spa Ceylon Luxury Ayurveda', 'note' => 'Exclusive Ayurvedic Spa Gift Collections', 'since' => 2019],
                ],
            ],
            [
                'category' => 'Premium Tea & Food Cartons',
                'name' => 'Gold Foil Embossed Ceylon Tea Caddy',
                'short_description' => '350gsm virgin bleached food-grade board with intricate micro-embossed gold foil stamping and high-gloss spot UV.',
                'description' => '<p>Designed specifically for premium single-origin tea exporters meeting strict international food safety standards (FDA / EU compliant).</p><ul><li>350gsm Virgin SBS bleached board</li><li>Micro-embossing with dual-tone gold foil</li><li>High-barrier inner aqueous coating for freshness retention</li><li>High-speed automated carton folding compatibility</li></ul>',
                'specs' => [
                    ['key' => 'Substrate', 'value' => '350gsm Virgin SBS Food-Grade Board'],
                    ['key' => 'Printing', 'value' => '6-Colour Offset UV Printing'],
                    ['key' => 'Finishing', 'value' => 'Micro-Embossed Gold Foil + Spot UV'],
                    ['key' => 'Barrier', 'value' => 'Moisture Barrier Aqueous Coating'],
                    ['key' => 'Certifications', 'value' => 'ISO 22000, FSC Certified'],
                ],
                'is_featured' => true,
                'sort_order' => 2,
                'companies' => [
                    ['name' => 'Dilmah Ceylon Tea', 'note' => 'Signature Single Origin Silver Jubilee Range', 'since' => 2018],
                    ['name' => 'Akbar Brothers', 'note' => 'Luxury Heritage Tea Export Line', 'since' => 2021],
                ],
            ],
            [
                'category' => 'Corrugated Cartons',
                'name' => 'Heavy-Duty Export Master Shipper (E/B Flute)',
                'short_description' => 'Double-wall 5-ply corrugated carton engineered for maritime transport with 32 ECT crush resistance.',
                'description' => '<p>Our high-performance shipping cartons are engineered to withstand humidity and severe transit shock during international sea freight.</p><ul><li>Double wall 5-ply kraft virgin liner</li><li>Edge Crush Test (ECT): 44 lbs/in minimum</li><li>Water-repellent external treatment</li><li>Precision slotting and clean flap closure</li></ul>',
                'specs' => [
                    ['key' => 'Structure', 'value' => '5-Ply Double Wall (B/C Flute Combination)'],
                    ['key' => 'Bursting Strength', 'value' => '250 PSI Mullen Test'],
                    ['key' => 'Recyclability', 'value' => '100% Biodegradable & Recyclable'],
                    ['key' => 'Printing', 'value' => 'Flexographic High-Definition 3-Colour'],
                ],
                'is_featured' => false,
                'sort_order' => 3,
                'companies' => [
                    ['name' => 'Brandix Apparel', 'note' => 'Apparel Export Cartons for US/EU Shipments', 'since' => 2020],
                    ['name' => 'Ceylon Biscuits Limited (Munchee)', 'note' => 'Global Master Distribution Shipper', 'since' => 2022],
                ],
            ],
            [
                'category' => 'Garment & Apparel Hangtags',
                'name' => 'FSC-Certified Recycled Cotton Swing Tag',
                'short_description' => '700gsm cotton-pulp artisanal card with debossed brand monogram, eyelet reinforcement, and raw hemp cord.',
                'description' => '<p>Sustainable luxury hangtags for ethical fashion brands. Made from 100% pre-consumer recycled cotton waste with no chlorine bleaching.</p><ul><li>700gsm tactile heavyweight cotton board</li><li>Blind deep debossing on face and back</li><li>Organic raw unbleached hemp string</li><li>Recycled brass metal eyelet</li></ul>',
                'specs' => [
                    ['key' => 'Material', 'value' => '700gsm Recycled Cotton Pulp'],
                    ['key' => 'Texture', 'value' => 'Artisanal Natural Laid Surface'],
                    ['key' => 'Finishing', 'value' => 'Deep Blind Deboss + Letterpress Black Ink'],
                    ['key' => 'Attachment', 'value' => 'Raw Hemp Cord with Antique Brass Eyelet'],
                ],
                'is_featured' => true,
                'sort_order' => 4,
                'companies' => [
                    ['name' => 'MAS Holdings', 'note' => 'Eco-Performance Apparel Line', 'since' => 2021],
                    ['name' => 'Brandix Apparel', 'note' => 'Zero-Carbon Organic Cotton Collection', 'since' => 2023],
                ],
            ],
            [
                'category' => 'Eco-Friendly Kraft Packaging',
                'name' => 'Kraft Resealable Stand-Up Pouch with Valve',
                'short_description' => 'Multi-layer high-barrier natural kraft paper with compostable PLA lining and one-way degassing valve.',
                'description' => '<p>Optimal for roasted coffee beans, dried fruits, and organic teas requiring airtight preservation without environmental toll.</p><ul><li>High-barrier home-compostable inner lining</li><li>Zip-lock resealable channel with easy-tear notch</li><li>One-way degassing aroma valve</li><li>Natural rustic kraft exterior with soy-ink print</li></ul>',
                'specs' => [
                    ['key' => 'Outer Layer', 'value' => '120gsm Natural Brown Kraft'],
                    ['key' => 'Barrier Layer', 'value' => 'Compostable Bio-Film PLA'],
                    ['key' => 'Closure', 'value' => 'Press-to-Close Airtight Zipper'],
                    ['key' => 'Degassing', 'value' => 'Integrated One-Way Degassing Valve'],
                ],
                'is_featured' => true,
                'sort_order' => 5,
                'companies' => [
                    ['name' => 'Dilmah Ceylon Tea', 'note' => 'Organic Loose Leaf Bio-Pouches', 'since' => 2022],
                ],
            ],
            [
                'category' => 'Rigid Luxury Boxes',
                'name' => 'Custom Book-Style Presentation Box',
                'short_description' => 'Hinged book-style box with concealed spine ribbons and metallic copper foil detailing.',
                'description' => '<p>Unfolds like a commemorative volume. Popular for VIP corporate introductions, anniversary releases, and jewellery presentations.</p>',
                'specs' => [
                    ['key' => 'Construction', 'value' => 'Book-Hinged Rigid Box'],
                    ['key' => 'Finish', 'value' => 'Matte Soft-Touch with Copper Foil'],
                    ['key' => 'Closure', 'value' => 'Satin Ribbon Pull & Magnetic Lock'],
                ],
                'is_featured' => false,
                'sort_order' => 6,
                'companies' => [
                    ['name' => 'Spa Ceylon Luxury Ayurveda', 'note' => 'Royal Heritage Gift Cases', 'since' => 2020],
                ],
            ],
        ];

        foreach ($productsData as $prodData) {
            $cat = $categories[$prodData['category']] ?? null;
            $product = Product::firstOrCreate(
                ['slug' => Str::slug($prodData['name'])],
                [
                    'category_id' => $cat?->id,
                    'name' => $prodData['name'],
                    'short_description' => $prodData['short_description'],
                    'description' => $prodData['description'],
                    'specs' => $prodData['specs'],
                    'is_featured' => $prodData['is_featured'],
                    'is_active' => true,
                    'sort_order' => $prodData['sort_order'],
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ]
            );

            // Attach companies with pivot data
            if (! empty($prodData['companies'])) {
                foreach ($prodData['companies'] as $pComp) {
                    $cModel = $companies[$pComp['name']] ?? null;
                    if ($cModel) {
                        $product->companies()->syncWithoutDetaching([
                            $cModel->id => [
                                'relationship_note' => $pComp['note'] ?? null,
                                'since_year' => $pComp['since'] ?? null,
                                'sort_order' => 0,
                            ],
                        ]);
                    }
                }
            }
        }
    }
}
