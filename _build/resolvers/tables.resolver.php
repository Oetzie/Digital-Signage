<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;
            $modx->addPackage('digitalsignage', $modx->getOption('digitalsignage.core_path', null, $modx->getOption('core_path') . 'components/digitalsignage/') . 'model/');

            $modx->getManager()->createObjectContainer('DigitalSignageBroadcast');
            $modx->getManager()->createObjectContainer('DigitalSignageBroadcastFeed');
            $modx->getManager()->createObjectContainer('DigitalSignageBroadcastSlide');
            $modx->getManager()->createObjectContainer('DigitalSignagePlayer');
            $modx->getManager()->createObjectContainer('DigitalSignagePlayerSchedule');
            $modx->getManager()->createObjectContainer('DigitalSignageSlide');
            $modx->getManager()->createObjectContainer('DigitalSignageSlideType');

            break;
    }
}

return true;
