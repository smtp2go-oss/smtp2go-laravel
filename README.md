# Symfony Laravel SMTP2Go Mailer

This is a Laravel Service Provider for sending mail from a Laravel Application via SMTP2GO.
It should also work in Symfony applications due to Laravel's underlying dependency on Symfony mailer.

## Requirements

- PHP 8.2 or higher
- Laravel 11.0 or higher
- SMTP2GO account

## Installation


1. Install via composer:
    ```sh
     composer require smtp2go-oss/smtp2go-symfony-laravel-transport
    ```

2. Set up environment variables:

    Add the following entry to your `.env` file with your SMTP2GO api key.
    
    `SMTP2GO_API_KEY=yourkeyhere`

    and change the existing `MAIL_MAILER` entry to `smtp2go`

    `MAIL_MAILER=smtp2go`

3. Update your config/mail.php file
```php
'smtp2go' => [
            'key' => env('SMTP2GO_API_KEY'),
            'transport' => 'smtp2go',
        ]
```

## Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss changes.

## License

This project is licensed under the MIT License. 

## Contact

For any questions or support, please contact SMTP2GO.