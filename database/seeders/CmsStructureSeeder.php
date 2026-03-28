<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsStructureSeeder extends Seeder
{
    public function run(): void
    {
        // ── Step 1: Navigation items with SEO data ────────────────

        DB::table('navigation_items')->insert([
            [
                'hotel_id'         => 1,
                'label'            => 'Home',
                'slug'             => 'home',
                'url'              => '/',
                'order'            => 1,
                'is_active'        => 1,
                'meta_title'       => 'Grand River Hotel — Luxury Stay in Colombo',
                'meta_description' => 'Experience luxury redefined at Grand River Hotel. World-class rooms, fine dining, and exceptional service in the heart of Colombo.',
                'meta_keywords'    => 'luxury hotel colombo, grand river hotel, hotel sri lanka, 5 star hotel',
                'og_title'         => 'Grand River Hotel — Luxury Stay in Colombo',
                'og_description'   => 'Experience luxury redefined. World-class rooms and exceptional service.',
                'og_image'         => null,
                'canonical_url'    => null,
                'is_indexable'     => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'hotel_id'         => 1,
                'label'            => 'About',
                'slug'             => 'about',
                'url'              => '/about',
                'order'            => 2,
                'is_active'        => 1,
                'meta_title'       => 'About Us — Grand River Hotel',
                'meta_description' => 'Learn about Grand River Hotel\'s history, our team, and our commitment to luxury hospitality since 1987.',
                'meta_keywords'    => 'about grand river hotel, hotel history, luxury hospitality',
                'og_title'         => 'About Grand River Hotel',
                'og_description'   => 'Delivering luxury hospitality since 1987. Discover our story.',
                'og_image'         => null,
                'canonical_url'    => null,
                'is_indexable'     => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'hotel_id'         => 1,
                'label'            => 'Rooms',
                'slug'             => 'rooms',
                'url'              => '/rooms',
                'order'            => 3,
                'is_active'        => 1,
                'meta_title'       => 'Rooms & Suites — Grand River Hotel',
                'meta_description' => 'Browse our luxury rooms and presidential suites. Each room features king beds, rain showers, and panoramic city views.',
                'meta_keywords'    => 'hotel rooms colombo, luxury suites, presidential suite sri lanka',
                'og_title'         => 'Rooms & Suites — Grand River Hotel',
                'og_description'   => 'Luxury rooms starting from LKR 8,000/night. Presidential suites with private terraces.',
                'og_image'         => null,
                'canonical_url'    => null,
                'is_indexable'     => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'hotel_id'         => 1,
                'label'            => 'Contact',
                'slug'             => 'contact',
                'url'              => '/contact',
                'order'            => 4,
                'is_active'        => 1,
                'meta_title'       => 'Contact Us — Grand River Hotel',
                'meta_description' => 'Get in touch with Grand River Hotel. Call +94 11 234 5678 or visit us at 123 River Road, Colombo 03.',
                'meta_keywords'    => 'contact grand river hotel, hotel phone, hotel address colombo',
                'og_title'         => 'Contact Grand River Hotel',
                'og_description'   => 'We are available 24/7. Call us or visit at 123 River Road, Colombo.',
                'og_image'         => null,
                'canonical_url'    => null,
                'is_indexable'     => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);

        // ── Step 2: Section templates ─────────────────────────────

        $heroTemplateId = DB::table('section_templates')->insertGetId([
            'name'       => 'Hero',
            'schema'     => json_encode([
                'fields' => [
                    ['key' => 'title',            'type' => 'text',  'label' => 'Heading'],
                    ['key' => 'subtitle',         'type' => 'text',  'label' => 'Subheading'],
                    ['key' => 'background_image', 'type' => 'image', 'label' => 'Background Image'],
                    ['key' => 'background_video', 'type' => 'video', 'label' => 'Background Video'],
                    ['key' => 'button_text',      'type' => 'text',  'label' => 'CTA Button Text'],
                    ['key' => 'button_url',       'type' => 'text',  'label' => 'CTA Button URL'],
                ],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $galleryTemplateId = DB::table('section_templates')->insertGetId([
            'name'       => 'Gallery',
            'schema'     => json_encode([
                'fields' => [
                    ['key' => 'title',  'type' => 'text', 'label' => 'Section Title'],
                    ['key' => 'images', 'type' => 'json', 'label' => 'Image URLs (JSON array)'],
                ],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $featuredRoomsTemplateId = DB::table('section_templates')->insertGetId([
            'name'       => 'Featured Rooms',
            'schema'     => json_encode([
                'fields' => [
                    ['key' => 'title', 'type' => 'text',   'label' => 'Section Title'],
                    ['key' => 'limit', 'type' => 'number', 'label' => 'Number of Rooms to Show'],
                ],
                'data_source' => 'rooms',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $aboutTemplateId = DB::table('section_templates')->insertGetId([
            'name'       => 'About',
            'schema'     => json_encode([
                'fields' => [
                    ['key' => 'title', 'type' => 'text',  'label' => 'Title'],
                    ['key' => 'body',  'type' => 'text',  'label' => 'Body Text'],
                    ['key' => 'image', 'type' => 'image', 'label' => 'Side Image'],
                ],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $contactTemplateId = DB::table('section_templates')->insertGetId([
            'name'       => 'Contact',
            'schema'     => json_encode([
                'fields' => [
                    ['key' => 'title',   'type' => 'text', 'label' => 'Title'],
                    ['key' => 'address', 'type' => 'text', 'label' => 'Address'],
                    ['key' => 'phone',   'type' => 'text', 'label' => 'Phone'],
                    ['key' => 'email',   'type' => 'text', 'label' => 'Email'],
                    ['key' => 'map_lat', 'type' => 'text', 'label' => 'Map Latitude'],
                    ['key' => 'map_lng', 'type' => 'text', 'label' => 'Map Longitude'],
                ],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── Step 3: Get nav item IDs ──────────────────────────────

        $homeNavId    = DB::table('navigation_items')->where('hotel_id', 1)->where('slug', 'home')->value('id');
        $aboutNavId   = DB::table('navigation_items')->where('hotel_id', 1)->where('slug', 'about')->value('id');
        $roomsNavId   = DB::table('navigation_items')->where('hotel_id', 1)->where('slug', 'rooms')->value('id');
        $contactNavId = DB::table('navigation_items')->where('hotel_id', 1)->where('slug', 'contact')->value('id');

        // ── Step 4: Page sections ─────────────────────────────────

        $heroSectionId = DB::table('page_sections')->insertGetId([
            'hotel_id'           => 1,
            'navigation_item_id' => $homeNavId,
            'section_name'       => 'hero',
            'section_key'        => 'hero',
            'template_id'        => $heroTemplateId,
            'data_source'        => 'static',
            'order'              => 1,
            'is_visible'         => 1,
            'settings'           => json_encode(['overlay' => true, 'full_width' => true]),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $featuredRoomsSectionId = DB::table('page_sections')->insertGetId([
            'hotel_id'           => 1,
            'navigation_item_id' => $homeNavId,
            'section_name'       => 'featured_rooms',
            'section_key'        => 'featured_rooms',
            'template_id'        => $featuredRoomsTemplateId,
            'data_source'        => 'rooms',
            'order'              => 2,
            'is_visible'         => 1,
            'settings'           => json_encode(['limit' => 3]),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $gallerySectionId = DB::table('page_sections')->insertGetId([
            'hotel_id'           => 1,
            'navigation_item_id' => $homeNavId,
            'section_name'       => 'gallery',
            'section_key'        => 'gallery',
            'template_id'        => $galleryTemplateId,
            'data_source'        => 'static',
            'order'              => 3,
            'is_visible'         => 1,
            'settings'           => null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $aboutSectionId = DB::table('page_sections')->insertGetId([
            'hotel_id'           => 1,
            'navigation_item_id' => $aboutNavId,
            'section_name'       => 'about',
            'section_key'        => 'about',
            'template_id'        => $aboutTemplateId,
            'data_source'        => 'static',
            'order'              => 1,
            'is_visible'         => 1,
            'settings'           => null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $contactSectionId = DB::table('page_sections')->insertGetId([
            'hotel_id'           => 1,
            'navigation_item_id' => $contactNavId,
            'section_name'       => 'contact',
            'section_key'        => 'contact',
            'template_id'        => $contactTemplateId,
            'data_source'        => 'static',
            'order'              => 1,
            'is_visible'         => 1,
            'settings'           => null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // ── Step 5: Section contents ──────────────────────────────

        // Hero
        DB::table('section_contents')->insert([
            ['section_id' => $heroSectionId, 'field_key' => 'title',            'field_value' => 'Welcome to Grand River Hotel',  'type' => 'text',  'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $heroSectionId, 'field_key' => 'subtitle',         'field_value' => 'Experience luxury redefined',   'type' => 'text',  'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $heroSectionId, 'field_key' => 'background_image', 'field_value' => null,                            'type' => 'image', 'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $heroSectionId, 'field_key' => 'background_video', 'field_value' => null,                            'type' => 'video', 'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $heroSectionId, 'field_key' => 'button_text',      'field_value' => 'Book Now',                      'type' => 'text',  'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $heroSectionId, 'field_key' => 'button_url',       'field_value' => '/rooms',                        'type' => 'text',  'created_at' => now(), 'updated_at' => now()],
        ]);

        // Featured rooms
        DB::table('section_contents')->insert([
            ['section_id' => $featuredRoomsSectionId, 'field_key' => 'title', 'field_value' => 'Our Rooms', 'type' => 'text',   'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $featuredRoomsSectionId, 'field_key' => 'limit', 'field_value' => '3',         'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Gallery
        DB::table('section_contents')->insert([
            ['section_id' => $gallerySectionId, 'field_key' => 'title',  'field_value' => 'Our Gallery',        'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $gallerySectionId, 'field_key' => 'images', 'field_value' => '["g1.jpg","g2.jpg"]','type' => 'json', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // About
        DB::table('section_contents')->insert([
            ['section_id' => $aboutSectionId, 'field_key' => 'title', 'field_value' => 'Our Story',                                                 'type' => 'text',  'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $aboutSectionId, 'field_key' => 'body',  'field_value' => 'Grand River Hotel has been delivering luxury since 1987.',  'type' => 'text',  'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $aboutSectionId, 'field_key' => 'image', 'field_value' => null,                                                         'type' => 'image', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Contact
        DB::table('section_contents')->insert([
            ['section_id' => $contactSectionId, 'field_key' => 'title',   'field_value' => 'Get in Touch',           'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $contactSectionId, 'field_key' => 'address', 'field_value' => '123 River Road, Colombo','type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $contactSectionId, 'field_key' => 'phone',   'field_value' => '+94 11 234 5678',         'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $contactSectionId, 'field_key' => 'email',   'field_value' => 'johnadmin@example.com',  'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $contactSectionId, 'field_key' => 'map_lat', 'field_value' => '6.9271',                 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['section_id' => $contactSectionId, 'field_key' => 'map_lng', 'field_value' => '79.8612',                'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Step 6: Sample rooms ──────────────────────────────────

        DB::table('rooms')->insert([
            [
                'hotel_id'     => 1,
                'title'        => 'Deluxe Room',
                'description'  => 'Spacious room with city view and king bed.',
                'price'        => 15000.00,
                'images'       => json_encode(['rooms/deluxe1.jpg', 'rooms/deluxe2.jpg']),
                'availability' => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'hotel_id'     => 1,
                'title'        => 'Presidential Suite',
                'description'  => 'Top floor suite with private terrace and butler service.',
                'price'        => 45000.00,
                'images'       => json_encode(['rooms/suite1.jpg', 'rooms/suite2.jpg']),
                'availability' => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'hotel_id'     => 1,
                'title'        => 'Standard Room',
                'description'  => 'Comfortable room with all essential amenities.',
                'price'        => 8000.00,
                'images'       => json_encode(['rooms/standard1.jpg']),
                'availability' => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        $this->command->info('✅ CMS structure seeded successfully.');
        $this->command->info('   - Navigation items: Home, About, Rooms, Contact (with SEO data)');
        $this->command->info('   - Section templates: Hero, Gallery, Featured Rooms, About, Contact');
        $this->command->info('   - Page sections + contents for Hotel 1');
        $this->command->info('   - Sample rooms: Deluxe, Presidential Suite, Standard');
    }
}
