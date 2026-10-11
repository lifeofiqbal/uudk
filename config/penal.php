<?php

return [

    'data_path' => env('PENAL_DATA_PATH', resource_path('data/pasal.json')),

    'max_pasal' => (int) env('PENAL_MAX_PASAL', 3),

    'dpo_multiplier' => (int) env('PENAL_DPO_MULTIPLIER', 2),

    'cache_key' => 'penal-code.payload',

];
