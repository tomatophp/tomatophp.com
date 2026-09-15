<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use TomatoPHP\FilamentEmployees\Models\AttendanceShift;
use TomatoPHP\FilamentEmployees\Models\EmployeeApply;
use TomatoPHP\FilamentEmployees\Models\EmployeeAttendance;
use TomatoPHP\FilamentTypes\Models\Type;

/**
 * Demo rows for tomatophp/filament-employees: departments, application statuses, shifts,
 * employees (accounts with the employee type and a meta profile), attendance and applications.
 * Requires App\Models\Account to use TomatoPHP\FilamentEmployees\Traits\IsEmployee.
 */
class DemoEmployeesSeeder extends Seeder
{
    public function run(): void
    {
        $this->types('employees', 'departments', [
            'engineering' => ['Engineering', '#2563eb', 'heroicon-o-code-bracket'],
            'design' => ['Design', '#db2777', 'heroicon-o-paint-brush'],
            'hr' => ['Human Resources', '#16a34a', 'heroicon-o-user-group'],
        ]);

        $this->types('employee_apply', 'status', [
            'pending' => ['Pending', '#f59e0b', 'heroicon-o-clock'],
            'interview' => ['Interview', '#0ea5e9', 'heroicon-o-chat-bubble-left-right'],
            'hired' => ['Hired', '#16a34a', 'heroicon-o-check-badge'],
        ]);

        $morning = AttendanceShift::query()->firstOrCreate(['name' => 'Morning'], [
            'department' => 'engineering',
            'start_at' => '09:00:00',
            'end_at' => '17:00:00',
            'offs' => [['start_at' => 'fri'], ['start_at' => 'sat']],
            'is_activated' => true,
        ]);

        AttendanceShift::query()->firstOrCreate(['name' => 'Evening'], [
            'department' => 'design',
            'start_at' => '14:00:00',
            'end_at' => '22:00:00',
            'offs' => [['start_at' => 'fri']],
            'is_activated' => true,
        ]);

        $employees = [
            ['Hany Fawzy', 'Backend Developer', 'engineering', 18000],
            ['Dina Wagdy', 'Product Designer', 'design', 15000],
            ['Tarek Mansour', 'HR Specialist', 'hr', 12000],
            ['Farida Ashraf', 'QA Engineer', 'engineering', 14000],
            ['Mostafa Emad', 'DevOps Engineer', 'engineering', 17000],
        ];

        foreach ($employees as $index => [$name, $position, $department, $salary]) {
            $email = str($name)->slug('.').'@example.com';

            $employee = Account::query()->firstOrCreate(['email' => $email], [
                'name' => $name,
                'username' => $email,
                'phone' => '+2011000000'.$index,
                'type' => 'employee',
                'loginBy' => 'email',
                'address' => 'Cairo, Egypt',
                'is_active' => true,
                'is_login' => false,
            ]);

            $employee->meta('position', $position);
            $employee->meta('department', $department);
            $employee->meta('salary', $salary);
            $employee->meta('attendance_shift_id', $morning->id);
        }

        $first = Account::query()->where('type', 'employee')->first();
        $userId = User::query()->value('id');

        foreach ([['09:05:00', '17:10:00'], ['09:40:00', '18:00:00'], ['08:55:00', '17:30:00']] as $day => [$in, $out]) {
            EmployeeAttendance::query()->firstOrCreate(
                ['account_id' => $first->id, 'date' => now()->subDays($day + 1)->toDateString()],
                ['user_id' => $userId, 'department' => 'engineering', 'source' => 'fingerprint', 'in_at' => $in, 'out_at' => $out, 'total' => 8],
            );
        }

        $applies = [
            ['Youssef', 'Nabil', 'Frontend Developer', 'pending'],
            ['Mariam', 'Samir', 'Data Analyst', 'interview'],
            ['Omar', 'Hassan', 'Support Engineer', 'hired'],
        ];

        foreach ($applies as $index => [$first, $last, $position, $status]) {
            EmployeeApply::query()->firstOrCreate(['email' => strtolower("{$first}.{$last}").'@example.com'], [
                'first_name' => $first,
                'last_name' => $last,
                'address' => 'Giza, Egypt',
                'phone' => '+2012000000'.$index,
                'id_type' => 'national',
                'id_number' => '2900101123456'.$index,
                'position' => $position,
                'status' => $status,
                'explicated_salary' => 12000 + $index * 1000,
                'is_activated' => true,
                'ready_for_interview' => $status !== 'pending',
            ]);
        }
    }

    /**
     * @param  array<string, array{0: string, 1: string, 2: string}>  $types
     */
    protected function types(string $for, string $type, array $types): void
    {
        foreach ($types as $key => [$name, $color, $icon]) {
            Type::query()->firstOrCreate(
                ['for' => $for, 'type' => $type, 'key' => $key],
                ['name' => ['en' => $name], 'color' => $color, 'icon' => $icon],
            );
        }
    }
}
