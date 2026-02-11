# Outlet Module

A Laravel module for managing outlet/branch locations in the system.

## Installation

This module is part of the Universe monorepo and is automatically loaded via the `nwidart/laravel-modules` package.

## CLI Commands

### `outlet:list`

List all outlets in the system.

**Signature:**
```bash
php artisan outlet:list [options]
```

**Options:**

| Option | Description |
|--------|-------------|
| `--active` | Show only active outlets |

**Examples:**

```bash
# List all outlets
php artisan outlet:list

# List only active outlets
php artisan outlet:list --active
```

**Output:**
```
Outlet Module Command
---------------------
Showing all outlets...
```

## Directory Structure

```
Modules/Outlet/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── OutletCommand.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── OutletController.php
│   ├── Models/
│   │   └── Outlet.php
│   └── Providers/
│       ├── EventServiceProvider.php
│       ├── OutletServiceProvider.php
│       └── RouteServiceProvider.php
├── config/
│   └── config.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│       └── OutletDatabaseSeeder.php
├── resources/
│   ├── assets/
│   ├── js/
│   └── views/
├── routes/
│   ├── api.php
│   └── web.php
└── tests/
    ├── Feature/
    └── Unit/
```

## Configuration

Module configuration is located at `config/config.php`. You can publish the config file using:

```bash
php artisan vendor:publish --tag=config --provider="Modules\Outlet\Providers\OutletServiceProvider"
```

## Routes

### Web Routes
Defined in `routes/web.php`

### API Routes
Defined in `routes/api.php`

## License

Proprietary
