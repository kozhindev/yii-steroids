<?php

namespace steroids\helpers;

class DateHelper
{
    public static function parseTimeZone($timeZone)
    {
        $tz = new \DateTimeZone($timeZone);
        $date = new \DateTime('now', $tz);
        $offset = $tz->getOffset($date) . ' seconds';
        $dateOffset = clone $date;
        $dateOffset->sub(\DateInterval::createFromDateString($offset));

        $interval = $dateOffset->diff($date);
        return $interval->format('%R%H:%I');
    }
}
