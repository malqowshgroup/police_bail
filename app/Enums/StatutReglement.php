<?php

namespace App\Enums;

use App\Support\Nomenclatures;

enum StatutReglement: string
{
    public const CATEGORIE = 'statut_reglement';

    case APayer            = 'a_payer';
    case EnAttenteVirement = 'en_attente_virement';
    case Vire              = 'vire';
    case Annule            = 'annule';

    public function label(): string
    {
        return Nomenclatures::label(self::CATEGORIE, $this->value, match($this) {
            self::APayer            => 'À payer',
            self::EnAttenteVirement => 'En attente de virement',
            self::Vire              => 'Viré',
            self::Annule            => 'Annulé',
        });
    }

    public function badge(): string
    {
        return Nomenclatures::badge(self::CATEGORIE, $this->value, match($this) {
            self::APayer            => 'bg-amber-100 text-amber-700',
            self::EnAttenteVirement => 'bg-blue-100 text-blue-700',
            self::Vire              => 'bg-green-100 text-green-700',
            self::Annule            => 'bg-slate-100 text-slate-500',
        });
    }

    public function dot(): string
    {
        return Nomenclatures::dot(self::CATEGORIE, $this->value, match($this) {
            self::APayer            => 'bg-amber-500',
            self::EnAttenteVirement => 'bg-blue-500',
            self::Vire              => 'bg-green-500',
            self::Annule            => 'bg-slate-400',
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
