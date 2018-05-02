# Currency Converter

## Installation

You need to have Docker installed.

- Clone this repository.
- Run `docker-compose up -d` to create the environment.
- Connect to the Docker container via `docker exec -it currencyconverter_php_1 bash`.
- There you run `./install.sh` to install Composer and Symfony Framework.
- Input your fixer.io API key into `config/services.yaml`.
- For unit tests run `./bin/phpunit tests`.
- Visit `http://localhost/currency/convert` to run the app in your browser.
