<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

$xpdo_meta_map['DigitalSignageBroadcast'] = [
    'package'           => 'digitalsignage',
    'version'           => '1.0',
    'table'             => 'digitalsignage_broadcast',
    'extends'           => 'xPDOObject',
    'tableMeta'         => [
        'engine'            => 'InnoDB'
    ],
    'fields'            => [
        'id'                => null,
        'resource_id'       => null,
        'ticker_url'        => null,
        'protected'         => null,
        'color'             => null,
        'hash'              => null,
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
        'resource_id'       => [
            'dbtype'            => 'int',
            'precision'         => '11',
            'phptype'           => 'integer',
            'default'           => 0
        ],
        'ticker_url'        => [
            'dbtype'            => 'varchar',
            'precision'         => '255',
            'phptype'           => 'string',
            'default'           => ''
        ],
        'protected'         => [
            'dbtype'            => 'int',
            'precision'         => '1',
            'phptype'           => 'integer',
            'default'           => 0
        ],
        'color'             => [
            'dbtype'            => 'int',
            'precision'         => '1',
            'phptype'           => 'integer',
            'default'           => 0
        ],
        'hash'              => [
            'dbtype'            => 'varchar',
            'precision'         => '255',
            'phptype'           => 'string',
            'null'              => true,
            'default'           => ''
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
        'modResource'       => [
            'local'             => 'resource_id',
            'class'             => 'modResource',
            'foreign'           => 'id',
            'owner'             => 'foreign',
            'cardinality'       => 'one'
        ],
        'Slides'            => [
            'local'             => 'id',
            'class'             => 'DigitalSignageBroadcastSlide',
            'foreign'           => 'broadcast_id',
            'owner'             => 'local',
            'cardinality'       => 'many'
        ],
        'Feeds'             => [
            'local'             => 'id',
            'class'             => 'DigitalSignageBroadcastFeed',
            'foreign'           => 'broadcast_id',
            'owner'             => 'local',
            'cardinality'       => 'many'
        ],
        'PlayerSchedules'   => [
            'local'             => 'id',
            'class'             => 'DigitalSignagePlayerSchedule',
            'foreign'           => 'broadcast_id',
            'owner'             => 'local',
            'cardinality'       => 'many'
        ]
    ]
];
