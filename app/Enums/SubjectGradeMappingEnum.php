<?php

namespace App\Enums;

enum SubjectGradeMappingEnum: int
{
    // 9th grade and above officers
    case NINTH_GRADE_ALL = 1;
    case NINTH_GRADE_SINGLE = 2;
    case NINTH_GRADE_SUBJECT_BASED = 3;

    // 10th grade officers
    case TENTH_GRADE_ALL = 4;
    case TENTH_GRADE_SINGLE = 5;
    case TENTH_GRADE_SUBJECT_BASED = 6;

    // 11th-18th grade staff
    case ELEVENTH_TO_EIGHTEENTH_GRADE_ALL = 7;
    case ELEVENTH_TO_EIGHTEENTH_GRADE_SINGLE = 8;
    case ELEVENTH_TO_EIGHTEENTH_GRADE_SUBJECT_BASED = 9;

    // 19th-20th grade staff
    case NINETEENTH_TO_TWENTIETH_GRADE_ALL = 10;
    case NINETEENTH_TO_TWENTIETH_GRADE_SINGLE = 11;
    case NINETEENTH_TO_TWENTIETH_GRADE_SUBJECT_BASED = 12;

    // Project based
    case PROJECT_BASED = 13;

    /**
     * Get designation range for the subject
     */
    public function getDesignationRange(): array|string
    {
        return match($this) {
            self::NINTH_GRADE_ALL,
            self::NINTH_GRADE_SINGLE,
            self::NINTH_GRADE_SUBJECT_BASED => range(1, 29),

            self::TENTH_GRADE_ALL,
            self::TENTH_GRADE_SINGLE,
            self::TENTH_GRADE_SUBJECT_BASED => range(30, 34),

            self::ELEVENTH_TO_EIGHTEENTH_GRADE_ALL,
            self::ELEVENTH_TO_EIGHTEENTH_GRADE_SINGLE,
            self::ELEVENTH_TO_EIGHTEENTH_GRADE_SUBJECT_BASED => range(35, 45),

            self::NINETEENTH_TO_TWENTIETH_GRADE_ALL,
            self::NINETEENTH_TO_TWENTIETH_GRADE_SINGLE,
            self::NINETEENTH_TO_TWENTIETH_GRADE_SUBJECT_BASED => '>45',

            self::PROJECT_BASED => []
        };
    }

    /**
     * Check if this subject requires employee selection
     */
    public function requiresEmployee(): bool
    {
        return match($this) {
            self::NINTH_GRADE_SINGLE,
            self::TENTH_GRADE_SINGLE,
            self::ELEVENTH_TO_EIGHTEENTH_GRADE_SINGLE,
            self::NINETEENTH_TO_TWENTIETH_GRADE_SINGLE => true,
            default => false
        };
    }

    /**
     * Check if this subject requires training selection
     */
    public function requiresTraining(): bool
    {
        return match($this) {
            self::NINTH_GRADE_SUBJECT_BASED,
            self::TENTH_GRADE_SUBJECT_BASED,
            self::ELEVENTH_TO_EIGHTEENTH_GRADE_SUBJECT_BASED,
            self::NINETEENTH_TO_TWENTIETH_GRADE_SUBJECT_BASED => true,
            default => false
        };
    }

    /**
     * Check if this is a grade-wise all employee report
     */
    public function isGradeWiseAllReport(): bool
    {
        return match($this) {
            self::NINTH_GRADE_ALL,
            self::NINTH_GRADE_SUBJECT_BASED,
            self::TENTH_GRADE_ALL,
            self::TENTH_GRADE_SUBJECT_BASED,
            self::ELEVENTH_TO_EIGHTEENTH_GRADE_ALL,
            self::ELEVENTH_TO_EIGHTEENTH_GRADE_SUBJECT_BASED,
            self::NINETEENTH_TO_TWENTIETH_GRADE_ALL,
            self::NINETEENTH_TO_TWENTIETH_GRADE_SUBJECT_BASED => true,
            default => false
        };
    }

    /**
     * Check if this is a single employee report
     */
    public function isSingleEmployeeReport(): bool
    {
        return match($this) {
            self::NINTH_GRADE_SINGLE,
            self::TENTH_GRADE_SINGLE,
            self::ELEVENTH_TO_EIGHTEENTH_GRADE_SINGLE,
            self::NINETEENTH_TO_TWENTIETH_GRADE_SINGLE => true,
            default => false
        };
    }

    /**
     * Get enum instance from integer value
     */
    public static function fromInt(int $value): ?self
    {
        return self::tryFrom($value);
    }

    /**
     * Get all grade-wise all employee subjects
     */
    public static function getGradeWiseAllEmployeeSubjects(): array
    {
        return [
            self::NINTH_GRADE_ALL->value,
            self::NINTH_GRADE_SUBJECT_BASED->value,
            self::TENTH_GRADE_ALL->value,
            self::TENTH_GRADE_SUBJECT_BASED->value,
            self::ELEVENTH_TO_EIGHTEENTH_GRADE_ALL->value,
            self::ELEVENTH_TO_EIGHTEENTH_GRADE_SUBJECT_BASED->value,
            self::NINETEENTH_TO_TWENTIETH_GRADE_ALL->value,
            self::NINETEENTH_TO_TWENTIETH_GRADE_SUBJECT_BASED->value,
        ];
    }

    /**
     * Get all single employee subjects
     */
    public static function getSingleEmployeeSubjects(): array
    {
        return [
            self::NINTH_GRADE_SINGLE->value,
            self::TENTH_GRADE_SINGLE->value,
            self::ELEVENTH_TO_EIGHTEENTH_GRADE_SINGLE->value,
            self::NINETEENTH_TO_TWENTIETH_GRADE_SINGLE->value,
        ];
    }

    /**
     * Get grade search term mapping
     */
    public static function getGradeSearchMapping(): array
    {
        return [
            'grade-9' => self::NINTH_GRADE_ALL,
            'grade-10' => self::TENTH_GRADE_ALL,
            'grade-11-18' => self::ELEVENTH_TO_EIGHTEENTH_GRADE_ALL,
            'grade-19-20' => self::NINETEENTH_TO_TWENTIETH_GRADE_ALL,
        ];
    }

    /**
     * Get enum from grade search term
     */
    public static function fromGradeSearchTerm(string $searchTerm): ?self
    {
        $mapping = self::getGradeSearchMapping();
        return $mapping[strtolower($searchTerm)] ?? null;
    }
}