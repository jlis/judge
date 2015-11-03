<?php

namespace jlis\Judge\Adapters;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
interface AdapterInterface
{
    /**
     * @return array
     */
    public function getFeatures();

    /**
     * @return array
     */
    public function getValues();
}
