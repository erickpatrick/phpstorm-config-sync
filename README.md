# Translation Generator
This repository contains the source code for the PHP application that generates translation files for Magento (and/or
 other systems)from the XLS translation spreadsheet we receive from translators.

This app only dependencies, as of now, are `composer/composer` (for dependency management) and `phpoffice/phpexcel` 
(to deal with reading XLS/Excel files). It uses a single XLS file input (entered on composer.json file) to generate 
various `.csv` files with the translations entries.

As of now, it used as a Web app though, in the future, it may be turned into a CLI app, using some sort of CLI 
framework (Symfony CLI, for example).

## Developers

- Erick Alvarenga (erick.alvarenga@nintendo.de)
- Jan-Martin Frühwacht (jan-martin.fruehwacht@nintendo.de) 