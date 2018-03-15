# 2018-Evidencio

A web applicaction that allows patients to calculate medical predictions based on [Evidencio library](https://www.evidencio.com) models in a user-friendly way.

A more detailed description is available in the Requirements Document under the `docs` folder.

### How to install

Clone the repository and create your own `.env` file by copying `.env.example` and providing necessary data. Then run these commands inside the repository:

    composer install
    php artisan key:generate

### How to run

Run the following command:

    php artisan serve

### Troubleshooting

In case of any issues with the framework please refer to the [laravel documentation](https://laravel.com/docs/5.6).
