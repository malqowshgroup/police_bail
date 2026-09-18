# GESTBAIL — Guide de développement

Système de gestion des baux de la Police Nationale de Côte d'Ivoire.

## Démarrage

```bash
# Serveur de développement
php artisan serve --port=8001

# Build assets (dans un autre terminal)
npm run dev

# Compte admin par défaut
# Email : admin@gestbail.ci / Mot de passe : Admin@2026
```

### ⚠️ Binaire PHP — IMPORTANT

Le projet **exige PHP ≥ 8.4.1** (verrou Composer). Le `php` du PATH (Homebrew 8.3.9) et MAMP (≤ 8.2)
**échouent** avec `platform_check.php`. Utiliser le binaire **Laravel Herd** :

```bash
PHP84="/Users/sergegouety/Library/Application Support/Herd/bin/php84"
"$PHP84" artisan migrate:fresh --seed
"$PHP84" artisan tinker --execute="..."
```

Astuce : `alias php84='/Users/sergegouety/Library/Application Support/Herd/bin/php84'` pour la session.

## Stack technique

| Couche | Technologie |
|---|---|
| Framework | Laravel 11, PHP 8.4 |
| Base de données | MySQL (MAMP), port 3306 |
| Frontend | Tailwind CSS v4 via `@tailwindcss/vite`, Alpine.js 3 (CDN), Chart.js 4.4 (CDN) |
| Icônes | Tabler Icons (CDN webfont) |
| Temps réel | Livewire 4 |
| Auth/Rôles | Spatie Laravel Permission |

## Palette de couleurs (Police CI)

```
Orange CI  : #F77F00   (accent principal, boutons, badges actifs)
Vert CI    : #009A44   (statuts positifs, tendances)
Navy foncé : #1a2440   (sidebar)
Navy noir  : #111827   (logo area sidebar)
```

## Base de données

### Règle importante : noms de tables non-standards

Plusieurs tables ont des noms qui ne suivent pas les conventions Laravel. **Toujours déclarer `$table` explicitement** dans ces modèles :

| Modèle | Table réelle | Pourquoi explicite |
|---|---|---|
| `ContratBail` | `contrats_bail` | Laravel attendrait `contrat_bails` |
| `CitePolicieres` | `cites_policieres` | Laravel attendrait `cite_policieres` |
| `LogementCite` | `logements_cites` | Laravel attendrait `logement_cites` |
| `LogementCivil` | `logements_civils` | Laravel attendrait `logement_civils` |
| `ConfirmationTrimestrielle` | `confirmations_trimestrielles` | Pluriel incorrect |
| `Bordereau` | `bordereaux` | Laravel attendrait `bordereaus` |

### Migrations

Toutes les migrations utilisent le préfixe `2026_06_26_01NNNN` avec numérotation séquentielle pour respecter l'ordre des FK. Ne pas ajouter de migration avec un timestamp antérieur sans revoir l'ordre. Dernier indice utilisé : **`010019`** (`virements`=`010016`, `reglements.virement_id`=`010017`,
`nomenclatures`=`010018`, **`grades.libelle` unique**=`010019`).

> ⚠️ Seeders **idempotents** obligatoires pour les tables de référence : `GradesSeeder` utilise
> `updateOrInsert(['libelle' => …])` (et `grades.libelle` est désormais unique) car un rejeu dupliquait
> les 15 grades. `ServiceSeeder`/`NomenclatureSeeder` sont aussi ré-exécutables. En cas de doublons
> historiques sur une table référencée, **repointer les FK** (policiers/contrats…) vers l'enregistrement
> canonique **avant** suppression.

### Reseed complet

```bash
"$PHP84" artisan migrate:fresh --seed          # tout reconstruire
"$PHP84" artisan db:seed --class=ContratBailSeeder   # un seul seeder (ré-exécutable)
```

### Seeders (ordre dans `DatabaseSeeder`)

`GradesSeeder` → `LocalitesSeeder` → `RolesPermissionsSeeder` → `AdminUserSeeder` →
`PolicierSeeder` → `ProprietaireSeeder` → `LogementCivilSeeder` → `ContratBailSeeder`

