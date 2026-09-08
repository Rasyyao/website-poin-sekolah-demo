<?php

namespace Tests\Feature\Admin;

use App\Enums\PointsLogStatus;
use App\Enums\RuleType;
use App\Enums\UserRole;
use App\Models\PointsLog;
use App\Models\Rule;
use App\Models\RuleThreshold;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentRankingReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_student_ranking_report()
    {
        $school = School::create([
            'name' => 'Test School',
            'slug' => 'test-school',
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
            'school_id' => $school->id,
        ]);

        $teacher = User::create([
            'name' => 'Teacher',
            'email' => 'teacher@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Teacher,
            'school_id' => $school->id,
        ]);
        
        $academicYear = \App\Models\AcademicYear::create([
            'school_id' => $school->id,
            'year_label' => '2026/2027',
            'semester' => \App\Enums\Semester::Ganjil,
            'is_active' => true,
        ]);

        $class = SchoolClass::create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Kelas 10',
        ]);

        $student1 = Student::create([
            'school_id' => $school->id,
            'class_id' => $class->id,
            'nisn' => '111111',
            'name' => 'Siswa A',
            'access_code' => 'DEMO1',
        ]);
        $student2 = Student::create([
            'school_id' => $school->id,
            'class_id' => $class->id,
            'nisn' => '222222',
            'name' => 'Siswa B',
            'access_code' => 'DEMO2',
        ]);

        // Create threshold
        RuleThreshold::create([
            'school_id' => $school->id,
            'min_points' => 50,
            'action' => 'Notifikasi Orang Tua',
            'description' => 'Notifikasi ke ortu',
        ]);

        $rule = Rule::create([
            'school_id' => $school->id,
            'name' => 'Terlambat',
            'type' => RuleType::Violation,
            'points' => 20,
            'is_active' => true,
        ]);

        // Student 1 gets 60 points
        PointsLog::create([
            'school_id' => $school->id,
            'student_id' => $student1->id,
            'rule_id' => $rule->id,
            'reported_by' => $teacher->id,
            'points' => 20,
            'status' => PointsLogStatus::Approved,
            'occurred_at' => now(),
        ]);
        PointsLog::create([
            'school_id' => $school->id,
            'student_id' => $student1->id,
            'rule_id' => $rule->id,
            'reported_by' => $teacher->id,
            'points' => 40,
            'status' => PointsLogStatus::Approved,
            'occurred_at' => now(),
        ]);

        // Student 2 gets 20 points
        PointsLog::create([
            'school_id' => $school->id,
            'student_id' => $student2->id,
            'rule_id' => $rule->id,
            'reported_by' => $teacher->id,
            'points' => 20,
            'status' => PointsLogStatus::Approved,
            'occurred_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['school_id' => $school->id])
            ->get(route('admin.reports.ranking'));

        $response->assertStatus(200);
        $response->assertSee('Siswa Melampaui Batas');
        $response->assertSee('Siswa A');
        $response->assertSee('60'); // total points student 1
        $response->assertSee('Tindakan: Notifikasi Orang Tua'); // threshold notice
        
        // Student B has 20 points, which is less than the 50 point threshold,
        // so they should NOT be visible in the threshold report.
        $response->assertDontSee('Siswa B');
    }
}
