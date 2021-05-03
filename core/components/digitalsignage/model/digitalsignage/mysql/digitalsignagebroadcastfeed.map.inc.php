<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

$xpdo_meta_map['DigitalSignageBroadcastFeed'] = [
    'package'           => 'digitalsignage',
    'version'           => '1.0',
    'table'             => 'digitalsignage_broadcast_feed',
    'extends'           => 'xPDOObject',
    'tableMeta'         => [
        'engine'            => 'InnoDB'
    ],
    'fields'            => [
        'id'                => null,
        'slide_type_id'     => null,
        'broadcast_id'      => null,
        'key'               => null,
        'url'               => null,
        'time'              => null,
        'frequency'         => null,
        'active'            => null,
        'editedon'          => null
    ],
    'fieldMeta'         => [
        'id'                => [
            'dbtype'            => 'int',
            'precision'         => '11',
            'phptype'           => 'integer',
            'null'              => false,
            'index'             => 'pk',
            'generated'         => 'native'
        ],
        'slide_type_id'     => [
            'dbtype'            => 'int',
            'precision'         => '11',
            'phptype'           => 'integer',
            'default'           => 0
        ],
        'broadcast_id'      => [
            'dbtype'            => 'int',
            'precision'         => '11',
            'phptype'           => 'integer',
            'default'           => 0
        ],
        'key'               => [
            'dbtype'            => 'varchar',
            'precision'         => '75',
            'phptype'           => 'string',
            'default'           => ''
        ],
        'url'               => [
            'dbtype'            => 'varchar',
            'precision'         => '255',
            'phptype'           => 'string',
            'default'           => ''
        ],
        'time'              => [
            'dbtype'            => 'int',
            'precision'         => '3',
            'phptype'           => 'integer',
            'default'           => 15
        ],
        'frequency'         => [
            'dbtype'            => 'int',
            'precision'         => '3',
            'phptype'           => 'integer',
            'default'           => 2
        ],
        'active'            => [
            'dbtype'            => 'int',
            'precision'         => '3',
            'phptype'           => 'integer',
            'default'           => 1
        ],
        'editedon'          => [
            'dbtype'            => 'timestamp',
            'phptype'           => 'timestamp',
            'attributes'        => 'ON UPDATE CURRENT_TIMESTAMP',
            'null'              => false
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
        'SlideType'         => [
            'local'             => 'slide_type_id',
            'class'             => 'DigitalSignageSlideType',
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