> L'ordre est imposé par les FK : un logement référence propriétaire + localité ; un contrat
> référence policier + logement + grade. `ContratBailSeeder` est **ré-exécutable** (purge en début de `run()`).

### Jeu de données après seed (volumétrie indicative)

| Table | Volume | Notes |
|---|---|---|
| `grades` | 30 | porteurs du `taux_bail` par grade |
| `policiers` | 150 | ~97 éligibles (statuts `actif`/`detachement`/`stagiaire`) |
| `proprietaires` | variable | physiques + morales |
| `logements_civils` | 200 | `actif=true` majoritaire |
| `contrats_bail` | 92 | statuts variés ; certains rattachés à un bordereau |
| `bordereaux` | 3 | 1 en saisie · 1 en contrôle · 1 validé (15 contrats liés) |
| `reglements` | 178 | loyers sur 3 mois : à payer · en attente virement · virés (via virement) |
| `virements` | 9 | 3 en préparation · 3 émis · 3 exécutés (regroupent des règlements) |
| `documents` / `document_liens` | 35 / 35 | GED : métadonnées seules en démo (pas de fichier physique) |
| `services` | 10 | directions/services de la Police (seedés) |
| `nomenclatures` | 55 | libellés/couleurs des statuts & types (8 catégories) |

### Relations clés (Eloquent)

```
Grade 1───* Policier *───1 Service / Localite
Policier 1───* ContratBail *───1 LogementCivil 1───* (un seul « en cours »)
LogementCivil *───1 Proprietaire / Localite
ContratBail *───1 Grade (snapshot) , *───1 Bordereau (nullable)
ContratBail 1───* Reglement , 1───* ConfirmationTrimestrielle
ContratBail *───* Document (via DocumentLien, morphMany)
ContratBail *───1 User  (saisi_par / controle_par / valide_par)
Reglement *───1 Virement (nullable) , Virement *───1 Proprietaire (bailleur)
Document 1───* DocumentLien *───(morph) Policier|LogementCivil|Proprietaire|ContratBail|Bordereau
```

> Règle d'unicité métier : un policier **et** un logement ne peuvent avoir qu'**un seul** contrat
> dont le statut ∈ {`en_attente`, `actif`, `suspendu`, `en_resiliation`}.

## Enums — règle absolue

### `StatutPolicier`

Le statut d'un policier est casté en `App\Enums\StatutPolicier` dans le modèle.  
**Dans les vues, utiliser les méthodes de l'enum :**

```blade
{{-- Correct --}}
{{ $policier->statut->label() }}
{{ $policier->statut->badge() }}
{{ $policier->statut->value }}

{{-- INTERDIT — provoque "Cannot access offset of type StatutPolicier on array" --}}
$badgeMap[$policier->statut]
```

L'enum expose :
- `->label()` : libellé français affiché
- `->badge()` : classes Tailwind CSS pour le badge coloré
- `->value` : valeur string brute (`'actif'`, `'suspendu'`, etc.)
- `::cases()` : liste de tous les cas (pour les `<select>` dans les formulaires)
- `::options()` : tableau `[['value' => ..., 'label' => ...]]`

**Pattern formulaire select :**
```blade
@foreach(\App\Enums\StatutPolicier::cases() as $case)
    <option value="{{ $case->value }}" {{ old('statut', $policier->statut->value) === $case->value ? 'selected' : '' }}>
        {{ $case->label() }}
    </option>
@endforeach
```

### Nouveaux enums

Tout nouveau statut métier (`StatutContrat`, `StatutLogement`, etc.) doit suivre le même pattern : enum PHP 8.1 backed string avec méthodes `label()` et `badge()`.

> ⚠️ Depuis le module Paramètres, `label()/badge()/dot()` des 8 enums lisent la table `nomenclatures`
> (via `App\Support\Nomenclatures`) **avec fallback** sur le `match()` codé en dur. Un nouvel enum
> destiné à être éditable doit définir `const CATEGORIE`, envelopper ses retours dans
> `Nomenclatures::xxx(self::CATEGORIE, $this->value, <fallback>)`, et être ajouté au
> `NomenclatureSeeder` + `Nomenclature::CATEGORIES`. Voir « Module Paramètres ».

### `StatutContrat`

