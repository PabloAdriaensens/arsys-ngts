ARSYS Technical Test
========================

Given some cities and connections, find the most economical routes for a shipment between 2 given cities or between 1 and all its possible destinations.

Technological Stack
------------

1. PHP 8.0.28;
2. Symfony 5.4.21
3. Postman / Browser

Installation
------------

* Clone GitHub repository
```
git clone https://github.com/PabloAdriaensens/arsys-ngts
```

* Access into directory:
```
cd arsys-ngts
```

* Start Project and server:
```
composer install
```
```
symfony server:start -d
```

* Ready to access to browser or Postman collection:
```
http://localhost:8000/
```

* Execute tests:
```
php bin/phpunit
```

Endpoints
-----

**API Urls** (Import Postman collection)

* Default URL:
  * <http://localhost:8000>

* Main URL initialization:
  * <http://localhost:8000/transport>

* GET given 2 parameters:
    * <https://localhost:8000/transport?origin=Logroño&destination=Ciudad+Real>

* GET given 1 parameter:
    * <https://localhost:8000/transport?origin=Logroño>