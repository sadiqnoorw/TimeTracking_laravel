<?php

namespace App\Repositories\Contracts;

interface TimeTrackingRepositoryInterface
{
    public function startWork($userId, $workTypeId);
    public function stopWork($userId);
    public function startBreak($userId, $breakReasonId);
    public function stopBreak($userId);
    public function getDailyReport($userId);
}
