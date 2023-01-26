<?php

declare(strict_types=1);

namespace Acme\Shared\Date;

final class Calendar
{
    public static function calculateYearAccordingToWeekNumberIntersection(\DateTimeInterface $dateTime): string
    {
        if (self::isLastOfSixDaysOfYear($dateTime) && $dateTime->format('W') === '01') {
            return $dateTime->modify('+1 year')->format('Y');
        }

        return $dateTime->format('Y');
    }

    public static function isLastOfSixDaysOfYear(\DateTimeInterface $dateTime): bool
    {
        $lastSixDaysOfYear = ['31', '30', '29', '28', '27', '26'];
        $dayOfMonth = $dateTime->format('d');

        return in_array($dayOfMonth, $lastSixDaysOfYear, true);
    }
}
