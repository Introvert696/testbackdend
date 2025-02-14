# Readme
Для запуска проекта в докере нужно:
## 1. Создать файл .env
Его можно создать из файла .example.env, просто клонируем и переименовываем в .env
## 2. Запускаем docker compose
```shell
docker-compose up --build -d
```
В данном случае у нас поднимаются все необходимые контейнеры и проект работает

## P.S Для заполнения тестовыми данными
Нужно в env файле прописать APP_ENV - dev , т.е. режим разработки и войти в в контейнер **php** .
1. Прописываем в .env и количество данных
```dotenv
APP_ENV=prod
FIXTURE_RATE=300
```
2. Входим в контейнер **php**
```shell
docker exec -it php /bin/bash
```
3. Выполняем загрузку тестовых данных
```shell
bin/console doctrine:fixtures:load --no-interaction
```
4. После переходим опять в режим прода
```dotenv
APP_ENV=prod
```