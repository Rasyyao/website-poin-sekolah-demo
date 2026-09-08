<?php

namespace Tests\Feature\Points;

use App\Enums\PointsLogStatus;
use App\Enums\RuleType;
use App\Enums\UserRole;
use App\Enums\ViolationCategory;
use App\Models\PointsLog;
use App\Models\Rule;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Services\PointsService;
use App\Services\ThresholdEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointsSeparationTest extends TestCase
{
    use RefreshDatabase;

    private School $school;
    private User $teacher;
    private Student $student;
    private Rule $violationRule;
    private Rule $achievementRule;
    private PointsService $pointsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::create([
            'name' => 'Test School',
            'slug' => 'test-school',
        ]);

        $this->teacher = User::create([
            'name' => 'Guru Test',
            'email' => 'guru@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Teacher,
            'school_id' => $this->school->id,
        ]);

        $this->student = Student::create([
            'school_id' => $this->school->id,
            'nisn' => '1234567890',
            'name' => 'Budi Santoso',
            'access_code' => 'DEMO1234',
        ]);

        $this->violationRule = Rule::create([
            'school_id' => $this->school->id,
            'name' => 'Terlambat Masuk',
            'type' => RuleType::Violation,
            'category' => ViolationCategory::Ringan,
            'points' => 15,
            'is_active' => true,
        ]);

        $this->achievementRule = Rule::create([
            'school_id' => $this->school->id,
            'name' => 'Juara Kelas',
            'type' => RuleType::Achievement,
            'points' => 50,
            'is_active' => true,
        ]);

        $this->pointsService = app(PointsService::class);
    }

    public function test_violations_do_not_reduce_achievement_points()
    {
        // 1. Give student an achievement of 50 points
        $this->pointsService->recordPoints(
            student: $this->student,
            rule: $this->achievementRule,
            reporter: $this->teacher,
            note: 'Prestasi akademik',
        );

        $this->assertEquals(50, $this->student->totalAchievementPoints());
        $this->assertEquals(0, $this->student->totalViolationPoints());

        // 2. Give student a violation of 15 points
        $this->pointsService->recordPoints(
            student: $this->student,
            rule: $this->violationRule,
            reporter: $this->teacher,
            note: 'Terlambat 10 menit',
        );

        // 3. Verify achievement points remain intact (NOT reduced by violation)
        $this->assertEquals(50, $this->student->totalAchievementPoints(), 'Achievement points should not be reduced by violations');
        $this->assertEquals(15, $this->student->totalViolationPoints(), 'Violation points should be tracked separately');
    }

    public function test_achievements_do_not_reduce_violation_points()
    {
        // 1. Give student a violation of 15 points
        $this->pointsService->recordPoints(
            student: $this->student,
            rule: $this->violationRule,
            reporter: $this->teacher,
            note: 'Terlambat',
        );

        $this->assertEquals(15, $this->student->totalViolationPoints());
        $this->assertEquals(0, $this->student->totalAchievementPoints());

        // 2. Give student an achievement of 50 points
        $this->pointsService->recordPoints(
            student: $this->student,
            rule: $this->achievementRule,
            reporter: $this->teacher,
            note: 'Lomba karya ilmiah',
        );

        // 3. Verify violation points remain intact (NOT reduced by achievement)
        $this->assertEquals(15, $this->student->totalViolationPoints(), 'Violation points should not be reduced by achievements');
        $this->assertEquals(50, $this->student->totalAchievementPoints(), 'Achievement points should be tracked separately');
    }

    public function test_student_dashboard_displays_separate_points()
    {
        // Record both violation and achievement
        $this->pointsService->recordPoints(
            student: $this->student,
            rule: $this->violationRule,
            reporter: $this->teacher,
        );
        $this->pointsService->recordPoints(
            student: $this->student,
            rule: $this->achievementRule,
            reporter: $this->teacher,
        );

        $response = $this->withSession([
            'parent_student_id' => $this->student->id,
            'parent_school_id' => $this->school->id,
        ])->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Poin Pelanggaran');
        $response->assertSee('Poin Prestasi');
        $response->assertSee('15');
        $response->assertSee('+50');
    }

    public function test_parent_dashboard_displays_separate_points()
    {
        // Record both violation and achievement
        $this->pointsService->recordPoints(
            student: $this->student,
            rule: $this->violationRule,
            reporter: $this->teacher,
        );
        $this->pointsService->recordPoints(
            student: $this->student,
            rule: $this->achievementRule,
            reporter: $this->teacher,
        );

        $response = $this->withSession([
            'parent_student_id' => $this->student->id,
            'parent_school_id' => $this->school->id,
        ])->get(route('parent.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Poin Pelanggaran');
        $response->assertSee('Poin Prestasi');
        $response->assertSee('15');
        $response->assertSee('+50');
    }
}
