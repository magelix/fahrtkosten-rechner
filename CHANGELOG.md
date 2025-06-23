# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

## [1.0.0] - 2025-06-23

### Hinzugefügt
- ✨ Arbeitsplatz-Verwaltung mit wiederverwendbaren Standorten
- 🚗 Fahrten-Management mit automatischer Kostenberechnung
- 📊 Übersichtsseiten mit Statistiken und Zusammenfassungen
- 🇨🇭 Schweizer Lokalisierung (CHF, de_CH, Zürich Zeitzone)
- 🐳 Docker-basierte Entwicklungsumgebung mit Laravel Sail
- 📱 Responsive Bootstrap 5 Benutzeroberfläche
- ⚡ JavaScript für dynamisches Laden von Arbeitsplatz-Daten
- 🔄 CRUD-Operationen für Arbeitsplätze und Fahrten
- 💰 Flexible Kostenansätze (0,70 CHF/km Standard)
- 📅 Datum-Validation und mehrtägige Aufenthalte

### Technische Details
- Laravel 12 Framework
- PHP 8.4 Support
- MySQL 8.0 Datenbank
- Bootstrap 5 Frontend
- Docker Compose Setup
- Blade Template Engine

### Datenmodell
- **Workplaces**: Wiederverwendbare Arbeitsplätze mit Standard-Entfernungen
- **Trips**: Fahrten mit Workplace-Referenz und überschreibbaren Werten
- **Relationships**: One-to-Many zwischen Workplace und Trips

### UI/UX Features
- Automatisches Vorausfüllen von Entfernung und Kosten
- Überschreibbare Werte für individuelle Fahrten
- Zusammenfassungen und Statistiken
- Intuitive Navigation
- Mobile-optimiert