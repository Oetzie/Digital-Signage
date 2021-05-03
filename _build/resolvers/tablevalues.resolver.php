<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

$package = 'DigitalSignage';

$settings = [];

$contexts = [[
    'key'       => 'ds',
    'name'      => 'Digital Signage',
    'settings'      => [
        'base_url'      => [
            'value'         => '/ds/',
        ],
        'site_status'   => [
            'value'         => '1'
        ],
        'site_url'      => [
            'value'         => 'http://{http_host}/ds/'
        ],
        'mgr_tree_icon_context' => [
            'value'         => 'icon-play-circle',
            'area'          => 'manager'
        ]
    ]
]];

$resources = [[
    'pagetitle'     => 'Home',
    'content'       => '',
    'content_type'  => '.json',
    'uri'           => 'home.json',
    'setting'       => 'request_resource'
]];

$slidetypes = [[
    'key'           => 'default',
    'time'          => '10',
    'data'          => '{"image":{"xtype":"modx-combo-browser","default_value":"","label":"","description":"","menuindex":1,"required":null,"values":""},"ticker":{"xtype":"checkbox","default_value":"1","label":"","description":"","menuindex":2,"required":null,"values":""},"content":{"xtype":"richtext","default_value":"","label":"","description":"","menuindex":0},"fullscreen":{"xtype":"checkbox","required":null,"values":"","default_value":"1","label":"","description":"","menuindex":3}}',
    'active'        => 1
], [
    'key'           => 'media',
    'icon'          => 'picture-o',
    'time'          => '10',
    'data'          => '{"image":{"xtype":"modx-combo-browser","default_value":"","label":"","description":"","menuindex":0},"fullscreen":{"xtype":"checkbox","default_value":"1","label":"","description":"","menuindex":4},"ticker":{"xtype":"checkbox","default_value":"1","label":"","description":"","menuindex":3},"video_extern":{"xtype":"textfield","required":null,"values":"","default_value":"","label":"","description":"","menuindex":1}}',
    'active'        => 1
], [
    'key'           => 'buienradar',
    'icon'          => 'cloud',
    'time'          => '10',
    'data'          => '{"location":{"xtype":"textfield","default_value":"","label":"","description":"","value":"","menuindex":0},"fullscreen":{"xtype":"checkbox","default_value":"1","label":"","description":"","menuindex":2},"ticker":{"xtype":"checkbox","default_value":"1","label":"","description":"","menuindex":1}}',
    'active'        => 1
], [
    'key'           => 'feed',
    'icon'          => 'rss',
    'time'          => '10',
    'data'          => '{"url":{"xtype":"textfield","default_value":"","label":"","description":"","menuindex":0},"limit":{"xtype":"combo","default_value":"3","label":"","description":"","menuindex":1,"required":"1","values":"2 items==2||3 items==3"},"fullscreen":{"xtype":"checkbox","default_value":"1","label":"","description":"","menuindex":3},"ticker":{"xtype":"checkbox","default_value":"1","label":"","description":"","menuindex":2}}',
    'active'        => 1
], [
    'key'           => 'payoff',
    'icon'          => 'bullhorn',
    'time'          => '10',
    'data'          => '{"content":{"xtype":"textfield","required":"1","values":"","default_value":"","label":"","description":"","menuindex":1},"size":{"xtype":"combo","required":"1","values":"Standaard==size1||Groot==size2||Groter==size3||Grootst==size4","default_value":"size1","label":"","description":"","menuindex":2},"ticker":{"xtype":"checkbox","required":null,"values":"","default_value":"1","label":"","description":"","menuindex":3},"fullscreen":{"xtype":"checkbox","required":null,"values":"","default_value":"1","label":"","description":"","menuindex":4}}',
    'active'        => 1
], [
    'key'           => 'countdown',
    'icon'          => 'calendar',
    'time'          => '10',
    'data'          => '{"content":{"xtype":"richtext","required":null,"values":"","default_value":"","label":"","description":"","menuindex":1},"date":{"xtype":"xdatetime","required":"1","values":"","default_value":"","label":"","description":"","menuindex":2},"ticker":{"xtype":"checkbox","required":null,"values":"","default_value":"1","label":"","description":"","menuindex":3},"fullscreen":{"xtype":"checkbox","required":null,"values":"","default_value":"1","label":"","description":"","menuindex":4}}',
    'active'        => 1
], [
    'key'           => 'clock',
    'icon'          => 'clock-o',
    'time'          => '10',
    'data'          => '{"ticker":{"xtype":"checkbox","required":null,"values":"","default_value":"1","label":"","description":"","menuindex":1},"fullscreen":{"xtype":"checkbox","required":null,"values":"","default_value":"1","label":"","description":"","menuindex":2}}',
    'active'        => 1
], [
    'key'           => 'iframe',
    'icon'          => 'window-restore',
    'time'          => '10',
    'data'          => '{"url":{"xtype":"textfield","required":"1","values":"","default_value":"","label":"","description":"","menuindex":1},"ticker":{"xtype":"checkbox","required":null,"values":"","default_value":"1","label":"","description":"","menuindex":2},"fullscreen":{"xtype":"checkbox","required":null,"values":"","default_value":"1","label":"","description":"","menuindex":3}}',
    'active'        => 1
]];

