# 📦 Build the Docker image
build:
	@echo "🔧 Building Docker containers..."
	@docker-compose build

# 🚀 Start the bot in the background
up:
	@echo "🚀 Starting the bot..."
	@docker-compose up -d

# ⛔ Stop the bot and remove containers
down:
	@echo "🛑 Stopping the bot..."
	@docker-compose down

# 📺 Live logs from the bot
logs:
	@echo "📟 Streaming logs..."
	@docker-compose logs -f

# 🐚 Interactive shell access to the bot container
sh:
	@docker-compose exec bot sh

# 📥 Install PHP dependencies using Composer
composer-install:
	@echo "📥 Installing composer dependencies..."
	@docker-compose run --rm bot composer install

# 🔄 Regenerate the Composer autoloader
composer-dump:
	@echo "🔄 Dumping composer autoloader..."
	@docker-compose run --rm bot composer dump-autoload

# ♻️ Restart the bot without rebuilding
restart:
	@echo "♻️ Restarting the bot..."
	@docker-compose down
	@docker-compose up -d

# ✅ Run tests (if you use PHPUnit)
test:
	@echo "✅ Running tests..."
	@docker-compose run --rm bot vendor/bin/phpunit

# 🧹 Lint PHP code (if PHP-CS-Fixer or PHPCS is used)
lint:
	@echo "🔍 Linting PHP files..."
	@docker-compose run --rm bot vendor/bin/phpcs --standard=PSR12 src/

# 🧼 Fix code style
fix:
	@echo "🧼 Fixing code style..."
	@docker-compose run --rm bot vendor/bin/phpcbf --standard=PSR12 src/

# 🧽 Clean up temporary/cache/dev files
clean:
	@echo "🧽 Cleaning build and cache files..."
	rm -rf vendor \
	       .phpunit.result.cache \
	       /tmp/* \
	       data/*/lang.txt \
	       data/*/user_config.json \
	       data/*/*.sqlite \
	       tests/output \
	       *.log

# 📁 Normalize line endings via .gitattributes
gitattributes-check:
	@echo "🔄 Checking for line-ending normalization via .gitattributes..."
	@git add --renormalize .

# 🖥 Show the status of Docker containers
ps:
	@echo "📊 Showing Docker container status..."
	@docker-compose ps