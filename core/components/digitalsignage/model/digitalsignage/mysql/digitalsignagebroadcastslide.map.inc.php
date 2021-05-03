<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

$xpdo_meta_map['DigitalSignageBroadcastSlide'] = [
    'package'           => 'digitalsignage',
    'version'           => '1.0',
    'table'             => 'digitalsignage_broadcast_slide',
    'extends'           => 'xPDOObject',
    'tableMeta'         => [
        'engine'        => 'InnoDB'
    ],
    'fields'            => [
        'id'                => null,
        'broadcast_id'      => null,
        'slide_id'          => null,
        'sortindex'         => null
    ],
    'fieldMeta'         => [
        'id'                => [
            'dbtype'            => 'int',
            'precision'         => '11',
            'phptype'           => 'integer',
            'index'             => 'pk',
            'generated'         => 'native'
        ],
        'broadcast_id'      => [
            'dbtype'            => 'int',
            'precision'         => '11',
            'phptype'           => 'integer',
            'default'           => 0
        ],
        'slide_id'          => [
            'dbtype'            => 'int',
            'precision'         => '11',
            'phptype'           => 'integer',
            'default'           => 0
        ],
        'sortindex'         => [
            'dbtype'            => 'int',
            'precision'         => '11',
            'phptype'           => 'integer',
            'default'           => 0
        ]
    ],
    'indexes'           => [
        'PRIMARY'           => [
            'alias'             => 'PRIMARY',
            'primary'           => true,
            'unique'            => true,
            'columns'           => [
                'id'                => [
                    'collation'         => 'A',
                    'null'              => false
                ]
            ]
        ]
    ],
    'aggregates'        => [
        'Slide'             => [
            'local'             => 'slide_id',
            'class'             => 'DigitalSignageSlide',
            'foreign'           => 'id',
            'owner'             => 'foreign',
            'cardinality'       => 'one'
        ],
        'Broadcast'         => [
            'local'             => 'broadcast_id',
            'class'             => 'DigitalSignageBroadcast',
            'foreign'           => 'id',
            'owner'             => 'foreign',
            'cardinality'       => 'one'
        ]
    ]
];
