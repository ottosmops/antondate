<?php

namespace Ottosmops\Antondate;

trait HasAntondateTrait
{
    public function getDateLabelAttribute()
    {
        return \Ottosmops\Antondate\DateHelper::renderDate($this->date_start, $this->date_start_ca, $this->date_end, $this->date_end_ca);
    }
}
