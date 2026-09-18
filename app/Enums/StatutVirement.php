<?php

namespace App\Enums;

use App\Support\Nomenclatures;

enum StatutVirement: string
{
    public const CATEGORIE = 'statut_virement';

    case EnPreparation = 'en_preparation';
    case Emis          = 'emis';
    case Execute       = 'execute';
    case Annule        = 'annule';

    public function label(): string
    {
        return Nomenclatures::label(self::CATEGORIE, $this->value, match($this) {
            self::EnPreparation => 'En préparation',
            self::Emis          => 'Émis',
            self::Execute       => 'Exécuté',
            self::Annule        => 'Annulé',
        });
    }

    public function badge(): string
    {
        return Nomenclatures::badge(self::CATEGORIE, $this->value, match($this) {
            self::EnPreparation => 'bg-slate-100 text-slate-600',
            self::Emis          => 'bg-blue-100 text-blue-700',
            self::Execute       => 'bg-green-100 text-green-700',
            self::Annule        => 'bg-red-100 text-red-700',
        });
    }

    public function dot(): string
    {
        return Nomenclatures::dot(self::CATEGORIE, $this->value, match($this) {
            self::EnPreparation => 'bg-slate-400',
            self::Emis          => 'bg-blue-500',
            self::Execute       => 'bg-green-500',
            self::Annule        => 'bg-red-500',
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
