#!/usr/bin/env bash

# Exit if any command fails.
set -e

# Change to the expected directory.
cd "$(dirname "$0")"
cd ..
DIR=$(pwd)
BUILD_DIR="$DIR/build/plugin-composer"

# Enable nicer messaging for build status.
BLUE_BOLD='\033[1;34m'
GREEN_BOLD='\033[1;32m'
RED_BOLD='\033[1;31m'
YELLOW_BOLD='\033[1;33m'
COLOR_RESET='\033[0m'

error() {
    echo -e "\n${RED_BOLD}$1${COLOR_RESET}\n"
}
status() {
    echo -e "\n${BLUE_BOLD}$1${COLOR_RESET}\n"
}
success() {
    echo -e "\n${GREEN_BOLD}$1${COLOR_RESET}\n"
}
warning() {
    echo -e "\n${YELLOW_BOLD}$1${COLOR_RESET}\n"
}

status "💃 Time to build the Plugin Composer ZIP file 🕺"

# remove the build directory if exists and create one
rm -rf "$DIR/build"
mkdir -p "$BUILD_DIR"

# Install npm dependencies and build assets
status "Installing npm dependencies... 📦"
npm install

status "Building assets... 🏗️"
npm run build

status "Generating localization files... 🌍"
wp i18n make-pot . languages/plugin-composer.pot --domain=welabs-plugin-composer

status "Generating build... 👷‍♀️"

# Copy all files
status "Copying files... ✌️"
FILES=(plugin-composer.php readme.txt assets includes plugin-stub templates languages composer.json composer.lock src webpack.config.js package.json package-lock.json)

for file in ${FILES[@]}; do
    if [ -f "$file" ] || [ -d "$file" ]; then
        cp -R $file $BUILD_DIR
    fi
done

# Install composer dependencies
status "Installing composer dependencies... 📦"
cd $BUILD_DIR
composer install --optimize-autoloader --no-dev -q

# Remove development files
rm composer.json composer.lock package-lock.json package.json
rm -rf src webpack.config.js

# go one up, to the build dir
status "Creating archive... 🎁"
cd ..
zip -r -q plugin-composer.zip plugin-composer

# remove the source directory
rm -rf plugin-composer

success "Done. You've built Plugin Composer! 🎉 "

echo -e "\n${BLUE_BOLD}File Path${COLOR_RESET}: ${YELLOW_BOLD}$(pwd)/plugin-composer.zip${COLOR_RESET} \n"