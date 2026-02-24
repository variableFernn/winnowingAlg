<?php
// config/winnowing.php

return [
    /*
    |--------------------------------------------------------------------------
    | Winnowing Algorithm Parameters
    |--------------------------------------------------------------------------
    */

    // Ukuran K-gram (panjang karakter per segmen)
    'k_gram' => env('WINNOWING_K_GRAM', 5),

    // Ukuran Window
    'window_size' => env('WINNOWING_WINDOW_SIZE', 4),

    // Base untuk rolling hash
    'hash_base' => env('WINNOWING_HASH_BASE', 31),

    // Modulo untuk rolling hash
    'hash_mod' => env('WINNOWING_HASH_MOD', 1000000007),

    /*
    |--------------------------------------------------------------------------
    | Threshold untuk Status
    |--------------------------------------------------------------------------
    */
    'thresholds' => [
        'low' => 25,      // < 25% = Aman
        'medium' => 50,   // 25-50% = Sedang
        // > 50% = Tinggi
    ],
];