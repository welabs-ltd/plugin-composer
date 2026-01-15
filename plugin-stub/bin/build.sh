#!/usr/bin/env bash

# Exit if any command fails.
set -e

# Change to the expected directory.
cd "$(dirname "$0")"
cd ..
DIR=$(pwd)
BUILD_DIR="$DIR/build/plugin-stub"

# Extract plugin version from plugin-stub.php
VERSION=$(grep -E '^[[:space:]]*\* Version:' "$DIR/plugin-stub.php" | head -n1 | sed -E 's/.*Version:[[:space:]]*([^[:space:]]+).*/\1/')
if [ -z "$VERSION" ]; then
    error "Could not determine version from plugin-stub.php"
    exit 1
fi



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

status "💃 Time to build the Plugin Stub ZIP file 🕺"

# remove the build directory if exists and create one
rm -rf "$DIR/build"
mkdir -p "$BUILD_DIR"

# Run the build.
NODE_PRODUCTION_COMMANDS

status "Generating build... 👷‍♀️"

# Copy all files
status "Copying files... ✌️"
FILES=(plugin-stub.php readme.txt dist includes templates assets languages composer.json composer.lock)

for file in ${FILES[@]}; do
    if [ -f "$file" ] || [ -d "$file" ]; then
        cp -R $file $BUILD_DIR
    fi
done

# Install composer dependencies
status "Installing dependencies... 📦"
cd $BUILD_DIR
composer install --optimize-autoloader --no-dev -q

# Remove composer files
rm composer.json composer.lock

# go one up, to the build dir
status "Creating archive... 🎁"
cd ..
zip -r -q plugin-stub-${VERSION}.zip plugin-stub

# remove the source directory
rm -rf plugin-stub

success "Done. You've built Plugin Stub! 🎉 "
echo -e "\n${BLUE_BOLD}File Path${COLOR_RESET}: ${YELLOW_BOLD}$(pwd)/plugin-stub-${VERSION}.zip${COLOR_RESET} \n"
