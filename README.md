# Ruinas & Sabores GIS

"Ruinas & Sabores" is a web mapping experience built for the Geographic Information Systems course (PTAS 2025). The application highlights archaeological heritage around Aveiro (Portugal) and lets visitors discover nearby food, fuel and mobility services through an interactive Leaflet map, contextual filters, and guided interactions.

## Features
- Leaflet map with OpenStreetMap, Topographic and Satellite base layers, responsive layout and Bootstrap UI components.
- Archaeological, gastronomy and public-service layers loaded from a remote PostgreSQL/PostGIS database via PHP endpoints (`get_locais.php`, `get_restauracao.php`, `get_servicos.php`).
- Dynamic marker filtering by viewport, category toggles and configurable radius around a selected archaeological site.
- Address search powered by the Nominatim API, with inline suggestions and smooth map transitions to selected results.
- Route planning through OpenRouteService supporting multiple transport modes, distance/time summaries, and graceful error handling.
- Intro.js guided tour that introduces the main tools, including the archaeology radius control.

## Project Structure
- `index.html` - main entry point that wires Bootstrap layout, the sidebar tools, map container and toast notifications.
- `css/` - styling split into map presentation, UI layout and tooltip refinements.
- `js/mapa.js` - Leaflet bootstrapping, base-layer switching and radius-selection workflow.
- `js/marcadores.js` - marker fetching, categorisation, viewport filtering and iconography.
- `js/pesquisa.js` - address lookup logic, suggestion list rendering and helper utilities.
- `js/rotas.js` - OpenRouteService integration, user-driven waypoint selection and travel summaries.
- `js/intro.js` - guided onboarding using Intro.js and contextual UI tweaks.
- `get_*.php` - lightweight JSON APIs backed by a remote Postgres/PostGIS dataset; `conexao.php` centralises PDO access.
- `Tests/` - PHPUnit smoke tests covering JSON schema expectations for the PHP endpoints.
- `Relatorio SIG.pdf` - original Portuguese project report with background information and data sources.

## Prerequisites
- PHP 8.x with PDO_PGSQL extension enabled (required for the PHP data endpoints).
- Access to the configured PostgreSQL/PostGIS database (update `conexao.php` if credentials change).
- Internet connectivity for third-party services (Leaflet tiles, Bootstrap CDN, Intro.js CDN, Nominatim, OpenRouteService).
- (Optional) Composer and PHPUnit if you intend to execute the bundled tests.

## Local Development
1. Clone the repository and navigate to its root.
2. (Recommended) copy `conexao.php` and adjust credentials for your environment instead of committing production secrets.
3. Start a local PHP server so the static assets and PHP endpoints share the same origin:
   ```bash
   php -S 127.0.0.1:8000
   ```
4. Open `http://127.0.0.1:8000/index.html` in your browser.
5. Pan the map, toggle layers or pick an archaeological location to activate the radius filter. Use the "Criar Rota" button to start route planning.

## Running Tests
1. Install dependencies if you do not already have PHPUnit in your PATH:
   ```bash
   composer require --dev phpunit/phpunit
   ```
2. Execute the test suite (note: the default `phpunit.xml` expects tests under `tests/` - adjust to `Tests/` or update the XML if required):
   ```bash
   vendor/bin/phpunit --configuration phpunit.xml
   ```

## External Services & Keys
- OpenRouteService: replace the demo key in `js/rotas.js` with your own to avoid rate limits.
- Nominatim usage must respect their usage policy; consider hosting your own instance for production workloads.
- Leaflet base layers rely on public tile services subject to fair-use guidelines.

## Deployment Notes
- Disable `display_errors` and move database credentials to environment variables before deploying `get_*.php` endpoints.
- Consider reverse-proxy caching for static assets and rate limiting for the public APIs.
- Validate TLS/HTTPS configuration if exposing the application publicly, especially when handling API keys or user data.

## Further Reading
Consult `Relatorio SIG.pdf` (Portuguese) for detailed methodology, dataset sourcing and user scenarios that guided the implementation.
