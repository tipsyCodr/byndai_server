<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\MasterUserDetail;
use App\Models\BusinessInfo;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductLink;
use App\Models\UserLink;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create a user
        $user = User::firstOrCreate([
            'user_name' => 'demo_ceo',
        ], [
            'user_mobile' => '9876543210',
            'email' => 'ceo@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Clean up previous partial seeds for this user
        DB::table('master_user_details')->where('user_id', $user->id)->delete();
        DB::table('business_infos')->where('user_id', $user->id)->delete();
        $prods = DB::table('products')->where('user_id', $user->id)->pluck('id');
        DB::table('product_links')->whereIn('prod_id', $prods)->delete();
        DB::table('products')->where('user_id', $user->id)->delete();
        DB::table('user_links')->where('user_id', $user->id)->delete();

        // 2. Insert Master User Details
        DB::table('master_user_details')->insert([
            'user_id' => $user->id,
            'fname' => 'John Doe',
            'desg' => 'Chief Executive Officer',
            'email_one' => 'ceo@example.com',
            'mobile_one' => '9876543210',
            // Default placeholders can be used if fields are nullable
            'address' => '123 Tech Park, Innovation Hub',
        ]);

        // 3. Insert Business Details
        DB::table('business_infos')->insert([
            'user_id' => $user->id,
            'legal_name' => 'Acme Innovation Corp',
            'description' => "We are a futuristic tech agency specializing in AI-driven business solutions and smart networking. Over the past decade, we've helped hundreds of startups scale their digital presence seamlessly.",
            'address' => '123 Tech Park, Innovation Hub',
            'contact_email' => 'hello@acmeinnovation.corp',
            'website_url' => 'https://acmeinnovation.corp',
            'gst_no' => '22AAAAA0000A1Z5',
        ]);

        // 4. Products with Links
        // Product 1
        $product1Id = DB::table('products')->insertGetId([
            'user_id' => $user->id,
            'product_name' => 'AI Networking Tool',
            'product_price' => '499.00',
        ]);

        DB::table('product_links')->insert([
            ['prod_id' => $product1Id, 'title' => 'Live Demo', 'links' => 'https://example.com/demo1'],
            ['prod_id' => $product1Id, 'title' => 'Buy Now', 'links' => 'https://example.com/buy1'],
        ]);

        // Product 2
        $product2Id = DB::table('products')->insertGetId([
            'user_id' => $user->id,
            'product_name' => 'Enterprise CRM',
            'product_price' => '999.00',
        ]);

        DB::table('product_links')->insert([
            ['prod_id' => $product2Id, 'title' => 'Documentation', 'links' => 'https://example.com/docs2'],
            ['prod_id' => $product2Id, 'title' => 'Schedule Call', 'links' => 'https://example.com/calendly'],
        ]);

        // Product 3
        $product3Id = DB::table('products')->insertGetId([
            'user_id' => $user->id,
            'product_name' => 'Custom Software Dev',
            'product_price' => '1500.00',
        ]);
        
        DB::table('product_links')->insert([
            ['prod_id' => $product3Id, 'title' => 'Portfolio', 'links' => 'https://example.com/portfolio'],
        ]);

        // 5. Social Links
        // Retrieve some master links to associate with the user
        $masterLinks = DB::table('master_links')->limit(6)->get();

        $customLinksToMake = [
            ['name' => 'LinkedIn', 'data' => 'https://linkedin.com/in/johndoe'],
            ['name' => 'Twitter/X', 'data' => 'https://x.com/johndoe'],
            ['name' => 'Facebook', 'data' => 'https://facebook.com/johndoe'],
            ['name' => 'Instagram', 'data' => 'https://instagram.com/johndoe'],
            ['name' => 'My Portfolio', 'data' => 'https://portfolio.johndoe.me'],
            ['name' => 'Book a Meeting', 'data' => 'https://calendly.com'],
        ];

        foreach($customLinksToMake as $idx => $linkObj) {
            $ml = isset($masterLinks[$idx]) ? $masterLinks[$idx]->id : (isset($masterLinks[0]) ? $masterLinks[0]->id : null);
            if($ml) {
                DB::table('user_links')->insert([
                    'user_id' => $user->id,
                    'link_id' => $ml,
                    'title' => $linkObj['name'],
                    'link_data' => $linkObj['data'],
                ]);
            }
        }
    }
}
