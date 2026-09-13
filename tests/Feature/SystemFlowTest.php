<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Holiday;
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
        SchoolSetting::set('default_latitude', -6.3472);
        SchoolSetting::set('default_longitude', 106.7645);
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

    public function test_student_face_registration_saves_embedding()
    {
        $student = Student::create([
            'nis' => '1003',
            'name' => 'Student Three',
            'email' => 'student3@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $fakeDescriptor = json_encode(array_fill(0, 128, 0.05));

        $response = $this->actingAs($student, 'student')->postJson(route('student.face-registration.store'), [
            'face_embedding' => $fakeDescriptor,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $student->refresh();
        $this->assertTrue($student->hasEnrolledFace());
    }

    public function test_student_attendance_flow_and_geofencing()
    {
        $major = Major::create([
            'name' => 'Rekayasa Perangkat Lunak',
            'code' => 'RPL',
            'latitude' => -6.34720000,
            'longitude' => 106.76450000,
            'radius_meters' => 50,
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
            'nis' => '1004',
            'name' => 'Student Four',
            'email' => 'student4@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'face_embedding' => json_encode(array_fill(0, 128, 0.05)),
        ]);

        Carbon::setTestNow(Carbon::today()->setTime(7, 0, 0));

        // Fake photo base64
        $fakePhoto = 'data:image/jpeg;base64,' . base64_encode('fake image content');

        // 1. Outside geofence should fail (e.g. 5km away: -6.39, 106.79)
        $outsideResponse = $this->actingAs($student, 'student')->postJson(route('student.attendance.store'), [
            'latitude' => -6.39000000,
            'longitude' => 106.79000000,
            'photo' => $fakePhoto,
            'face_verified' => true,
        ]);
        $outsideResponse->assertStatus(422);
        $outsideResponse->assertJson(['success' => false]);
        $this->assertStringContainsString('melebihi radius toleransi', $outsideResponse->json('message'));

        // 2. Inside geofence should succeed (same exact coordinate)
        $insideResponse = $this->actingAs($student, 'student')->postJson(route('student.attendance.store'), [
            'latitude' => -6.34720000,
            'longitude' => 106.76450000,
            'photo' => $fakePhoto,
            'face_verified' => true,
        ]);
        $insideResponse->assertStatus(200);
        $insideResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => 'present',
        ]);
        $attendanceRecord = Attendance::where('student_id', $student->id)->first();
        $this->assertNotNull($attendanceRecord);
        $this->assertEquals(Carbon::today()->toDateString(), Carbon::parse($attendanceRecord->date)->toDateString());

        // 3. Second attendance on same day should be rejected (strict 1x per day)
        $secondResponse = $this->actingAs($student, 'student')->postJson(route('student.attendance.store'), [
            'latitude' => -6.34720000,
            'longitude' => 106.76450000,
            'photo' => $fakePhoto,
            'face_verified' => true,
        ]);
        $secondResponse->assertStatus(422);
        $this->assertStringContainsString('sudah melakukan presensi hari ini', $secondResponse->json('message'));

        Carbon::setTestNow();
    }

    public function test_student_cannot_attend_on_holiday()
    {
        Holiday::create([
            'date' => Carbon::today()->toDateString(),
            'title' => 'Libur Nasional Uji Coba',
        ]);

        $major = Major::create([
            'name' => 'Rekayasa Perangkat Lunak',
            'code' => 'RPL',
            'latitude' => -6.34720000,
            'longitude' => 106.76450000,
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
            'nis' => '1005',
            'name' => 'Student Five',
            'email' => 'student5@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'face_embedding' => json_encode(array_fill(0, 128, 0.05)),
        ]);

        $fakePhoto = 'data:image/jpeg;base64,' . base64_encode('fake image content');

        $res = $this->actingAs($student, 'student')->postJson(route('student.attendance.store'), [
            'latitude' => -6.34720000,
            'longitude' => 106.76450000,
            'photo' => $fakePhoto,
            'face_verified' => true,
        ]);

        $res->assertStatus(422);
        $this->assertStringContainsString('hari libur sekolah', $res->json('message'));
    }

    public function test_admin_can_reset_student_face_embedding()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $student = Student::create([
            'nis' => '1006',
            'name' => 'Student Six',
            'email' => 'student6@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'face_embedding' => json_encode(array_fill(0, 128, 0.05)),
        ]);

        $this->assertTrue($student->hasEnrolledFace());

        $res = $this->actingAs($admin, 'web')->post(route('admin.students.reset-face', $student));
        $res->assertSessionHas('success');

        $student->refresh();
        $this->assertFalse($student->hasEnrolledFace());
        $this->assertNull($student->face_embedding);
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

    public function test_student_attendance_records_late_status_after_cutoff_time()
    {
        $major = Major::create([
            'name' => 'Teknik Komputer & Jaringan',
            'code' => 'TKJ',
            'latitude' => -6.34720000,
            'longitude' => 106.76450000,
            'radius_meters' => 100,
        ]);

        $ay = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $class = SchoolClass::create([
            'name' => 'X TKJ 1',
            'major_id' => $major->id,
            'academic_year_id' => $ay->id,
        ]);

        $student = Student::create([
            'class_id' => $class->id,
            'nis' => '1008',
            'name' => 'Late Student',
            'email' => 'late@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'face_embedding' => json_encode(array_fill(0, 128, 0.05)),
        ]);

        // Freeze time to 07:35:00 (after 07:15 cutoff)
        Carbon::setTestNow(Carbon::today()->setTime(7, 35, 0));

        $fakePhoto = 'data:image/jpeg;base64,' . base64_encode('fake image content');

        $res = $this->actingAs($student, 'student')->postJson(route('student.attendance.store'), [
            'latitude' => -6.34720000,
            'longitude' => 106.76450000,
            'photo' => $fakePhoto,
            'face_verified' => true,
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => 'late',
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_can_update_major_geofence_coordinates_and_radius()
    {
        $admin = User::create([
            'name' => 'Admin Geo',
            'email' => 'admingeo@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $major = Major::create([
            'name' => 'Multimedia',
            'code' => 'MM',
            'latitude' => -6.34720000,
            'longitude' => 106.76450000,
            'radius_meters' => 50,
        ]);

        $res = $this->actingAs($admin, 'web')->put(route('admin.majors.update', $major), [
            'name' => 'Desain Komunikasi Visual',
            'code' => 'DKV',
            'latitude' => -6.34800000,
            'longitude' => 106.76500000,
            'radius_meters' => 120,
        ]);

        $res->assertRedirect(route('admin.majors.index'));
        $this->assertDatabaseHas('majors', [
            'id' => $major->id,
            'code' => 'DKV',
            'radius_meters' => 120,
        ]);
    }

    public function test_admin_can_update_school_settings_cutoff_time()
    {
        $admin = User::create([
            'name' => 'Admin Settings',
            'email' => 'adminset@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $res = $this->actingAs($admin, 'web')->put(route('admin.settings.update'), [
            'school_name' => 'SMK Putra Pertiwi Tangerang Selatan',
            'late_cutoff_time' => '07:30',
            'default_latitude' => -6.34720000,
            'default_longitude' => 106.76450000,
        ]);

        $res->assertRedirect(route('admin.settings.index'));
        $this->assertEquals('07:30', SchoolSetting::get('late_cutoff_time'));
    }

    public function test_student_can_view_schedule_and_history()
    {
        $major = Major::create([
            'name' => 'RPL',
            'code' => 'RPL2',
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
            'name' => 'X RPL 2',
            'major_id' => $major->id,
            'academic_year_id' => $ay->id,
        ]);

        $student = Student::create([
            'class_id' => $class->id,
            'nis' => '1009',
            'name' => 'Schedule Student',
            'email' => 'schedule@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        // Schedule view
        $scheduleRes = $this->actingAs($student, 'student')->get(route('student.schedule'));
        $scheduleRes->assertStatus(200);

        // History view
        $historyRes = $this->actingAs($student, 'student')->get(route('student.history'));
        $historyRes->assertStatus(200);

        // Profile view
        $profileRes = $this->actingAs($student, 'student')->get(route('student.profile'));
        $profileRes->assertStatus(200);
    }
}
