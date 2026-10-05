<?php

namespace App\Enums;

enum DesignationEnum: string {
    case INTERN = 'intern';
    case TL = 'tl';
    case EXECUTIVE = 'executive';
    case MANAGER = 'manager';


    public function label(): string {
        return match ($this) {
            self::INTERN => 'Intern',
            self::TL => 'Team Leader (TL)',
            self::EXECUTIVE => 'Executive',
            self::MANAGER => 'Manager',
        };
    }

    public static function all_designations(): array {
        return [
            self::MANAGER,
            self::TL,
            self::EXECUTIVE,
            self::INTERN,
        ];
    }
}
