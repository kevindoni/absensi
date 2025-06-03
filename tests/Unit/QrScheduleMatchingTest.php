<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\QrController;
use Carbon\Carbon;

class QrScheduleMatchingTest extends TestCase
{

    private function createMockSchedules()
    {
        // Create mock schedules for testing
        $schedules = collect([
            (object)[
                'id' => 1,
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:30:00',
                'mata_pelajaran' => 'Matematika',
            ],
            (object)[
                'id' => 2,
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '11:30:00',
                'mata_pelajaran' => 'Bahasa Indonesia',
            ],
            (object)[
                'id' => 3,
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '14:30:00',
                'mata_pelajaran' => 'IPA',
            ],
        ]);

        return $schedules;
    }    /** @test */
    public function it_selects_ongoing_class_when_current_time_is_within_class_duration()
    {
        $schedules = $this->createMockSchedules();
        
        // Create a mock controller to avoid database dependencies
        $controller = $this->getMockBuilder(QrController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('findBestScheduleMatch');
        $method->setAccessible(true);
        
        // Test at 08:30 - should select first class (08:00-09:30)
        $currentTime = '08:30:00';
        $result = $method->invoke($controller, $schedules, $currentTime);
        
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Matematika', $result->mata_pelajaran);
    }    /** @test */
    public function it_selects_upcoming_class_when_current_time_is_before_any_class()
    {
        $schedules = $this->createMockSchedules();
        
        $controller = $this->getMockBuilder(QrController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('findBestScheduleMatch');
        $method->setAccessible(true);
        
        // Test at 07:30 - should select closest upcoming class (08:00-09:30)
        $currentTime = '07:30:00';
        $result = $method->invoke($controller, $schedules, $currentTime);
        
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Matematika', $result->mata_pelajaran);
    }    /** @test */
    public function it_selects_most_recent_finished_class_when_all_classes_are_finished()
    {
        $schedules = $this->createMockSchedules();
        
        $controller = $this->getMockBuilder(QrController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('findBestScheduleMatch');
        $method->setAccessible(true);
        
        // Test at 16:00 - should select most recently finished class (13:00-14:30)
        $currentTime = '16:00:00';
        $result = $method->invoke($controller, $schedules, $currentTime);
        
        $this->assertEquals(3, $result->id);
        $this->assertEquals('IPA', $result->mata_pelajaran);
    }    /** @test */
    public function it_selects_closest_upcoming_class_when_between_classes()
    {
        $schedules = $this->createMockSchedules();
        
        $controller = $this->getMockBuilder(QrController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('findBestScheduleMatch');
        $method->setAccessible(true);
        
        // Test at 09:45 - should select next upcoming class (10:00-11:30)
        $currentTime = '09:45:00';
        $result = $method->invoke($controller, $schedules, $currentTime);
        
        $this->assertEquals(2, $result->id);
        $this->assertEquals('Bahasa Indonesia', $result->mata_pelajaran);
    }    /** @test */
    public function it_handles_empty_schedule_collection()
    {
        $schedules = collect([]);
        
        $controller = $this->getMockBuilder(QrController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('findBestScheduleMatch');
        $method->setAccessible(true);
        
        $currentTime = '08:30:00';
        $result = $method->invoke($controller, $schedules, $currentTime);
        
        $this->assertNull($result);
    }
}
