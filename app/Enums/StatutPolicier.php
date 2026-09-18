<?php

namespace App\Enums;

use App\Support\Nomenclatures;

enum StatutPolicier: string
{
    public const CATEGORIE = 'statut_policier';

    case Actif         = 'actif';
    case Suspendu      = 'suspendu';
    case Retraite      = 'retraite';
    case Decede        = 'decede';
    case Radie         = 'radie';
    case Disponibilite = 'disponibilite';
    case Demission     = 'demission';
    case HorsCadre     = 'hors_cadre';
    case Detachement   = 'detachement';
    case Stagiaire     = 'stagiaire';

    public function label(): string
    {
        return Nomenclatures::label(self::CATEGORIE, $this->value, match($this) {
            self::Actif         => 'Actif',
            self::Suspendu      => 'Suspendu',
            self::Retraite      => 'Retraité',
            self::Decede        => 'Décédé',
            self::Radie         => 'Radié',
            self::Disponibilite => 'Disponibilité',
            self::Demission     => 'Démission',
            self::HorsCadre     => 'Hors cadre',
            self::Detachement   => 'Détachement',
            self::Stagiaire     => 'Stagiaire',
        });
    }

    public function badge(): string
    {
        return Nomenclatures::badge(self::CATEGORIE, $this->value, match($this) {
            self::Actif         => 'bg-green-100 text-green-700',
            self::Suspendu      => 'bg-amber-100 text-amber-700',
            self::Retraite      => 'bg-slate-100 text-slate-600',
            self::Decede        => 'bg-gray-800 text-white',
            self::Radie         => 'bg-red-100 text-red-700',
            self::Disponibilite => 'bg-blue-100 text-blue-700',
            self::Demission     => 'bg-slate-100 text-slate-600',
            self::HorsCadre     => 'bg-slate-100 text-slate-600',
            self::Detachement   => 'bg-indigo-100 text-indigo-700',
            self::Stagiaire     => 'bg-cyan-100 text-cyan-700',
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
