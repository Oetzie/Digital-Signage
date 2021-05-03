<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignagePlayer extends xPDOSimpleObject
{
    const KEY_CHARS         = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const MODE_LANDSCAPE    = 'landscape';
    const MODE_PORTRAIT     = 'portrait';

    /**
     * @access public.
     * @return String
     */
    public function generateKey()
    {
        $key = '';

        for ($i = 0; $i < 12; $i++) {
            $key .= self::KEY_CHARS[mt_rand(0, strlen(self::KEY_CHARS) - 1)];
        }

        return implode(':', str_split($key, 3));
    }

    /**
     * @access public.
     * @return String.
     */
    public function getMode()
    {
        if (preg_match('/^(\d+)x(\d+)$/', $this->get('resolution'))) {
            list($width, $height) = explode('x', $this->get('resolution'));

            return (int) $width > (int) $height ? self::MODE_LANDSCAPE : self::MODE_PORTRAIT;
        }

        return self::MODE_LANDSCAPE;
    }

    /**
     * @access public.
     * @return String.
     */
    public function getUrl()
    {
        return $this->xpdo->makeUrl($this->xpdo->getOption('digitalsignage.request_resource'), null, [
            'pl' => $this->get('key')
        ], 'full');
    }

    /**
     * @access public.
     * @param String|Integer $date.
     * @return Null|Object.
     */
    public function getBroadcastFor($date)
    {
        if (is_string($date)) {
            $date = strtotime($date);
        }

        if ($broadcast = $this->getBroadcastForType($date, 'date')) {
            return $broadcast;
        }

        if ($broadcast = $this->getBroadcastForType($date, 'day')) {
            return $broadcast;
        }

        return null;
    }

    /**
     * @access public.
     * @param Integer $date.
     * @param String $type.
     * @return Null|Object.
     */
    private function getBroadcastForType($date, $type)
    {
        $criteria = $this->xpdo->newQuery('DigitalSignageBroadcast');

        $criteria->select($this->xpdo->getSelectColumns('DigitalSignageBroadcast', 'DigitalSignageBroadcast'));
        $criteria->select($this->xpdo->getSelectColumns('modResource', 'modResource', 'resource_', ['pagetitle', 'template']));

        $criteria->innerJoin('DigitalSignagePlayerSchedule', 'DigitalSignagePlayerSchedule', [
            'DigitalSignageBroadcast.id = DigitalSignagePlayerSchedule.broadcast_id'
        ]);
        $criteria->innerJoin('modResource', 'modResource', [
            'modResource.id = DigitalSignageBroadcast.resource_id'
        ]);

        $criteria->where([
            'DigitalSignagePlayerSchedule.player_id' => $this->get('id'),
        ]);

        if ($type === 'day') {
            $criteria->where([
                'DigitalSignagePlayerSchedule.type'             => 'day',
                'DigitalSignagePlayerSchedule.day'              => date('w', $date),
                'DigitalSignagePlayerSchedule.start_time:<='    => date('H:i:s', $date),
                'DigitalSignagePlayerSchedule.end_time:>='      => date('H:i:s', $date)
            ]);
        } else if ($type === 'date') {
            $criteria->where([
                'DigitalSignagePlayerSchedule.type'             => 'date',
                'DigitalSignagePlayerSchedule.start_time:<='    => date('H:i:s', $date),
                'DigitalSignagePlayerSchedule.end_time:>='      => date('H:i:s', $date),
            ]);
        }

        return $this->xpdo->getObject('DigitalSignageBroadcast', $criteria);
    }

    /**
     * @access public.
     * @param String|Null $type.
     * @return Array.
     */
    public function getSchedules($type = null)
    {
        $schedules = [];

        if ($type === null || in_array($type, ['day', 'date'], true)) {
            foreach ($this->getMany('PlayerSchedules') as $schedule) {
                if ($type === null || $schedule->get('type') === $type) {
                    $schedules[] = $schedule;
                }
            }
        }

        return $schedules;
    }

    /**
     * @access public.
     * @return Array.
     */
    public function getBroadcasts()
    {
        $broadcasts = [];

        foreach ($this->getSchedules() as $schedule) {
            $broadcast = $schedule->getBroadcast();

            if ($broadcast) {
                if (!isset($broadcasts[(int) $broadcast->get('id')])) {
                    $broadcasts[(int) $broadcast->get('id')] = $broadcast;
                }
            }
        }

        return $broadcasts;
    }

    /**
     * @access public.
     * @return Object|Null.
     */
    public function getCurrentBroadcast()
    {
        if ($this->isOnline()) {
            $criteria = $this->xpdo->newQuery('DigitalSignageBroadcast');

            $criteria->select($this->xpdo->getSelectColumns('DigitalSignageBroadcast', 'DigitalSignageBroadcast'));
            $criteria->select($this->xpdo->getSelectColumns('modResource', 'modResource', 'resource_', ['pagetitle']));

            $criteria->innerJoin('modResource', 'modResource', [
                '`modResource`.`id` = `DigitalSignageBroadcast`.`resource_id`'
            ]);

            $criteria->where([
                'DigitalSignageBroadcast.id' => $this->get('last_broadcast_id')
            ]);

            return $this->xpdo->getObject('DigitalSignageBroadcast', $criteria);
        }

        return null;
    }

    /**
     * @access public.
     * @param Integer $broadcast.
     * @return Boolean.
     */
    public function isOnline($broadcast = null)
    {
        $online = strtotime($this->get('last_online')) + (int) $this->get('last_online_time') >= time();

        if ($broadcast !== null) {
            $online = $online && (int) $broadcast === (int) $this->get('last_broadcast_id');
        }

        return $online;
    }

    /**
     * @access public.
     * @return String.
     */
    public function getLastOnline()
    {
        $timestamp = $this->get('last_online');

        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        if (!$timestamp) {
            return $this->xpdo->lexicon('digitalsignage.time_never');
        }

        $days       = (int) floor((time() - $timestamp) / 86400);
        $minutes    = (int) floor((time() - $timestamp) / 60);

        $output     = [
            'minutes'   => $minutes,
            'hours'     => ceil($minutes / 60),
            'days'      => $days,
            'weeks'     => ceil($days / 7),
            'months'    => ceil($days / 30),
            'date'      => date('Y-m-d H:i:s', $timestamp)
        ];

        if ($days < 1) {
            if ($minutes < 1) {
                return $this->xpdo->lexicon('digitalsignage.time_seconds', $output);
            }

            if ($minutes === 1) {
                return $this->xpdo->lexicon('digitalsignage.time_minute', $output);
            }

            if ($minutes <= 59) {
                return $this->xpdo->lexicon('digitalsignage.time_minutes', $output);
            }

            if ($minutes === 60) {
                return $this->xpdo->lexicon('digitalsignage.time_hour', $output);
            }

            if ($minutes <= 1380) {
                return $this->xpdo->lexicon('digitalsignage.time_hours', $output);
            }

            return $this->xpdo->lexicon('digitalsignage.time_day', $output);
        }

        if ($days === 1) {
            return $this->xpdo->lexicon('digitalsignage.time_day', $output);
        }

        if ($days <= 6) {
            return $this->xpdo->lexicon('digitalsignage.time_days', $output);
        }

        if ($days <= 7) {
            return $this->xpdo->lexicon('digitalsignage.time_week', $output);
        }

        if ($days <= 29) {
            return $this->xpdo->lexicon('digitalsignage.time_weeks', $output);
        }

        if ($days <= 30) {
            return $this->xpdo->lexicon('digitalsignage.time_month', $output);
        }

        if ($days <= 180) {
            return $this->xpdo->lexicon('digitalsignage.time_months', $output);
        }

        return $this->xpdo->lexicon('digitalsignage.time_to_long', $output);
    }
}
