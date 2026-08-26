<?php

use App\Services\Payment\MockGateway;

return [

    'default' => env('PAYMENT_GATEWAY', 'mock'),

    'drivers' => [
        'mock' => MockGateway::class,
    ],

];
