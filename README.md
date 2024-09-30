# Symfony Laravel SMTP2Go Mailer

This is a Laravel Service Provider for sending mail from a Laravel Application via SMTP2GO.
It should also work in Symfony applications due to Laravels' underlying dependency on Symfony mailer.

## Requirements

- PHP 8.2 or higher
- Laravel 11.0 or higher
- SMTP2GO account

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/yourusername/symfony-laravel-smtp2gomailer.git
    cd symfony-laravel-smtp2gomailer
    ```

2. Install dependencies:
    ```sh
    composer install
    ```

3. Set up environment variables:

    Update the `.env` file with your SMTP2GO api key.
    
    `SMTP2GO_API_KEY=yourkeyhere`
    `MAIL_MAILER=smtp2go`

4. Update your config/mail.php file
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