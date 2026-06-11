# Gestion de flotte de véhicules

Projet réalisé dans le cadre d'un exercice technique.

L'application permet de gérer une flotte de véhicules et leur stationnement en respectant les règles métier définies dans les scénarios fournis.

## Fonctionnalités

- Enregistrer un véhicule dans une flotte
- Empêcher l'enregistrement d'un même véhicule plusieurs fois dans une même flotte
- Autoriser un véhicule à appartenir à plusieurs flottes
- Enregistrer la position de stationnement d'un véhicule
- Consulter la dernière position connue d'un véhicule

## Choix techniques

Le projet a été développé en :

- PHP 8.4
- PHPUnit
- Beat
- Docker (PostgreSQL)

L'organisation du code s'inspire des principes suivants :

- **DDD** (Domain-Driven Design)
- **CQSR**
- **Clean Architecture**

L'objectif principal est de garder la logique métier isolée et facilement testable.

## Lancement du projet

```bash
docker compose up -d
composer install
```

La base PostgreSQL est initialisée automatiquement via les scripts dans `migrations/`.

## Commandes CLI

Point d'entrée : `bin/fleet`

### Écriture (commandes)


| Commande           | Usage                                                          | Description                            | Réponse                 |
| ------------------ | -------------------------------------------------------------- | -------------------------------------- | ----------------------- |
| `create`           | `./fleet create <userId>`                                      | Crée une flotte pour un utilisateur    | ID de la flotte (texte) |
| `register-vehicle` | `./fleet register-vehicle <fleetId> <plate>`                   | Enregistre un véhicule dans une flotte | `Vehicle registered.`   |
| `localize-vehicle` | `./fleet localize-vehicle <fleetId> <plate> <lat> <lng> [alt]` | Enregistre la position d'un véhicule   | `Vehicle localized.`    |


**Exemples :**

```bash
./bin/fleet create user-1
./bin/fleet register-vehicle abc-123 AB-123-CD
./bin/fleet localize-vehicle abc-123 AB-123-CD 48.8566 2.3522 35
```

**Erreurs métier possibles** (écrites sur `stderr`, code de sortie `1`) :

- `Fleet "<id>" was not found.`
- `This vehicle has already been registered into this fleet.`
- `This vehicle is not registered into this fleet.`
- `This vehicle is already parked at this location.`

### Lecture (requêtes)

Toutes les commandes de lecture renvoient du JSON sur la sortie standard.


| Commande              | Usage                                   | Description                                                  |
| --------------------- | --------------------------------------- | ------------------------------------------------------------ |
| `list-fleets`         | `./fleet list-fleets [userId]`          | Liste les flottes, optionnellement filtrées par propriétaire |
| `list-vehicles`       | `./fleet list-vehicles`                 | Liste tous les véhicules connus                              |
| `list-fleet-vehicles` | `./fleet list-fleet-vehicles [fleetId]` | Liste les associations flotte / véhicule                     |
| `list-locations`      | `./fleet list-locations [fleetId]`      | Liste les positions de stationnement                         |
| `list-parkings`       | `./fleet list-parkings [fleetId]`       | Alias de `list-locations`                                    |
| `list-positions`      | `./fleet list-positions [fleetId]`      | Alias de `list-locations`                                    |
| `list-all`            | `./fleet list-all [userId] [fleetId]`   | Agrège toutes les données de lecture                         |


**Formats de réponse :**

```json
// list-fleets
[{ "id": "...", "ownerId": "...", "createdAt": "..." }]

// list-vehicles
[{ "plateNumber": "AB-123-CD" }]

// list-fleet-vehicles
[{ "fleetId": "...", "plateNumber": "AB-123-CD" }]

// list-locations / list-parkings / list-positions
[{
  "fleetId": "...",
  "plateNumber": "AB-123-CD",
  "latitude": 48.8566,
  "longitude": 2.3522,
  "altitude": 35,
  "localizedAt": "..."
}]

// list-all
{
  "fleets": [...],
  "vehicles": [...],
  "fleetVehicles": [...],
  "parkings": [...],
  "positions": [...]
}
```

## Tests

```bash
make unit          # tests unitaires PHPUnit
make integration   # tests d'intégration PostgreSQL
make bdd           # scénarios Behat
```

Les tests couvrent les différents scénarios fonctionnels décrits dans l'énoncé.

## Structure du projet

```
Domain/              # Logique métier (agrégats, value objects, exceptions)
├── Fleet/
├── Vehicle/
├── Location/
└── Exception/

App/                 # Couche application (CQRS)
├── Command/         # Commandes (écriture)
├── Handler/         # Handlers des commandes et requêtes
├── Query/           # Requêtes (lecture)
│   └── ReadModel/
└── Cli/             # Sortie JSON du CLI

src/Infra/           # Implémentations techniques
├── InMemory/
└── Postgres/

bin/fleet            # Point d'entrée CLI
features/            # Scénarios BDD (Behat)
migrations/          # Schéma PostgreSQL
tests/               # Tests PHPUnit (unitaires et intégration)
```

