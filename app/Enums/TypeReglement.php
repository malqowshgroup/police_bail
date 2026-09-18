<?php

namespace App\Enums;

use App\Support\Nomenclatures;

enum TypeReglement: string
{
    public const CATEGORIE = 'type_reglement';

    case LoyerMensuel = 'loyer_mensuel';
    case Arriere      = 'ariere';          // valeur BD historique (sans double « r »)
    case RappelGrade  = 'rappel_grade';
    case TropPercu    = 'trop_percu';

    public function label(): string
    {
        return Nomenclatures::label(self::CATEGORIE, $this->value, match($this) {
            self::LoyerMensuel => 'Loyer mensuel',
            self::Arriere      => 'Arriéré',
            self::RappelGrade  => 'Rappel de grade',
            self::TropPercu    => 'Trop-perçu',
        });
    }

    public function badge(): string
    {
        return Nomenclatures::badge(self::CATEGORIE, $this->value, match($this) {
            self::LoyerMensuel => 'bg-slate-100 text-slate-600',
            self::Arriere      => 'bg-orange-100 text-orange-700',
            self::RappelGrade  => 'bg-indigo-100 text-indigo-700',
            self::TropPercu    => 'bg-red-100 text-red-700',
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
