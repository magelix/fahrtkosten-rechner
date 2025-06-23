# Fahrtkosten-Rechner 🚗💰

Ein Laravel-basierter Rechner für Fahrtkosten zu entfernten Arbeitsplätzen, speziell entwickelt für die Schweiz.

## Screenshots

![Fahrten-Übersicht](docs/trips-overview.png)
![Arbeitsplätze verwalten](docs/workplaces.png)

## 🚀 Quick Start mit Laravel Sail

### Voraussetzungen
- Docker & Docker Compose
- Git

### Installation

```bash
# Repository klonen
git clone https://github.com/yourusername/fahrtkosten-rechner.git
cd fahrtkosten-rechner

# Docker Container starten
./sail up -d

# Datenbank-Migration ausführen
./sail artisan migrate

# 🎉 Anwendung ist verfügbar unter: http://localhost
```

### Entwicklung

```bash
# Container stoppen
./sail down

# Artisan Befehle
./sail artisan migrate
./sail artisan make:model Example

# Composer Befehle
./sail composer install
./sail composer require package/name

# MySQL Shell
./sail mysql

# Application Shell
./sail shell
```

## ✨ Features

### 🏢 Arbeitsplatz-Verwaltung
- **Wiederverwendbare Arbeitsplätze**: Einmal erfassen, immer wieder nutzen
- **Standard-Entfernungen**: Automatisches Vorausfüllen bei neuen Fahrten
- **Flexible Anpassung**: Entfernung pro Fahrt überschreibbar (für Umwege)
- **Aktiv/Inaktiv Status**: Alte Arbeitsplätze ausblenden

### 🚗 Fahrten-Management
- **Einfache Erfassung**: Arbeitsplatz auswählen, Daten werden geladen
- **Schweizer Ansatz**: 0,70 CHF/km Standard (anpassbar)
- **Automatische Berechnung**: Hin- und Rückfahrt × Kosten/km
- **Übernachtungen**: Tracking von mehrtägigen Aufenthalten

### 📊 Auswertungen
- **Gesamtübersicht**: Alle Fahrten mit Kostenübersicht
- **Statistiken**: Pro Arbeitsplatz und gesamt
- **Export-ready**: Daten für Spesenabrechnung vorbereitet

### 🎨 Benutzerfreundlichkeit
- **Responsive Design**: Funktioniert auf Desktop und Mobile
- **Deutsche/Schweizer Lokalisierung**: Datum, Währung, Sprache
- **Bootstrap UI**: Moderne, saubere Oberfläche
- **Intuitive Navigation**: Schneller Zugriff auf alle Funktionen

## 🗄️ Datenmodell

### Trip (Fahrt)
- `workplace_name` - Name des Arbeitsplatzes
- `workplace_address` - Adresse des Arbeitsplatzes  
- `distance_km` - Entfernung in km (einfach)
- `departure_date` - Abreisedatum
- `return_date` - Rückkehrdatum
- `overnight_days` - Anzahl Übernachtungen
- `cost_per_km` - Kosten pro Kilometer (Standard: 0,70 CHF)
- `total_cost` - Automatisch berechnete Gesamtkosten

## 🔧 Technologie-Stack

- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: Bootstrap 5, Blade Templates, JavaScript
- **Datenbank**: MySQL 8.0
- **Container**: Docker + Laravel Sail
- **Entwicklung**: Hot Reload, Live Development

## 📁 Projektstruktur

```
app/
├── Http/Controllers/TripController.php  # CRUD-Controller
├── Models/Trip.php                      # Datenmodell
database/
├── migrations/                          # Datenbank-Schema
resources/views/
├── layout.blade.php                     # Basis-Layout
├── trips/                              # Trip-Views
routes/web.php                          # Routen-Definition
```

## 🛠️ Entwicklung

### Neue Features hinzufügen
1. Model erweitern: `app/Models/Trip.php`
2. Controller anpassen: `app/Http/Controllers/TripController.php`
3. Views aktualisieren: `resources/views/trips/`
4. Migration erstellen: `php artisan make:migration`

### Tests ausführen
```bash
php artisan test
```

## 📊 Kostenberechnung

```
Gesamtkosten = Entfernung × 2 × Kosten pro Kilometer
```

Beispiel: 50km × 2 × 0,70CHF = 70,00CHF

## 🤝 Contributing

1. Fork das Repository
2. Erstelle einen Feature Branch (`git checkout -b feature/amazing-feature`)
3. Commit deine Änderungen (`git commit -m 'Add amazing feature'`)
4. Push zum Branch (`git push origin feature/amazing-feature`)
5. Öffne eine Pull Request

## 📄 Lizenz

Dieses Projekt steht unter der [MIT Lizenz](LICENSE).

## 🙋‍♂️ Support

Bei Fragen oder Problemen:
- Erstelle ein [Issue](https://github.com/yourusername/fahrtkosten-rechner/issues)
- Oder kontaktiere uns direkt

---

**Entwickelt für Schweizer Unternehmen und Freelancer** 🇨🇭
