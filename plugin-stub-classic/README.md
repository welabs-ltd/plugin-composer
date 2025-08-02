### For dev. environment

Run the following command for development environment.

```
composer update
```

### For production environment
Run the following command for production environment to ignore the dev dependencies.

```
composer update --no-dev
```

### Build Release
Now, Run the following bash script to create the release version.
```
bash bin/build.sh
```