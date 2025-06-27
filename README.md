
<img src="https://github.com/wizardloop/JewishPulse/raw/main/assets/JewishPulse.png" width="300" height="300" />

---

# ✡️✨ JewishPulse

**JewishPulse** is a smart Telegram bot that connects users to daily Jewish life — from 📜 Daf Yomi to 🕯 Shabbat times and 🌍 location-based Halachic data.

> ✡️✨ Time. Holiness. Connection.

[![AGPL License](https://img.shields.io/badge/license-AGPL--3.0-blue.svg)](LICENSE)
[![Made with ❤️ in Israel](https://img.shields.io/badge/Made%20with-%E2%9D%A4%EF%B8%8F%20in%20Israel-blue)](https://github.com/WizardLoop/JewishPulse)
[![Docker Ready](https://img.shields.io/badge/docker-ready-blue.svg)](https://www.docker.com/)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://www.php.net/)

[![Telegram](https://img.shields.io/badge/Channel-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)](https://t.me/JewishPulse)
[![Telegram](https://img.shields.io/badge/Group-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)](https://t.me/Jewish_Pulse)

[![Telegram](https://img.shields.io/badge/Official%20Bot-000000?style=for-the-badge&logo=telegram&logoColor=white)](https://t.me/JewishPulseBot)

---

## 📦 Features

- 📍 Inline location-based Shabbat times & Daf Yomi
- 🕯 Real-time candle lighting info
- 📚 Daily Daf Yomi updates via Hebcal
- ⚙️ Built using `MadelineProto` & PHP Coroutine Engine
- 🌐 Multilingual and Geonames-integrated search

---

### 🛠 Installation Setup

#### 1️⃣ Clone the repository

```bash
git clone https://github.com/WizardLoop/JewishPulse.git
cd JewishPulse
```

#### 2️⃣ Install dependencies

Install PHP dependencies using Docker:
```bash
docker compose run --rm composer install
```

#### 3️⃣ Launch the bot

```bash
docker compose up --pull always -d
```

The bot will start running in the background.

#### 🔍 View logs

```bash
docker compose logs
```

Live log output of your bot.

---

## ⚙️ Common Commands

| Command                        | Description                                      |
|--------------------------------|--------------------------------------------------|
| `docker compose build`         | Build the Docker image                          |
| `docker compose up --pull always -d`         | Start the bot in the background                |
| `docker compose down`          | Stop and remove the bot container              |
| `docker compose restart`       | Restart the bot quickly                        |
| `docker compose logs`       | View real-time bot logs                        |
| `docker compose exec bot composer dump-autoload` | Reload Composer autoload |
| `docker-compose ps`            | Show the status of Docker containers           |

---

## 🔐 Environment Configuration

Copy the `.env.example` to `.env` and customize as needed:

```bash
cp .env.example .env
```

Fill in values like:

- `API_ID`
- `API_HASH`
- `BOT_TOKEN`
- `ADMIN_ID`
- `GEONAMES_USERNAME`

---

## 🌐 Inline Location Search

Type `@YourBotUsername your_city_name` in any chat to:

- Set Shabbat timezones

---

## 🧪 Testing & Code Quality

This project uses **PHPUnit** for unit testing and **PHP_CodeSniffer** / **PHP-CS-Fixer** for code style enforcement.

### ✅ Running Tests

To run all unit tests:

```bash
docker compose exec bot vendor/bin/phpunit
```

Tests live in the `/tests` directory and follow PSR standards.

### 🎨 Code Style

Run PHP_CodeSniffer to check your code:

```bash
docker compose exec bot vendor/bin/phpcs
```

Auto-fix code style issues using PHP-CS-Fixer:

```bash
docker compose exec bot vendor/bin/php-cs-fixer fix
```

---

## 🛠 Advanced Usage

- 📡 Geolocation-based Halachic times (via GeoNames API)
- 🔄 Persistent storage of user location
- 🕍 Personalized Zmanim via Hebcal API
- 🧪 Fully asynchronous using `Amp` and `MadelineProto`
- ⚙️ GitHub Actions CI with: PHPUnit, PHPCS, CS-Fixer, locale checks, bot output simulation

---

## 🤝 Contributing

Pull requests are welcome! To contribute:

1. Fork the repo
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Commit your changes: `git commit -m 'Add my feature'`
4. Push to the branch: `git push origin feature/my-feature`
5. Open a PR 🙌

---

## 📄 License

This project is licensed under the **GNU Affero General Public License v3.0 (AGPL-3.0)**.

See [`LICENSE`](LICENSE) for details.

---

## 🙏 Acknowledgments

- [Hebcal.com](https://www.hebcal.com/home/developer-apis) for Jewish calendar data
- [GeoNames.org](https://www.geonames.org/export/web-services.html) for geographical location support
- [MadelineProto](https://docs.madelineproto.xyz/) for powerful Telegram API interface

---

📫 Questions, suggestions, feedback? Open an [issue](https://github.com/WizardLoop/JewishPulse/issues) or contact @WizardLoop.
