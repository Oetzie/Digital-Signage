<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignageBroadcast extends xPDOSimpleObject
{
    /**
     * @access public.
     * @var Object|Null.
     */
    public $resource = null;

    /**
     * @access public.
     * @return Object|Null.
     */
    public function getResource()
    {
        if ($this->resource === null) {
            $resource = $this->getOne('modResource');

            if ($resource) {
                $this->resource = $resource;
            }
        }

        return $this->resource;
    }

    /**
     * @access public.
     * @return Boolean.
     */
    public function isValid()
    {
        return $this->getResource() !== null;
    }

    /**
     * @access public.
     * @return Array.
     */
    public function getSlides()
    {
        $criteria = $this->xpdo->newQuery('DigitalSignageSlide');

        $criteria->innerJoin('DigitalSignageSlideType', 'DigitalSignageSlideType', [
            '`DigitalSignageSlide`.`slide_type_id` = `DigitalSignageSlideType`.`id`'
        ]);

        $criteria->innerJoin('DigitalSignageBroadcastSlide', 'DigitalSignageBroadcastSlide', [
            '`DigitalSignageBroadcastSlide`.`slide_id` = `DigitalSignageSlide`.`id`'
        ]);

        $criteria->where([
            'DigitalSignageSlideType.active'            => 1,
            'DigitalSignageBroadcastSlide.broadcast_id' => $this->get('id')
        ]);

        $criteria->groupby('DigitalSignageSlide.id');
        $criteria->sortby('DigitalSignageBroadcastSlide.sortindex');

        return $this->xpdo->getCollection('DigitalSignageSlide', $criteria);
    }

    /**
     * @access public.
     * @return Array.
     */
    public function getFeeds()
    {
        $criteria = $this->xpdo->newQuery('DigitalSignageBroadcastFeed');

        $criteria->where([
            'DigitalSignageBroadcastFeed.active'       => 1,
            'DigitalSignageBroadcastFeed.broadcast_id' => $this->get('id')
        ]);

        return $this->xpdo->getCollection('DigitalSignageBroadcastFeed', $criteria);
    }

    /**
     * @access public.
     * @return Array.
     */
    public function getPlayers()
    {
        $criteria = $this->xpdo->newQuery('DigitalSignagePlayer');

        $criteria->innerJoin('DigitalSignagePlayerSchedule', 'DigitalSignagePlayerSchedule', [
            '`DigitalSignagePlayerSchedule`.`player_id` = `DigitalSignagePlayer`.`id`'
        ]);

        $criteria->where([
            'DigitalSignagePlayerSchedule.broadcast_id' => $this->get('id')
        ]);

        $criteria->groupby('DigitalSignagePlayer.id');

        return $this->xpdo->getCollection('DigitalSignagePlayer', $criteria);
    }

    /**
     * @access public.
     * @return Boolean.
     */
    public function sync()
    {
        $slides = [];

        foreach ((array) $this->getSlides() as $slide) {
            if ((int) $slide->get('active') === 1) {
                if ($data = $slide->getFormatted()) {
                    $slides[] = $data;
                }
            }
        }

        return $this->toExport($slides);
    }

    /**
     * @access public.
     * @return Boolean.
     */
    public function getSync()
    {
        $export = $this->getLastSync();

        if ($export) {
            $timestamp = strtotime($export);

            if (strtotime($this->get('editedon')) <= $timestamp) {
                foreach ($this->getSlides() as $slide) {
                    if (strtotime($slide->get('editedon')) >= $timestamp) {
                        return true;
                    }
                }

                foreach ($this->getFeeds() as $feed) {
                    if (strtotime($feed->get('editedon')) >= $timestamp) {
                        return true;
                    }
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @access public.
     * @return Null|String.
     */
    public function getLastSync()
    {
        $export = $this->getExportFile();

        if ($export && file_exists($export)) {
            return date('Y-m-d H:i:s', filemtime($export));
        }

        return null;
    }

    /**
     * @access public.
     * @param Array $data.
     * @return Boolean.
     */
    public function toExport(array $data = [])
    {
        $export = $this->getExportFile();

        if ($export) {
            $handle = fopen($export, 'wb');

            if ($handle) {
                fwrite($handle, json_encode([
                    'slides' => $data
                ]));

                fclose($handle);

                return true;
            }
        }

        return false;
    }

    /**
     * @access public.
     * @return Array.
     */
    public function fromExport()
    {
        $output = [];
        $export = $this->getExportFile();

        if ($export && file_exists($export)) {
            $handle = fopen($this->getExportFile(), 'rb');

            if ($handle) {
                $slides = fread($handle, filesize($export));
                $data   = json_decode($slides, true);

                if (isset($data['slides'])) {
                    $output = $data['slides'];
                }

                fclose($handle);
            }
        }

        return $output;
    }

    /**
     * @access public.
     * @return String|Boolean.
     */
    public function getExportFile()
    {
        $path = dirname(dirname(__DIR__)) . '/export/';

        if (is_dir($path) || is_writable($path)) {
            return $path . 'broadcast-' . $this->get('id') . '.export';
        }

        return false;
    }
}