Statuts d'un contrat de bail : `en_attente → actif ⇄ suspendu → resilie` (+ `en_resiliation`).
Expose `label()`, `badge()`, `dot()` (couleur de pastille) et `options()`.

**Important** : `statut` n'est PAS casté en enum sur `ContratBail` (les comparaisons string `where('statut','actif')` du module Logements en dépendent). Utiliser l'accesseur `$contrat->statutEnum` dans les vues :

```blade
<span class="{{ $contrat->statutEnum->badge() }}">{{ $contrat->statutEnum->label() }}</span>
```

## Module Contrats de bail — cycle de vie

Le `ContratBailController` expose le CRUD `Route::resource` + 3 actions POST :

| Action | Route | Transition |
|---|---|---|
| `activer` | `contrats.activer` | `en_attente`/`suspendu` → `actif` (renseigne `valide_par`, `date_validation`) |
| `suspendre` | `contrats.suspendre` | `actif` → `suspendu` (motif obligatoire) |
| `resilier` | `contrats.resilier` | `actif`/`suspendu` → `resilie` (motif + préavis, calcule `date_fin_preavis`) |

Règles métier :
- **Un seul contrat « en cours »** (statut ≠ `resilie`) par policier ET par logement — vérifié dans les `FormRequest` via `withValidator`.
- `numero_contrat` auto-généré : `ContratBail::genererNumero()` → `CB-{année}-{séquence}` (ex. `CB-2026-0001`).
- À la création, `grade_id` et `taux_bail` sont figés (snapshot) depuis le grade du policier ; resynchronisés en édition si le policier change.
- Seul un contrat `en_attente` est supprimable (sinon résilier).
- Les policiers/logements proposés en création sont filtrés (éligibles + sans contrat en cours).

### `StatutBordereau`

Statuts d'un bordereau : `en_saisie → en_controle → en_validation → valide` (ou `rejete`).
Comme `StatutContrat`, **non casté** sur le modèle — accesseur `$bordereau->statutEnum`.
Expose `label()`, `badge()`, `dot()`, `suivant()` (étape suivante) et `options()`.

## Module Bordereaux — circuit de validation

Un **bordereau regroupe des contrats** (`contrats_bail.bordereau_id`) pour les faire valider en lot,
suivant le circuit des rôles `saisie → contrôle → validation`. Le `BordereauController` expose le CRUD
`Route::resource` + 4 actions POST :

| Action | Route | Transition |
|---|---|---|
| `soumettre` | `bordereaux.soumettre` | `en_saisie` → `en_controle` (exige ≥ 1 contrat) |
| `controler` | `bordereaux.controler` | `en_controle` → `en_validation` (renseigne `controle_par`) |
| `valider` | `bordereaux.valider` | `en_validation` → `valide` **+ active les contrats** rattachés |
| `rejeter` | `bordereaux.rejeter` | `en_controle`/`en_validation` → `rejete` (motif ajouté aux observations) |

Règles métier :
- `numero` auto-généré : `Bordereau::genererNumero($annee)` → `BORD-{année}-{séquence}`.
- **Couplage clé** : valider un bordereau passe tous ses contrats `en_attente` → `actif`
  (même effet que `contrats.activer`, mais en lot). C'est la voie « normale » d'activation.
- Contenu (contrats) **modifiable uniquement en `en_saisie`** (`contenuModifiable()`).
- Seuls les contrats `en_attente` **sans** bordereau sont sélectionnables (création/édition).
- Suppression réservée au statut `en_saisie` (détache les contrats avant suppression).
- `montant_total` = somme des `taux_bail` des contrats rattachés (accesseur).

### `StatutReglement` & `TypeReglement`

Règlement = échéance de loyer d'un contrat actif.
- `StatutReglement` : `a_payer → en_attente_virement → vire` (ou `annule`). Accesseur `$reglement->statutEnum`.
- `TypeReglement` : `loyer_mensuel`, `ariere`, `rappel_grade`, `trop_percu`. Accesseur `$reglement->typeEnum`.

> ⚠️ La valeur BD de l'arriéré est `ariere` (sans double « r ») — `TypeReglement::Arriere->value === 'ariere'`.
> Le modèle expose aussi l'accesseur `periode_label` (ex. « Juin 2026 »).

