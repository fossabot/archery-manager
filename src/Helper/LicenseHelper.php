<?php

namespace App\Helper;

use App\DBAL\Types\EventType;
use App\DBAL\Types\LicenseAgeCategoryType;
use App\DBAL\Types\LicenseCategoryType;
use App\DBAL\Types\LicenseType;
use App\Entity\License;

class LicenseHelper
{
    protected int $season = 2023;

    protected array $mappingSeason = [
        2023 => [
            '<1964-01-01' => LicenseAgeCategoryType::SENIOR_3,
            '>1964-01-01_<1983-12-31' => LicenseAgeCategoryType::SENIOR_2,
            '>1984-01-01_<2002-12-31' => LicenseAgeCategoryType::SENIOR_1,
            '>2003-01-01_<2005-12-31' => LicenseAgeCategoryType::U21,
            '>2006-01-01_<2008-12-31' => LicenseAgeCategoryType::U18,
            '>2009-01-01_<2010-12-31' => LicenseAgeCategoryType::U15,
            '>2011-01-01_<2012-12-31' => LicenseAgeCategoryType::U13,
            '>2013-01-01' => LicenseAgeCategoryType::U11,
        ],
    ];

    public function __construct(private readonly LicenseeHelper $licenseeHelper)
    {
        $this->season = 2023;
    }

    public function getCurrentLicenseeCurrentLicense(): License
    {
        $licensee = $this->licenseeHelper->getLicenseeFromSession();

        return $licensee->getLicenseForSeason($this->season);
    }

    public function licenseIsValidForEventType(License $license, string $eventType): bool
    {
        EventType::assertValidChoice($eventType);
        $licenseType = $license->getType();
        $isValid = false;
        if (EventType::CONTEST_OFFICIAL === $eventType) {
            if (in_array($licenseType, [LicenseType::ADULTES_COMPETITION, LicenseType::JEUNES])) {
                $isValid = true;
            }
        } elseif (EventType::CONTEST_HOBBY === $eventType) {
            if (in_array($licenseType, [LicenseType::ADULTES_CLUB, LicenseType::JEUNES, LicenseType::POUSSINS])) {
                $isValid = true;
            }
        } elseif (EventType::TRAINING) {
            $isValid = true;
        } elseif (EventType::OTHER) {
            $isValid = true;
        }

        return $isValid;
    }

    public function licenseTypeForBirthdate(
        \DateTimeInterface $birthdate,
        bool $tournament,
    ): string {
        $categoryType = $this->licenseCategoryTypeForBirthdate($birthdate);

        return match (true) {
            LicenseCategoryType::ADULTES === $categoryType && $tournament => LicenseType::ADULTES_COMPETITION,
            LicenseCategoryType::ADULTES === $categoryType && !$tournament => LicenseType::ADULTES_CLUB,
            LicenseCategoryType::JEUNES === $categoryType => LicenseType::JEUNES,
            LicenseCategoryType::POUSSINS === $categoryType => LicenseType::POUSSINS,
        };
    }

    public function licenseCategoryTypeForBirthdate(
        \DateTimeInterface $birthdate,
    ): string {
        $ageCategory = $this->ageCategoryForBirthdate($birthdate);

        return $this->categoryTypeForAgeCategory($ageCategory);
    }

    public function categoryTypeForAgeCategory(string $ageCategory): string
    {
        LicenseAgeCategoryType::assertValidChoice($ageCategory);

        return match ($ageCategory) {
            LicenseAgeCategoryType::U11 => LicenseCategoryType::POUSSINS,
            LicenseAgeCategoryType::U13,
            LicenseAgeCategoryType::U15,
            LicenseAgeCategoryType::U18,
            LicenseAgeCategoryType::U21 => LicenseCategoryType::JEUNES,
            LicenseAgeCategoryType::SENIOR_1,
            LicenseAgeCategoryType::SENIOR_2,
            LicenseAgeCategoryType::SENIOR_3 => LicenseCategoryType::ADULTES,
        };
    }

    public function ageCategoryForBirthdate(
        \DateTimeInterface $birthdate,
    ): string {
        $mapping = $this->mappingSeason[$this->season];

        foreach ($mapping as $dateKey => $ageCategory) {
            $parts = explode('_', $dateKey);
            $leftPart = $parts[0];
            $rightPart = $parts[1] ?? null;
            $after = null;
            $before = null;
            if (!$rightPart) {
                $sign = substr($leftPart, 0, 1);
                if ('>' === $sign) {
                    $after = $this->dateTimeFromKeyPart($leftPart);
                }
                if ('<' === $sign) {
                    $before = $this->dateTimeFromKeyPart($leftPart);
                }
            } else {
                $after = $this->dateTimeFromKeyPart($leftPart);
                $before = $this->dateTimeFromKeyPart($rightPart);
            }

            if (
                $birthdate > $after
                && ($birthdate < $before || null === $before)
            ) {
                return $ageCategory;
            }
        }

        throw new \LogicException(sprintf('Should have found a value. %s given', $birthdate->format('Y-m-d')));
    }

    private function dateTimeFromKeyPart(string $keyPart): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(
            'Y-m-d',
            substr($keyPart, 1, 10),
        );
    }
}
