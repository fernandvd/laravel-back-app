<p align="left"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


## Description

> Usage of Laravel Framework as backend application with mysql database. This project include use of: Restful services, authentication, routing, pagination, testing.

## Installation 

Clone this repository

### For Local

1) Install dependences 
```
    composer create-project 
```

2) Copy".env.example" into ".env" and replace a values of database. 

3) Migrate the database with seeding:

    php artisan migrate --seed

    This command generate user with password "p@S52024"

4) Run application
```
    php artisan serve
```

5) Install frontend
```
    npm install
```

6) Run fronted in dev mode
```
    npm run dev
```


### For Laravel Sail [Still for implement]
1) Install dependencies:

    ```
    docker run --rm -it \
        --volume $PWD:/app \
        --user $(id -u):$(id -g) \
        composer create-project
    ```

2) Start container

    ./vendor/bin/sail up -d

3) Migrate the database with seeding:

    sail artisan migrate --seed

## Run tests

    php artisan test


## OpenAPI specification 

Swagger UI be live at [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation).


## Contributions

Feedback, suggestions, and improvements are welcome, feel free to contribute.


## License

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information.