## Module Règlements — échéances de loyer

Le `ReglementController` expose le CRUD `Route::resource` + 3 actions POST :

| Action | Route | Effet |
|---|---|---|
| `genererMensuel` | `reglements.generer` | **Génère en lot** un loyer pour chaque contrat actif non couvert sur (mois, année) |
| `mettreEnAttente` | `reglements.en_attente` | `a_payer` → `en_attente_virement` |
| `annuler` | `reglements.annuler` | `a_payer`/`en_attente_virement` → `annule` |

Règles métier :
- **Génération mensuelle** (fonction centrale) : pour la période choisie, crée un `loyer_mensuel` au
  `taux_bail` du contrat, pour tout contrat `actif` dont `date_debut ≤ fin de période`, **en sautant**
  ceux déjà couverts (anti-doublon : 1 seul loyer non annulé par contrat/période).
- Saisie manuelle possible pour les autres types (arriéré, rappel de grade, trop-perçu).
- Modifiable / supprimable uniquement tant que `a_payer` (un règlement `vire` est verrouillé).
- Les champs virement (`numero_virement`, `date_virement`, `banque_bailleur`, `reference_fichier_virement`)
  sont renseignés par le **module Virements** lors de l'exécution d'un virement (dénormalisation).

### `StatutVirement`

Statuts d'un virement : `en_preparation → emis → execute` (ou `annule`). Accesseur `$virement->statutEnum`.
Expose `label()`, `badge()`, `dot()`, `options()`.

## Module Virements — ordres de virement (option B : table dédiée)

Un **virement regroupe des règlements** `en_attente_virement` d'un **même propriétaire bailleur**
(`reglements.virement_id` → `virements.id`). Choix d'architecture : table `virements` dédiée
(pas un simple regroupement logique), pour le suivi et le reporting.

Le `VirementController` expose le CRUD `Route::resource` + 3 actions POST :

| Action | Route | Transition / effet |
|---|---|---|
| `emettre` | `virements.emettre` | `en_preparation` → `emis` (génère `reference_fichier`, `emis_par`) |
| `executer` | `virements.executer` | `emis` → `execute` **+ passe les règlements à `vire`** (dénormalise n° virement, banque, fichier) |
| `annuler` | `virements.annuler` | `en_preparation`/`emis` → `annule` (libère les règlements : `virement_id = null`) |

Règles métier :
- `numero` auto-généré : `Virement::genererNumero()` → `VIR-{année}-{séquence}`.
- Création en 2 temps : choisir le **bailleur** (rechargement GET `?proprietaire_id=`) puis cocher ses règlements.
- Seuls les règlements `en_attente_virement` **sans** virement, **du bailleur choisi**, sont sélectionnables.
- `montant_total` recalculé via `recalculerMontant()` à chaque (dé)rattachement.
- Contenu modifiable / suppression réservés au statut `en_preparation`.
- **Couplage clé** : `executer` est la seule voie normale pour faire passer un règlement à `vire`.

### `TypeDocument` & `StatutDocument`

- `TypeDocument` : 20 types métier (`carte_pro`, `rib`, `contrat_bail_signe`, `quitus_sodeci`…). Accesseur `$document->typeEnum`.
- `StatutDocument` : `en_attente → valide` / `rejete`. Accesseur `$document->statutEnum`.

## Module GED / Documents — ⚠️ morphologie personnalisée

La table `document_liens` n'utilise **pas** la convention Laravel (`documentable_type/id`) mais des
colonnes **`entite_type`** (enum de clés courtes) + **`entite_id`**. Pièges et solutions :

1. **Morph map** (dans `AppServiceProvider::boot()`) — mappe les clés courtes vers les classes :
   ```php
   Relation::morphMap(['policier' => Policier::class, 'logement_civil' => LogementCivil::class,
       'proprietaire' => Proprietaire::class, 'contrat_bail' => ContratBail::class, 'bordereau' => Bordereau::class]);
   ```
   ⚠️ Utiliser **`morphMap`** et **PAS `enforceMorphMap`** : ce dernier active `requireMorphMap` et fait
   planter tout modèle non mappé (`User`, etc.).

