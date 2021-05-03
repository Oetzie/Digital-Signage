<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageSlide extends xPDOSimpleObject
{
    /**
     * @access public.
     * @return Array|Null.
     */
    public function getFormatted()
    {
        if ($type = $this->getSlideType()) {
            return array_merge([
                'id'        => $this->get('id'),
                'source'    => 'intern',
                'slide'     => $type->get('key'),
                'title'     => $this->get('name'),
                'time'      => $this->get('time'),
                'image'     => null
            ], $this->getData());
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
     * @return Array.
     */
    public function getBroadcasts()
    {
        $criteria = $this->xpdo->newQuery('DigitalSignageBroadcast');

        $criteria->innerJoin('DigitalSignageBroadcastSlide', 'DigitalSignageBroadcastSlide', [
            '`DigitalSignageBroadcastSlide`.`broadcast_id` = `DigitalSignageBroadcast`.`id`'
        ]);

        $criteria->where([
            'DigitalSignageBroadcastSlide.slide_id' => $this->get('id')
        ]);

        $criteria->groupby('DigitalSignageBroadcast.id');

        return $this->xpdo->getCollection('DigitalSignageBroadcast', $criteria);
    }

    /**
     * @access public.
     * @return Array.
     */
    public function getData()
    {
        $data = json_decode($this->get('data'), true);

        if ($data) {
            return $data;
        }

        return [];
    }
}
