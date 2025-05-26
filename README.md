# ✨ JewishPulse

**JewishPulse** is a smart Telegram bot that connects users to daily Jewish life — from 📜 Daf Yomi to 🕯 Shabbat times and 🌍 location-based Halachic data.

> Time. Holiness. Connection.

[![AGPL License](https://img.shields.io/badge/license-AGPL--3.0-blue.svg)](LICENSE)
[![Made with ❤️ in Israel](https://img.shields.io/badge/Made%20with-%E2%9D%A4%EF%B8%8F%20in%20Israel-blue)](https://github.com/WizardLoop/JewishPulse)
[![Docker Ready](https://img.shields.io/badge/docker-ready-blue.svg)](https://www.docker.com/)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://www.php.net/)

---

## 📦 Features

- 📍 Inline location-based Shabbat times & Daf Yomi
- 🕯 Real-time candle lighting info
- 📚 Daily Daf Yomi updates via Hebcal
- ⚙️ Built using `MadelineProto` & PHP Coroutine Engine
- 🌐 Multilingual and Geonames-integrated search

---

## 🚀 Quickstart Guide

### 1️⃣ Clone the repository

```bash
git clone https://github.com/WizardLoop/JewishPulse.git
cd JewishPulse
```

### 2️⃣ Install dependencies

Install PHP dependencies using Docker:

```bash
make composer-install
```

This runs `composer install` inside the container.

### 3️⃣ Launch the bot

```bash
make up
```

The bot will start running in the background.

### 🔍 View logs

```bash
make logs
```

Live log output of your bot.

---

## ⚙️ Common Commands

| Command                  | Description                                      |
|--------------------------|--------------------------------------------------|
| `make build`             | Build the Docker image                          |
| `make up`                | Start the bot in the background                |
| `make down`              | Stop and remove the bot container              |
| `make restart`           | Restart the bot quickly                        |
| `make logs`              | View real-time bot logs                        |
| `make sh`                | Access shell inside the Docker container       |
| `make composer-dump`     | Reload Composer autoload                       |
| `make test`              | Run PHPUnit tests                              |
| `make phpcs`             | Run PHP_CodeSniffer checks                     |
| `make cs-fix`            | Fix code style using PHP-CS-Fixer              |
| `make clean`             | Clean up cache, data, and vendor folders       |
| `make gitattributes-check` | Normalize line endings using .gitattributes |

---

## 🔐 Environment Configuration

Copy the `.env.example` to `.env` and customize as needed:

```bash
cp .env.example .env
```

Fill in values like:

- `BOT_TOKEN`
- `ADMIN_ID`
- `GEONAMES_USERNAME`

---

## 🌐 Inline Location Search

Type `@YourBotUsername your_city_name` in any chat to:

- Get your Daf Yomi for today
- Set Shabbat timezones per user

---

## 🧪 Testing & Code Quality

This project uses **PHPUnit** for unit testing and **PHP_CodeSniffer** / **PHP-CS-Fixer** for code style enforcement.

### ✅ Running Tests

To run all unit tests:

```bash
make test
```

Tests live in the `/tests` directory and follow PSR standards.

### 🎨 Code Style

Run PHP_CodeSniffer to check your code:

```bash
make phpcs
```

Auto-fix code style issues using PHP-CS-Fixer:

```bash
make cs-fix
```

---

## 🛠 Advanced Usage

- 📡 Geolocation-based Halachic times (via GeoNames API)
- 🔄 Persistent storage of user location
- 🕍 Personalized Zmanim via Hebcal API
- 🧪 Fully asynchronous using `Amp` and `MadelineProto`

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
