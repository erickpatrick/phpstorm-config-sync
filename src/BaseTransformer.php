<?php namespace Nintendo\Translator;

use Nintendo\Translator\Interfaces\DataTransformer;
use Nintendo\Translator\Interfaces\DataTransporter;
use Nintendo\Translator\Interfaces\Performer;

abstract class BaseTransformer implements DataTransformer, Performer
{
    /** @var Performer */
    private $next;

    /**
     * @param DataTransformer $next
     */
    public function setNext(DataTransformer $next): void
    {
        $this->next = $next;
    }

    /**
     * @param DataTransporter $transporter
     * @param $newData
     * @return DataTransporter
     */
    public function next(DataTransporter $transporter, $newData): DataTransporter
    {
        $transporter->setData($newData);

        return $this->next->execute($transporter);
    }
}