<?php

/**
 * Digital Signage
 *
 * Copyright 2021 by Oene Tjeerd de Bruin <modx@oetzie.nl>
 */

class DigitalSignagePlayerScheduleCreateProcessor extends modObjectCreateProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'DigitalSignagePlayerSchedule';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['digitalsignage:default'];

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

        if ($this->getProperty('entire_day') === null) {
            $this->setProperty('entire_day', 0);
        }

        if (empty($this->getProperty('start_time'))) {
            $this->setProperty('start_time', '00:00:00');
        }

        if (empty($this->getProperty('start_date'))) {
            $this->setProperty('start_date', '0000-00-00');
        }

        if (empty($this->getProperty('end_time'))) {
            $this->setProperty('end_time', '00:00:00');
        }

        if (empty($this->getProperty('end_date'))) {
            $this->setProperty('end_date', '0000-00-00');
        }

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function beforeSet()
    {
        if ((int) $this->getProperty('entire_day') === 1) {
            $this->setProperty('start_time', '00:00:00');
            $this->setProperty('end_time', '23:59:59');
        }

        if ($this->getProperty('type') === 'day') {
            $this->setProperty('start_date', '0000-00-00');
            $this->setProperty('end_date', '0000-00-00');
        } else if ($this->getProperty('type') === 'date') {
            $this->setProperty('day', 0);

            if ($this->getProperty('start_date') !== '0000-00-00') {
                $this->setProperty('start_date', date('Y-m-d', strtotime($this->getProperty('start_date'))));
            } else {
                $this->addFieldError('start_date', $this->modx->lexicon('field_required'));
            }

            if ($this->getProperty('end_date') !== '0000-00-00') {
                $this->setProperty('end_date', date('Y-m-d', strtotime($this->getProperty('end_date'))));
            } else {
                $this->addFieldError('end_date', $this->modx->lexicon('field_required'));
            }
        }

        return parent::beforeSet();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function beforeSave()
    {
        $schedules = $this->modx->getCollection('DigitalSignagePlayerSchedule', [
            'id:!='     => $this->getProperty('id'),
            'player_id' => $this->getProperty('player_id')
        ]);

        foreach ($schedules as $schedule) {
            if ($schedule->isScheduledFor($this->getProperties())) {
                if ($this->getProperty('type') === $schedule->get('type')) {
                    $this->addFieldError('type', $this->modx->lexicon('digitalsignage.error_broadcast_schedule_exists', [
                        'schedule' => $schedule->toString()
                    ]));
                }
            }
        }

        return parent::beforeSave();
    }
}

return 'DigitalSignagePlayerScheduleCreateProcessor';