2. **Relations morph avec colonnes explicites** — sur chaque entité liable :
   ```php
   public function documentLiens(): MorphMany {
       return $this->morphMany(DocumentLien::class, 'entite', 'entite_type', 'entite_id');
   }
   ```
   (présent sur Policier, LogementCivil, Proprietaire, ContratBail, Bordereau). `DocumentLien::entite()`
   = `morphTo('entite', 'entite_type', 'entite_id')`. `DocumentLien` expose `entite_label` / `entite_url`.

3. **Stockage** : disque **`local`** (privé, `storage/app/documents/`). Téléchargement via la route
   authentifiée `documents.download` (`Storage::download`) — **pas** de symlink public (documents sensibles).
   Champs `minio_*` réservés à un futur backend objet ; `chemin_local` sert de stockage effectif.

Le `DocumentController` expose le CRUD + `valider` / `rejeter` / `download`. Upload validé (PDF/JPG/PNG/TIFF,
10 Mo max). À la création, on peut rattacher le document à une entité (sélecteur dépendant en Alpine).

> En démo, les documents seedés ont `chemin_local = null` (métadonnées seules) : `fichierDisponible()`
> est faux et le téléchargement renvoie une erreur propre. Les vrais uploads écrivent dans `storage/app/documents/`.

## Module Rapports — tableaux de bord (lecture seule)

`RapportController@index` calcule des **agrégations réelles** (aucune donnée mockée, pas de table propre) :

- **KPIs** : contrats actifs/total, loyers du mois, en attente de virement, total viré.
- **Graphiques Chart.js** (données passées via `@json`, CDN dans `@push('scripts')`) :
  contrats par statut (doughnut), règlements/mois (line), loyers par localité (bar),
  règlements & virements par statut (bar horizontal).
- **Tableaux** : contrats par grade, top 10 bailleurs (montants virés), synthèse financière des règlements.

Les agrégats utilisent des `join` + `selectRaw('… , SUM(…), COUNT(*)')->groupBy(…)` (pas de N+1).
Bouton « Imprimer » (`window.print()`).

**KPIs** (2 rangées + bandeau) : contrats actifs/total, loyers du mois, en attente virement, total viré,
**taux d'occupation**, **loyer moyen**, **projection annuelle** (masse × 12), **taux de recouvrement**
(virés / facturés), bailleurs actifs, suspendus, documents, etc.

### Dashboard (`DashboardController`)

Le tableau de bord est désormais **piloté par la BD** (plus aucune donnée mockée) : route `/dashboard`
branchée sur `DashboardController@index`. Fournit KPIs (contrats actifs + nouveaux du mois, loyers du mois
avec évolution vs mois précédent, règlements à payer, suspendus), 2 graphiques Chart.js (loyers/localité,
contrats/statut), taux par grade, **alertes cliquables** (liens filtrés vers chaque module) et un **flux
d'activité réel** (derniers contrats/règlements/documents/virements fusionnés par `created_at`).

> ⚠️ `APP_LOCALE = en` → ne pas utiliser `translatedFormat('F')` (afficherait « June »). Les libellés de
> mois français sont fournis en dur (tableau `MOIS` côté contrôleur, array inline côté vue).

## Module Paramètres — listes de référence (réservé administrateur)

Accessible sous `/parametres`, **réservé au rôle `administrateur`**. Contrôleurs sous
`App\Http\Controllers\Parametres\*`, FormRequests sous `App\Http\Requests\Parametres\*`,
vues sous `resources/views/parametres/*` (partials communs : `partials/{flash,filtres,vide,actif}`).

### Gating administrateur (premier rôle-gating de l'app)

- Alias middleware Spatie enregistrés dans `bootstrap/app.php` (`role`, `permission`, `role_or_permission`).
- Routes Paramètres groupées sous `->middleware('role:administrateur')`.
- Sidebar : groupe « Paramètres » encadré par `@role('administrateur') … @endrole` (invisible aux autres).
- Le compte admin (`admin@gestbail.ci`) possède déjà ce rôle (AdminUserSeeder).

### Tables de référence (CRUD direct)

