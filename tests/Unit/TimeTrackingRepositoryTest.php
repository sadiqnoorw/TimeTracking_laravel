<?php
namespace Tests\Unit;

use App\Models\TimeTrackingEntry;
use App\Models\User;
use App\Models\WorkType;
use App\Models\BreakReason;
use App\Repositories\TimeTrackingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeTrackingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TimeTrackingRepository $repository;
    private User $user;
    private WorkType $workType;
    private BreakReason $breakReason;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = new TimeTrackingRepository();
        $this->user = User::factory()->create();
        $this->workType = WorkType::factory()->create();
        $this->breakReason = BreakReason::factory()->create();
    }

    public function test_it_can_start_work_for_an_employee(): void
    {
        $entry = $this->repository->startWork($this->user->id, $this->workType->id);
        
        $this->assertDatabaseHas('time_tracking_entries', [
            'user_id' => $this->user->id,
            'type' => 'work',
            'work_type_id' => $this->workType->id,
            'end_time' => null
        ]);
        $this->assertNotNull($entry->start_time);
    }

    public function test_it_can_stop_work_for_an_employee(): void
    {
        $entry = TimeTrackingEntry::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'work',
            'start_time' => now()->subHour(),
            'end_time' => null,
            'work_type_id' => $this->workType->id
        ]);

        $result = $this->repository->stopWork($this->user->id);

        $this->assertTrue($result);
        $this->assertNotNull($entry->fresh()->end_time);
    }

    public function test_stopping_work_with_no_active_work_returns_false(): void
    {
        $result = $this->repository->stopWork($this->user->id);
        $this->assertFalse($result);
    }

    public function test_it_can_start_break_for_an_employee(): void
    {
        $breakReason = BreakReason::factory()->create();
        $entry = $this->repository->startBreak($this->user->id, $breakReason->id);

        $this->assertDatabaseHas('time_tracking_entries', [
            'user_id' => $this->user->id,
            'type' => 'break',
            'break_reason_id' => $breakReason->id,
            'end_time' => null
        ]);
        $this->assertNotNull($entry->start_time);
    }

    public function test_it_can_stop_break_for_an_employee(): void
    {
        $breakReason = BreakReason::factory()->create();
        $entry = TimeTrackingEntry::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'break',
            'start_time' => now()->subMinutes(15),
            'end_time' => null,
            'break_reason_id' => $breakReason->id
        ]);

        $result = $this->repository->stopBreak($this->user->id);

        $this->assertTrue($result);
        $this->assertNotNull($entry->fresh()->end_time);
    }

    public function test_stopping_break_with_no_active_break_returns_false(): void
    {
        $result = $this->repository->stopBreak($this->user->id);
        $this->assertFalse($result);
    }

    public function test_it_can_get_entries_by_date(): void
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        TimeTrackingEntry::factory()->create([
            'user_id' => $this->user->id,
            'start_time' => now(),
            'work_type_id' => $this->workType->id
        ]);
        
        TimeTrackingEntry::factory()->create([
            'user_id' => $this->user->id,
            'start_time' => now()->subDay(),
            'work_type_id' => $this->workType->id
        ]);
        
        TimeTrackingEntry::factory()->create([
            'user_id' => User::factory()->create()->id,
            'start_time' => now(),
            'work_type_id' => $this->workType->id
        ]);

        $entries = $this->repository->getEntriesByDate($this->user->id, $today);

        $this->assertCount(1, $entries);
        $this->assertEquals($today, $entries->first()->start_time->format('Y-m-d'));
    }

    public function test_it_can_generate_daily_report(): void
    {
        $today = now();
        $yesterday = now()->subDay();

        // Create test data
        TimeTrackingEntry::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'work',
            'start_time' => $today->copy()->setHour(9),
            'end_time' => $today->copy()->setHour(12),
            'work_type_id' => $this->workType->id
        ]);
        
        TimeTrackingEntry::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'break',
            'start_time' => $today->copy()->setHour(12),
            'end_time' => $today->copy()->setHour(12)->addMinutes(30),
            'break_reason_id' => $this->breakReason->id
        ]);
        
        TimeTrackingEntry::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'work',
            'start_time' => $today->copy()->setHour(12)->addMinutes(30),
            'end_time' => $today->copy()->setHour(17),
            'work_type_id' => $this->workType->id
        ]);
        
        TimeTrackingEntry::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'work',
            'start_time' => $yesterday->copy()->setHour(10),
            'end_time' => $yesterday->copy()->setHour(16),
            'work_type_id' => $this->workType->id
        ]);

        $report = $this->repository->getDailyReport($this->user->id);

        $this->assertCount(2, $report);
        $this->assertArrayHasKey($today->format('Y-m-d'), $report->toArray());
        $this->assertArrayHasKey($yesterday->format('Y-m-d'), $report->toArray());

        $todayReport = $report[$today->format('Y-m-d')];
        $this->assertEquals(450, $todayReport['work_time']); // 7.5 hours work (450 minutes)
        $this->assertEquals(30, $todayReport['break_time']); // 30 minutes break
        $this->assertCount(3, $todayReport['entries']);

        $yesterdayReport = $report[$yesterday->format('Y-m-d')];
        $this->assertEquals(360, $yesterdayReport['work_time']); // 6 hours work
        $this->assertEquals(0, $yesterdayReport['break_time']); // no break
        $this->assertCount(1, $yesterdayReport['entries']);
    }

    public function test_it_can_get_current_status(): void
    {
        $workType = WorkType::factory()->create();
        $breakReason = BreakReason::factory()->create();

        // No active entries
        $status = $this->repository->getCurrentStatus($this->user->id);
        $this->assertFalse($status['activeWork']);
        $this->assertFalse($status['activeBreak']);

        // Active work
        TimeTrackingEntry::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'work',
            'start_time' => now()->subHour(),
            'end_time' => null,
            'work_type_id' => $workType->id
        ]);

        $status = $this->repository->getCurrentStatus($this->user->id);
        $this->assertTrue($status['activeWork']);
        $this->assertFalse($status['activeBreak']);

        // Active break
        TimeTrackingEntry::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'break',
            'start_time' => now()->subMinutes(10),
            'end_time' => null,
            'break_reason_id' => $breakReason->id
        ]);

        $status = $this->repository->getCurrentStatus($this->user->id);
        $this->assertTrue($status['activeWork']);
        $this->assertTrue($status['activeBreak']);
    }
}