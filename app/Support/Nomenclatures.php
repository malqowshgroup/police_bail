<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Résolveur centralisé des nomenclatures (statuts, types) chargées depuis la BD,
 * avec FALLBACK systématique sur les valeurs codées en dur des enums.
 *
 * Sûreté : tout accès BD est protégé. Si la table n'existe pas encore
 * (migration en cours) ou est vide, les fallbacks de l'enum s'appliquent.
 */
class Nomenclatures
{
    private const CACHE_KEY = 'nomenclatures.map';

    /** Cache mémoire du process (évite de retoucher le cache applicatif). */
    private static ?array $memo = null;

    /** Libellé pour (catégorie, code), avec repli sur $fallback. */
    public static function label(string $categorie, string $code, string $fallback): string
    {
        return self::resolve($categorie, $code)['libelle'] ?? $fallback;
    }

    /** Classes de badge Tailwind, avec repli sur $fallback. */
    public static function badge(string $categorie, string $code, string $fallback): string
    {
        $val = self::resolve($categorie, $code)['couleur_badge'] ?? null;
        return $val ?: $fallback;
    }

    /** Classe de pastille Tailwind, avec repli sur $fallback. */
    public static function dot(string $categorie, string $code, string $fallback): string
    {
        $val = self::resolve($categorie, $code)['couleur_dot'] ?? null;
        return $val ?: $fallback;
    }

    private static function resolve(string $categorie, string $code): array
    {
        $map = self::map();
        return $map[$categorie][$code] ?? [];
    }

    /** Carte catégorie => code => attributs. Mise en cache, tolérante aux erreurs. */
    private static function map(): array
    {
        if (self::$memo !== null) {
            return self::$memo;
        }

        try {
            $rows = DB::table('nomenclatures')
                ->select('categorie', 'code', 'libelle', 'couleur_badge', 'couleur_dot')
                ->get();

            $map = [];
            foreach ($rows as $r) {
                $map[$r->categorie][$r->code] = [
                    'libelle'       => $r->libelle,
                    'couleur_badge' => $r->couleur_badge,
                    'couleur_dot'   => $r->couleur_dot,
                ];
            }

            return self::$memo = $map;
        } catch (\Throwable $e) {
            // Table absente / BD indisponible → on laisse les fallbacks agir.
            return self::$memo = [];
        }
    }

    /** Réinitialise le cache (à appeler après toute modification). */
    public static function flush(): void
    {
        self::$memo = null;
    }
}
