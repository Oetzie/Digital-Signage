<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignagePlayerScheduleGetCalendarProcessor extends modProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignagePlayerSchedule';

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'digitalsignage.schedule';

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('digitalsignage', 'DigitalSignage', $this->modx->getOption('digitalsignage.core_path', null, $this->modx->getOption('core_path') . 'components/digitalsignage/') . 'model/digitalsignage/');

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        $data = [];

        foreach ($this->getSchedules('date') as $schedule) {
            foreach ((array) $schedule->getRange() as $date) {
                $ranges = (strtotime($date['end_date']) - strtotime($date['start_date'])) / 1800;

                for ($range = 0; $range < $ranges; $range++) {
                    $start = strtotime($date['start_date']) + ($range * 1800);

                    if (date('H:i:s', $start + 1800) !== '00:00:00') {
                        $end = $start + 1800;
                    } else {
                        $end = $start + 1799;
                    }

                    if (!isset($data[$start])) {
                        $data[$start] = [
                            'cid'   => $schedule->get('broadcast_id'),
                            'title' => $schedule->get('broadcast_pagetitle'),
                            'start' => date('Y-m-d\TH:i:sP', $start),
                            'end'   => date('Y-m-d\TH:i:sP', $end),
                            'type'  => $schedule->get('id')
                        ];
                    }
                }
            }
        }

        foreach ($this->getSchedules('day') as $schedule) {
            foreach ((array) $schedule->getRange($this->getProperty('start'), $this->getProperty('end')) as $date) {
                $ranges = (strtotime($date['end_date']) - strtotime($date['start_date'])) / 1800;

                for ($range = 0; $range < $ranges; $range++) {
                    $start = strtotime($date['start_date']) + ($range * 1800);

                    if (date('H:i:s', $start + 1800) !== '00:00:00') {
                        $end = $start + 1800;
                    } else {
                        $end = $start + 1799;
                    }

                    if (!isset($data[$start])) {
                        $data[$start] = [
                            'cid'   => $schedule->get('broadcast_id'),
                            'title' => $schedule->get('broadcast_pagetitle'),
                            'start' => date('Y-m-d\TH:i:sP', $start),
                            'end'   => date('Y-m-d\TH:i:sP', $end),
                            'type'  => $schedule->get('id')
                        ];
                    }
                }
            }
        }

        ksort($data);

        $output = [];

        foreach ($data as $schedule) {
            if (isset($output[count($output) - 1])) {
                $last = $output[count($output) - 1];

                if ($last['type'] === $schedule['type'] && date('H:i:s', strtotime($last['end'])) !== '23:59:59') {
                    $output[count($output) - 1] = array_merge($last, [
                        'end' => $schedule['end']
                    ]);
                } else {
                    $output[] = array_merge($schedule, [
                        'id' => uniqid()
                    ]);
                }
            } else {
                $output[] = array_merge($schedule, [
                    'id' => uniqid()
                ]);
            }
        }

        return $this->outputArray($output);
    }

    /**
     * @access public.
     * @param String $type.
     * @return Array.
     */
    public function getSchedules($type)
    {
        $criteria = $this->modx->newQuery('DigitalSignagePlayerSchedule');

        $criteria->select($this->modx->getSelectColumns('DigitalSignagePlayerSchedule', 'DigitalSignagePlayerSchedule'));
        $criteria->select($this->modx->getSelectColumns('DigitalSignageBroadcast', 'DigitalSignageBroadcast', null, ['resource_id']));
        $criteria->select($this->modx->getSelectColumns('modResource', 'modResource', 'broadcast_', ['pagetitle']));

        $criteria->innerJoin('DigitalSignageBroadcast', 'DigitalSignageBroadcast', [
            '`DigitalSignageBroadcast`.`id` = `DigitalSignagePlayerSchedule`.`broadcast_id`'
        ]);

        $criteria->innerJoin('modResource', 'modResource', [
            '`modResource`.`id` = `DigitalSignageBroadcast`.`resource_id`'
        ]);

        $criteria->where([
            'DigitalSignagePlayerSchedule.player_id'    => $this->getProperty('player_id'),
            'DigitalSignagePlayerSchedule.type'         => $type
        ]);

        if ($type === 'date') {
            $criteria->where([
                [
                    'DigitalSignagePlayerSchedule.start_date:>='        => $this->getProperty('start'),
                    'AND:DigitalSignagePlayerSchedule.start_date:<='    => $this->getProperty('end')
                ], [
                    'DigitalSignagePlayerSchedule.end_date:>='          => $this->getProperty('start'),
                    'AND:DigitalSignagePlayerSchedule.end_date:<='      => $this->getProperty('end')
                ]
            ], 'OR');
        }

        return $this->modx->getCollection('DigitalSignagePlayerSchedule', $criteria);
    }
}

return 'DigitalSignagePlayerScheduleGetCalendarProcessor';
