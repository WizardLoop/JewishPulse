#!/bin/bash

set -e

bold=$(tput bold)
normal=$(tput sgr0)

echo "\n$bold📦 Setting up JewishPulse...$normal"

# 1. Check Docker
if ! command -v docker &> /dev/null
then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

# 2. Check Docker Compose
if ! command -v docker-compose &> /dev/null
then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# 3. Clone repository (if not inside it already)
if [ ! -f Makefile ]; then
  echo "🔍 Cloning repository..."
  git clone https://github.com/WizardLoop/JewishPulse.git
  cd JewishPulse
fi

# 4. Create .env file
if [ ! -f .env ]; then
  echo "📝 Creating .env from .env.example..."
  cp .env.example .env
fi

# 5. Install dependencies
echo "📥 Installing composer dependencies..."
make composer-install

# 6. Launch bot
echo "🚀 Starting the bot..."
make up

# 7. Gitattributes normalization (optional)
echo "📐 Normalizing line endings with .gitattributes..."
git add . && git commit -m "chore: normalize line endings" || true

# 8. Add to .gitattributes if not present
grep -qxF 'install.sh text eol=lf' .gitattributes || echo 'install.sh text eol=lf' >> .gitattributes

echo "\n✅ Installation complete!"
echo ""  
echo "To check logs:    ${bold}make logs${normal}"
echo "To enter shell:    ${bold}make sh${normal}"
echo "To stop bot:       ${bold}make down${normal}"
echo ""
echo "🌐 Bot is now running and ready. Configure your .env file if needed."
echo "---"
echo "GitHub: https://github.com/WizardLoop/JewishPulse"
