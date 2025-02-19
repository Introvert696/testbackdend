# Readme
Для запуска проекта в докере нужно:
## 1. Запускаем docker compose
```shell
docker-compose up --build -d
```
В данном случае у нас поднимаются все необходимые контейнеры и проект работает
## 2. Для проверки доступных роутов, нужно перейти по адресу
```shell
http://localhost:8080/api/doc
```
## P.S Для заполнения тестовыми данными
1. Заходим в контейнер 
```shell
docker exec -it php sh
```
2. Выполняем команду
```shell
php bin/console doctrine:fixtures:load --env=dev --no-interaction
```


# FAQ
## Ошибка - "502 Bad Gateway nginx/1.27.4"
Проверьте, создали ли вы файл .env