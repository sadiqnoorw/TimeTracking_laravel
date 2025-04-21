<?php

namespace App\Services;

use App\Repositories\Contracts\TimeTrackingRepositoryInterface;

class TimeTrackingService
{
    protected $repository;

    public function __construct(TimeTrackingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function startWork($userId, $workTypeId)
    {
        return $this->repository->startWork($userId, $workTypeId);
    }

    public function stopWork($userId)
    {
        return $this->repository->stopWork($userId);
    }

    public function startBreak($userId, $breakReasonId)
    {
        return $this->repository->startBreak($userId, $breakReasonId);
    }

    public function stopBreak($userId)
    {
        return $this->repository->stopBreak($userId);
    }

    public function getDailyReport($userId)
    {
        return $this->repository->getDailyReport($userId);
    }

    public function getCurrentStatus($userId)
    {
        return $this->repository->getCurrentStatus($userId);
       
    }
}
