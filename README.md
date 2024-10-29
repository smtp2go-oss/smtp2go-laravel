# Symfony Laravel SMTP2Go Mailer

## This package is currently in BETA and is not recommended for use in production. Currently we are only testing in Laravel applications.

This is a Laravel Service Provider for sending mail from a Laravel Application via SMTP2GO.
It should also work in Symfony applications due to Laravel's underlying dependency on Symfony mailer.

## Requirements

- PHP 8.2 or higher
- Laravel 11.0 or higher
- SMTP2GO account



## Install

Install via composer:
    ```sh
     composer require smtp2go-oss/smtp2go-symfony-laravel-transport
    ```

## Setup - Laravel
1. Set up environment variables:

Add the following entry to your `.env` file with your SMTP2GO api key.

`SMTP2GO_API_KEY=api-YOUR_API_KEY_HERE`

and change the existing `MAIL_MAILER` entry to `smtp2go`

`MAIL_MAILER=smtp2go`

2. Update your config/mail.php file
```php
'smtp2go' => [
            'key' => env('SMTP2GO_API_KEY'),
            'transport' => 'smtp2go',
        ]
```

## Setup - Symfony
1. Add the following to your .env file
```
SMTP2GO_API_KEY=api-YOUR_API_KEY_HERE
MAILER_DSN=SMTP2GO://${SMTP2GO_API_KEY}@default
```
2. If not already configured, import a services.php file in the `config/services.yaml` file
```
imports:
    - { resource: 'services.php' }
```

3. Setup config/services.php, example below...
```php
<?php
//config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use SMTP2GO\Transport\SMTP2GOTransportFactory;

return function (ContainerConfigurator $container): void {

    $services = $container->services();

    //if you already have a services.php file set up, you just
    //need to add this line...
    $services->set(SMTP2GOTransportFactory::class)
    ->tag('mailer.transport_factory');
};

```


## Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss changes.

## License

This project is licensed under the MIT License. 

## Contact

For any questions or support, please contact SMTP2GO.