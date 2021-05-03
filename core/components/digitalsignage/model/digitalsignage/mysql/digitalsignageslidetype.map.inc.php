<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

$xpdo_meta_map['DigitalSignageSlideType'] = [
    'package'           => 'digitalsignage',
    'version'           => '1.0',
    'table'             => 'digitalsignage_slide_type',
    'extends'           => 'xPDOObject',
    'tableMeta'         => [
        'engine'            => 'InnoDB'
    ],
    'fields'            => [
        'id'                => null,
        'key'               => null,
        'name'              => null,
        'description'       => null,
        'icon'              => null,
        'time'              => null,
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
        'key'               => [
            'dbtype'            => 'varchar',
            'precision'         => '75',
            'phptype'           => 'string',
            'null'              => false
        ],
        'name'              => [
            'dbtype'            => 'varchar',
            'precision'         => '75',
            'phptype'           => 'string',
            'default'           => ''
        ],
        'description'       => [
            'dbtype'            => 'varchar',
            'precision'         => '255',
            'phptype'           => 'string',
            'default'           => ''
        ],
        'icon'              => [
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
        'data'              => [
            'dbtype'            => 'text',
            'phptype'           => 'string',
            'null'              => true,
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
        'Slides'            => [
            'local'             => 'id',
            'class'             => 'DigitalSignageSlide',
            'foreign'           => 'slide_type_id',
            'owner'             => 'local',
            'cardinality'       => 'many'
        ]
    ]
];
