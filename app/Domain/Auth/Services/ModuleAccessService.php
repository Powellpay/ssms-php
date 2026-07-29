<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Models\User;

class ModuleAccessService
{
    public const ALL_MODULES = [
        'dashboard', 'academic', 'staff', 'students', 'curriculum',
        'assessment', 'reports', 'attendance', 'timetable', 'finance',
        'discipline', 'library', 'announcements',
    ];

    public const MODULE_LABELS = [
        'dashboard' => 'Dashboard',
        'academic' => 'Academic',
        'staff' => 'Staff',
        'students' => 'Students',
        'curriculum' => 'Curriculum',
        'assessment' => 'Assessment',
        'reports' => 'Reports',
        'attendance' => 'Attendance',
        'timetable' => 'Timetable',
        'finance' => 'Finance',
        'discipline' => 'Discipline',
        'library' => 'Library',
        'announcements' => 'Announcements',
    ];

    public function isSchoolAdmin(User $user): bool
    {
        return $user->role_id === 1 || $user->school?->owner_id === $user->id;
    }

    public function getAllowedModules(User $user): array
    {
        if ($this->isSchoolAdmin($user)) {
            return self::ALL_MODULES;
        }

        return array_intersect(
            $user->modules ?? [],
            self::ALL_MODULES
        );
    }

    public function canAccess(User $user, string $module): bool
    {
        return in_array($module, $this->getAllowedModules($user), true);
    }
}
