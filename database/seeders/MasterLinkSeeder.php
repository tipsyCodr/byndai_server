<?php

namespace Database\Seeders;

use App\Models\MasterLink;
use Illuminate\Database\Seeder;

class MasterLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $links = [
            ['name' => 'phone', 'icon_class' => 'fa fa-phone', 'title' => "Let's Call"],
            ['name' => 'whatsapp', 'icon_class' => 'fa fa-whatsapp', 'title' => "Let's WhatsApp"],
            ['name' => 'website', 'icon_class' => 'fa fa-globe', 'title' => "Visit My Website"],
            ['name' => 'mail', 'icon_class' => 'fa fa-envelope', 'title' => "Send an Email"],
            ['name' => 'facebook', 'icon_class' => 'fa fa-facebook', 'title' => "Connect on Facebook"],
            ['name' => 'instagram', 'icon_class' => 'fa fa-instagram', 'title' => "Follow on Instagram"],
            ['name' => 'location', 'icon_class' => 'fa fa-map-marker', 'title' => "Find My Location"],
            ['name' => 'linkedin', 'icon_class' => 'fa fa-linkedin', 'title' => "Connect on LinkedIn"],
            ['name' => 'youtube', 'icon_class' => 'fa fa-youtube', 'title' => "Watch on YouTube"],
            ['name' => 'google-review', 'icon_class' => 'fa fa-google', 'title' => "Give Google Review"],
            ['name' => 'vimeo', 'icon_class' => 'fa fa-vimeo', 'title' => "Check on Vimeo"],
            ['name' => 'twitter', 'icon_class' => 'fa fa-twitter', 'title' => "Follow on Twitter"],
            ['name' => 'messenger', 'icon_class' => 'fa fa-facebook-messenger', 'title' => "Message on Messenger"],
            ['name' => 'telegram', 'icon_class' => 'fa fa-telegram', 'title' => "Chat on Telegram"],
            ['name' => 'skype', 'icon_class' => 'fa fa-skype', 'title' => "Connect on Skype"],
            ['name' => 'artstation', 'icon_class' => 'fa fa-artstation', 'title' => "View on ArtStation"],
            ['name' => 'behance', 'icon_class' => 'fa fa-behance', 'title' => "Check Behance Portfolio"],
            ['name' => 'github', 'icon_class' => 'fa fa-github', 'title' => "View GitHub"],
            ['name' => 'deviantart', 'icon_class' => 'fa fa-deviantart', 'title' => "See DeviantArt"],
            ['name' => 'dribbble', 'icon_class' => 'fa fa-dribbble', 'title' => "See Dribbble Work"],
            ['name' => 'slack', 'icon_class' => 'fa fa-slack', 'title' => "Join Slack"],
            ['name' => 'wechat', 'icon_class' => 'fa fa-wechat', 'title' => "Chat on WeChat"],
            ['name' => 'tumblr', 'icon_class' => 'fa fa-tumblr', 'title' => "Follow Tumblr"],
            ['name' => 'qzone', 'icon_class' => 'fa fa-star', 'title' => "Visit Qzone"],
            ['name' => 'reddit', 'icon_class' => 'fa fa-reddit', 'title' => "Join Reddit"],
            ['name' => 'tiktok', 'icon_class' => 'fa fa-tiktok', 'title' => "Follow on TikTok"],
            ['name' => 'baidutieda', 'icon_class' => 'fa fa-paw', 'title' => "Visit BaiduTieda"],
            ['name' => 'viber', 'icon_class' => 'fa fa-viber', 'title' => "Chat on Viber"],
            ['name' => 'snapchat', 'icon_class' => 'fa fa-snapchat', 'title' => "Add on Snapchat"],
            ['name' => 'line', 'icon_class' => 'fa fa-line', 'title' => "Connect on Line"],
            ['name' => 'medium', 'icon_class' => 'fa fa-medium', 'title' => "Read on Medium"],
            ['name' => 'signal', 'icon_class' => 'fa fa-signal', 'title' => "Message on Signal"],
            ['name' => 'pinterest', 'icon_class' => 'fa fa-pinterest', 'title' => "Explore Pinterest"],
            ['name' => 'yelp', 'icon_class' => 'fa fa-yelp', 'title' => "Find on Yelp"],
            ['name' => 'venom', 'icon_class' => 'fa fa-venmo', 'title' => "Use Venom"],
            ['name' => 'paypal', 'icon_class' => 'fa fa-paypal', 'title' => "Pay via PayPal"],
            ['name' => 'cashapp', 'icon_class' => 'fa fa-money', 'title' => "Send via CashApp"],
            ['name' => 'dayflash', 'icon_class' => 'fa fa-camera', 'title' => "See on Dayflash"],
            ['name' => 'thedots', 'icon_class' => 'fa fa-circle', 'title' => "Join The Dots"],
            ['name' => 'quora', 'icon_class' => 'fa fa-quora', 'title' => "Answer on Quora"],
            ['name' => 'xbox', 'icon_class' => 'fa fa-xbox', 'title' => "Play on Xbox"],
            ['name' => 'soundcloud', 'icon_class' => 'fa fa-soundcloud', 'title' => "Listen on SoundCloud"],
        ];
        MasterLink::insert($links);
    }
}
