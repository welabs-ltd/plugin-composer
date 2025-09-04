### 🚀 For Development Environment

Run the following command for development environment for the first time.

```
composer update
```

Run the following command for development environment for the existing plugin to keep packages same version.

```
composer install
```

### For QA

Run the following command for QA.

```
composer install
```

### For production environment
Run the following command for production environment to ignore the dev dependencies.

```
composer update --no-dev
```

### Build Release
Set execution permission to the script file by `chmod +x bin/build.sh` command. Now, Run the following bash script.
```
bin/build.sh
```