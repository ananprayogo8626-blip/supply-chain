# Global Supply Chain Risk Intelligence Platform

---

## 📖 Project Overview
A **Laravel 12** application that provides a comprehensive, real‑time view of global supply‑chain risks. It aggregates **world‑wide data** from a single unified API and visualises the information via a beautiful Blade/Tailwind UI, Chart.js analytics, and an interactive Leaflet map.

---

## 🛠️ Tech Stack
| Layer | Technology |
|-------|------------|
| **Backend** | PHP 8.2, Laravel 12, MySQL |
| **Frontend** | Blade, Tailwind CSS, Chart.js, Leaflet.js |
| **Queue / Cache** | Laravel Queues (sync driver for local dev), file‑based cache |
| **Testing** | PHPUnit + Laravel Feature Tests |

---

## 🌐 Unified Data API
All external data is fetched from **WorldDataAPI** – a single public service that aggregates the datasets previously used from multiple sources.

| Endpoint | Description | Sample Request |
|----------|-------------|----------------|
| `GET /countries` | List of all countries with ISO code, capital, region, currency, flag, latitude, longitude. | `https://api.worlddataapi.com/v1/countries` |
| `GET /economics?country={iso}` | Economic indicators (GDP, inflation, population, exports, imports). | `https://api.worlddataapi.com/v1/economics?country=ID` |
| `GET /weather?lat={lat}&lon={lon}` | Current weather + forecast (temperature, rainfall, wind speed, storm risk). | `https://api.worlddataapi.com/v1/weather?lat=-6.2&lon=106.8` |
| `GET /exchange-rates?base={code}` | Exchange rates for all supported currencies. | `https://api.worlddataapi.com/v1/exchange-rates?base=USD` |
| `GET /news?topic=economy&country={iso}` | Latest news articles (image URL, publish date, source). | `https://api.worlddataapi.com/v1/news?topic=economy&country=ID` |
| `GET /ports` | World port index (port name, country, city, latitude, longitude). | `https://api.worlddataapi.com/v1/ports` |

> **Note:** The API is rate‑limited to 100 requests/minute and returns JSON. Authentication is performed via an API key passed in the `Authorization: Bearer <key>` header.

---

## 📂 Project Structure *(unchanged)*
```
app/
 ├─ Console/Commands/               # Artisan commands (fetch:countries, fetch:economic, …)
 ├─ Models/                         # Eloquent models
 ├─ Services/                       # One service per API (CountryService, EconomicService, …)
 ├─ Http/Controllers/               # Resource controllers
 └─ Http/Resources/                 # API resources

database/
 └─ migrations/                     # Existing migrations – **do not modify**

resources/
 └─ views/                         # Blade templates (countries, weather, news, …)

routes/
 ├─ web.php
 └─ api.php                        # REST endpoints

config/
 └─ cache.php                       # Default cache config – keep unchanged
```

---

## ⚙️ Artisan Commands & Services
| Command | Service | Purpose |
|---------|---------|---------|
| `php artisan fetch:countries` | `CountryService` | Pulls all country data from **WorldDataAPI** and stores it in `countries` table. |
| `php artisan fetch:economic` | `EconomicService` | Retrieves economic indicators for each country. |
| `php artisan fetch:weather` | `WeatherService` | Gets current weather & forecast using latitude/longitude. |
| `php artisan fetch:exchangerates` | `ExchangeRateService` | Updates `currency_data` table with latest rates. |
| `php artisan fetch:news` | `NewsService` | Imports news articles, saves image URL and performs lexicon‑based sentiment analysis. |
| `php artisan import:ports` | `PortService` | Populates `ports` table from the World Port Index endpoint. |
| `php artisan calculate:risk` | `RiskScoreService` | Computes the weighted risk score for every country. |

All services live under `app/Services/` and encapsulate the HTTP client (`Http::withHeaders([...])`) and response parsing logic.

---

## 📊 Risk Score Model
The platform uses a **Weighted Risk Model** (configurable via `config/risk.php`).

| Component | Weight |
|-----------|--------|
| Weather | 20 % |
| Economy | 25 % |
| Currency | 15 % |
| News Sentiment | 20 % |
| Port Connectivity | 20 % |

The total score (0‑100) maps to three risk levels:
* **LOW** (0‑30)
* **MEDIUM** (31‑70)
* **HIGH** (71‑100)

---

## 🗣️ Sentiment Analysis (Lexicon‑Based)
* Positive & negative word lists are stored in `positive_words` and `negative_words` tables.
* `SentimentService` tokenises each article, counts matches and assigns **Positive**, **Negative**, or **Neutral**.
* No external AI services are used – fully open‑source.

---

## 📡 REST API Endpoints
All endpoints return JSON using Laravel API Resources.
```php
GET /api/countries                // Paginated list of countries
GET /api/countries/{id}           // Country details (incl. weather, economics)
GET /api/risk                     // Countries with risk scores & level
GET /api/news                     // Latest news with image & sentiment
GET /api/currency                 // Exchange rates for all currencies
GET /api/ports                    // Port index with geo‑coordinates
```

---

## 🚀 Getting Started (Local Development)
1. **Clone the repo** and install dependencies:
   ```bash
   composer install
   npm install && npm run dev   # Tailwind compilation
   ```
2. **Create a `.env`** file (copy from `.env.example`) and set:
   * `DB_CONNECTION=mysql`
   * `DB_DATABASE=your_db`
   * `DB_USERNAME=...`
   * `DB_PASSWORD=...`
   * `WORLDDATAAPI_KEY=your_api_key`
3. **Run migrations**:
   ```bash
   php artisan migrate
   ```
4. **Seed sentiment lexicon** (optional, first run only):
   ```bash
   php artisan db:seed --class=SentimentWordsSeeder
   ```
5. **Fetch initial data** (run the commands in the order below):
   ```bash
   php artisan fetch:countries
   php artisan fetch:economic
   php artisan fetch:weather
   php artisan fetch:exchangerates
   php artisan fetch:news
   php artisan import:ports
   php artisan calculate:risk
   ```
6. **Serve the app**:
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000`.

---

## 🧪 Testing
```bash
php artisan test
```
Feature tests cover each Artisan command, controller response, and the risk‑calculation logic.

---

## 📦 Deployment Checklist
* `php artisan config:cache && php artisan route:cache`
* Set up a proper web server (Apache/Nginx) pointing to the `public/` directory.
* Configure a queue worker for background sync in production (e.g., `php artisan queue:work --daemon`).
* Schedule the sync commands via cron (e.g., hourly for weather, daily for economics).

---

## 🤝 Contributing
1. Fork the repository.
2. Create a feature branch (`git checkout -b feature/awesome-feature`).
3. Follow the existing code style (PSR‑12, Laravel conventions).
4. Submit a Pull Request with a clear description.

---

## 📜 License
This project is licensed under the **MIT License**.

---

*Last updated: 2026‑07‑04*
