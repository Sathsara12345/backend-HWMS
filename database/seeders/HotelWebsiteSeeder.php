<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * HotelWebsiteSeeder
 * 
 * This seeder defines EVERY section the website will render.
 * The website components are fixed in structure — customers can only
 * change the field_values inside section_contents, not the structure itself.
 *
 * Section structure:
 *   page_sections  → one row per section (what section exists on what page)
 *   section_contents → one row per field (the actual editable content)
 *
 * To add a new field to a section, add a row to section_contents.
 * To add a new section to a page, add a row to page_sections + its content rows.
 */
class HotelWebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $hotelId = 1; // Grand River Hotel

        // ── STEP 1: Navigation items ───────────────────────────────────────────
        DB::table('navigation_items')->insert([
            [
                'hotel_id'         => $hotelId,
                'label'            => 'Home',
                'slug'             => 'home',
                'url'              => '/',
                'order'            => 1,
                'is_active'        => 1,
                'meta_title'       => 'Grand River Hotel — Luxury Stay in Colombo',
                'meta_description' => 'Experience luxury redefined at Grand River Hotel. World-class rooms, fine dining, and exceptional service.',
                'meta_keywords'    => 'luxury hotel colombo, grand river hotel, 5 star hotel sri lanka',
                'og_title'         => 'Grand River Hotel',
                'og_description'   => 'Luxury beyond imagination.',
                'og_image'         => null,
                'canonical_url'    => null,
                'is_indexable'     => 1,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'hotel_id'         => $hotelId,
                'label'            => 'About',
                'slug'             => 'about',
                'url'              => '/about',
                'order'            => 2,
                'is_active'        => 1,
                'meta_title'       => 'About Us — Grand River Hotel',
                'meta_description' => 'Our story, our team, and our commitment to luxury since 1987.',
                'meta_keywords'    => 'about grand river hotel, hotel history',
                'og_title'         => 'About Grand River Hotel',
                'og_description'   => 'Delivering luxury hospitality since 1987.',
                'og_image'         => null,
                'canonical_url'    => null,
                'is_indexable'     => 1,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'hotel_id'         => $hotelId,
                'label'            => 'Rooms',
                'slug'             => 'rooms',
                'url'              => '/rooms',
                'order'            => 3,
                'is_active'        => 1,
                'meta_title'       => 'Rooms & Suites — Grand River Hotel',
                'meta_description' => 'Browse luxury rooms from LKR 8,000/night.',
                'meta_keywords'    => 'hotel rooms colombo, luxury suites',
                'og_title'         => 'Rooms & Suites',
                'og_description'   => 'Luxury from LKR 8,000/night.',
                'og_image'         => null,
                'canonical_url'    => null,
                'is_indexable'     => 1,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'hotel_id'         => $hotelId,
                'label'            => 'Dining',
                'slug'             => 'dining',
                'url'              => '/dining',
                'order'            => 4,
                'is_active'        => 1,
                'meta_title'       => 'Fine Dining — Grand River Hotel',
                'meta_description' => 'Award-winning cuisine and exceptional dining experiences.',
                'meta_keywords'    => 'hotel restaurant colombo, fine dining',
                'og_title'         => 'Fine Dining at Grand River',
                'og_description'   => 'Award-winning cuisine.',
                'og_image'         => null,
                'canonical_url'    => null,
                'is_indexable'     => 1,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'hotel_id'         => $hotelId,
                'label'            => 'Gallery',
                'slug'             => 'gallery',
                'url'              => '/gallery',
                'order'            => 5,
                'is_active'        => 1,
                'meta_title'       => 'Gallery — Grand River Hotel',
                'meta_description' => 'Explore our hotel through stunning photography.',
                'meta_keywords'    => 'hotel gallery colombo, hotel photos',
                'og_title'         => 'Grand River Hotel Gallery',
                'og_description'   => 'Stunning photography of our hotel.',
                'og_image'         => null,
                'canonical_url'    => null,
                'is_indexable'     => 1,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'hotel_id'         => $hotelId,
                'label'            => 'Contact',
                'slug'             => 'contact',
                'url'              => '/contact',
                'order'            => 6,
                'is_active'        => 1,
                'meta_title'       => 'Contact Us — Grand River Hotel',
                'meta_description' => 'Get in touch. We are available 24/7.',
                'meta_keywords'    => 'contact grand river hotel, hotel phone',
                'og_title'         => 'Contact Grand River Hotel',
                'og_description'   => 'Available 24/7 for your enquiries.',
                'og_image'         => null,
                'canonical_url'    => null,
                'is_indexable'     => 1,
                'created_at'       => now(), 'updated_at' => now(),
            ],
        ]);

        // ── Helper to get nav IDs ──────────────────────────────────────────────
        $nav = fn(string $slug) => DB::table('navigation_items')
            ->where('hotel_id', $hotelId)->where('slug', $slug)->value('id');

        // ── STEP 2: Section templates ──────────────────────────────────────────
        $tplHero = DB::table('section_templates')->insertGetId([
            'name' => 'Hero', 'schema' => json_encode(['fields' => [
                ['key' => 'eyebrow',           'type' => 'text',  'label' => 'Eyebrow Text'],
                ['key' => 'title',             'type' => 'text',  'label' => 'Main Heading'],
                ['key' => 'subtitle',          'type' => 'text',  'label' => 'Subtitle'],
                ['key' => 'description',       'type' => 'text',  'label' => 'Description'],
                ['key' => 'primary_btn_text',  'type' => 'text',  'label' => 'Primary Button Text'],
                ['key' => 'primary_btn_link',  'type' => 'text',  'label' => 'Primary Button Link'],
                ['key' => 'secondary_btn_text','type' => 'text',  'label' => 'Secondary Button Text'],
                ['key' => 'secondary_btn_link','type' => 'text',  'label' => 'Secondary Button Link'],
                ['key' => 'background_image',  'type' => 'image', 'label' => 'Background Image'],
                ['key' => 'background_video',  'type' => 'video', 'label' => 'Background Video'],
            ]]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $tplTextImage = DB::table('section_templates')->insertGetId([
            'name' => 'Text + Image', 'schema' => json_encode(['fields' => [
                ['key' => 'label',   'type' => 'text',  'label' => 'Section Label'],
                ['key' => 'title',   'type' => 'text',  'label' => 'Title'],
                ['key' => 'content', 'type' => 'text',  'label' => 'Content'],
                ['key' => 'image',   'type' => 'image', 'label' => 'Image'],
                ['key' => 'image_alt','type' => 'text', 'label' => 'Image Alt Text'],
                ['key' => 'btn_text','type' => 'text',  'label' => 'Button Text'],
                ['key' => 'btn_link','type' => 'text',  'label' => 'Button Link'],
            ]]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $tplStats = DB::table('section_templates')->insertGetId([
            'name' => 'Stats', 'schema' => json_encode(['fields' => [
                ['key' => 'stat_1_number', 'type' => 'text', 'label' => 'Stat 1 Number'],
                ['key' => 'stat_1_label',  'type' => 'text', 'label' => 'Stat 1 Label'],
                ['key' => 'stat_2_number', 'type' => 'text', 'label' => 'Stat 2 Number'],
                ['key' => 'stat_2_label',  'type' => 'text', 'label' => 'Stat 2 Label'],
                ['key' => 'stat_3_number', 'type' => 'text', 'label' => 'Stat 3 Number'],
                ['key' => 'stat_3_label',  'type' => 'text', 'label' => 'Stat 3 Label'],
                ['key' => 'stat_4_number', 'type' => 'text', 'label' => 'Stat 4 Number'],
                ['key' => 'stat_4_label',  'type' => 'text', 'label' => 'Stat 4 Label'],
            ]]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $tplFeaturedRooms = DB::table('section_templates')->insertGetId([
            'name' => 'Featured Rooms', 'schema' => json_encode(['fields' => [
                ['key' => 'label',   'type' => 'text',   'label' => 'Section Label'],
                ['key' => 'title',   'type' => 'text',   'label' => 'Section Title'],
                ['key' => 'limit',   'type' => 'number', 'label' => 'Rooms to Show'],
                ['key' => 'btn_text','type' => 'text',   'label' => 'Button Text'],
                ['key' => 'btn_link','type' => 'text',   'label' => 'Button Link'],
            ], 'data_source' => 'rooms']),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $tplTestimonials = DB::table('section_templates')->insertGetId([
            'name' => 'Testimonials', 'schema' => json_encode(['fields' => [
                ['key' => 'label',          'type' => 'text', 'label' => 'Section Label'],
                ['key' => 'title',          'type' => 'text', 'label' => 'Section Title'],
                ['key' => 't1_quote',       'type' => 'text', 'label' => 'Testimonial 1 Quote'],
                ['key' => 't1_name',        'type' => 'text', 'label' => 'Testimonial 1 Name'],
                ['key' => 't1_origin',      'type' => 'text', 'label' => 'Testimonial 1 Origin'],
                ['key' => 't2_quote',       'type' => 'text', 'label' => 'Testimonial 2 Quote'],
                ['key' => 't2_name',        'type' => 'text', 'label' => 'Testimonial 2 Name'],
                ['key' => 't2_origin',      'type' => 'text', 'label' => 'Testimonial 2 Origin'],
                ['key' => 't3_quote',       'type' => 'text', 'label' => 'Testimonial 3 Quote'],
                ['key' => 't3_name',        'type' => 'text', 'label' => 'Testimonial 3 Name'],
                ['key' => 't3_origin',      'type' => 'text', 'label' => 'Testimonial 3 Origin'],
            ]]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $tplAmenities = DB::table('section_templates')->insertGetId([
            'name' => 'Amenities', 'schema' => json_encode(['fields' => [
                ['key' => 'label',    'type' => 'text',  'label' => 'Section Label'],
                ['key' => 'title',    'type' => 'text',  'label' => 'Title'],
                ['key' => 'subtitle', 'type' => 'text',  'label' => 'Subtitle'],
                ['key' => 'a1_icon',  'type' => 'text',  'label' => 'Amenity 1 Icon (emoji)'],
                ['key' => 'a1_name',  'type' => 'text',  'label' => 'Amenity 1 Name'],
                ['key' => 'a2_icon',  'type' => 'text',  'label' => 'Amenity 2 Icon'],
                ['key' => 'a2_name',  'type' => 'text',  'label' => 'Amenity 2 Name'],
                ['key' => 'a3_icon',  'type' => 'text',  'label' => 'Amenity 3 Icon'],
                ['key' => 'a3_name',  'type' => 'text',  'label' => 'Amenity 3 Name'],
                ['key' => 'a4_icon',  'type' => 'text',  'label' => 'Amenity 4 Icon'],
                ['key' => 'a4_name',  'type' => 'text',  'label' => 'Amenity 4 Name'],
                ['key' => 'a5_icon',  'type' => 'text',  'label' => 'Amenity 5 Icon'],
                ['key' => 'a5_name',  'type' => 'text',  'label' => 'Amenity 5 Name'],
                ['key' => 'a6_icon',  'type' => 'text',  'label' => 'Amenity 6 Icon'],
                ['key' => 'a6_name',  'type' => 'text',  'label' => 'Amenity 6 Name'],
            ]]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $tplCta = DB::table('section_templates')->insertGetId([
            'name' => 'CTA Banner', 'schema' => json_encode(['fields' => [
                ['key' => 'title',    'type' => 'text',  'label' => 'Title'],
                ['key' => 'subtitle', 'type' => 'text',  'label' => 'Subtitle'],
                ['key' => 'btn_text', 'type' => 'text',  'label' => 'Button Text'],
                ['key' => 'btn_link', 'type' => 'text',  'label' => 'Button Link'],
                ['key' => 'image',    'type' => 'image', 'label' => 'Background Image'],
            ]]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $tplGallery = DB::table('section_templates')->insertGetId([
            'name' => 'Gallery Grid', 'schema' => json_encode(['fields' => [
                ['key' => 'label',   'type' => 'text',  'label' => 'Section Label'],
                ['key' => 'title',   'type' => 'text',  'label' => 'Title'],
                ['key' => 'img_1',   'type' => 'image', 'label' => 'Image 1'],
                ['key' => 'img_1_alt','type' => 'text', 'label' => 'Image 1 Alt'],
                ['key' => 'img_2',   'type' => 'image', 'label' => 'Image 2'],
                ['key' => 'img_2_alt','type' => 'text', 'label' => 'Image 2 Alt'],
                ['key' => 'img_3',   'type' => 'image', 'label' => 'Image 3'],
                ['key' => 'img_3_alt','type' => 'text', 'label' => 'Image 3 Alt'],
                ['key' => 'img_4',   'type' => 'image', 'label' => 'Image 4'],
                ['key' => 'img_4_alt','type' => 'text', 'label' => 'Image 4 Alt'],
                ['key' => 'img_5',   'type' => 'image', 'label' => 'Image 5'],
                ['key' => 'img_5_alt','type' => 'text', 'label' => 'Image 5 Alt'],
                ['key' => 'img_6',   'type' => 'image', 'label' => 'Image 6'],
                ['key' => 'img_6_alt','type' => 'text', 'label' => 'Image 6 Alt'],
            ]]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $tplContact = DB::table('section_templates')->insertGetId([
            'name' => 'Contact', 'schema' => json_encode(['fields' => [
                ['key' => 'label',   'type' => 'text', 'label' => 'Section Label'],
                ['key' => 'title',   'type' => 'text', 'label' => 'Title'],
                ['key' => 'address', 'type' => 'text', 'label' => 'Address'],
                ['key' => 'phone',   'type' => 'text', 'label' => 'Phone'],
                ['key' => 'email',   'type' => 'text', 'label' => 'Email'],
                ['key' => 'checkin', 'type' => 'text', 'label' => 'Check-in Time'],
                ['key' => 'checkout','type' => 'text', 'label' => 'Check-out Time'],
                ['key' => 'map_lat', 'type' => 'text', 'label' => 'Map Latitude'],
                ['key' => 'map_lng', 'type' => 'text', 'label' => 'Map Longitude'],
            ]]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $tplDining = DB::table('section_templates')->insertGetId([
            'name' => 'Dining Feature', 'schema' => json_encode(['fields' => [
                ['key' => 'label',     'type' => 'text',  'label' => 'Section Label'],
                ['key' => 'title',     'type' => 'text',  'label' => 'Restaurant Name'],
                ['key' => 'content',   'type' => 'text',  'label' => 'Description'],
                ['key' => 'hours',     'type' => 'text',  'label' => 'Opening Hours'],
                ['key' => 'cuisine',   'type' => 'text',  'label' => 'Cuisine Type'],
                ['key' => 'dress_code','type' => 'text',  'label' => 'Dress Code'],
                ['key' => 'image',     'type' => 'image', 'label' => 'Restaurant Image'],
                ['key' => 'btn_text',  'type' => 'text',  'label' => 'Button Text'],
                ['key' => 'btn_link',  'type' => 'text',  'label' => 'Button Link'],
            ]]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ── STEP 3: Create all page sections ──────────────────────────────────

        // ────────────────────────────────────────────────────────── HOME PAGE
        $s_home_hero = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('home'),
            'section_name' => 'hero', 'section_key' => 'hero',
            'template_id' => $tplHero, 'data_source' => 'static',
            'order' => 1, 'is_visible' => 1,
            'settings' => json_encode(['overlay' => 0.5]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_home_intro = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('home'),
            'section_name' => 'intro', 'section_key' => 'intro',
            'template_id' => $tplTextImage, 'data_source' => 'static',
            'order' => 2, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_home_stats = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('home'),
            'section_name' => 'stats', 'section_key' => 'stats',
            'template_id' => $tplStats, 'data_source' => 'static',
            'order' => 3, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_home_rooms = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('home'),
            'section_name' => 'featured_rooms', 'section_key' => 'featured_rooms',
            'template_id' => $tplFeaturedRooms, 'data_source' => 'rooms',
            'order' => 4, 'is_visible' => 1,
            'settings' => json_encode(['limit' => 3]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_home_amenities = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('home'),
            'section_name' => 'amenities', 'section_key' => 'amenities',
            'template_id' => $tplAmenities, 'data_source' => 'static',
            'order' => 5, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_home_testimonials = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('home'),
            'section_name' => 'testimonials', 'section_key' => 'testimonials',
            'template_id' => $tplTestimonials, 'data_source' => 'static',
            'order' => 6, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_home_cta = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('home'),
            'section_name' => 'cta', 'section_key' => 'cta',
            'template_id' => $tplCta, 'data_source' => 'static',
            'order' => 7, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ───────────────────────────────────────────────────────── ABOUT PAGE
        $s_about_hero = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('about'),
            'section_name' => 'hero', 'section_key' => 'hero',
            'template_id' => $tplHero, 'data_source' => 'static',
            'order' => 1, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_about_history = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('about'),
            'section_name' => 'our_history', 'section_key' => 'our_history',
            'template_id' => $tplTextImage, 'data_source' => 'static',
            'order' => 2, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_about_mission = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('about'),
            'section_name' => 'our_mission', 'section_key' => 'our_mission',
            'template_id' => $tplTextImage, 'data_source' => 'static',
            'order' => 3, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_about_stats = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('about'),
            'section_name' => 'stats', 'section_key' => 'stats',
            'template_id' => $tplStats, 'data_source' => 'static',
            'order' => 4, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_about_team = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('about'),
            'section_name' => 'team', 'section_key' => 'team',
            'template_id' => $tplTextImage, 'data_source' => 'static',
            'order' => 5, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ───────────────────────────────────────────────────────── ROOMS PAGE
        $s_rooms_hero = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('rooms'),
            'section_name' => 'hero', 'section_key' => 'hero',
            'template_id' => $tplHero, 'data_source' => 'static',
            'order' => 1, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_rooms_list = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('rooms'),
            'section_name' => 'featured_rooms', 'section_key' => 'featured_rooms',
            'template_id' => $tplFeaturedRooms, 'data_source' => 'rooms',
            'order' => 2, 'is_visible' => 1,
            'settings' => json_encode(['limit' => 20]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_rooms_amenities = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('rooms'),
            'section_name' => 'amenities', 'section_key' => 'amenities',
            'template_id' => $tplAmenities, 'data_source' => 'static',
            'order' => 3, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ──────────────────────────────────────────────────────── DINING PAGE
        $s_dining_hero = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('dining'),
            'section_name' => 'hero', 'section_key' => 'hero',
            'template_id' => $tplHero, 'data_source' => 'static',
            'order' => 1, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_dining_main = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('dining'),
            'section_name' => 'main_restaurant', 'section_key' => 'main_restaurant',
            'template_id' => $tplDining, 'data_source' => 'static',
            'order' => 2, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_dining_bar = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('dining'),
            'section_name' => 'bar_lounge', 'section_key' => 'bar_lounge',
            'template_id' => $tplDining, 'data_source' => 'static',
            'order' => 3, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ─────────────────────────────────────────────────────── GALLERY PAGE
        $s_gallery_hero = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('gallery'),
            'section_name' => 'hero', 'section_key' => 'hero',
            'template_id' => $tplHero, 'data_source' => 'static',
            'order' => 1, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_gallery_grid = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('gallery'),
            'section_name' => 'gallery_grid', 'section_key' => 'gallery_grid',
            'template_id' => $tplGallery, 'data_source' => 'static',
            'order' => 2, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ─────────────────────────────────────────────────────── CONTACT PAGE
        $s_contact_hero = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('contact'),
            'section_name' => 'hero', 'section_key' => 'hero',
            'template_id' => $tplHero, 'data_source' => 'static',
            'order' => 1, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $s_contact_info = DB::table('page_sections')->insertGetId([
            'hotel_id' => $hotelId, 'navigation_item_id' => $nav('contact'),
            'section_name' => 'contact_info', 'section_key' => 'contact_info',
            'template_id' => $tplContact, 'data_source' => 'static',
            'order' => 2, 'is_visible' => 1, 'settings' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ── STEP 4: section_contents — all field values ────────────────────────
        $c = fn(int $sid, string $key, string $val, string $type = 'text') =>
            ['section_id' => $sid, 'field_key' => $key, 'field_value' => $val,
             'type' => $type, 'created_at' => now(), 'updated_at' => now()];
        $img = fn(int $sid, string $key) =>
            ['section_id' => $sid, 'field_key' => $key, 'field_value' => null,
             'type' => 'image', 'created_at' => now(), 'updated_at' => now()];
        $vid = fn(int $sid, string $key) =>
            ['section_id' => $sid, 'field_key' => $key, 'field_value' => null,
             'type' => 'video', 'created_at' => now(), 'updated_at' => now()];

        DB::table('section_contents')->insert([
            // ── HOME HERO ────────────────────────────────────────────────────
            $c($s_home_hero, 'eyebrow',            'Welcome to Grand River Hotel'),
            $c($s_home_hero, 'title',              'Luxury Beyond Imagination'),
            $c($s_home_hero, 'subtitle',           'Where every detail is crafted for your comfort'),
            $c($s_home_hero, 'description',        'Experience unparalleled luxury in the heart of Colombo. Award-winning service, world-class amenities, and unforgettable moments await.'),
            $c($s_home_hero, 'primary_btn_text',   'Explore Rooms'),
            $c($s_home_hero, 'primary_btn_link',   '/rooms'),
            $c($s_home_hero, 'secondary_btn_text', 'Our Story'),
            $c($s_home_hero, 'secondary_btn_link', '/about'),
            $img($s_home_hero, 'background_image'),
            $vid($s_home_hero, 'background_video'),

            // ── HOME INTRO ───────────────────────────────────────────────────
            $c($s_home_intro, 'label',   'Discover'),
            $c($s_home_intro, 'title',   'A Legacy of Luxury'),
            $c($s_home_intro, 'content', 'Since 1987, Grand River Hotel has been setting the standard for luxury hospitality in Sri Lanka. Nestled along the banks of the Kelani River, our hotel offers an oasis of tranquility and sophistication in the heart of Colombo.'),
            $img($s_home_intro, 'image'),
            $c($s_home_intro, 'image_alt', 'Grand River Hotel exterior'),
            $c($s_home_intro, 'btn_text',  'Learn More'),
            $c($s_home_intro, 'btn_link',  '/about'),

            // ── HOME STATS ───────────────────────────────────────────────────
            $c($s_home_stats, 'stat_1_number', '35+'),
            $c($s_home_stats, 'stat_1_label',  'Years of Excellence'),
            $c($s_home_stats, 'stat_2_number', '120'),
            $c($s_home_stats, 'stat_2_label',  'Luxury Rooms'),
            $c($s_home_stats, 'stat_3_number', '18'),
            $c($s_home_stats, 'stat_3_label',  'Awards Won'),
            $c($s_home_stats, 'stat_4_number', '98%'),
            $c($s_home_stats, 'stat_4_label',  'Guest Satisfaction'),

            // ── HOME ROOMS ───────────────────────────────────────────────────
            $c($s_home_rooms, 'label',    'Accommodations'),
            $c($s_home_rooms, 'title',    'Our Rooms & Suites'),
            $c($s_home_rooms, 'limit',    '3', 'number'),
            $c($s_home_rooms, 'btn_text', 'View All Rooms'),
            $c($s_home_rooms, 'btn_link', '/rooms'),

            // ── HOME AMENITIES ───────────────────────────────────────────────
            $c($s_home_amenities, 'label',    'World-Class'),
            $c($s_home_amenities, 'title',    'Hotel Amenities'),
            $c($s_home_amenities, 'subtitle', 'Everything you need for a perfect stay'),
            $c($s_home_amenities, 'a1_icon',  '🏊'),
            $c($s_home_amenities, 'a1_name',  'Infinity Pool'),
            $c($s_home_amenities, 'a2_icon',  '🍽'),
            $c($s_home_amenities, 'a2_name',  'Fine Dining'),
            $c($s_home_amenities, 'a3_icon',  '💆'),
            $c($s_home_amenities, 'a3_name',  'Luxury Spa'),
            $c($s_home_amenities, 'a4_icon',  '🏋'),
            $c($s_home_amenities, 'a4_name',  'Fitness Centre'),
            $c($s_home_amenities, 'a5_icon',  '🚗'),
            $c($s_home_amenities, 'a5_name',  'Valet Parking'),
            $c($s_home_amenities, 'a6_icon',  '📶'),
            $c($s_home_amenities, 'a6_name',  'Free High-Speed WiFi'),

            // ── HOME TESTIMONIALS ────────────────────────────────────────────
            $c($s_home_testimonials, 'label',     'Guest Voices'),
            $c($s_home_testimonials, 'title',     'What Our Guests Say'),
            $c($s_home_testimonials, 't1_quote',  'An absolutely extraordinary experience. The service was impeccable and the rooms were stunning. We will definitely return.'),
            $c($s_home_testimonials, 't1_name',   'Sarah & James Mitchell'),
            $c($s_home_testimonials, 't1_origin', 'London, UK'),
            $c($s_home_testimonials, 't2_quote',  'The finest hotel in Colombo without question. The presidential suite was beyond our expectations. Truly world-class.'),
            $c($s_home_testimonials, 't2_name',   'Rajesh Kumar'),
            $c($s_home_testimonials, 't2_origin', 'Mumbai, India'),
            $c($s_home_testimonials, 't3_quote',  'From the moment we arrived, every detail was perfect. The dining experience alone is worth the visit.'),
            $c($s_home_testimonials, 't3_name',   'Marie Dubois'),
            $c($s_home_testimonials, 't3_origin', 'Paris, France'),

            // ── HOME CTA ────────────────────────────────────────────────────
            $c($s_home_cta, 'title',    'Begin Your Stay'),
            $c($s_home_cta, 'subtitle', 'Reserve your room today and experience luxury redefined'),
            $c($s_home_cta, 'btn_text', 'Book a Room'),
            $c($s_home_cta, 'btn_link', '/rooms'),
            $img($s_home_cta, 'image'),

            // ── ABOUT HERO ───────────────────────────────────────────────────
            $c($s_about_hero, 'eyebrow',            'Grand River Hotel'),
            $c($s_about_hero, 'title',              'Our Story'),
            $c($s_about_hero, 'subtitle',           'A Legacy Built on Excellence'),
            $c($s_about_hero, 'primary_btn_text',   'Our History'),
            $c($s_about_hero, 'primary_btn_link',   '#history'),
            $c($s_about_hero, 'secondary_btn_text', ''),
            $c($s_about_hero, 'secondary_btn_link', ''),
            $img($s_about_hero, 'background_image'),
            $vid($s_about_hero, 'background_video'),

            // ── ABOUT HISTORY ────────────────────────────────────────────────
            $c($s_about_history, 'label',     'Our History'),
            $c($s_about_history, 'title',     'Our History'),
            $c($s_about_history, 'content',   'Founded in 1987 by the Perera family, Grand River Hotel began as a vision to bring world-class hospitality to Sri Lanka. What started as a 40-room boutique hotel has grown into the premier luxury destination in Colombo, welcoming guests from over 80 countries. Our commitment to excellence has never wavered — every renovation, every addition has been guided by our founding principle: that true luxury lies in the details.'),
            $img($s_about_history, 'image'),
            $c($s_about_history, 'image_alt', 'Grand River Hotel historic photo'),
            $c($s_about_history, 'btn_text',  ''),
            $c($s_about_history, 'btn_link',  ''),

            // ── ABOUT MISSION ────────────────────────────────────────────────
            $c($s_about_mission, 'label',     'Our Mission'),
            $c($s_about_mission, 'title',     'Our Mission'),
            $c($s_about_mission, 'content',   'We believe hospitality is more than a service — it is an art. Our mission is to create moments that transform a stay into a cherished memory. Through personalised service, exceptional cuisine, and curated experiences, we strive to exceed the expectations of every guest who walks through our doors.'),
            $img($s_about_mission, 'image'),
            $c($s_about_mission, 'image_alt', 'Hotel team serving guests'),
            $c($s_about_mission, 'btn_text',  ''),
            $c($s_about_mission, 'btn_link',  ''),

            // ── ABOUT STATS ──────────────────────────────────────────────────
            $c($s_about_stats, 'stat_1_number', '1987'),
            $c($s_about_stats, 'stat_1_label',  'Founded'),
            $c($s_about_stats, 'stat_2_number', '120'),
            $c($s_about_stats, 'stat_2_label',  'Luxury Rooms'),
            $c($s_about_stats, 'stat_3_number', '500+'),
            $c($s_about_stats, 'stat_3_label',  'Team Members'),
            $c($s_about_stats, 'stat_4_number', '80+'),
            $c($s_about_stats, 'stat_4_label',  'Countries Served'),

            // ── ABOUT TEAM ───────────────────────────────────────────────────
            $c($s_about_team, 'label',     'Our People'),
            $c($s_about_team, 'title',     'Meet Our Leadership Team'),
            $c($s_about_team, 'content',   'Our leadership team brings together decades of hospitality experience from the world\'s finest establishments. United by a passion for service excellence, they guide every aspect of the Grand River experience.'),
            $img($s_about_team, 'image'),
            $c($s_about_team, 'image_alt', 'Hotel leadership team'),
            $c($s_about_team, 'btn_text',  ''),
            $c($s_about_team, 'btn_link',  ''),

            // ── ROOMS HERO ───────────────────────────────────────────────────
            $c($s_rooms_hero, 'eyebrow',            'Grand River Hotel'),
            $c($s_rooms_hero, 'title',              'Rooms & Suites'),
            $c($s_rooms_hero, 'subtitle',           'Every room tells a story of luxury'),
            $c($s_rooms_hero, 'primary_btn_text',   ''),
            $c($s_rooms_hero, 'primary_btn_link',   ''),
            $c($s_rooms_hero, 'secondary_btn_text', ''),
            $c($s_rooms_hero, 'secondary_btn_link', ''),
            $img($s_rooms_hero, 'background_image'),
            $vid($s_rooms_hero, 'background_video'),

            // ── ROOMS LIST ───────────────────────────────────────────────────
            $c($s_rooms_list, 'label',    'Accommodations'),
            $c($s_rooms_list, 'title',    'Choose Your Room'),
            $c($s_rooms_list, 'limit',    '20', 'number'),
            $c($s_rooms_list, 'btn_text', ''),
            $c($s_rooms_list, 'btn_link', ''),

            // ── ROOMS AMENITIES ──────────────────────────────────────────────
            $c($s_rooms_amenities, 'label',    'Included'),
            $c($s_rooms_amenities, 'title',    'Room Amenities'),
            $c($s_rooms_amenities, 'subtitle', 'Every room includes these standard amenities'),
            $c($s_rooms_amenities, 'a1_icon',  '❄'),
            $c($s_rooms_amenities, 'a1_name',  'Air Conditioning'),
            $c($s_rooms_amenities, 'a2_icon',  '📺'),
            $c($s_rooms_amenities, 'a2_name',  '55" Smart TV'),
            $c($s_rooms_amenities, 'a3_icon',  '🛁'),
            $c($s_rooms_amenities, 'a3_name',  'Luxury Bathtub'),
            $c($s_rooms_amenities, 'a4_icon',  '☕'),
            $c($s_rooms_amenities, 'a4_name',  'Nespresso Machine'),
            $c($s_rooms_amenities, 'a5_icon',  '📶'),
            $c($s_rooms_amenities, 'a5_name',  'Gigabit WiFi'),
            $c($s_rooms_amenities, 'a6_icon',  '🌅'),
            $c($s_rooms_amenities, 'a6_name',  'City or River View'),

            // ── DINING HERO ──────────────────────────────────────────────────
            $c($s_dining_hero, 'eyebrow',            'Grand River Hotel'),
            $c($s_dining_hero, 'title',              'Fine Dining'),
            $c($s_dining_hero, 'subtitle',           'An extraordinary culinary journey'),
            $c($s_dining_hero, 'primary_btn_text',   ''),
            $c($s_dining_hero, 'primary_btn_link',   ''),
            $c($s_dining_hero, 'secondary_btn_text', ''),
            $c($s_dining_hero, 'secondary_btn_link', ''),
            $img($s_dining_hero, 'background_image'),
            $vid($s_dining_hero, 'background_video'),

            // ── DINING MAIN ──────────────────────────────────────────────────
            $c($s_dining_main, 'label',      'Main Restaurant'),
            $c($s_dining_main, 'title',      'The River Terrace'),
            $c($s_dining_main, 'content',    'Our signature restaurant offers an unrivalled dining experience with panoramic views of the Kelani River. Executive Chef Prasad Jayasinghe brings decades of experience from Michelin-starred kitchens to craft a menu that celebrates the finest local and international ingredients.'),
            $c($s_dining_main, 'hours',      'Breakfast: 6:30 – 10:30 | Lunch: 12:00 – 15:00 | Dinner: 19:00 – 23:00'),
            $c($s_dining_main, 'cuisine',    'International & Sri Lankan'),
            $c($s_dining_main, 'dress_code', 'Smart Casual'),
            $img($s_dining_main, 'image'),
            $c($s_dining_main, 'btn_text',   'Make a Reservation'),
            $c($s_dining_main, 'btn_link',   '/contact'),

            // ── DINING BAR ───────────────────────────────────────────────────
            $c($s_dining_bar, 'label',      'Bar & Lounge'),
            $c($s_dining_bar, 'title',      'The Grand Lounge'),
            $c($s_dining_bar, 'content',    'Unwind in our elegant bar and lounge, where master mixologists craft exceptional cocktails alongside an extensive wine and spirits list. Live music Friday and Saturday evenings creates the perfect ambience for a memorable evening.'),
            $c($s_dining_bar, 'hours',      'Sunday – Thursday: 12:00 – 01:00 | Friday – Saturday: 12:00 – 02:00'),
            $c($s_dining_bar, 'cuisine',    'Bar Bites & Cocktails'),
            $c($s_dining_bar, 'dress_code', 'Smart Casual'),
            $img($s_dining_bar, 'image'),
            $c($s_dining_bar, 'btn_text',   ''),
            $c($s_dining_bar, 'btn_link',   ''),

            // ── GALLERY HERO ─────────────────────────────────────────────────
            $c($s_gallery_hero, 'eyebrow',            'Grand River Hotel'),
            $c($s_gallery_hero, 'title',              'Gallery'),
            $c($s_gallery_hero, 'subtitle',           'Experience our hotel through your eyes'),
            $c($s_gallery_hero, 'primary_btn_text',   ''),
            $c($s_gallery_hero, 'primary_btn_link',   ''),
            $c($s_gallery_hero, 'secondary_btn_text', ''),
            $c($s_gallery_hero, 'secondary_btn_link', ''),
            $img($s_gallery_hero, 'background_image'),
            $vid($s_gallery_hero, 'background_video'),

            // ── GALLERY GRID ─────────────────────────────────────────────────
            $c($s_gallery_grid, 'label',     'Photo Gallery'),
            $c($s_gallery_grid, 'title',     'Our Hotel'),
            $img($s_gallery_grid, 'img_1'), $c($s_gallery_grid, 'img_1_alt', 'Hotel lobby'),
            $img($s_gallery_grid, 'img_2'), $c($s_gallery_grid, 'img_2_alt', 'Infinity pool'),
            $img($s_gallery_grid, 'img_3'), $c($s_gallery_grid, 'img_3_alt', 'Deluxe room'),
            $img($s_gallery_grid, 'img_4'), $c($s_gallery_grid, 'img_4_alt', 'Restaurant'),
            $img($s_gallery_grid, 'img_5'), $c($s_gallery_grid, 'img_5_alt', 'Spa'),
            $img($s_gallery_grid, 'img_6'), $c($s_gallery_grid, 'img_6_alt', 'River view'),

            // ── CONTACT HERO ─────────────────────────────────────────────────
            $c($s_contact_hero, 'eyebrow',            'We are here for you'),
            $c($s_contact_hero, 'title',              'Contact Us'),
            $c($s_contact_hero, 'subtitle',           'Available 24 hours, 7 days a week'),
            $c($s_contact_hero, 'primary_btn_text',   ''),
            $c($s_contact_hero, 'primary_btn_link',   ''),
            $c($s_contact_hero, 'secondary_btn_text', ''),
            $c($s_contact_hero, 'secondary_btn_link', ''),
            $img($s_contact_hero, 'background_image'),
            $vid($s_contact_hero, 'background_video'),

            // ── CONTACT INFO ─────────────────────────────────────────────────
            $c($s_contact_info, 'label',    'Find Us'),
            $c($s_contact_info, 'title',    'Get in Touch'),
            $c($s_contact_info, 'address',  '123 River Road, Colombo 03, Sri Lanka'),
            $c($s_contact_info, 'phone',    '+94 11 234 5678'),
            $c($s_contact_info, 'email',    'reservations@grandriverhotel.com'),
            $c($s_contact_info, 'checkin',  '3:00 PM'),
            $c($s_contact_info, 'checkout', '12:00 PM'),
            $c($s_contact_info, 'map_lat',  '6.9271'),
            $c($s_contact_info, 'map_lng',  '79.8612'),
        ]);

        // ── STEP 5: Sample rooms ───────────────────────────────────────────────
        DB::table('rooms')->insert([
            ['hotel_id' => $hotelId, 'title' => 'Deluxe Room',        'description' => 'Spacious 45m² room with city view, king bed, and marble bathroom.',         'price' => 15000, 'images' => json_encode([]), 'availability' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['hotel_id' => $hotelId, 'title' => 'Superior River Room','description' => 'Elegant 55m² room with panoramic river views and private balcony.',          'price' => 22000, 'images' => json_encode([]), 'availability' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['hotel_id' => $hotelId, 'title' => 'Junior Suite',       'description' => 'Luxurious 75m² suite with separate living area and butler service.',          'price' => 35000, 'images' => json_encode([]), 'availability' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['hotel_id' => $hotelId, 'title' => 'Presidential Suite', 'description' => 'Our finest 180m² suite with private terrace, jacuzzi, and 24hr butler.', 'price' => 75000, 'images' => json_encode([]), 'availability' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('✅ Hotel website seeded successfully.');
        $this->command->info('   Pages: Home, About, Rooms, Dining, Gallery, Contact');
        $this->command->info('   Sections: ' . DB::table('page_sections')->where('hotel_id', $hotelId)->count());
        $this->command->info('   Content fields: ' . DB::table('section_contents')->count());
    }
}
