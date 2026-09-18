<?php

namespace App\Enums;

use App\Support\Nomenclatures;

enum TypeDocument: string
{
    public const CATEGORIE = 'type_document';

    case CartePro               = 'carte_pro';
    case FicheIdentite          = 'fiche_identite';
    case CertificatPriseService = 'certificat_prise_service';
    case DecisionGrade          = 'decision_grade';
    case PieceIdentiteProprio   = 'piece_identite_proprio';
    case Rib                    = 'rib';
    case Procuration            = 'procuration';
    case DeclarationFonciere    = 'declaration_fonciere';
    case ContratBailSigne       = 'contrat_bail_signe';
    case ExtraitTopographique   = 'extrait_topographique';
    case EtatLieuxEntree        = 'etat_lieux_entree';
    case EtatLieuxSortie        = 'etat_lieux_sortie';
    case PvRemiseCles           = 'pv_remise_cles';
    case PvRestitutionCles      = 'pv_restitution_cles';
    case AttestationOccupation  = 'attestation_occupation';
    case QuitusSodeci           = 'quitus_sodeci';
    case QuitusCie              = 'quitus_cie';
    case AvisResiliation        = 'avis_resiliation';
    case DecisionJudiciaire     = 'decision_judiciaire';
    case Autre                  = 'autre';

    public function label(): string
    {
        return Nomenclatures::label(self::CATEGORIE, $this->value, match($this) {
            self::CartePro               => 'Carte professionnelle',
            self::FicheIdentite          => 'Fiche d\'identité',
            self::CertificatPriseService => 'Certificat de prise de service',
            self::DecisionGrade          => 'Décision de grade',
            self::PieceIdentiteProprio   => 'Pièce d\'identité (propriétaire)',
            self::Rib                    => 'RIB',
            self::Procuration            => 'Procuration',
            self::DeclarationFonciere    => 'Déclaration foncière',
            self::ContratBailSigne       => 'Contrat de bail signé',
            self::ExtraitTopographique   => 'Extrait topographique',
            self::EtatLieuxEntree        => 'État des lieux (entrée)',
            self::EtatLieuxSortie        => 'État des lieux (sortie)',
            self::PvRemiseCles           => 'PV de remise des clés',
            self::PvRestitutionCles      => 'PV de restitution des clés',
            self::AttestationOccupation  => 'Attestation d\'occupation',
            self::QuitusSodeci           => 'Quitus SODECI',
            self::QuitusCie              => 'Quitus CIE',
            self::AvisResiliation        => 'Avis de résiliation',
            self::DecisionJudiciaire     => 'Décision judiciaire',
            self::Autre                  => 'Autre',
        });
    }

    public function badge(): string
    {
        return Nomenclatures::badge(self::CATEGORIE, $this->value, match($this) {
            self::ContratBailSigne, self::AvisResiliation => 'bg-orange-100 text-orange-700',
            self::Rib, self::QuitusSodeci, self::QuitusCie => 'bg-green-100 text-green-700',
            self::DecisionJudiciaire                      => 'bg-red-100 text-red-700',
            self::CartePro, self::FicheIdentite, self::PieceIdentiteProprio => 'bg-indigo-100 text-indigo-700',
            default                                       => 'bg-slate-100 text-slate-600',
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
