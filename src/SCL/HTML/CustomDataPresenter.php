<?php

namespace SCL\HTML;

interface CustomDataPresenter
{
    /**
     * @param mixed  $value
     * @param string $name
     *
     * @return bool (true if the data was handled)
     */
    public function transformData($value, $name);
}