`grades` (libelle, taux_bail, ordre, actif), `localites` (code, libelle, actif),
`services` (code, libelle, **localite = chaîne**, actif). Suppression **gardée** (bloquée si
référencée → message + suggestion de désactiver) ; bascule `actif` comme alternative douce.

### Statuts & types ÉDITABLES — table `nomenclatures` + résolveur (le point d'architecture clé)

Les 8 enums (`StatutContrat`, `StatutPolicier`, `StatutBordereau`, `StatutReglement`,
`TypeReglement`, `StatutVirement`, `StatutDocument`, `TypeDocument`) restent la **source de vérité
de la logique métier** (valeurs, transitions, scopes). Mais leurs **libellés / couleurs / ordre**
sont désormais pilotés par la BD :

1. `nomenclatures` (categorie, code, libelle, couleur_badge, couleur_dot, ordre, actif, systeme).
2. `App\Support\Nomenclatures` : résolveur statique **mis en cache** (mémo process) et **tolérant aux
   erreurs** — si la table manque/est vide, on retombe sur les fallbacks.
3. Chaque enum définit `const CATEGORIE` et ses `label()/badge()/dot()` font
   `Nomenclatures::xxx(self::CATEGORIE, $this->value, <match() codé en dur>)` → **la BD surcharge,
   le code reste le filet de sécurité**. Zéro changement de vue : tout passe toujours par
   `$x->statutEnum->label()`.
4. `NomenclatureSeeder` peuple la table depuis l'état courant des enums (`systeme = true`).
   Le model `Nomenclature` vide le cache (`Nomenclatures::flush()`) sur `saved`/`deleted`.

**Limite assumée** : on édite librement libellé/couleur/ordre/activation et on peut **ajouter** des
valeurs, mais une valeur ajoutée est **d'affichage uniquement** (aucune transition/logique nouvelle
sans code). Les valeurs `systeme = true` : code non modifiable, non supprimables.

> Pour ajouter une vraie liste éditable plus tard : créer l'enum (avec `CATEGORIE` + fallbacks),
> l'ajouter à `NomenclatureSeeder::ENUMS` et à `Nomenclature::CATEGORIES`.

## Module Utilisateurs (administration — admin only)

`UserController` (namespace `Parametres`), route `parametres.utilisateurs.*`
(`->parameters(['utilisateurs' => 'user'])` pour binder `User $user`). Groupe sidebar
**« Administration »** (au-dessus de Paramètres), visible admin uniquement.

- CRUD complet ; rôles assignés via Spatie (`syncRoles`) en cases à cocher.
- Mot de passe : `confirmed`, hashé par le cast `'hashed'` du modèle User ; **optionnel en édition**
  (vide = inchangé).
- Métadonnées de rôles (libellé FR + badge) centralisées dans `UserController::ROLES`.
- **Garde-fous** : on ne peut pas supprimer son propre compte, ni retirer son propre rôle admin,
  ni supprimer / déclasser le **dernier administrateur** (`estDernierAdmin()`).

## Architecture des rôles

6 rôles Spatie, du moins au plus permissif :

```
agent_saisie → agent_controle → agent_validation
→ responsable_baux → directeur_solde → administrateur
```

## Layout et vues

- Layout principal : `layouts/app.blade.php` (sidebar + header)
- Layout auth : `layouts/guest.blade.php`
- Alpine.js chargé en `defer` dans le `<head>` — ne jamais le charger en bas de page
- `[x-cloak]` masqué via `<style>` inline dans le head (avant Alpine)
- Les scripts Chart.js vont dans `@push('scripts')` en bas de chaque vue

## Sidebar — groupes déroulants pilotés par données

La sidebar (`layouts/app.blade.php`) est **data-driven** : un tableau `$groups` (PHP dans la vue)
décrit chaque groupe et ses items `[route, pattern, icon, label]`. Chaque groupe est un **accordéon
Alpine** (`x-data="{ open }"` + bouton chevron + `x-show`), **ouvert par défaut s'il contient la route
active** (`$groupActif`). `$solo` liste les liens toujours visibles (Dashboard).

**Ajouter un item** = ajouter une entrée dans le bon groupe de `$groups`. **Nouveau groupe** = ajouter
un élément `['title' => …, 'icon' => 'ti-…', 'items' => [...]]`. Réservé admin = pousser le groupe dans
`if (auth()->user()?->hasRole('administrateur')) { $groups[] = … }` (cf. Paramètres).

