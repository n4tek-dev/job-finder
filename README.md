# Job Finder Application

## Instalacja

1. Sklonuj repozytorium:

```bash
git clone https://github.com/n4tek-dev/job-finder.git
cd job-finder/app
```

2. Skonfiguruj plik `.env`:

Skopiuj plik `.env.test` do `.env`:

```bash
cp .env.test .env
```

3. Zainstaluj zależności za pomocą Composer:

```bash
composer install
```

4. Przejdź do folderu projektu i uruchom kontenery Docker:

```bash
cd ..
docker-compose up -d
```

 5. Wejdź do kontenera aplikacji:

```bash    
docker-compose exec php bash
```

6. Wykonaj migrację bazy danych

```bash
php bin/console doctrine:migrations:migrate
```
    
7. Wygeneruj kilka ofert pracy

```bash
php bin/console app:generate-job-offers --count=35
```

## Uruchomienie aplikacji

Otwórz przeglądarkę i przejdź do adresu:

```bash
http://localhost:8000
```

## Testowanie

1. Wejdź do kontenera aplikacji:

```bash
docker-compose exec php bash
```

2. Utwórz testową bazę danych:

```bash
php bin/console doctrine:database:create --env=test
```

3. Zaktualizuj schemat bazy danych

```bash
php bin/console doctrine:schema:update --force --env=test
```

4. Załduj dane testowe do bazy danych w środowisku testowym:

```bash
php bin/console doctrine:fixtures:load --env=test
```

5. Aby uruchomić testy jednostkowe, użyj następującego polecenia w kontenerze:

```bash
php bin/phpunit
```
