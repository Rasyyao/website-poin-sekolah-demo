<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Kesiswaan = 'kesiswaan';
    case Teacher = 'teacher';
    case Homeroom = 'homeroom';
    case Counselor = 'counselor'; // BK

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin Sekolah',
            self::Kesiswaan => 'Kesiswaan',
            self::Teacher => 'Guru',
            self::Homeroom => 'Wali Kelas',
            self::Counselor => 'Guru BK',
        };
    }

    /**
     * Roles that belong to a specific school (non-super-admin).
     */
    public static function schoolRoles(): array
    {
        return [
            self::Admin,
            self::Kesiswaan,
            self::Teacher,
            self::Homeroom,
            self::Counselor,
        ];
    }

    /**
     * Roles that can input student points.
     */
    public static function canInputPoints(): array
    {
        return [
            self::Admin,
            self::Kesiswaan,
            self::Teacher,
            self::Homeroom,
            self::Counselor,
        ];
    }
}