> Pas de dépendance au plugin Alpine Collapse : on utilise `x-show` + un `style="display:none"`
> serveur pour les groupes fermés (évite le flash avant init Alpine).

## Modules développés

| Module | Status | Routes |
|---|---|---|
| Authentification | ✅ | `/login`, `/logout` |
| Dashboard | ✅ | `/dashboard` (KPIs/graphiques/activité — données réelles via `DashboardController`) |
| Policiers | ✅ | `/policiers` (CRUD complet) |
| Logements | ✅ | `/logements` (CRUD complet) |
| Propriétaires | ✅ | `/proprietaires` (CRUD complet) |
| Bordereaux | ✅ | `/bordereaux` (CRUD + circuit de validation) |
| Contrats de bail | ✅ | `/contrats` (CRUD + cycle de vie) |
| Règlements | ✅ | `/reglements` (CRUD + génération mensuelle) |
| Virements | ✅ | `/virements` (CRUD + émission/exécution, table dédiée) |
| GED (Documents) | ✅ | `/documents` (upload + rattachement morph + validation) |
| Rapports | ✅ | `/rapports` (tableaux de bord Chart.js, données réelles) |
| Paramètres | ✅ | `/parametres` (grades/localités/services + statuts/types éditables — **admin only**) |
| Utilisateurs | ✅ | `/parametres/utilisateurs` (CRUD comptes + rôles Spatie — **admin only**) |

### Patron de référence d'un module CRUD

Tout module complet suit ce squelette (copier sur **Contrats** ou **Logements**) :

```
app/Enums/Statut<Module>.php            (si statut métier)
app/Models/<Model>.php                  ($table explicite si non-standard, relations typées)
app/Http/Controllers/<Model>Controller.php
app/Http/Requests/Store<Model>Request.php
app/Http/Requests/Update<Model>Request.php
resources/views/<module>/{index,create,show,edit}.blade.php
database/seeders/<Model>Seeder.php      (+ enregistrer dans DatabaseSeeder)
routes/web.php                          (Route::resource + actions custom)
layouts/app.blade.php                   (item sidebar — déjà présents pour les 11 modules)
```

Module **Contrats** = exemple le plus complet (CRUD + cycle de vie + Alpine + modales + seeder).

## Prochains modules (feuille de route)

> **État : la chaîne métier complète est livrée** (référentiels → contrats → bordereaux → règlements →
> virements → GED → rapports). Il ne reste qu'un module fonctionnel optionnel.

**Confirmations trimestrielles** (reporté par le client) —
`/confirmations`. Lié aux contrats actifs : chaque trimestre, confirmer que le policier occupe toujours
le logement (condition de paiement). Modèle `ConfirmationTrimestrielle` à compléter ; table
`confirmations_trimestrielles` migrée. À prévoir : un `StatutConfirmation`, génération trimestrielle par
contrat actif (comme les loyers), et le blocage des règlements si la confirmation manque.
**Pas de route ni d'item sidebar encore** (à ajouter).

> Astuce d'intégration GED : appeler `route('documents.create', ['entite_type' => 'contrat_bail', 'entite_id' => $c->id])`
> depuis une fiche pré-remplit le rattachement. Ajouter un panneau « Documents liés » sur les fiches existantes
> (`$model->documentLiens()->with('document')`) est un enrichissement simple et à forte valeur.

> Avant de démarrer un module : vérifier sa migration/table (souvent déjà créée mais vide) et
> son modèle (souvent un stub `//` à compléter — ex. `Reglement` était un stub, désormais complété).

## Conventions de code

- Les controllers utilisent des `FormRequest` dédiés (`Store*Request`, `Update*Request`)
- Les relations Eloquent sont déclarées avec les types de retour (`BelongsTo`, `HasMany`, etc.)
- Pagination : 20 résultats par page avec `withQueryString()`
- Les factories utilisent des noms ivoiriens (KONÉ, DIALLO, COULIBALY…)
- `$guarded = []` sur tous les modèles (pas de `$fillable` partiel)
