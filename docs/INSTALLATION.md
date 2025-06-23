# Installation Guide

## Schritt-für-Schritt Anleitung

### 1. Repository klonen

```bash
git clone https://github.com/yourusername/fahrtkosten-rechner.git
cd fahrtkosten-rechner
```

### 2. Laravel Sail starten

```bash
# Container im Hintergrund starten
./sail up -d

# Warten bis Container bereit sind (ca. 2-3 Minuten beim ersten Mal)
./sail ps
```

### 3. Datenbank einrichten

```bash
# Migrationen ausführen
./sail artisan migrate

# Optional: Beispieldaten laden
./sail artisan db:seed
```

### 4. Anwendung testen

Öffne http://localhost in deinem Browser.

## Entwicklung

### Logs anzeigen
```bash
./sail logs
```

### Container Shell
```bash
./sail shell
```

### MySQL Shell
```bash
./sail mysql
```

### Tests ausführen
```bash
./sail test
```

## Produktions-Deployment

### Umgebungsvariablen anpassen

```bash
cp .env.example .env.production
```

Wichtige Einstellungen:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_*` Einstellungen für Produktions-Datenbank

### Mit Docker Compose

```bash
docker-compose -f docker-compose.prod.yml up -d
```

## Troubleshooting

### Container starten nicht
```bash
# Ports prüfen
netstat -tulpn | grep :80
netstat -tulpn | grep :3306

# Container neu starten
./sail down
./sail up -d
```

### Datenbank-Probleme
```bash
# Container logs prüfen
./sail logs mysql

# Datenbank zurücksetzen
./sail artisan migrate:fresh
```

### Berechtigungen
```bash
# Storage Ordner Berechtigungen
./sail shell
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```