<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignagePlayerSchedule extends xPDOSimpleObject
{
    /**
     * @access public.
     * @return Object|null.
     */
    public function getBroadcast()
    {
        $broadcast = $this->getOne('Broadcast');

        if ($broadcast) {
            if ($broadcast->isValid()) {
                return $broadcast;
            }
        }

        return null;
    }

    /**
     * @access public.
     * @param String $type.
     * @return Boolean.
     */
    public function is($type)
    {
        return strtolower($this->get('type')) === strtolower($type);
    }

    /**
     * @access public.
     * @return Boolean.
     */
    public function isDay()
    {
        return $this->is('day');
    }

    /**
     * @access public.
     * @return Boolean.
     */
    public function isDate()
    {
        return $this->is('date');
    }

    /**
     * @access public.
     * @return Boolean.
     */
    public function isEntireDay()
    {
        return (int) $this->get('entire_day') === 1;
    }

    /**
     * @access public.
     * @param Array $properties.
     * @return Boolean.
     */
    public function isScheduledFor(array $properties = [])
    {
        if (isset($properties['start_time'], $properties['end_time'])) {
            if ($this->isDay()) {
                if (isset($properties['day']) && (int) $this->get('day') === (int) $properties['day']) {
                    $start      = strtotime($this->get('start_time'));
                    $end        = strtotime($this->get('end_time'));

                    $startCheck = strtotime($properties['start_time']);
                    $endCheck   = strtotime($properties['end_time']);

                    if (($startCheck >= $start && $startCheck <= $end) || ($endCheck >= $start && $endCheck <= $end)) {
                        return true;
                    }
                }
            } else if ($this->isDate()) {
                if (isset($properties['start_date'], $properties['end_date'])) {
                    $start      = strtotime($this->get('start_date')  . ' ' . $this->get('start_time'));
                    $end        = strtotime($this->get('end_date')  . ' ' . $this->get('end_time'));
                    $startCheck = strtotime($properties['start_date'] . ' ' . $properties['start_time']);
                    $endCheck   = strtotime($properties['start_date'] . ' ' . $properties['start_time']);

                    if (($startCheck >= $start && $startCheck <= $end) || ($endCheck >= $start && $endCheck <= $end)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @access public.
     * @return String.
     */
    public function getDayOfWeek()
    {
        return date('l', strtotime('Sunday +' . $this->get('day') . ' days'));
    }

    /**
     * @access public.
     * @return String.
     */
    public function toString()
    {
        $string = [];

        if ($this->isDay()) {
            $string[] = $this->xpdo->lexicon(strtolower($this->getDayOfWeek()));

            if ($this->isEntireDay()) {
                $string[] = $this->xpdo->lexicon('digitalsignage.schedule_time_format_entire_day');
            } else {
                $string[] = $this->xpdo->lexicon('digitalsignage.schedule_time_format_set', [
                    'start_time'    => date($this->xpdo->getOption('manager_time_format'), strtotime($this->get('start_time'))),
                    'end_time'      => date($this->xpdo->getOption('manager_time_format'), strtotime($this->get('end_time')))
                ]);
            }
        } else if ($this->isDate()) {
            if ($this->get('start_date') === $this->get('end_date')) {
                $string[] = date($this->xpdo->getOption('manager_date_format'), strtotime($this->get('start_date')));

                if ($this->isEntireDay()) {
                    $string[] = $this->xpdo->lexicon('digitalsignage.schedule_time_format_entire_day');
                } else {
                    $string[] = $this->xpdo->lexicon('digitalsignage.schedule_time_format_set', [
                        'start_time'    => date($this->xpdo->getOption('manager_time_format'), strtotime($this->get('start_time'))),
                        'end_time'      => date($this->xpdo->getOption('manager_time_format'), strtotime($this->get('end_time')))
                    ]);
                }
            } else {
                if ($this->isEntireDay()) {
                    $string[] = $this->xpdo->lexicon('digitalsignage.schedule_date_format_set', [
                        'start_date'    => date($this->xpdo->getOption('manager_date_format'), strtotime($this->get('start_date'))),
                        'end_date'      => date($this->xpdo->getOption('manager_date_format'), strtotime($this->get('end_date')))
                    ]);

                    $string[] = $this->xpdo->lexicon('digitalsignage.schedule_time_format_entire_day');
                } else {
                    $string[] = $this->xpdo->lexicon('digitalsignage.schedule_date_format_set_long', [
                        'start_date'    => date($this->xpdo->getOption('manager_date_format'), strtotime($this->get('start_date'))),
                        'end_date'      => date($this->xpdo->getOption('manager_date_format'), strtotime($this->get('end_date'))),
                        'start_time'    => date($this->xpdo->getOption('manager_time_format'), strtotime($this->get('start_time'))),
                        'end_time'      => date($this->xpdo->getOption('manager_time_format'), strtotime($this->get('end_time')))
                    ]);
                }
            }
        }

        return implode(' ', $string);
    }

    /**
     * @access public.
     * @param String $date1.
     * @param String $date2.
     * @return Array.
     */
    public function getRange($date1 = null, $date2 = null)
    {
        $dates  = [];
        $date1  = $date1 ?: $this->get('start_date');
        $date2  = $date2 ?: $this->get('end_date');
        $ranges = (int) ceil((strtotime($date2) - strtotime($date1)) / (60 * 60 * 24)) + 1;

        if ($this->isDay()) {
            for ($range = 0; $range < $ranges; $range++) {
                $start  = date('Y-m-d ' . $this->get('start_time'), strtotime('+' . $range . ' days', strtotime($date1)));
                $end    = date('Y-m-d ' . $this->get('end_time'), strtotime('+' . $range . ' days', strtotime($date1)));

                if ($this->getDayOfWeek() === date('l', strtotime($start))) {
                    $dates[] = [
                        'start_date'    => $start,
                        'end_date'      => $end,
                        'entire_day'    => date('H:i:s', $start) === '00:00:00' && date('H:i:s', $end) === '23:59:59'
                    ];
                }
            }
        } else if ($this->isDate()) {
            for ($range = 0; $range < $ranges; $range++) {
                $start  = date('Y-m-d 00:00:00', strtotime('+' . $range . ' days', strtotime($date1)));
                $end    = date('Y-m-d 23:59:59', strtotime('+' . $range . ' days', strtotime($date1)));

                if ($range === 0) {
                    $start = date('Y-m-d ' . $this->get('start_time'), strtotime($start));
                }

                if ($range === ($ranges - 1)) {
                    $end = date('Y-m-d ' . $this->get('end_time'), strtotime($end));
                }

                $dates[] = [
                    'start_date'    => $start,
                    'end_date'      => $end,
                    'entire_day'    => date('H:i:s', $start) === '00:00:00' && date('H:i:s', $end) === '23:59:59'
                ];
            }
        }

        return $dates;
    }
}
