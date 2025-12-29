<?php

namespace App\Services;

use Carbon\Carbon;

class AuctionTimeService
{
    /**
     * Calculate auction start time based on approval time.
     * Rules per PDF:
     * - Approved before 12:00 PM: Start same day, random time between 12 PM and 8 PM
     * - Approved between 12:00 PM and 8:00 PM: Start at next immediate 15-minute interval
     * - Approved after 8:00 PM: Start next day, random time between 12 PM and 8 PM
     * - All start times must be at :00, :15, :30, or :45
     * - Approvals only allowed 8am-8pm
     * 
     * @param Carbon $approvalTime
     * @return Carbon
     */
    public function calculateStartTime(Carbon $approvalTime): Carbon
    {
        // Ensure approval is between 8am and 8pm
        $approvalHour = $approvalTime->hour;
        if ($approvalHour < 8 || $approvalHour >= 20) {
            throw new \Exception('Listings can only be approved between 8:00 AM and 8:00 PM.');
        }

        $now = Carbon::now();
        $approvalDate = $approvalTime->copy()->startOfDay();

        // Rule 1: Approved before 12:00 PM (8:00 AM - 11:59 AM)
        if ($approvalTime->hour < 12) {
            // Start same day, random time between 12 PM and 8 PM
            $startDate = $approvalDate;
            $startTime = $this->getRandomTimeBetween12And8();
            return $startDate->copy()->setTimeFromTimeString($startTime);
        }

        // Rule 2: Approved between 12:00 PM and 8:00 PM
        if ($approvalTime->hour >= 12 && $approvalTime->hour < 20) {
            // Start at next immediate 15-minute interval
            return $this->getNext15MinuteInterval($approvalTime);
        }

        // Rule 3: Approved at or after 8:00 PM (shouldn't happen per rules, but handle it)
        // Start next day, random time between 12 PM and 8 PM
        $startDate = $approvalDate->copy()->addDay();
        $startTime = $this->getRandomTimeBetween12And8();
        return $startDate->copy()->setTimeFromTimeString($startTime);
    }

    /**
     * Get random time between 12:00 PM and 8:00 PM at 15-minute intervals.
     * 
     * @return string Time string (HH:MM:SS)
     */
    protected function getRandomTimeBetween12And8(): string
    {
        // Available 15-minute intervals between 12:00 PM and 8:00 PM
        $intervals = [];
        for ($hour = 12; $hour < 20; $hour++) {
            foreach ([0, 15, 30, 45] as $minute) {
                $intervals[] = sprintf('%02d:%02d:00', $hour, $minute);
            }
        }

        // Randomly select one
        return $intervals[array_rand($intervals)];
    }

    /**
     * Get next immediate 15-minute interval from given time.
     * 
     * @param Carbon $time
     * @return Carbon
     */
    protected function getNext15MinuteInterval(Carbon $time): Carbon
    {
        $minute = $time->minute;
        $hour = $time->hour;

        // Calculate next 15-minute interval
        if ($minute < 15) {
            $nextMinute = 15;
        } elseif ($minute < 30) {
            $nextMinute = 30;
        } elseif ($minute < 45) {
            $nextMinute = 45;
        } else {
            $nextMinute = 0;
            $hour += 1;
        }

        // If we've gone past 8 PM, start next day at random time
        if ($hour >= 20) {
            $nextDay = $time->copy()->addDay()->startOfDay();
            $startTime = $this->getRandomTimeBetween12And8();
            return $nextDay->copy()->setTimeFromTimeString($startTime);
        }

        return $time->copy()->setTime($hour, $nextMinute, 0);
    }

    /**
     * Calculate auction end time ensuring it's between 12:00 PM and 8:00 PM.
     * 
     * @param Carbon $startTime
     * @param int $durationDays
     * @return Carbon
     */
    public function calculateEndTime(Carbon $startTime, int $durationDays): Carbon
    {
        // Calculate end time based on start + duration
        $endTime = $startTime->copy()->addDays($durationDays);

        // Ensure end time is between 12:00 PM and 8:00 PM
        $endHour = $endTime->hour;

        // If before 12 PM, set to 12 PM
        if ($endHour < 12) {
            $endTime->setTime(12, 0, 0);
        }
        // If at or after 8 PM, set to 7:45 PM (last valid time)
        elseif ($endHour >= 20) {
            $endTime->setTime(19, 45, 0);
        }
        // If between 12 PM and 8 PM, round to nearest 15-minute interval
        else {
            $minute = $endTime->minute;
            if ($minute < 15) {
                $endTime->setTime($endHour, 0, 0);
            } elseif ($minute < 30) {
                $endTime->setTime($endHour, 15, 0);
            } elseif ($minute < 45) {
                $endTime->setTime($endHour, 30, 0);
            } else {
                $endTime->setTime($endHour, 45, 0);
            }
        }

        return $endTime;
    }

    /**
     * Validate that a time is at a 15-minute interval.
     * 
     * @param Carbon $time
     * @return bool
     */
    public function isValid15MinuteInterval(Carbon $time): bool
    {
        $minute = $time->minute;
        return in_array($minute, [0, 15, 30, 45]);
    }
}
