<?php

return [
    'admin_path' => env('ADMIN_PATH', 'admin'),
    'brand' => [
        'name' => 'Harian Merah Putih',
        'tagline' => 'Kebanggaan Indonesia',
    ],

    'categories' => [
        ['name' => 'Nasional', 'slug' => 'nasional'],
        ['name' => 'Politik', 'slug' => 'politik'],
        ['name' => 'Hukrim', 'slug' => 'hukrim'],
        ['name' => 'Kesehatan', 'slug' => 'kesehatan'],
        ['name' => 'Ekbis', 'slug' => 'ekbis'],
        ['name' => 'Pendidikan', 'slug' => 'pendidikan'],
        ['name' => 'Olahraga', 'slug' => 'olahraga'],
        ['name' => 'Lifestyle', 'slug' => 'lifestyle'],
        ['name' => 'Viral', 'slug' => 'viral'],
        ['name' => 'Internasional', 'slug' => 'internasional'],
    ],
];