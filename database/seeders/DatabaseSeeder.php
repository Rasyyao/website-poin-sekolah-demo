<?php

namespace Database\Seeders;

use App\Enums\PointsLogStatus;
use App\Enums\RuleType;
use App\Enums\Semester;
use App\Enums\SubscriptionStatus;
use App\Enums\ThresholdAction;
use App\Enums\UserRole;
use App\Enums\ViolationCategory;
use App\Models\AcademicYear;
use App\Models\PointsLog;
use App\Models\Rule;
use App\Models\RuleThreshold;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {

        // ── Demo School ─────────────────────────────────────
        $school = School::create([
            'name' => 'SMP Negeri 1 Demo',
            'slug' => 'smpn1-demo-' . Str::random(5),
            'subscription_status' => SubscriptionStatus::Active,
        ]);

        // ── School Staff ────────────────────────────────────
        $admin = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@smpn1demo.sch.id',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
            'school_id' => $school->id,
        ]);

        $guru1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@smpn1demo.sch.id',
            'password' => bcrypt('password'),
            'role' => UserRole::Homeroom,
            'school_id' => $school->id,
        ]);

        $guru2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@smpn1demo.sch.id',
            'password' => bcrypt('password'),
            'role' => UserRole::Teacher,
            'school_id' => $school->id,
        ]);

        $guruBk = User::create([
            'name' => 'Dewi Kartika',
            'email' => 'dewi@smpn1demo.sch.id',
            'password' => bcrypt('password'),
            'role' => UserRole::Counselor,
            'school_id' => $school->id,
        ]);

        // ── Academic Year ───────────────────────────────────
        $tahunAjaran = AcademicYear::withoutGlobalScopes()->create([
            'school_id' => $school->id,
            'year_label' => '2026/2027',
            'semester' => Semester::Ganjil,
            'is_active' => true,
        ]);

        // ── Classes ─────────────────────────────────────────
        $kelas7A = SchoolClass::withoutGlobalScopes()->create([
            'school_id' => $school->id,
            'name' => '7A',
            'homeroom_teacher_id' => $guru1->id,
            'academic_year_id' => $tahunAjaran->id,
        ]);

        $kelas7B = SchoolClass::withoutGlobalScopes()->create([
            'school_id' => $school->id,
            'name' => '7B',
            'homeroom_teacher_id' => $guru2->id,
            'academic_year_id' => $tahunAjaran->id,
        ]);

        $kelas8A = SchoolClass::withoutGlobalScopes()->create([
            'school_id' => $school->id,
            'name' => '8A',
            'homeroom_teacher_id' => null,
            'academic_year_id' => $tahunAjaran->id,
        ]);

        // ── Students ────────────────────────────────────────
        $students = [];
        $classes = [$kelas7A, $kelas7B, $kelas8A];
        $studentCounter = 1;
        $faker = \Faker\Factory::create('id_ID');

        foreach ($classes as $classModel) {
            for ($i = 1; $i <= 10; $i++) {
                $nisn = '001234' . str_pad($studentCounter, 4, '0', STR_PAD_LEFT);
                $students[] = Student::withoutGlobalScopes()->create([
                    'school_id' => $school->id,
                    'class_id' => $classModel->id,
                    'nisn' => $nisn,
                    'name' => $faker->name(),
                    'birth_date' => $faker->dateTimeBetween('-15 years', '-12 years')->format('Y-m-d'),
                    'parent_contact' => $faker->phoneNumber(),
                    'access_code' => 'DEMO' . substr($nisn, -4),
                ]);
                $studentCounter++;
            }
        }

        // ── Rules (Violations & Achievements) ──────────────
        $rules = [];

        // Violations
        $violationData = [
            ['name' => 'Terlambat masuk sekolah', 'cat' => ViolationCategory::Ringan, 'pts' => 5],
            ['name' => 'Tidak memakai seragam lengkap', 'cat' => ViolationCategory::Ringan, 'pts' => 5],
            ['name' => 'Tidak mengerjakan PR', 'cat' => ViolationCategory::Ringan, 'pts' => 10],
            ['name' => 'Membolos pelajaran', 'cat' => ViolationCategory::Sedang, 'pts' => 20],
            ['name' => 'Berkata kasar/tidak sopan', 'cat' => ViolationCategory::Sedang, 'pts' => 15],
            ['name' => 'Berkelahi di sekolah', 'cat' => ViolationCategory::Berat, 'pts' => 50],
            ['name' => 'Membawa rokok/vape', 'cat' => ViolationCategory::Berat, 'pts' => 75],
            ['name' => 'Bullying/intimidasi', 'cat' => ViolationCategory::Berat, 'pts' => 100],
        ];

        foreach ($violationData as $v) {
            $rules[] = Rule::withoutGlobalScopes()->create([
                'school_id' => $school->id,
                'name' => $v['name'],
                'type' => RuleType::Violation,
                'category' => $v['cat'],
                'points' => $v['pts'],
                'description' => 'Pelanggaran: ' . $v['name'],
                'is_active' => true,
            ]);
        }

        // Achievements
        $achievementData = [
            ['name' => 'Juara kelas', 'pts' => 50],
            ['name' => 'Juara lomba tingkat kota', 'pts' => 75],
            ['name' => 'Ketua OSIS', 'pts' => 30],
            ['name' => 'Membantu kegiatan sekolah', 'pts' => 15],
        ];

        foreach ($achievementData as $a) {
            $rules[] = Rule::withoutGlobalScopes()->create([
                'school_id' => $school->id,
                'name' => $a['name'],
                'type' => RuleType::Achievement,
                'category' => null,
                'points' => $a['pts'],
                'description' => 'Prestasi: ' . $a['name'],
                'is_active' => true,
            ]);
        }

        // ── Sample Points Log ───────────────────────────────
        $scenarios = [
            ['student_index' => 0, 'logs' => [[7, 2], [5, 1]]], // > 200
            ['student_index' => 1, 'logs' => [[7, 1], [4, 1]]], // > 100
            ['student_index' => 2, 'logs' => [[5, 1], [2, 1]]], // > 50
            ['student_index' => 3, 'logs' => [[3, 1], [2, 1]]], // > 25
        ];

        foreach ($scenarios as $scenario) {
            foreach ($scenario['logs'] as $logDef) {
                $ruleIndex = $logDef[0];
                $count = $logDef[1];
                for ($i = 0; $i < $count; $i++) {
                    PointsLog::withoutGlobalScopes()->create([
                        'school_id' => $school->id,
                        'student_id' => $students[$scenario['student_index']]->id,
                        'rule_id' => $rules[$ruleIndex]->id,
                        'reported_by' => $guru1->id,
                        'points' => $rules[$ruleIndex]->points,
                        'status' => PointsLogStatus::Approved,
                        'occurred_at' => now()->subDays(rand(1, 15)),
                    ]);
                }
            }
        }

        // ── Rule Thresholds ─────────────────────────────────
        RuleThreshold::withoutGlobalScopes()->create([
            'school_id' => $school->id,
            'min_points' => 25,
            'action' => 'Notifikasi Wali Kelas',
            'description' => 'Notifikasi ke wali kelas jika poin pelanggaran ≥ 25.',
        ]);

        RuleThreshold::withoutGlobalScopes()->create([
            'school_id' => $school->id,
            'min_points' => 50,
            'action' => 'Notifikasi Orang Tua',
            'description' => 'Notifikasi ke orang tua jika poin pelanggaran ≥ 50.',
        ]);

        RuleThreshold::withoutGlobalScopes()->create([
            'school_id' => $school->id,
            'min_points' => 100,
            'action' => 'Panggilan Orang Tua',
            'description' => 'Panggilan orang tua jika poin pelanggaran ≥ 100.',
        ]);

        RuleThreshold::withoutGlobalScopes()->create([
            'school_id' => $school->id,
            'min_points' => 200,
            'action' => 'Skorsing',
            'description' => 'Skorsing jika poin pelanggaran ≥ 200.',
        ]);
    }
}
