# Библиотека создания консольных команд
## Как запустить
— загрузите в БД файл
```sh
db\lvnd.sql
```
— создайте конфиг main-local.php
```sh
cp config\main.php config\main-local.php
```
— настройте в config\main.php параметры подключения к БД
— запустите скрипт через консоль
```sh
php public\index.php [параметры скрипта]
```

## Регистрация команд (класс CommandMapper)
— Команды сохраняю в БД
— Из можно регистрировать столько, сколько позволяет свободное место на сервере =)

## Установка названия и описание для каждой команды (класс Command)
— Название можно установить через метод команды setName
— Описание команды — это ее аргументы и опции. Из можно установить через методы setArguments и setOptions, а также можно добавить новую опцию к имеющимся через addOption

## Обработка пользователя (класс Parser)
— Ввод пользователя обрабатываю статическим методом readCommand
—— Если скрипт запущен без параметров — вывожу из БД все зарегистрированные команды или ошибку, если команд нет
—— Если скрипт запущен с названием команды и единственным параметром {help} — вывожу описание команды или ошибку, если команды нет или если указаны дополнительные параметры
—— Если указано название команды и ее параметры — то регистрирую команду. Выдаю ошибки, если команда с таким названием есть или если возникли ошибки при парсинге аргументов или опций команды

## Выполнение заданной логики с возможностью вывода информации в консоль
— Логика выполняется в классах Command, Parser и CommandMapper
— Протестировать логику можно через консоль (это точка входа):
```sh
php public\index.php
```	 
## Что использовал
— PHP 8.0 без фреймворков
— MySQL для регистрации команд

## Тесты
— Выдаст ошибку, потому что нет команд в БД
```sh
php public\index.php
```

— Выдаст ошибку, потому что такая команда не зарегистрирована
```sh
php public\index.php command_name {help}
```

— Зарегистрирует и выведет команду
```sh
php public\index.php command_name {verbose,overwrite} [log_file=app.log] {unlimited} [methods={create,update,delete}] [paginate=50] {log}
```

— Выведет ошибку, потому что нельзя использовать {help} с другими параметрами
```sh
php public\index.php command_name {verbose,overwrite} [log_file=app.log] {unlimited} {help} [methods={create,update,delete}] [paginate=50] {log}
```

— Выведет описание команды command_name
```sh
php public\index.php command_name {help}
```

— Зарегистрирует и выведет команду без аргументов
```sh
php public\index.php command_name_1 [log_file=app.log] [methods={create,update,delete}] [paginate=50]
```

— Выведет ошибку, потому что {help} используется без названия команды
```sh
php public\index.php {help}
```

— Зарегистрирует и выведет команду без опций
```sh
php public\index.php command_name_2 {verbose,overwrite} {unlimited} {log}
```

— Выведет ошибки при парсинге опций
```sh
php public\index.php command_name_3 [log_file=]
php public\index.php command_name_3 [log_file]
php public\index.php command_name_3 [=log_file]
php public\index.php command_name_3 [log_file=,]
php public\index.php command_name_3 [log_file={}]
php public\index.php command_name_3 [log_file={,}]
php public\index.php command_name_3 [{log_file}={create,update,delete}]
php public\index.php command_name_3 [log_file=[create,update,delete]]
```

— Выведет список всех зарегистрированных команд
```sh
php public\index.php
```
