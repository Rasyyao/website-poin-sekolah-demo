import os

tests = {
    'tests/Feature/Auth/StaffLoginTest.php': """<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('staff login page is accessible', function () {
    $this->get('/auth/login')->assertStatus(200);
});

test('staff can login with correct credentials', function () {
    $user = User::where('email', 'admin@smpn1demo.sch.id')->first();
    $this->post('/auth/login', [
        'email' => 'admin@smpn1demo.sch.id',
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('staff login fails with wrong password', function () {
    $this->post('/auth/login', [
        'email' => 'admin@smpn1demo.sch.id',
        'password' => 'wrong',
    ])->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('staff can logout', function () {
    $user = User::where('email', 'admin@smpn1demo.sch.id')->first();
    $this->actingAs($user)->post('/auth/logout')->assertRedirect('/auth/login');
    $this->assertGuest();
});
""",
    'tests/Feature/Auth/ParentLoginTest.php': """<?php
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('parent login page is accessible', function () {
    $this->get('/auth/parent/login')->assertStatus(200);
});

test('parent can login with correct credentials', function () {
    $student = Student::first();
    $student->update(['access_code' => 'DEMO1234']); // override for test
    $this->post('/auth/parent/login', [
        'school_slug' => $student->school->slug,
        'nisn' => $student->nisn,
        'access_code' => 'DEMO1234',
    ])->assertRedirect(route('parent.dashboard'));
    $this->assertSessionHas('parent_student_id', $student->id);
});

test('parent login fails with wrong access code', function () {
    $student = Student::first();
    $this->post('/auth/parent/login', [
        'school_slug' => $student->school->slug,
        'nisn' => $student->nisn,
        'access_code' => 'WRONG',
    ])->assertSessionHasErrors('access_code');
});
""",
    'tests/Feature/SuperAdmin/SchoolManagementTest.php': """<?php
use App\Models\User;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('super admin can view schools', function () {
    $superadmin = User::where('role', 'super_admin')->first();
    $this->actingAs($superadmin)->get('/super-admin/schools')->assertStatus(200);
});

test('super admin can create school', function () {
    $superadmin = User::where('role', 'super_admin')->first();
    $this->actingAs($superadmin)->post('/super-admin/schools', [
        'name' => 'SMP Test',
        'slug' => 'smp-test',
        'subscription_status' => 'trial',
    ])->assertRedirect('/super-admin/schools')->assertSessionHas('success');
    
    $this->assertDatabaseHas('schools', ['slug' => 'smp-test']);
});

test('school creation fails validation on missing fields', function () {
    $superadmin = User::where('role', 'super_admin')->first();
    $this->actingAs($superadmin)->post('/super-admin/schools', [
        'name' => '', // required
    ])->assertSessionHasErrors(['name', 'slug', 'subscription_status']);
});
""",
    'tests/Feature/Admin/AcademicYearTest.php': """<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view academic years', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/academic-years')->assertStatus(200);
});

test('admin can create academic year', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/academic-years', [
        'year_label' => '2030/2031',
        'semester' => 'Ganjil',
        'is_active' => '1',
    ])->assertRedirect('/admin/academic-years')->assertSessionHas('success');
    
    $this->assertDatabaseHas('academic_years', ['year_label' => '2030/2031']);
});

test('academic year creation validation', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/academic-years', [])
        ->assertSessionHasErrors(['year_label', 'semester', 'is_active']);
});
""",
    'tests/Feature/Admin/ClassTest.php': """<?php
use App\Models\User;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view classes', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/classes')->assertStatus(200);
});

test('admin can create class', function () {
    $admin = User::where('role', 'admin')->first();
    $academicYear = AcademicYear::first();
    $this->actingAs($admin)->post('/admin/classes', [
        'name' => 'Class X',
        'academic_year_id' => $academicYear->id,
        'homeroom_teacher_id' => $admin->id, // just for test
    ])->assertRedirect('/admin/classes')->assertSessionHas('success');
    
    $this->assertDatabaseHas('school_classes', ['name' => 'Class X']);
});
""",
    'tests/Feature/Admin/RuleTest.php': """<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view rules', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/rules')->assertStatus(200);
});

test('admin can create a violation rule', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/rules', [
        'name' => 'Test Rule',
        'type' => 'violation',
        'category' => 'ringan',
        'points' => '-10',
        'is_active' => '1',
    ])->assertRedirect('/admin/rules')->assertSessionHas('success');
    
    $this->assertDatabaseHas('rules', ['name' => 'Test Rule']);
});
""",
    'tests/Feature/Admin/ThresholdTest.php': """<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view thresholds', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/thresholds')->assertStatus(200);
});

test('admin can create threshold', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/thresholds', [
        'min_points' => 300,
        'action' => 'call_parent',
    ])->assertRedirect('/admin/thresholds')->assertSessionHas('success');
    
    $this->assertDatabaseHas('rule_thresholds', ['min_points' => 300]);
});
""",
    'tests/Feature/Admin/StudentTest.php': """<?php
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('admin can view students', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get('/admin/students')->assertStatus(200);
});

test('admin can create student', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->post('/admin/students', [
        'nisn' => '999999',
        'name' => 'New Student',
        'parent_contact' => '0812345678',
    ])->assertRedirect('/admin/students')->assertSessionHas('success');
    
    $this->assertDatabaseHas('students', ['nisn' => '999999']);
});

test('admin can generate student access code', function () {
    $admin = User::where('role', 'admin')->first();
    $student = Student::first();
    $this->actingAs($admin)->post("/admin/students/{$student->id}/generate-access-code")
        ->assertSessionHas('access_code');
});
""",
    'tests/Feature/Teacher/PointsInputTest.php': """<?php
use App\Models\User;
use App\Models\Student;
use App\Models\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('teacher can input points', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $rule = Rule::first();
    
    $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'occurred_at' => now()->format('Y-m-d\TH:i:s'),
        'note' => 'Test test',
    ])->assertRedirect('/teacher/points')->assertSessionHas('success');
    
    $this->assertDatabaseHas('points_logs', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
    ]);
});

test('points input fails validation with future date', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $rule = Rule::first();
    
    $this->actingAs($teacher)->post('/teacher/points', [
        'student_id' => $student->id,
        'rule_id' => $rule->id,
        'occurred_at' => now()->addDays(2)->format('Y-m-d\TH:i:s'),
    ])->assertSessionHasErrors('occurred_at');
});
""",
    'tests/Feature/Teacher/StudentMonitorTest.php': """<?php
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('teacher can view my students page', function () {
    $teacher = User::where('role', 'teacher')->first();
    $this->actingAs($teacher)->get('/teacher/my-students')->assertStatus(200);
});

test('teacher can view student history', function () {
    $teacher = User::where('role', 'teacher')->first();
    $student = Student::first();
    $this->actingAs($teacher)->get("/teacher/students/{$student->id}/history")->assertStatus(200);
});
""",
    'tests/Feature/Parent/DashboardTest.php': """<?php
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(fn () => $this->seed());

test('parent can view dashboard after login', function () {
    $student = Student::first();
    $this->withSession([
        'parent_student_id' => $student->id,
        'parent_school_id' => $student->school_id,
    ])->get('/parent/dashboard')->assertStatus(200);
});

test('guest parent cannot view dashboard', function () {
    $this->get('/parent/dashboard')->assertRedirect('/auth/parent/login');
});
"""
}

for filepath, content in tests.items():
    os.makedirs(os.path.dirname(filepath), exist_ok=True)
    with open(filepath, 'w') as f:
        f.write(content)
        
print("Tests generated successfully!")
