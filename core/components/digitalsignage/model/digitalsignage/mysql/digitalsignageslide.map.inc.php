<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

$xpdo_meta_map['DigitalSignageSlide'] = [
    'package'           => 'digitalsignage',
    'version'           => '1.0',
    'table'             => 'digitalsignage_slide',
    'extends'           => 'xPDOObject',
    'tableMeta'         => [
        'engine'            => 'InnoDB'
    ],
    'fields'            => [
        'id'                => null,
        'slide_type_id'     => null,
        'name'              => null,
        'time'              => null,
        'protected'         => null,
        'data'              => null,
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
            'null'              => false
        ],
        'name'              => [
            'dbtype'            => 'varchar',
            'precision'         => '75',
            'phptype'           => 'string',
            'default'           => ''
        ],
        'time'              => [
            'dbtype'            => 'int',
            'precision'         => '3',
            'phptype'           => 'integer',
            'default'           => 10
        ],
        'protected'         => [
            'dbtype'            => 'int',
            'precision'         => '1',
            'phptype'           => 'integer',
            'default'           => 0
        ],
        'data'              => [
            'dbtype'            => 'text',
            'phptype'           => 'string',
            'default'           => ''
        ],
        'active'            => [
            'dbtype'            => 'int',
            'precision'         => '1',
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
        'Broadcasts'        => [
            'local'             => 'id',
            'class'             => 'DigitalSignageBroadcastSlide',
            'foreign'           => 'slide_id',
            'owner'             => 'local',
            'cardinality'       => 'many'
        ]
    ]
];
