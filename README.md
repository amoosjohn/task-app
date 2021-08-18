## Installation process

- Clone the repository with __git clone__
- Copy __.env.example__ file to __.env__
- Run __composer install__
- Run __php artisan key:generate__
- Run __php artisan storage:link__
- Run CLI command to convert csv into json & xml __php artisan convert:csv-json-xml path__ (Ex: D:\FolderName\file.csv)
- Run __php artisan serve__ to run project
- launch the main URL
- On front view search by name or pvp and xml result will appear inside textarea.
- I have also added postman collection for api result kindly import collection in postman