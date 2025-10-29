<?php

namespace App\Infrastructure\Shared\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'alert',
    template: 'components/alert.html.twig'
)]
class Alert
{
    public string $message;
    public string $type = 'success'; // ✅ Valeur par défaut
}
