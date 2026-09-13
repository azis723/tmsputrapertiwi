<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Category;
use App\Models\Holiday;
use App\Models\Major;
use App\Models\Post;
use App\Models\Schedule;
use App\Models\ScheduleTime;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        $settings = [
            'school_name' => 'SMK Putra Pertiwi',
            'late_cutoff_time' => '07:15',
            'default_latitude' => '-6.34720000',
            'default_longitude' => '106.76450000',
            'school_address' => 'Jl. Pondok Cabe Ilir No. 45, Pamulang, Tangerang Selatan, Banten',
            'school_phone' => '(021) 7491234',
        ];

        foreach ($settings as $k => $v) {
            SchoolSetting::set($k, $v);
        }

        // 2. Administrator Account
        User::updateOrCreate(
            ['email' => 'admin@putrapertiwi.sch.id'],
            [
                'name' => 'Administrator SMK Putra Pertiwi',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 3. Majors with Pondok Cabe Ilir Geofence Coordinates
        $rpl = Major::create([
            'name' => 'Rekayasa Perangkat Lunak',
            'code' => 'RPL',
            'latitude' => -6.34720000,
            'longitude' => 106.76450000,
            'radius_meters' => 100,
        ]);

        $dkv = Major::create([
            'name' => 'Desain Komunikasi Visual',
            'code' => 'DKV',
            'latitude' => -6.34735000,
            'longitude' => 106.76465000,
            'radius_meters' => 100,
        ]);

        $tkj = Major::create([
            'name' => 'Teknik Komputer & Jaringan',
            'code' => 'TKJ',
            'latitude' => -6.34710000,
            'longitude' => 106.76435000,
            'radius_meters' => 100,
        ]);

        $otkp = Major::create([
            'name' => 'Otomatisasi & Tata Kelola Perkantoran',
            'code' => 'OTKP',
            'latitude' => -6.34725000,
            'longitude' => 106.76480000,
            'radius_meters' => 100,
        ]);

        // 4. Reference Teachers
        $teacherBudi = Teacher::create([
            'nip' => '198501102009011001',
            'name' => 'Budi Santoso, S.Kom.',
            'email' => 'budi.santoso@putrapertiwi.sch.id',
            'phone' => '081234567890',
            'address' => 'Pondok Cabe Ilir, Tangerang Selatan',
            'status' => 'active',
        ]);

        $teacherSiti = Teacher::create([
            'nip' => '198803152011012002',
            'name' => 'Siti Nurhaliza, S.Pd.',
            'email' => 'siti.nurhaliza@putrapertiwi.sch.id',
            'phone' => '081234567891',
            'address' => 'Pamulang Permai, Tangerang Selatan',
            'status' => 'active',
        ]);

        $teacherHendra = Teacher::create([
            'nip' => '198207202008011003',
            'name' => 'Hendra Wijaya, M.Kom.',
            'email' => 'hendra.wijaya@putrapertiwi.sch.id',
            'phone' => '081234567892',
            'address' => 'Cirendeu, Ciputat Timur',
            'status' => 'active',
        ]);

        $teacherDewi = Teacher::create([
            'nip' => '199011052014022004',
            'name' => 'Dewi Lestari, S.Sn.',
            'email' => 'dewi.lestari@putrapertiwi.sch.id',
            'phone' => '081234567893',
            'address' => 'Cinere, Depok',
            'status' => 'active',
        ]);

        $teacherAhmad = Teacher::create([
            'nip' => '198705122010011005',
            'name' => 'Ahmad Fauzi, M.Pd.',
            'email' => 'ahmad.fauzi@putrapertiwi.sch.id',
            'phone' => '081234567894',
            'address' => 'Pondok Ranji, Ciputat',
            'status' => 'active',
        ]);

        // 5. Academic Years
        $academicYearActive = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 'genap',
            'is_active' => false,
        ]);

        // 6. Classes
        $classXRPL1 = SchoolClass::create([
            'name' => 'X RPL 1',
            'major_id' => $rpl->id,
            'academic_year_id' => $academicYearActive->id,
            'teacher_id' => $teacherBudi->id,
        ]);

        $classXIRPL1 = SchoolClass::create([
            'name' => 'XI RPL 1',
            'major_id' => $rpl->id,
            'academic_year_id' => $academicYearActive->id,
            'teacher_id' => $teacherHendra->id,
        ]);

        $classXDKV1 = SchoolClass::create([
            'name' => 'X DKV 1',
            'major_id' => $dkv->id,
            'academic_year_id' => $academicYearActive->id,
            'teacher_id' => $teacherDewi->id,
        ]);

        $classXTKJ1 = SchoolClass::create([
            'name' => 'X TKJ 1',
            'major_id' => $tkj->id,
            'academic_year_id' => $academicYearActive->id,
            'teacher_id' => $teacherAhmad->id,
        ]);

        $classXOTKP1 = SchoolClass::create([
            'name' => 'X OTKP 1',
            'major_id' => $otkp->id,
            'academic_year_id' => $academicYearActive->id,
            'teacher_id' => $teacherSiti->id,
        ]);

        // 7. Students (Ready for Testing)
        // Main test student without today attendance so user can test attendance immediately:
        $studentAzis = Student::create([
            'class_id' => $classXRPL1->id,
            'nis' => '1001',
            'name' => 'Muhammad Azis',
            'email' => 'siswa@putrapertiwi.sch.id',
            'password' => Hash::make('password'),
            'status' => 'active',
            'face_embedding' => null, // Ready to test face registration!
        ]);

        $studentRian = Student::create([
            'class_id' => $classXRPL1->id,
            'nis' => '1002',
            'name' => 'Ahmad Rian Pratama',
            'email' => 'rian@putrapertiwi.sch.id',
            'password' => Hash::make('password'),
            'status' => 'active',
            'face_embedding' => json_encode(array_fill(0, 128, 0.05)),
        ]);

        $studentSalsa = Student::create([
            'class_id' => $classXDKV1->id,
            'nis' => '1003',
            'name' => 'Salsabila Putri',
            'email' => 'salsa@putrapertiwi.sch.id',
            'password' => Hash::make('password'),
            'status' => 'active',
            'face_embedding' => json_encode(array_fill(0, 128, 0.04)),
        ]);

        $studentFarhan = Student::create([
            'class_id' => $classXTKJ1->id,
            'nis' => '1004',
            'name' => 'Farhan Ramadhan',
            'email' => 'farhan@putrapertiwi.sch.id',
            'password' => Hash::make('password'),
            'status' => 'active',
            'face_embedding' => json_encode(array_fill(0, 128, 0.03)),
        ]);

        $studentNabila = Student::create([
            'class_id' => $classXOTKP1->id,
            'nis' => '1005',
            'name' => 'Nabila Khairunnisa',
            'email' => 'nabila@putrapertiwi.sch.id',
            'password' => Hash::make('password'),
            'status' => 'active',
            'face_embedding' => json_encode(array_fill(0, 128, 0.02)),
        ]);

        // Secondary test student without face registered & without today attendance
        $studentBayu = Student::create([
            'class_id' => $classXRPL1->id,
            'nis' => '1006',
            'name' => 'Bayu Pratama (Uji Wajah 2)',
            'email' => 'bayu@putrapertiwi.sch.id',
            'password' => Hash::make('password'),
            'status' => 'active',
            'face_embedding' => null, // Ready to test face registration!
        ]);

        // 8. Subjects
        $subWeb = Subject::create([
            'code' => 'RPL-01',
            'name' => 'Pemrograman Web & Mobile',
            'teacher_id' => $teacherBudi->id,
        ]);

        $subPbo = Subject::create([
            'code' => 'RPL-02',
            'name' => 'Pemrograman Berorientasi Objek',
            'teacher_id' => $teacherHendra->id,
        ]);

        $subDkv = Subject::create([
            'code' => 'DKV-01',
            'name' => 'Desain Grafis & Tipografi',
            'teacher_id' => $teacherDewi->id,
        ]);

        $subTkj = Subject::create([
            'code' => 'TKJ-01',
            'name' => 'Administrasi Jaringan Komputer',
            'teacher_id' => $teacherAhmad->id,
        ]);

        $subMtk = Subject::create([
            'code' => 'UMUM-01',
            'name' => 'Matematika Terapan',
            'teacher_id' => $teacherSiti->id,
        ]);

        // 9. Schedule Times
        $stSenin1 = ScheduleTime::create(['day' => 'Monday', 'start_time' => '07:00', 'end_time' => '08:30']);
        $stSenin2 = ScheduleTime::create(['day' => 'Monday', 'start_time' => '08:30', 'end_time' => '10:00']);
        $stSelasa1 = ScheduleTime::create(['day' => 'Tuesday', 'start_time' => '07:00', 'end_time' => '08:30']);
        $stRabu1 = ScheduleTime::create(['day' => 'Wednesday', 'start_time' => '07:00', 'end_time' => '08:30']);
        $stKamis1 = ScheduleTime::create(['day' => 'Thursday', 'start_time' => '07:00', 'end_time' => '08:30']);
        $stJumat1 = ScheduleTime::create(['day' => 'Friday', 'start_time' => '07:00', 'end_time' => '08:30']);

        // 10. Schedules
        Schedule::create([
            'class_id' => $classXRPL1->id,
            'subject_id' => $subWeb->id,
            'teacher_id' => $teacherBudi->id,
            'academic_year_id' => $academicYearActive->id,
            'schedule_time_id' => $stSenin1->id,
        ]);

        Schedule::create([
            'class_id' => $classXRPL1->id,
            'subject_id' => $subMtk->id,
            'teacher_id' => $teacherSiti->id,
            'academic_year_id' => $academicYearActive->id,
            'schedule_time_id' => $stSenin2->id,
        ]);

        Schedule::create([
            'class_id' => $classXRPL1->id,
            'subject_id' => $subPbo->id,
            'teacher_id' => $teacherHendra->id,
            'academic_year_id' => $academicYearActive->id,
            'schedule_time_id' => $stSelasa1->id,
        ]);

        Schedule::create([
            'class_id' => $classXDKV1->id,
            'subject_id' => $subDkv->id,
            'teacher_id' => $teacherDewi->id,
            'academic_year_id' => $academicYearActive->id,
            'schedule_time_id' => $stSenin1->id,
        ]);

        Schedule::create([
            'class_id' => $classXTKJ1->id,
            'subject_id' => $subTkj->id,
            'teacher_id' => $teacherAhmad->id,
            'academic_year_id' => $academicYearActive->id,
            'schedule_time_id' => $stSenin1->id,
        ]);

        // 11. Sample Holidays
        Holiday::create([
            'date' => '2026-08-17',
            'title' => 'Hari Kemerdekaan Republik Indonesia',
        ]);
        Holiday::create([
            'date' => '2026-12-25',
            'title' => 'Hari Raya Natal',
        ]);
        Holiday::create([
            'date' => '2026-01-01',
            'title' => 'Tahun Baru Masehi',
        ]);

        // 12. Sample Historical Attendances for other students
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        $twoDaysAgo = Carbon::today()->subDays(2)->toDateString();

        // Yesterday attendances
        Attendance::create([
            'student_id' => $studentAzis->id,
            'date' => $yesterday,
            'time' => '07:05:00',
            'status' => 'present',
            'latitude' => -6.34718000,
            'longitude' => 106.76451000,
            'notes' => 'Presensi mandiri Geofencing + Face Recognition',
        ]);

        Attendance::create([
            'student_id' => $studentRian->id,
            'date' => $yesterday,
            'time' => '07:22:00',
            'status' => 'late',
            'latitude' => -6.34719000,
            'longitude' => 106.76452000,
            'notes' => 'Presensi mandiri Geofencing + Face Recognition',
        ]);

        // Two days ago
        Attendance::create([
            'student_id' => $studentAzis->id,
            'date' => $twoDaysAgo,
            'time' => '07:10:00',
            'status' => 'present',
            'latitude' => -6.34717500,
            'longitude' => 106.76449000,
            'notes' => 'Presensi mandiri Geofencing + Face Recognition',
        ]);

        // Today attendances for other students (studentAzis has not attended today so he can test live!)
        Attendance::create([
            'student_id' => $studentRian->id,
            'date' => $today,
            'time' => '07:08:15',
            'status' => 'present',
            'latitude' => -6.34718500,
            'longitude' => 106.76450500,
            'notes' => 'Presensi mandiri Geofencing + Face Recognition',
        ]);

        Attendance::create([
            'student_id' => $studentSalsa->id,
            'date' => $today,
            'time' => '07:20:30',
            'status' => 'late',
            'latitude' => -6.34734000,
            'longitude' => 106.76466000,
            'notes' => 'Presensi mandiri Geofencing + Face Recognition',
        ]);

        Attendance::create([
            'student_id' => $studentFarhan->id,
            'date' => $today,
            'time' => '07:12:00',
            'status' => 'present',
            'latitude' => -6.34711000,
            'longitude' => 106.76436000,
            'notes' => 'Presensi mandiri Geofencing + Face Recognition',
        ]);

        // 13. Categories & Posts
        $catPengumuman = Category::create(['name' => 'Pengumuman Akademik', 'slug' => 'pengumuman-akademik']);
        $catPrestasi = Category::create(['name' => 'Prestasi Siswa', 'slug' => 'prestasi-siswa']);
        $catKegiatan = Category::create(['name' => 'Kegiatan Sekolah', 'slug' => 'kegiatan-sekolah']);

        Post::create([
            'category_id' => $catPengumuman->id,
            'title' => 'Pengumuman Pelaksanaan Presensi Mandiri Berbasis Geofencing & Face Recognition',
            'slug' => 'pengumuman-presensi-geofencing-face-recognition',
            'content' => "Diberitahukan kepada seluruh siswa/i SMK Putra Pertiwi bahwa mulai semester ganjil tahun ajaran 2026/2027, sekolah resmi memberlakukan sistem presensi mandiri berbasis titik radius lokasi (Geofencing) dan pemindaian wajah (Face Recognition).\n\nSiswa diwajibkan melakukan presensi sebelum pukul 07:15 WIB di area gedung jurusan masing-masing.",
            'status' => 'published',
        ]);

        Post::create([
            'category_id' => $catPrestasi->id,
            'title' => 'Siswa Rekayasa Perangkat Lunak Raih Juara 1 LKS Tingkat Kota Tangerang Selatan',
            'slug' => 'siswa-rpl-raih-juara-1-lks-tangsel',
            'content' => "Selamat kepada perwakilan siswa jurusan Rekayasa Perangkat Lunak (RPL) SMK Putra Pertiwi yang berhasil meraih juara pertama dalam Lomba Kompetensi Siswa (LKS) bidang Web Technologies tingkat Kota Tangerang Selatan.",
            'status' => 'published',
        ]);
    }
}
