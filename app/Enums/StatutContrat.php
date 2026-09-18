<?php

namespace App\Enums;

use App\Support\Nomenclatures;

enum StatutContrat: string
{
    public const CATEGORIE = 'statut_contrat';

    case EnAttente     = 'en_attente';
    case Actif         = 'actif';
    case Suspendu      = 'suspendu';
    case EnResiliation = 'en_resiliation';
    case Resilie       = 'resilie';

    public function label(): string
    {
        return Nomenclatures::label(self::CATEGORIE, $this->value, match($this) {
            self::EnAttente     => 'En attente',
            self::Actif         => 'Actif',
            self::Suspendu      => 'Suspendu',
            self::EnResiliation => 'En résiliation',
            self::Resilie       => 'Résilié',
        });
    }

    public function badge(): string
    {
        return Nomenclatures::badge(self::CATEGORIE, $this->value, match($this) {
            self::EnAttente     => 'bg-slate-100 text-slate-600',
            self::Actif         => 'bg-green-100 text-green-700',
            self::Suspendu      => 'bg-amber-100 text-amber-700',
            self::EnResiliation => 'bg-orange-100 text-orange-700',
            self::Resilie       => 'bg-red-100 text-red-700',
        });
    }

    public function dot(): string
    {
        return Nomenclatures::dot(self::CATEGORIE, $this->value, match($this) {
            self::EnAttente     => 'bg-slate-400',
            self::Actif         => 'bg-green-500',
            self::Suspendu      => 'bg-amber-500',
            self::EnResiliation => 'bg-orange-500',
            self::Resilie       => 'bg-red-500',
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
