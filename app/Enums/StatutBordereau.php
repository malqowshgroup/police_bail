<?php

namespace App\Enums;

use App\Support\Nomenclatures;

enum StatutBordereau: string
{
    public const CATEGORIE = 'statut_bordereau';

    case EnSaisie     = 'en_saisie';
    case EnControle   = 'en_controle';
    case EnValidation = 'en_validation';
    case Valide       = 'valide';
    case Rejete       = 'rejete';

    public function label(): string
    {
        return Nomenclatures::label(self::CATEGORIE, $this->value, match($this) {
            self::EnSaisie     => 'En saisie',
            self::EnControle   => 'En contrôle',
            self::EnValidation => 'En validation',
            self::Valide       => 'Validé',
            self::Rejete       => 'Rejeté',
        });
    }

    public function badge(): string
    {
        return Nomenclatures::badge(self::CATEGORIE, $this->value, match($this) {
            self::EnSaisie     => 'bg-slate-100 text-slate-600',
            self::EnControle   => 'bg-blue-100 text-blue-700',
            self::EnValidation => 'bg-indigo-100 text-indigo-700',
            self::Valide       => 'bg-green-100 text-green-700',
            self::Rejete       => 'bg-red-100 text-red-700',
        });
    }

    public function dot(): string
    {
        return Nomenclatures::dot(self::CATEGORIE, $this->value, match($this) {
            self::EnSaisie     => 'bg-slate-400',
            self::EnControle   => 'bg-blue-500',
            self::EnValidation => 'bg-indigo-500',
            self::Valide       => 'bg-green-500',
            self::Rejete       => 'bg-red-500',
        });
    }

    /** Étape suivante du circuit (null si état terminal). */
    public function suivant(): ?self
    {
        return match($this) {
            self::EnSaisie     => self::EnControle,
            self::EnControle   => self::EnValidation,
            self::EnValidation => self::Valide,
            default            => null,
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
