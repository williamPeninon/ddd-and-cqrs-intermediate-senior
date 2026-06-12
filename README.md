# Gestion de flotte de véhicules

Exercice technique en deux parties : un warm-up algo (FizzBuzz) et une application DDD / CQRS de gestion de flotte.

## Prérequis

- PHP 8.2+
- Composer
- Docker (pour PostgreSQL et les tests d'intégration)

## Installation

```bash
composer install
docker compose up -d    # PostgreSQL 16, migration auto au premier démarrage
```

Variables d'environnement optionnelles : `DATABASE_DSN`, `DATABASE_USER`, `DATABASE_PASSWORD`  
(valeurs par défaut : `fleet` / `fleet` sur `localhost:5432`)

## Exercice algo — FizzBuzz

Fichier isolé à la racine : `fizzBuzz.php` (hors autoload Composer, non couvert par la CI).

```bash
php fizzBuzz.php
```

Affiche les nombres de 1 à N (N = 15 par défaut dans le fichier) :

- divisible par 3 → `Fizz`
- divisible par 5 → `Buzz`
- divisible par 3 et 5 → `FizzBuzz`
- sinon → le nombre

Pour tester une autre limite, modifier l'appel en bas du fichier : `fizzBuzz(30);`

## Tests (application fleet)

```bash
make unit           # tests unitaires (domaine, sans BDD)
make bdd-domain     # scénarios Behat en mémoire (sans PostgreSQL)
make integration    # tests PostgreSQL (nécessite docker compose up)
make bdd-infra      # scénarios Behat via PostgreSQL
make analyse        # PHPStan niveau 8
```

## CLI

Point d'entrée : `bin/fleet`

### Commandes (écriture)

```bash
./bin/fleet create <userId>
./bin/fleet register-vehicle <fleetId> <plate>
./bin/fleet localize-vehicle <fleetId> <plate> <lat> <lng> [alt]
```

### Requêtes (lecture, réponse JSON)

```bash
./bin/fleet list-fleets [userId]
./bin/fleet list-vehicles
./bin/fleet list-fleet-vehicles [fleetId]
./bin/fleet list-locations [fleetId]    # alias : list-parkings, list-positions
./bin/fleet list-all [userId] [fleetId]
```

Exemple :

```bash
FLEET_ID=$(./bin/fleet create william)
./bin/fleet register-vehicle $FLEET_ID ABC-123
./bin/fleet localize-vehicle $FLEET_ID ABC-123 48.8566 2.3522
./bin/fleet list-fleets william
./bin/fleet list-locations $FLEET_ID
```

## Note CQRS — pas d'event store

Le CQRS complet peut s'appuyer sur une table d'événements (`events`) pour historiser chaque action d'écriture et permettre de reconstruire la base dans un état passé donné (event sourcing / replay).

**Ce mécanisme n'a pas été implémenté ici.** Le projet applique un CQRS pragmatique : séparation commandes / requêtes dans le code, avec un modèle relationnel classique en persistance.

C'est un choix volontaire : pour un exercice d'évaluation, je considère qu'un event store ajouterait de la complexité disproportionnée par rapport au périmètre métier.

