<?php

namespace Database\Seeders;

use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use App\Domain\Auth\Services\ModuleAccessService;
use App\Domain\Staff\Models\Staff;
use App\Domain\Students\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleAccountSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'oscar@gmail.com')->first();
        if ($admin) {
            $admin->update(['modules' => ModuleAccessService::ALL_MODULES]);
        }

        $staffAccounts = [
            ['staff_no' => 'STF-0001', 'username' => 'brian.mutebi', 'email' => 'brian@demo.school', 'password' => 'Staff123!', 'modules' => ['dashboard', 'academic', 'staff', 'students', 'curriculum', 'assessment', 'reports', 'attendance', 'timetable']],
            ['staff_no' => 'STF-0002', 'username' => 'grace.namutebi', 'email' => 'grace@demo.school', 'password' => 'Staff123!', 'modules' => ['dashboard', 'students', 'curriculum', 'assessment', 'attendance']],
            ['staff_no' => 'STF-0003', 'username' => 'samuel.okello', 'email' => 'samuel@demo.school', 'password' => 'Staff123!', 'modules' => ['dashboard', 'students', 'curriculum', 'assessment', 'attendance']],
            ['staff_no' => 'STF-0004', 'username' => 'sarah.nakato', 'email' => 'sarah@demo.school', 'password' => 'Staff123!', 'modules' => ['dashboard', 'students', 'curriculum', 'assessment', 'attendance']],
            ['staff_no' => 'STF-0005', 'username' => 'peter.kato', 'email' => 'peter@demo.school', 'password' => 'Staff123!', 'modules' => ['dashboard', 'students', 'curriculum', 'assessment', 'attendance']],
            ['staff_no' => 'STF-0006', 'username' => 'esther.muhindo', 'email' => 'esther@demo.school', 'password' => 'Staff123!', 'modules' => ['dashboard', 'finance', 'students']],
            ['staff_no' => 'STF-0007', 'username' => 'john.ochieng', 'email' => 'john@demo.school', 'password' => 'Staff123!', 'modules' => ['dashboard', 'students', 'curriculum', 'assessment', 'attendance']],
            ['staff_no' => 'STF-0008', 'username' => 'mariam.nabbanja', 'email' => 'mariam@demo.school', 'password' => 'Staff123!', 'modules' => ['dashboard', 'library']],
            ['staff_no' => 'STF-0009', 'username' => 'richard.ssempijja', 'email' => 'richard@demo.school', 'password' => 'Staff123!', 'modules' => ['dashboard', 'students', 'curriculum', 'assessment', 'attendance']],
            ['staff_no' => 'STF-0010', 'username' => 'alice.kyomugisha', 'email' => 'alice@demo.school', 'password' => 'Staff123!', 'modules' => ['dashboard', 'students', 'curriculum', 'assessment', 'attendance']],
        ];

        foreach ($staffAccounts as $account) {
            $staff = Staff::where('staff_no', $account['staff_no'])->first();
            if (!$staff) continue;

            $modules = $account['modules'];
            unset($account['staff_no']);

            $teacherRole = Role::findBySlug('teacher', 1);

            $user = User::create([
                'school_id' => 1,
                'role_id' => $teacherRole?->id ?? 4,
                'username' => $account['username'],
                'name' => $staff->first_name . ' ' . $staff->last_name,
                'email' => $account['email'],
                'password' => Hash::make($account['password']),
                'status' => 'active',
                'modules' => $modules,
                'email_verified_at' => now(),
            ]);

            $staff->update(['user_id' => $user->id]);
        }

        $this->command->info('  ✅ 10 staff user accounts created');

        $student = Student::where('admission_no', 'S26-0001')->first();
        if ($student) {
            $studentRole = Role::findBySlug('student', 1);

            User::create([
                'school_id' => 1,
                'role_id' => $studentRole?->id ?? 8,
                'username' => 'faith.achieng',
                'name' => $student->first_name . ' ' . $student->last_name,
                'email' => 'faith@demo.school',
                'password' => Hash::make('Student123!'),
                'status' => 'active',
                'modules' => ['dashboard', 'reports', 'attendance', 'timetable'],
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('  ✅ 1 student user account created');

        $this->command->info('');
        $this->command->info('  🔑 Sample Login Credentials:');
        $this->command->info('  ─────────────────────────────────────');
        $this->command->info('  Admin:   oscar@gmail.com / ChangeMe123!');
        $this->command->info('  Teacher: brian@demo.school / Staff123!  (DOS - all academic modules)');
        $this->command->info('  Teacher: grace@demo.school / Staff123! (teacher - limited modules)');
        $this->command->info('  Bursar:  esther@demo.school / Staff123! (finance + dashboard + students)');
        $this->command->info('  Student: faith@demo.school  / Student123!');
    }
}
