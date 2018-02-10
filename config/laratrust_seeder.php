<?php

return [
    'role_structure' => [
        'admin' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u',
            'projects'=>'c,r,u,d',
            'invoices'=>'c,r,u,d',
            'checks'=>'c,r,u,d',
            'expenses'=>'c,r,u,d',
            'contacts'=>'c,r,u,d'
        ],
        'manager' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u',
            'projects'=>'c,r,u,d',
            'invoices'=>'c,r,u,d',
            'checks'=>'c,r,u,d',
            'expenses'=>'c,r,u,d',
            'contacts'=>'c,r,u,d'
        ],
        'client' => [
            'profile' => 'r,u',
            'projects'=>'r,u',
            'invoices'=>'r',
            'checks'=>'r',
        ],
        'user' => [
            'profile' => 'r,u'
        ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