$success = false;

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx =& $object->xpdo;

            foreach ($contexts as $context) {
                if (isset($context['key'])) {
                    $settings['context'] = $context['key'];

                    $object = $modx->getObject('modContext', [
                        'key' => $context['key']
                    ]);

                    if (!$object) {
                        $object = $modx->newObject('modContext');
                    }

                    $object->fromArray($context, '', true, true);
                    $object->save();

                    if (isset($context['settings'])) {
                        foreach ((array) $context['settings'] as $key => $setting) {
                            $settingObject = $modx->getObject('modContextSetting', [
                                'context_key'   => $object->get('key'),
                                'key'           => $key,
                            ]);

                            if (!$settingObject) {
                                $settingObject = $modx->newObject('modContextSetting');

                                $settingObject->fromArray(array_merge([
                                    'context_key'   => $context['key'],
                                    'key'           => $key,
                                    'namespace'     => 'core',
                                    'area'          => 'core'
                                ], $setting), '', true, true);

                                $settingObject->save();
                            }
                        }
                    }
                }
            }

            if (isset($settings['context'])) {
                foreach ($resources as $key => $resource) {
                    $object = $modx->getObject('modResource', [
                        'context_key'   => $settings['context'],
                        'uri'           => $resource['uri']
                    ]);

                    if (!$object) {
                        $object = $modx->newObject('modResource');
                    }

                    if (isset($resource['content_type'])) {
                        $contentTypeObject = $modx->getObject('modContentType', [
                            'file_extensions' => $resource['content_type']
                        ]);

                        if ($contentTypeObject) {
                            $resource['content_type'] = $contentTypeObject->get('id');
                        } else {
                            unset($resource['content_type']);
                        }
                    }

                    $object->fromArray(array_merge([
                        'context_key'   => $settings['context'],
                        'published'     => 1,
                        'deleted'       => 0,
                        'hidemenu'      => 0,
                        'richtext'      => 0,
                        'cacheable'     => 0,
                        'template'      => '',
                        'menuindex'     => $key
                    ], $resource), '', true, true);

                    $object->save();

                    if (isset($resource['setting'])) {
                        $settings[$resource['setting']] = $object->get('id');
                    }
                }
            }


            foreach ($settings as $key => $setting) {
                $object = $modx->getObject('modSystemSetting', [
                    'key' => strtolower($package) . '.' . $key
                ]);

                if ($object) {
                    $object->fromArray([
                        'value' => $setting
                    ]);

                    $object->save();
                }
            }

            foreach ($slidetypes as $slideType) {
                if (isset($slideType['key'])) {
                    $object = $modx->getObject('DigitalSignageSlideType', [
                        'key' => $slideType['key']
                    ]);

                    if (!$object) {
                        $object = $modx->newObject('DigitalSignageSlideType');
                    }

                    $object->fromArray($slideType);
                    $object->save();
                }
            }

            $success = true;

            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success = true;

            break;
    }
}

return $success;
