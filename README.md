# 2018-Evidencio

A web applicaction that allows patients to calculate medical predictions based on [Evidencio library](https://www.evidencio.com) models in a user-friendly way.

A more detailed description is available in the Requirements Document under the `docs` folder.

### How to install

Follow the instructions in the [Laravel documentation](https://laravel.com/docs/5.6/installation) to install required dependencies and the Laravel package itself.

Clone the repository and create your own `.env` file by copying `.env.example` and providing necessary data. Then run these commands inside the repository:

    composer install
    php artisan key:generate

### How to run

Run the following command:

    php artisan serve

### Troubleshooting

In case of any issues with the framework please refer to the [Laravel documentation](https://laravel.com/docs/5.6).
