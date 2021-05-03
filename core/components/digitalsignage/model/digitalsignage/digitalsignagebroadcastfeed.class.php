<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcastFeed extends xPDOSimpleObject
{
    /**
     * @access public.
     * @return Array|Null.
     */
    public function getFormatted()
    {
        if ($type = $this->getSlideType()) {
            return [
                'id'        => $this->get('id'),
                'source'    => 'extern',
                'slide'     => $type->get('key'),
                'time'      => $this->get('time')
            ];
        }

        return null;
    }

    /**
     * @access public.
     * @return String.
     */
    public function getTimeFormatted()
    {
        $minutes = floor((int) $this->get('time') / 60);
        $seconds = (int) $this->get('time') - ($minutes * 60);

        if ($minutes < 60) {
            return sprintf('%02d', $minutes) . ':' . sprintf('%02d', $seconds);
        }

        $hours      = floor($minutes / 60);
        $minutes    = $minutes - ($hours * 60);

        return sprintf('%02d', $hours) . ':' . sprintf('%02d', $minutes) . ':' . sprintf('%02d', $seconds);
    }

    /**
     * @access public.
     * @return Null|Object.
     */
    public function getSlideType()
    {
        return $this->getOne('SlideType');
    }

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
     * @return Array.
     */
    public function getSlides()
    {
        if (preg_match('/^(http|https)/i', $this->get('url'))) {
            $this->xpdo->getService('digitalsignage', 'DigitalSignage', $this->xpdo->getOption('digitalsignage.core_path', null, $this->xpdo->getOption('core_path') . 'components/digitalsignage/') . 'model/digitalsignage/');

            if ($this->xpdo->digitalsignage instanceof DigitalSignage) {
                return $this->xpdo->digitalsignage->getFeedData($this->get('url'));
            }
        }

        return [];
    }
}
