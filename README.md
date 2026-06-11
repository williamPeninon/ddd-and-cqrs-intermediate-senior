# Gestion de flotte de véhicules

Exercice technique DDD / CQRS — gestion d'une flotte de véhicules et de leur stationnement.

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

## Tests

```bash
make unit           # tests unitaires (domaine, sans BDD)
make bdd-domain     # scénarios Behat en mémoire (sans PostgreSQL)
make integration    # tests PostgreSQL (nécessite docker compose up)
make bdd-infra      # scénarios Behat via PostgreSQL
make analyse        # PHPStan niveau 8
```

## CLI

```bash
./bin/fleet create user-1
./bin/fleet register-vehicle <fleetId> ABC-123
./bin/fleet localize-vehicle <fleetId> ABC-123 48.8566 2.3522
./bin/fleet list-fleets
```
