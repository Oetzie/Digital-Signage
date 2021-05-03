<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

$publicActions = [
    'web/update',
    'web/data',
    'web/feed',
    'web/image'
];

if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $publicActions, true)) {
    define('MODX_REQP', false);
}

require_once dirname(dirname(dirname(__DIR__))) . '/config.core.php';

require_once MODX_CORE_PATH . 'config/'.MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

if (in_array($_REQUEST['action'], $publicActions, true)) {
    $token = 'modx.' . $modx->context->get('key') . '.user.token';

    if ($modx->user->hasSessionContext($modx->context->get('key'))) {
        $_SERVER['HTTP_MODAUTH']    = $_SESSION[$token];
    } else {
        $_SESSION[$token]           = 0;
        $_SERVER['HTTP_MODAUTH']    = 0;
    }

    $_REQUEST['HTTP_MODAUTH'] = $_SERVER['HTTP_MODAUTH'];
}

$modx->getService('digitalsignage', 'DigitalSignage', $modx->getOption('digitalsignage.core_path', null, $modx->getOption('core_path') . 'components/digitalsignage/') . 'model/digitalsignage/');

if ($modx->digitalsignage instanceof DigitalSignage) {
    $modx->request->handleRequest([
        'processors_path'   => $modx->digitalsignage->config['processors_path'],
        'location'          => ''
    ]);
}
