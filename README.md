# Demo Giphy Integration

API Giphy Integration en Laravel 10.48.4, PHP 8.3.4

## Inicio

Leer todo este archivo primero. Entorno utilizado en esta descripción Ubuntu 22.04.4 LTS

_Estas instrucciones te permitirán obtener una copia del proyecto en tu máquina local para propósitos de desarrollo y testing._

### Requisitos

-   Docker - [How to install](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-22-04)
-   Docker-compose - [How to install](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-compose-on-ubuntu-22-04)

**Docker & Compose versions used**

-   Docker version 25.0.4, build 1a576c5
-   docker-compose version v2.3.3
-   Docker-compose YML V3

**Docker image base**
- [php:8.3-apache-bookworm] (https://hub.docker.com/_/php)
- [mysql:5.7.22] (https://hub.docker.com/_/mysql)
- [redis:alpine] (https://hub.docker.com/_/redis)


**Docker Ports**

-   :::80->80/tcp (Port)
-   :::6379->6379/tcp (port)
-   :::3306->3306/tcp (port)

**Laravel Framework & Paquetes**

```sh
./composer.json
```

### Instalación

Setear usuario en el grupo de docker (en caso de no tenerlo):

```sh
sudo usermod -aG docker <my-user-name>
```

NOTA: es importante cerrar sesión y volver a iniciarla (o reiniciar) para que los cambios se apliquen

La primera vez se deben crear las imágenes docker, la ejecución puede tomar unos minutos,
si la imagen base no se encuentra en el host, baja automáticamente y luego comienza la instalación de
todos los paquetes indicados en el docker file.

Los puertos 80, 6379 y 3306 TCP deben estar libres siempre que se arranque el contenedor, de lo contrario fallara

Clonar este repositorio

```sh
git clone git@github.com:adriancoral/Giphy-Integration.git .

```

Iniciar el stack de docker

Editar los valores del usuario non-root de ser necesario en el archivo `dockerstack/docker/php83-apache/Dockerfile`

```sh
ARG user=acoral
ARG uid=1000
ARG gid=1000
```

```bash
# Primera vez
docker-compose up -d --build

# Luego
docker-compose up -d

# Detener los servicios
docker-compose down

```

La aplicación se montara dentro del contenedor en /var/www/

```bash
#  docker ps: muestra los contenedores activos
docker ps
CONTAINER ID   IMAGE              COMMAND                  CREATED        STATUS        PORTS                                       NAMES
728db4e570d2   php83apache:prex   "docker-php-entrypoi…"   21 hours ago   Up 21 hours   0.0.0.0:80->80/tcp, :::80->80/tcp           httpserverprex
b5ee471e3c63   mysql:5.7.22       "docker-entrypoint.s…"   21 hours ago   Up 21 hours   0.0.0.0:3306->3306/tcp, :::3306->3306/tcp   mysqldb
845e9d62e3fc   redis:alpine       "docker-entrypoint.s…"   21 hours ago   Up 21 hours   0.0.0.0:6379->6379/tcp, :::6379->6379/tcp   redis
```

### Configuración

Para instalar los paquetes de laravel hay que entrar al contendor PHP, la base de datos ya fue creada
cuando se inició el contenedor de MySQL, los datos de acceso ya está en `.env.example`

```bash
# ingresar al contenedor
docker exec -it -u acoral  httpserverprex /bin/bash

#developer@zoho:
composer install

# Configurar laravel, la configuración para entorno local esta en env.example (sobreescribir el que crea laravel)
# Configurar la DB y redis según su entorno
# La api_key de Giphy ya se encuentra seteada, solo a modo de pruebas GIPHY_API_KEY 
cp .env.example .env

# Si todo esta bien, probamos el comando artisan
php artisan

# `migrate` crea solo la estructura de tablas
php artisan migrate

# `db:seed` crea datos para el uso de la aplicacion
php artisan db:seed

# Passport keys
php artisan passport:keys

# Es posible que sea necesario asignarle permisos extras a las llaves
chmod 644 storage/oauth-* 
```

### Debug y logs

Si hay errores de PHP se mostraran por el stdout del contenedor o en los logs,
alternativamente y dependiendo de la configuración, se puede entrar al contenedor para ver otros logs

```bash
# Ver logs con docker-composer
docker-compose logs -f

# Logs con docker
docker logs -f [nombre-del-contenedor]

# Ingreso al contenedor
docker exec -it [nombre-del-contenedor] /bin/bash

```

### Problemas comunes
Si nos encontramos con puertos en uso, primero verificamos no haya otras instancias
```
docker-compose down  # Stop container on current dir if there is a docker-compose.yml
docker rm -fv $(docker ps -aq)  # Remove all containers
sudo lsof -i -P -n | grep <port number>  # List who's using the port
sudo kill -9 <pid>

```
### Testing

La app implementa test unitarios y features con PHPUnit.

[PHPUnit Manual](https://docs.phpunit.de/en/11.0/)

```bash
# Ingresando al contenedor
docker exec -it -u acoral  httpserverprex /bin/bash

# Run whole suite
php artisan test 
php artisan test --filter TournamentTest

# Testing from host
docker exec -it -u acoral httpserverprex php artisan test

# PhpUnit CLI Options
# https://docs.phpunit.de/en/11.0/textui.html#command-line-options
```

Output

```bash
  PASS  Unit\Events\RequestTerminatedTest
  ✓ request terminated event listened by storerequest                                                                                                                                                         0.58s  

   PASS  Unit\Listeners\StoreRequestTest
  ✓ store request listener save request data                                                                                                                                                                  0.44s  

   PASS  Unit\Requests\FavoriteAddRequestTest
  ✓ rules                                                                                                                                                                                                     0.45s  
  ✓ authorize                                                                                                                                                                                                 0.52s  

   PASS  Unit\Requests\GiphyGifsRequestTest
  ✓ rules                                                                                                                                                                                                     0.51s  
  ✓ authorize                                                                                                                                                                                                 0.50s  

   PASS  Unit\Requests\GiphySearchRequestTest
  ✓ rules                                                                                                                                                                                                     0.43s  
  ✓ authorize                                                                                                                                                                                                 0.46s  

   PASS  Tests\Unit\Requests\UserLoginRequestTest
  ✓ rules                                                                                                                                                                                                     0.51s  
  ✓ authorize                                                                                                                                                                                                 0.47s  

   PASS  Unit\Requests\UserRegisterRequestTest
  ✓ rules                                                                                                                                                                                                     0.42s  
  ✓ authorize                                                                                                                                                                                                 0.50s  

   PASS  Tests\Feature\AuthControllerTest
  ✓ a registered user can login                                                                                                                                                                               0.57s  
  ✓ email and password fields are required to login                                                                                                                                                           0.48s  
  ✓ trying to login without the required fields throws an exception                                                                                                                                           0.42s  
  ✓ a unregistered user cannot login error expected                                                                                                                                                           0.71s  
  ✓ a logged user can logout                                                                                                                                                                                  0.52s  
  ✓ an exception is expected when unauthenticated user try logout                                                                                                                                             0.42s  
  ✓ an logged user can get your info through the endpoint me                                                                                                                                                  0.53s  
  ✓ an exception is expected when unauthenticated user try access to endpoint me                                                                                                                              0.43s  
  ✓ a user can register                                                                                                                                                                                       0.43s  
  ✓ when a user registers their email must be unique                                                                                                                                                          0.40s  

   PASS  Tests\Feature\FavoriteControllerTest
  ✓ a logged user can save a favorite                                                                                                                                                                         0.50s  
  ✓ a logged user cannot duplicate favorite                                                                                                                                                                   0.51s  
  ✓ add favorite required alias and gif id fields                                                                                                                                                             0.53s  
  ✓ only an logged user can access add favorite                                                                                                                                                               0.50s  
  ✓ a logged user can see your favorite list                                                                                                                                                                  0.50s  
  ✓ a logged user only can see your favorite list                                                                                                                                                             0.46s  
  ✓ only an logged user can access favorite index                                                                                                                                                             0.47s  

   PASS  Tests\Feature\GiphyControllerTest
  ✓ giphy search works properly                                                                                                                                                                               0.58s  
  ✓ giphy search required q field                                                                                                                                                                             0.53s  
  ✓ only an logged user can access giphy search                                                                                                                                                               0.52s  
  ✓ giphy gifs works properly                                                                                                                                                                                 0.53s  
  ✓ giphy gifs required ids field                                                                                                                                                                             0.53s  
  ✓ only an logged user can access giphy gifs                                                                                                                                                                 0.39s  

  Tests:    35 passed (165 assertions)
  Duration: 17.46s
```

#### Otras herramientas

El contenedor incluye `xdebug-3.3.1`, el cual puede ser accedido desde nuestro IDE

Código estandar: el paquete `friendsofphp/php-cs-fixer` instalado, permite monitorear nuestro estilo de codigo acorde a PSR12.
Tambien podemos conectar nuestro IDE a este paquete o ejecutarlo manualmente:

```bash
# Ingresando al contenedor
docker exec -it -u acoral  httpserverprex /bin/bash

# Ver los cambios a realizar
php vendor/bin/php-cs-fixer fix --dry-run --diff

# Aplicar cambios sobre nuestro codigo de manera automatica
php vendor/bin/php-cs-fixer fix
```

## API Description

La aplicación permite el registro de usuario y posterior entrega de tokens (JWT) a traves de Laravel Passport (Oauth2),
Los usuario con acceso valido, puede realizar búsquedas en la api de Giphy o solicitar informacion de un elemento en particular,
localmente pueden guardar elementos como favoritos y ver un listado de los mismos.

Todos los endpoints que reciben información tienen un clase del tipo FormRequest que valida la informacion, adicionalmente 
los endpoints requeridos estan protegidos por el middleware de autenticación en el archivo de rutas.

Para la conexión con Giphy se creo un Facade que llama a la clase que realmente hace la conexion, quedando disponible para 
todo la app via el service container

Finalmente, todas las peticiones y respuestas son almacenadas en la tabla `request_histories` mediante el
middleware `RequestHistory`, el cual lanza un event/listener, donde este ultimo hace el trabajo.

## Endpoints Documentation

### Register User

POST http://localhost/user/register

```json
{
    "name": "Pedro",
    "email": "pedro@sample.net.ar",
    "password": "secretpass"
}
```
Response
```json
{
    "success": true,
    "payload": {
        "token_type": "bearer",
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYzBiZjFiMjA5MjEyODgxMjFmZTYyNmI4ZTc2N2U4OWZhZTcyMzI3MTEwNWE5MjhhYzg1ZDUxZTA1ZjcwMTY4ZDkzZTYzZGNlZDhkYTYwZjUiLCJpYXQiOjE3MTIwOTg5MjEuMjQ4ODE5LCJuYmYiOjE3MTIwOTg5MjEuMjQ4ODIzLCJleHAiOjE3MTIxMDA3MjEuMjQyOTEzLCJzdWIiOiIzIiwic2NvcGVzIjpbXX0.V1xii2GSdJgwKpFkuN3-p30lrKMXmBbdGAnP9JZqZ7aQLM54H8Xd8uS6wgHmMcfV5ltJsqcsopvkJrNR-w_pg9cXUZsI4sYMD4vApXxdSCMAHwBJn5j1PxxqJyCjiO_Kh_nX31wKtxJnDlqs679euhGCHDOHJNZ0EVf9bK1Vg4nXABrOqhXTLo5LPJbkVPDAY4x4EVIgcU6o9WRbxZJqDGJkTm6PrNqGrzEPEP3aws8P5wyvG7GBRRXfQhVWdYMaqVc3fU7UEctvQ84UTUgGdZjeraVoeL40Uo8cdhDskjxVdJFuS3gB3pVDyCoHe8pKyV4Sppu0z204pS_52nI4JynS-JMW-bPxoVOlqYzBrTel1N2B205lSlIxp4wpLuWvS_HUFC0ojQyQEh3dqRWWSkHdrPfnlthf29qH75zFkMgDcDtRYFjuoJGEYV1m_-1uVrXE8wUIFa5QOYtchfC6cXgn8qhKm1Uw_k_ETPMEMLFdG01Bx2vP-jc0MSRjEg9fCIwwfHmAGcK_lYnOOdNs25ziCkuQMDQANiDFwTfk2d-FqRQ5HxsFcP1-mZHogAa6H7SZ4agenr0vpcoXY1idS6iLLIcciNOnamUv5wNUEm3vqK3IRHZJMWJT600QlKXQ9Ce4RCMciKIL-3vBQ6BOfza7mmIZFRNzYuySWxEVxSQ",
        "id": 3
    }
}
```

### Login User

POST http://localhost/user/logout

```json
{
    "email": "test@sample.net",
    "password": "test"
}
```
Response
```json
{
	"success": true,
	"payload": {
		"token_type": "bearer",
		"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOWI5ZmEyYTg5ODhiMzAwMjEwMDI4MzNlYTdjNGM4ZDgyODk3NWYxZjA0NTZkMjk2OTBkODA2NzM2NDQzYjUyZDIyNTU0NWUxYTU0NmFmMTIiLCJpYXQiOjE3MTIxNjcwNjQuMDgzMjc2LCJuYmYiOjE3MTIxNjcwNjQuMDgzMjg4LCJleHAiOjE3MTIxNjg4NjQuMDY5MTk3LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.v7nV1GpDzOa3B2R525CDrazVAPf_Xnia_rGd6MHmFzDOcvWO8EDZhojEz1I39MTB34HwfSlWwOM5kV9HufZYeuBwSwLEDwjwwcaisgI06k5f2YVbhwA54xr6NBPvg0Y4b7OjNmVT6xABKp54QzRsTR-cnfUWDdi2vMOaVqNJyOEEQHLIJu5bYB55sGx7725JZJMsza_OYg-AC8RPOhHidy_liQZ4YVLNeiwq8EE4KZZVNrehGABQV2qhWpyEtuyfcOn6Gkd8yeyQjkE6ywNGYX0AymgTi1GCPIXXNKq8tkaHFFNwHNp_TCnt-ADIeQVz_CD7NK5nLKrUuo9j-8HlisPjxAGz9C8LtfDdTt-3FkW2Rm3Gp4k1Uo3weTrr19vSoLbFQM3AYCH7Xah9spKvlkrxNZn2yWf6eD1vibIjse0Ca7oR9EcLHP0fGtEYfDHQJ1b7x5bA7WTeXbFeuidODwr8aypVGDUGCe0wB_OdodQw0iRUpqJJdOXV_ZBmNM35NRoS5XUWTfATVNKezG2h6_U7epRLM4PG756_pGg31M8mU9QvgLNhqn0zQ3n4dJk4WFQsT2X7iy_uqvNLlc5DgqRKsPiYSsuImrIsSo68XHqk3rgVzXUNJlZUiAqWswhYhW7xdSACRfApoFZChZtklGjrVyeLPcIeS1wQ7nt_dzA",
		"id": 1
	}
}
```
### Logout User

POST http://localhost/user/logout

Response
```json
{
	"success": true,
	"payload": "Logged out successfully"
}
```

### User Me

GET http://localhost/user/me
Response
```json
{
	"success": true,
	"payload": {
		"id": 1,
		"name": "test",
		"email": "test@sample.net",
		"email_verified_at": "2024-03-31T19:15:43.000000Z",
		"created_at": "2024-03-31T19:15:43.000000Z",
		"updated_at": "2024-03-31T19:15:43.000000Z"
	}
}
```
### Giphy search

POST http://localhost/giphy/search
```json
{
	"q": "avion",
	"limit": 5
}
```
Response (truncated) 
```json
{
	"success": true,
	"payload": {
		"data": [
			{
				"type": "gif",
				"id": "cMKiyssfSf1laPo1wK",
				"url": "https:\/\/giphy.com\/gifs\/SafranGroup-airplane-avion-aircraft-cMKiyssfSf1laPo1wK",
				"slug": "SafranGroup-airplane-avion-aircraft-cMKiyssfSf1laPo1wK",
				"bitly_gif_url": "https:\/\/gph.is\/g\/ZPqB6BQ",
				"bitly_url": "https:\/\/gph.is\/g\/ZPqB6BQ",
				"embed_url": "https:\/\/giphy.com\/embed\/cMKiyssfSf1laPo1wK",
				"username": "SafranGroup",
				"source": "",
				"title": "Airplane Landing GIF by Safran",
				"rating": "g",
				"content_url": "",
				"source_tld": "",
				"source_post_url": "",
				"is_sticker": 0,
				"import_datetime": "2020-11-23 15:11:40",
				"trending_datetime": "0000-00-00 00:00:00",
				"images": {
					"original": {
						"height": "270",
						"width": "480",
						"size": "2756385",
						"url": "https:\/\/media2.giphy.com\/media\/cMKiyssfSf1laPo1wK\/giphy.gif",
						"mp4_size": "553448",
						"mp4": "https:\/\/media2.giphy.com\/media\/cMKiyssfSf1laPo1wK\/giphy.mp4",
						"webp_size": "686616",
						"webp": "https:\/\/media2.giphy.com\/media\/cMKiyssfSf1laPo1wK\/giphy.webp",
						"frames": "61",
						"hash": "0a85f71e1011d74238b785d9b5401899"
					},
					"downsized": {
						"height": "270",
						"width": "480",
						"size": "1530901",
						"url": "https:\/\/media2.giphy.com\/media\/cMKiyssfSf1laPo1wK\/giphy-downsized.gif"
					}
				},
				"user": {
					"avatar_url": "https:\/\/media2.giphy.com\/avatars\/SafranGroup\/mI9EaLJa61Ko.jpg",
					"banner_image": "",
					"banner_url": "",
					"profile_url": "https:\/\/giphy.com\/SafranGroup\/",
					"username": "SafranGroup",
					"display_name": "Safran",
					"description": "Discover how we work on inventing tomorrow’s sky through our GIFs #PoweredByTrust",
					"instagram_url": "https:\/\/instagram.com\/safran_group",
					"website_url": "http:\/\/www.safran-group.com\/",
					"is_verified": true
				},
				"analytics_response_payload": "e=Z2lmX2lkPWNNS2l5c3NmU2YxbGFQbzF3SyZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n",
				"analytics": {
					"onload": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPWNNS2l5c3NmU2YxbGFQbzF3SyZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=SEEN"
					},
					"onclick": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPWNNS2l5c3NmU2YxbGFQbzF3SyZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=CLICK"
					},
					"onsent": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPWNNS2l5c3NmU2YxbGFQbzF3SyZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=SENT"
					}
				},
				"alt_text": ""
			},
			{
				"type": "gif",
				"id": "ANiJpi8cLRlZ3FhPwC",
				"url": "https:\/\/giphy.com\/gifs\/SafranGroup-ANiJpi8cLRlZ3FhPwC",
				"slug": "SafranGroup-ANiJpi8cLRlZ3FhPwC",
				"bitly_gif_url": "https:\/\/gph.is\/g\/aNxn7jJ",
				"bitly_url": "https:\/\/gph.is\/g\/aNxn7jJ",
				"embed_url": "https:\/\/giphy.com\/embed\/ANiJpi8cLRlZ3FhPwC",
				"username": "SafranGroup",
				"source": "",
				"title": "Plane Airplane GIF by Safran",
				"rating": "g",
				"content_url": "",
				"source_tld": "",
				"source_post_url": "",
				"is_sticker": 0,
				"import_datetime": "2021-03-23 15:35:45",
				"trending_datetime": "0000-00-00 00:00:00",
				"images": {
					"original": {
						"height": "270",
						"width": "480",
						"size": "1811853",
						"url": "https:\/\/media1.giphy.com\/media\/ANiJpi8cLRlZ3FhPwC\/giphy.gif",
						"mp4_size": "476823",
						"mp4": "https:\/\/media1.giphy.com\/media\/ANiJpi8cLRlZ3FhPwC\/giphy.mp4",
						"webp_size": "485756",
						"webp": "https:\/\/media1.giphy.com\/media\/ANiJpi8cLRlZ3FhPwC\/giphy.webp",
						"frames": "56",
						"hash": "85dc1a95544f1a6c06a54cc81e7f6eac"
					},
					"downsized": {
						"height": "270",
						"width": "480",
						"size": "1811853",
						"url": "https:\/\/media1.giphy.com\/media\/ANiJpi8cLRlZ3FhPwC\/giphy.gif"
					}
				},
				"user": {
					"avatar_url": "https:\/\/media2.giphy.com\/avatars\/SafranGroup\/mI9EaLJa61Ko.jpg",
					"banner_image": "",
					"banner_url": "",
					"profile_url": "https:\/\/giphy.com\/SafranGroup\/",
					"username": "SafranGroup",
					"display_name": "Safran",
					"description": "Discover how we work on inventing tomorrow’s sky through our GIFs #PoweredByTrust",
					"instagram_url": "https:\/\/instagram.com\/safran_group",
					"website_url": "http:\/\/www.safran-group.com\/",
					"is_verified": true
				},
				"analytics_response_payload": "e=Z2lmX2lkPUFOaUpwaThjTFJsWjNGaFB3QyZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n",
				"analytics": {
					"onload": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPUFOaUpwaThjTFJsWjNGaFB3QyZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=SEEN"
					},
					"onclick": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPUFOaUpwaThjTFJsWjNGaFB3QyZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=CLICK"
					},
					"onsent": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPUFOaUpwaThjTFJsWjNGaFB3QyZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=SENT"
					}
				},
				"alt_text": ""
			},
			{
				"type": "gif",
				"id": "ug1EUFXepD5FaJBlJR",
				"url": "https:\/\/giphy.com\/gifs\/rvappstudios-safe-travels-flight-travel-ug1EUFXepD5FaJBlJR",
				"slug": "rvappstudios-safe-travels-flight-travel-ug1EUFXepD5FaJBlJR",
				"bitly_gif_url": "https:\/\/gph.is\/g\/4LwARd3",
				"bitly_url": "https:\/\/gph.is\/g\/4LwARd3",
				"embed_url": "https:\/\/giphy.com\/embed\/ug1EUFXepD5FaJBlJR",
				"username": "rvappstudios",
				"source": "https:\/\/www.rvappstudios.com\/",
				"title": "Get There Safe Bon Voyage GIF by Lucas and Friends by RV AppStudios",
				"rating": "g",
				"content_url": "",
				"source_tld": "www.rvappstudios.com",
				"source_post_url": "https:\/\/www.rvappstudios.com\/",
				"is_sticker": 0,
				"import_datetime": "2024-01-10 14:53:31",
				"trending_datetime": "0000-00-00 00:00:00",
				"images": {
					"original": {
						"height": "480",
						"width": "480",
						"size": "1404836",
						"url": "https:\/\/media4.giphy.com\/media\/ug1EUFXepD5FaJBlJR\/giphy.gif",
						"mp4_size": "206023",
						"mp4": "https:\/\/media4.giphy.com\/media\/ug1EUFXepD5FaJBlJR\/giphy.mp4",
						"webp_size": "356134",
						"webp": "https:\/\/media4.giphy.com\/media\/ug1EUFXepD5FaJBlJR\/giphy.webp",
						"frames": "60",
						"hash": "9a8b551188d8fb0a8024f1acadbdd63c"
					},
					"downsized": {
						"height": "480",
						"width": "480",
						"size": "1404836",
						"url": "https:\/\/media4.giphy.com\/media\/ug1EUFXepD5FaJBlJR\/giphy.gif"
					}
				},
				"user": {
					"avatar_url": "https:\/\/media2.giphy.com\/avatars\/rvappstudios\/0ax9ZCLRtVSK.jpg",
					"banner_image": "https:\/\/media2.giphy.com\/channel_assets\/rvappstudios\/OO67KPR3kRBn.jpg",
					"banner_url": "https:\/\/media2.giphy.com\/channel_assets\/rvappstudios\/OO67KPR3kRBn.jpg",
					"profile_url": "https:\/\/giphy.com\/rvappstudios\/",
					"username": "rvappstudios",
					"display_name": "Lucas and Friends by RV AppStudios",
					"description": "Lucas & Friends By RV AppStudios created free educational apps, nursery rhymes and kids songs on YouTube, children's books, and more. Over 30 million kids use our educational apps every month for free!",
					"instagram_url": "https:\/\/instagram.com\/rvappstudios",
					"website_url": "http:\/\/www.rvappstudios.com",
					"is_verified": true
				},
				"analytics_response_payload": "e=Z2lmX2lkPXVnMUVVRlhlcEQ1RmFKQmxKUiZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n",
				"analytics": {
					"onload": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPXVnMUVVRlhlcEQ1RmFKQmxKUiZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=SEEN"
					},
					"onclick": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPXVnMUVVRlhlcEQ1RmFKQmxKUiZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=CLICK"
					},
					"onsent": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPXVnMUVVRlhlcEQ1RmFKQmxKUiZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=SENT"
					}
				},
				"alt_text": ""
			},
			{
				"type": "gif",
				"id": "Btn42lfKKrOzS",
				"url": "https:\/\/giphy.com\/gifs\/plane-airplane-Btn42lfKKrOzS",
				"slug": "plane-airplane-Btn42lfKKrOzS",
				"bitly_gif_url": "http:\/\/gph.is\/1aEMNyd",
				"bitly_url": "http:\/\/gph.is\/1aEMNyd",
				"embed_url": "https:\/\/giphy.com\/embed\/Btn42lfKKrOzS",
				"username": "",
				"source": "http:\/\/www.reddit.com\/r\/gifs\/comments\/1qjqsa\/rich_peoples_stroller\/",
				"title": "Plane Airplane GIF",
				"rating": "g",
				"content_url": "",
				"source_tld": "www.reddit.com",
				"source_post_url": "http:\/\/www.reddit.com\/r\/gifs\/comments\/1qjqsa\/rich_peoples_stroller\/",
				"is_sticker": 0,
				"import_datetime": "2013-11-13 18:32:30",
				"trending_datetime": "1970-01-01 00:00:00",
				"images": {
					"original": {
						"height": "240",
						"width": "320",
						"size": "2425072",
						"url": "https:\/\/media2.giphy.com\/media\/Btn42lfKKrOzS\/giphy.gif",
						"mp4_size": "3284744",
						"mp4": "https:\/\/media2.giphy.com\/media\/Btn42lfKKrOzS\/giphy.mp4",
						"webp_size": "2066742",
						"webp": "https:\/\/media2.giphy.com\/media\/Btn42lfKKrOzS\/giphy.webp",
						"frames": "175",
						"hash": "e8fb3a3d8e1c4f320435471edbcbd94c"
					},
					"downsized": {
						"height": "240",
						"width": "320",
						"size": "1015003",
						"url": "https:\/\/media2.giphy.com\/media\/Btn42lfKKrOzS\/giphy-downsized.gif"
					}
				},
				"analytics_response_payload": "e=Z2lmX2lkPUJ0bjQybGZLS3JPelMmZXZlbnRfdHlwZT1HSUZfU0VBUkNIJmNpZD0mY3Q9Zw",
				"analytics": {
					"onload": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPUJ0bjQybGZLS3JPelMmZXZlbnRfdHlwZT1HSUZfU0VBUkNIJmNpZD0mY3Q9Zw&action_type=SEEN"
					},
					"onclick": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPUJ0bjQybGZLS3JPelMmZXZlbnRfdHlwZT1HSUZfU0VBUkNIJmNpZD0mY3Q9Zw&action_type=CLICK"
					},
					"onsent": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPUJ0bjQybGZLS3JPelMmZXZlbnRfdHlwZT1HSUZfU0VBUkNIJmNpZD0mY3Q9Zw&action_type=SENT"
					}
				},
				"alt_text": ""
			},
			{
				"type": "gif",
				"id": "VeHu18Pu9u4QI3VCZD",
				"url": "https:\/\/giphy.com\/gifs\/VeHu18Pu9u4QI3VCZD",
				"slug": "VeHu18Pu9u4QI3VCZD",
				"bitly_gif_url": "https:\/\/gph.is\/g\/a9nyzDJ",
				"bitly_url": "https:\/\/gph.is\/g\/a9nyzDJ",
				"embed_url": "https:\/\/giphy.com\/embed\/VeHu18Pu9u4QI3VCZD",
				"username": "CasaDeBalneario",
				"source": "",
				"title": "Plane Naves GIF by Casa De Balneario",
				"rating": "g",
				"content_url": "",
				"source_tld": "",
				"source_post_url": "",
				"is_sticker": 0,
				"import_datetime": "2020-06-19 15:12:06",
				"trending_datetime": "0000-00-00 00:00:00",
				"images": {
					"original": {
						"height": "480",
						"width": "381",
						"size": "226999",
						"url": "https:\/\/media4.giphy.com\/media\/VeHu18Pu9u4QI3VCZD\/giphy.gif",
						"mp4_size": "134363",
						"mp4": "https:\/\/media4.giphy.com\/media\/VeHu18Pu9u4QI3VCZD\/giphy.mp4",
						"webp_size": "115608",
						"webp": "https:\/\/media4.giphy.com\/media\/VeHu18Pu9u4QI3VCZD\/giphy.webp",
						"frames": "11",
						"hash": "8bdf5cf8e55b95392853901477217ec7"
					},
					"downsized": {
						"height": "480",
						"width": "381",
						"size": "226999",
						"url": "https:\/\/media4.giphy.com\/media\/VeHu18Pu9u4QI3VCZD\/giphy.gif"
					}			
				},
				"user": {
					"avatar_url": "https:\/\/media2.giphy.com\/avatars\/CasaDeBalneario\/svxokn03EWOG.gif",
					"banner_image": "",
					"banner_url": "",
					"profile_url": "https:\/\/giphy.com\/CasaDeBalneario\/",
					"username": "CasaDeBalneario",
					"display_name": "Casa De Balneario",
					"description": "We love gif!!",
					"instagram_url": "https:\/\/instagram.com\/Casa De Balneario",
					"website_url": "https:\/\/www.instagram.com\/casadebalneario\/",
					"is_verified": true
				},
				"analytics_response_payload": "e=Z2lmX2lkPVZlSHUxOFB1OXU0UUkzVkNaRCZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n",
				"analytics": {
					"onload": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPVZlSHUxOFB1OXU0UUkzVkNaRCZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=SEEN"
					},
					"onclick": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPVZlSHUxOFB1OXU0UUkzVkNaRCZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=CLICK"
					},
					"onsent": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPVZlSHUxOFB1OXU0UUkzVkNaRCZldmVudF90eXBlPUdJRl9TRUFSQ0gmY2lkPSZjdD1n&action_type=SENT"
					}
				},
				"alt_text": ""
			}
		],
		"meta": {
			"status": 200,
			"msg": "OK",
			"response_id": "keqbcagy7nerm5dyinn62fkmmni5uzb8xp69dx0y"
		},
		"pagination": {
			"total_count": 3595,
			"count": 5,
			"offset": 0
		}
	}
}
```
### Giphy gif

POST http://localhost/giphy/gifs

```json
{
	"ids": "L1cdLhPrp9wAL1CbQU"
}
```
Response (truncated)
```json
{
	"success": true,
	"payload": {
		"data": [
			{
				"type": "gif",
				"id": "MDJ9IbxxvDUQM",
				"url": "https:\/\/giphy.com\/gifs\/cat-kisses-hugs-MDJ9IbxxvDUQM",
				"slug": "cat-kisses-hugs-MDJ9IbxxvDUQM",
				"bitly_gif_url": "http:\/\/gph.is\/1Wv2LH6",
				"bitly_url": "http:\/\/gph.is\/1Wv2LH6",
				"embed_url": "https:\/\/giphy.com\/embed\/MDJ9IbxxvDUQM",
				"username": "",
				"source": "https:\/\/www.reddit.com\/r\/CatGifs\/comments\/4gzcp5\/hugs_and_kisses\/",
				"title": "In Love Cat GIF",
				"rating": "g",
				"content_url": "",
				"source_tld": "www.reddit.com",
				"source_post_url": "https:\/\/www.reddit.com\/r\/CatGifs\/comments\/4gzcp5\/hugs_and_kisses\/",
				"is_sticker": 0,
				"import_datetime": "2016-04-29 13:33:06",
				"trending_datetime": "2020-09-09 11:00:05",
				"images": {
					"original": {
						"height": "225",
						"width": "400",
						"size": "1404005",
						"url": "https:\/\/media4.giphy.com\/media\/MDJ9IbxxvDUQM\/giphy.gif",
						"mp4_size": "470729",
						"mp4": "https:\/\/media4.giphy.com\/media\/MDJ9IbxxvDUQM\/giphy.mp4",
						"webp_size": "360322",
						"webp": "https:\/\/media4.giphy.com\/media\/MDJ9IbxxvDUQM\/giphy.webp",
						"frames": "43",
						"hash": "c33553a6442d05f4eeea7b000e9d4245"
					},
					"downsized": {
						"height": "225",
						"width": "400",
						"size": "1404005",
						"url": "https:\/\/media4.giphy.com\/media\/MDJ9IbxxvDUQM\/giphy.gif"
					}
				},
				"analytics_response_payload": "e=Z2lmX2lkPU1ESjlJYnh4dkRVUU0mZXZlbnRfdHlwZT1HSUZfQllfSUQmY2lkPSZjdD1n",
				"analytics": {
					"onload": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPU1ESjlJYnh4dkRVUU0mZXZlbnRfdHlwZT1HSUZfQllfSUQmY2lkPSZjdD1n&action_type=SEEN"
					},
					"onclick": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPU1ESjlJYnh4dkRVUU0mZXZlbnRfdHlwZT1HSUZfQllfSUQmY2lkPSZjdD1n&action_type=CLICK"
					},
					"onsent": {
						"url": "https:\/\/giphy-analytics.giphy.com\/v2\/pingback_simple?analytics_response_payload=e%3DZ2lmX2lkPU1ESjlJYnh4dkRVUU0mZXZlbnRfdHlwZT1HSUZfQllfSUQmY2lkPSZjdD1n&action_type=SENT"
					}
				},
				"alt_text": "Video gif. A tabby cat looking into and reaching up toward a fisheye camera, as its face presses up to the lens."
			}
		],
		"meta": {
			"status": 200,
			"msg": "OK",
			"response_id": "0j6u8qh2roajz0pc2jt8ijmfcgbutynbmmmt3jkx"
		},
		"pagination": {
			"total_count": 2,
			"count": 2,
			"offset": 0
		}
	}
}
```

### Add favorite gif

POST http://localhost/favorite/add

```json
{
    "gif_id": "MDJ9IbxxvDUQM",
    "alias": "gato fav"
}
```
Response
```json
{
    "success": true,
    "payload": {
        "alias": "gato fav",
        "gif_id": "MDJ9IbxxvDUQM",
        "user_id": 1,
        "updated_at": "2024-04-02T23:00:13.000000Z",
        "created_at": "2024-04-02T23:00:13.000000Z",
        "id": 3
    }
}
```

### Add favorite gif

GET http://localhost/favorite/index

Response
```json
{
    "success": true,
    "payload": [
        {
            "id": 1,
            "user_id": 1,
            "gif_id": "Wds8J0sb4fnKo",
            "alias": "nombre del fav",
            "created_at": "2024-04-02T20:11:03.000000Z",
            "updated_at": "2024-04-02T20:11:03.000000Z"
        },
        {
            "id": 2,
            "user_id": 1,
            "gif_id": "Fu3OjBQiCs3s0ZuLY3",
            "alias": "perros fav",
            "created_at": "2024-04-02T20:15:25.000000Z",
            "updated_at": "2024-04-02T20:15:25.000000Z"
        },
        {
            "id": 3,
            "user_id": 1,
            "gif_id": "MDJ9IbxxvDUQM",
            "alias": "gato fav",
            "created_at": "2024-04-02T23:00:13.000000Z",
            "updated_at": "2024-04-02T23:00:13.000000Z"
        }
    ]
}
```
