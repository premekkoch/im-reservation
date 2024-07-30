Ideal Media reservation demo
============================

For information about my method of developing the project (in Czech), go [here](docs/documentation.md).  

Requirements
------------

This Web Project is compatible with Nette 3.2 and requires PHP 8.1.


Installation
------------

To install the Project, Composer is the recommended tool. If you're new to Composer,
follow [these instructions](https://doc.nette.org/composer). Then, run	`composer install`.

Ensure the `temp/` and `log/` directories are writable.

Create the MySQL/MariaDB database. Copy `config/parameters.example.neon` as `config/parameters.neon` 
and set required database connection parameters. 

Run console command `bin/console migrations:continue` to apply database migrations.


Web Server Setup
----------------

To quickly dive in, use PHP's built-in server:

	php -S localhost:8000 -t www

Then, open `http://localhost:8000` in your browser to view the welcome page.

For Apache or Nginx users, configure a virtual host pointing to your project's `www/` directory.

**Important Note:** Ensure `app/`, `config/`, `log/`, and `temp/` directories are not web-accessible.
Refer to [security warning](https://nette.org/security-warning) for more details.
