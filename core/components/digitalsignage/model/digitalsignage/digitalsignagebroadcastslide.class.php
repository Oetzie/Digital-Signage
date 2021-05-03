<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastSlide extends xPDOSimpleObject
{
    /**
     * @access public.
     * @return Null|Object.
     */
    public function getBroadcast()
    {
        return $this->getOne('Broadcast');
    }

    /**
     * @access public.
     * @return Integer.
     */
    public function getSortIndex()
    {
        $criteria = $this->xpdo->newQuery('DigitalSignageBroadcastSlide', [
            'broadcast_id' => $this->get('broadcast_id')
        ]);

        $criteria->sortby('sortindex', 'DESC');
        $criteria->limit(1);

        $object = $this->xpdo->getObject('DigitalSignageBroadcastSlide', $criteria);

        if ($object) {
            return (int) $object->get('sortindex') + 1;
        }

        return 0;
    }
}
