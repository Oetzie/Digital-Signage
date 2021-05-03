<?php
/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

if (in_array($modx->event->name, ['OnHandleRequest', 'OnLoadWebDocument'], true)) {
    $instance = $modx->getService('digitalsignageplugins', 'DigitalSignagePlugins', $modx->getOption('digitalsignage.core_path', null, $modx->getOption('core_path') . 'components/digitalsignage/') . 'model/digitalsignage/');

    if ($instance instanceof DigitalSignagePlugins) {
        $method = lcfirst($modx->event->name);

        if (method_exists($instance, $method)) {
            $instance->$method($scriptProperties);
        }
    }
}