<?php namespace Nintendo\Translator\Interfaces;

interface DataTransformer
{
    /**
     * Executes an action on received data
     *
     * @param DataTransporter $transporter
     * @return DataTransporter
     */
    public function execute(DataTransporter $transporter): DataTransporter;
}