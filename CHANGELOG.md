
# 📜 Changelog - JewishPulse

All notable changes to the **JewishPulse** project will be documented in this file.

---

## ✡️✨ [v1.0.1] - 2025-06-27

### 🐞 Bug Fix
- Replaced deprecated `reply_to_msg_id` with the new `reply_to` parameter in the `sendMessage` method.
- Location: `handleSaveLocationFromMessage` function in `bot.php`
- Ensures compatibility with the latest MadelineProto versions and avoids deprecation warnings.

### 🔧 Files Affected
- `bot.php`

> ✅ This update is backward-compatible — no changes required from end users.

[Full Changelog » v1.0.0...v1.0.1](https://github.com/WizardLoop/JewishPulse/compare/v1.0.0...v1.0.1)

---

## ✡️✨ [v1.0.0] - 2025-06-27

### 🚀 Initial Release
- First stable release of **JewishPulse** — a smart Telegram bot to enhance Jewish daily life.
- Built using `MadelineProto` and the PHP Coroutine Engine for high performance.

### 📦 Features
- 📍 Inline location-based Shabbat times & Daf Yomi updates
- 🕯 Real-time candle lighting information
- 📚 Daily Daf Yomi via Hebcal API
- 🌐 Multilingual support & GeoNames integration

---
