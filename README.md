# MontaAPI
This project is a PHP library to use the Monta API from within your PHP application.
If you would like to use the Monta API v5, please use a version prior to version 1.

## Installation
Install the project through composer:

```composer require soerbv/monta-api```

## Usage
You can create your own API credentials within the Montaportal.

Create a new client:
```php
$client = new Client('username', 'password';
```

For example, you can now request the health of the Monta application.
```php
$client->getHealth();
```
The health requests also returns your rate limits.

For a list of all available endpoints, please see the available source. The API documentation is available here: https://api-v6.monta.nl/index.html

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.