<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SystemFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SchoolSetting::set('school_name', 'SMK Putra Pertiwi');
        SchoolSetting::set('late_cutoff_time', '07:15');
    }

    public function test_login_portal_is_accessible()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SMK Putra Pertiwi');
        $response->assertSee('Portal Siswa');
        $response->assertSee('Administrator');
    }

    public function test_admin_can_login_and_access_dashboard()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('admin.login.process'), [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'web');

        $dashResponse = $this->actingAs($admin, 'web')->get(route('admin.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Dashboard Utama');
    }

    public function test_student_can_login_and_access_student_dashboard()
    {
        $major = Major::create([
            'name' => 'Rekayasa Perangkat Lunak',
            'code' => 'RPL',
            'latitude' => -6.3472,
            'longitude' => 106.7645,
            'radius_meters' => 100,
        ]);

        $ay = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $class = SchoolClass::create([
            'name' => 'X RPL 1',
            'major_id' => $major->id,
            'academic_year_id' => $ay->id,
        ]);

        $student = Student::create([
            'class_id' => $class->id,
            'nis' => '1001',
            'name' => 'Azis Test',
            'email' => 'azis@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->post(route('student.login.process'), [
            'identifier' => '1001',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student, 'student');

        $dashResponse = $this->actingAs($student, 'student')->get(route('student.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Azis Test');
        $dashResponse->assertSee('X RPL 1');
    }

    public function test_student_cannot_access_admin_dashboard()
    {
        $student = Student::create([
            'nis' => '1002',
            'name' => 'Student Two',
            'email' => 'student2@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($student, 'student')->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_attendance_report_exports()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $major = Major::create([
            'name' => 'Rekayasa Perangkat Lunak',
            'code' => 'RPL',
            'latitude' => -6.3472,
            'longitude' => 106.7645,
            'radius_meters' => 100,
        ]);

        $ay = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $class = SchoolClass::create([
            'name' => 'X RPL 1',
            'major_id' => $major->id,
            'academic_year_id' => $ay->id,
        ]);

        $student = Student::create([
            'class_id' => $class->id,
            'nis' => '1001',
            'name' => 'Azis Test',
            'email' => 'azis@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'date' => Carbon::today()->toDateString(),
            'time' => '07:05:00',
            'status' => 'present',
            'latitude' => -6.3472,
            'longitude' => 106.7645,
        ]);

        // PDF Export
        $pdfResponse = $this->actingAs($admin, 'web')->get(route('admin.attendances.export.pdf'));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));

        // Excel Export
        $excelResponse = $this->actingAs($admin, 'web')->get(route('admin.attendances.export.excel'));
        $excelResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $excelResponse->headers->get('content-type'));
    }
}
