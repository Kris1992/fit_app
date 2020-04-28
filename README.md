# Fit App 

Aplication written in php which help you track your activities and burnt out energy.

# Setup

Thanks to download the code. 

To get it working, follow these steps:

**Download Composer dependencies**

Make sure you have [Composer installed](https://getcomposer.org/download/)
and then run:

```
composer install
```

You may alternatively need to run `php composer.phar install`, depending
on how you installed Composer.

**Configure the .env (or .env.local) File**

Rename to `.env.local.dist` file to `.env.local` and make changes you need - specifically
in `DATABASE_URL`, `MAILER_URL`, `GOOGLE_RECAPTCHA_SITE_KEY` and `GOOGLE_RECAPTCHA_SECRET`.

**Setup the Database**

If you end previous step, it's time to create tables in database by commands below:

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

**Start the built-in web server**

You can use Nginx or Apache, but Symfony's local web server
works even better.

To install the Symfony local web server, follow
"Downloading the Symfony client" instructions found
here: https://symfony.com/download - you only need to do this
once on your system.

Then, to start the web server, open a terminal, move into the
project, and run:

```
symfony serve
```

or simply run:

```
php bin/console server:run
```

Now check out the site at `http://localhost:8000`

Have fun!

## Used Technologies

TO DO

## Next Week Patch (introduction to SCRUM flow) 

:hourglass_flowing_sand: Rebuild workouts history
:hourglass_flowing_sand: Refactor api error response (json+problem)
:heavy_check_mark: ADD possibility to add weight activities
:hourglass_flowing_sand: ADD more info about workout in reports
:hourglass_flowing_sand: ADD unit, integration and functional tests (every week add few) 

## Last weeks updates

:heavy_check_mark: ADD tracking route in real time
:heavy_check_mark: Refactor mailer service
:heavy_check_mark: ADD console commands which make works with code faster
:heavy_check_mark: ADD functional tests to admin panel 


## Have Ideas, Feedback or an Issue?

If you have suggestions or questions, please feel free to
open an issue on this repository.Thanks a lot for feedback 
from you;).
