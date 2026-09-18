<?php

namespace App\Enums;

use App\Support\Nomenclatures;

enum StatutDocument: string
{
    public const CATEGORIE = 'statut_document';

    case EnAttente = 'en_attente';
    case Valide    = 'valide';
    case Rejete    = 'rejete';

    public function label(): string
    {
        return Nomenclatures::label(self::CATEGORIE, $this->value, match($this) {
            self::EnAttente => 'En attente',
            self::Valide    => 'Validé',
            self::Rejete    => 'Rejeté',
        });
    }

    public function badge(): string
    {
        return Nomenclatures::badge(self::CATEGORIE, $this->value, match($this) {
            self::EnAttente => 'bg-amber-100 text-amber-700',
            self::Valide    => 'bg-green-100 text-green-700',
            self::Rejete    => 'bg-red-100 text-red-700',
        });
    }

    public function dot(): string
    {
        return Nomenclatures::dot(self::CATEGORIE, $this->value, match($this) {
            self::EnAttente => 'bg-amber-500',
            self::Valide    => 'bg-green-500',
            self::Rejete    => 'bg-red-500',
        });
    }

    public static function options(): array
    {
        return array_map(
            fn(self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
