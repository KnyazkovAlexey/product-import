<p align="center">
    <h1 align="center">Тестовое задание</h1>
    <br>
</p>

<pre>
Написать консольную команду, которая:
- скачивает файл по переданному параметру (например https://example.com/products.xml)
- парсит xml (xml файл в приложении)
- сохраняет продукты из файла в базе данных. Список полей: product_id, title, description,
rating, price, inet_price, image.
- необходимо запретить запуск команды в несколько экземпляров. Если команда определила,
что запущена другая копия этой команды, нужно завершить текущий процесс с кодом 0.

Рекомендуется реализовать на фреймворке Symfony
Написать Readme файл.
Результат выложить на github (bitbucket) или прислать архивом с комментариями.
</pre>

<h2>Развёртывание и запуск</h2>
<pre>
docker-compose up -d
docker exec -it AppSymfony composer install
docker exec -it AppSymfony bin/console doctrine:database:create
docker exec -it AppSymfony bin/console doctrine:migrations:migrate
docker exec -it AppSymfony bin/console app:import-products http://NginxSymfony/products.xml
</pre>

<h2>Замечания</h2>
Не всё успел.<br>
Накидал todo там, где можно улучшить (валидация файла, парсинг большого файла по частям, обработка ошибок, убрать пароли из гита, ...).<br>

