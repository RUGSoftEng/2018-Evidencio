# 2018-Evidencio

A web applicaction that allows patients to calculate medical predictions based on [Evidencio library](https://www.evidencio.com) models in a user-friendly way.

A more detailed description is available in the Requirements Document under the `docs` folder.




### How to install

Follow the instructions in the [Laravel documentation](https://laravel.com/docs/5.6/installation) to install required dependencies and the Laravel package itself.

Clone the repository and create your own `.env` file by copying `.env.example` and providing necessary data. Please provide Evidencio API key here as well and ensure that you have your database set up correctly. Then run this command inside the repository:

    ./install.sh

You will also need to run it every time there is a new database migration available.

### How to run

Run the following command:

    php artisan serve

### Troubleshooting


#### NOTE: in Windows, this server will run from a command prompt.
If you are using Apache through XAMPP (**maybe** even without XAMPP), you can change the following file:

    XAMPP\apache\conf\httpd.conf
(If not through XAMPP, then in the respective apache directory)
Under the DocumentRoot setting in the file, you need to change the line:

    C:\xampp\htdocs

into:

    yourDirectory\2018-Evidencio\public

where the "2018-Evidencio" is the local repository. This will then be the localhost after running the apache server. Note that this should more preferably be done through virtual hosts, but since it's more complicated, this works as well. The only problem is that you can only run one localhost at a time. The original solution can be found on the address:
[stack overflow link](https://stackoverflow.com/questions/1408/make-xampp-apache-serve-file-outside-of-htdocs/)


In case of any issues with the framework please refer to the [Laravel documentation](https://laravel.com/docs/5.6).
