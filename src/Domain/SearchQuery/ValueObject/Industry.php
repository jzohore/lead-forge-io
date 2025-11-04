<?php

declare(strict_types=1);

namespace App\Domain\SearchQuery\ValueObject;

enum Industry: string
{
    case SAAS = 'saas';
    case FINTECH = 'fintech';
    case ECOMMERCE = 'ecommerce';
    case CYBERSECURITY = 'cybersecurity';
    case MARKETING = 'marketing';
    case HEALTHTECH = 'healthtech';
    case EDTECH = 'edtech';
    case IOT = 'iot';
    case CONSULTING = 'consulting';
    case MEDIA = 'media';

    public function label(): string
    {
        return match ($this) {
            self::SAAS => 'SaaS',
            self::FINTECH => 'Fintech',
            self::ECOMMERCE => 'E-commerce',
            self::CYBERSECURITY => 'Cybersecurity',
            self::MARKETING => 'Marketing / Agences',
            self::HEALTHTECH => 'HealthTech',
            self::EDTECH => 'EdTech',
            self::IOT => 'IoT / Hardware',
            self::CONSULTING => 'Conseil / Services',
            self::MEDIA => 'Média / Communication',
        };
    }
}
