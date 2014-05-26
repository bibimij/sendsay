SDK для работы с API Sendsay (бывший Subscribe.PRO).
Библиотека писалась исключительно для покрытия рабочих необходимостей, поэтому некоторые методы могут отсутствовать.
Отсутствующие методы будут постепенно добавляться.

## Quick start

```php
include 'sendsay.php';

// Классческий способ создания экземпляра класса
$ss = new Sendsay('yourlogin','yoursublogin','yourpassword');

// Краткий способ создания класса
$ss = Sendsay('yourlogin','yoursublogin','yourpassword');

// Краткий способ так же подходит для однострочных вызовов
Sendsay('yourlogin','yoursublogin','yourpassword')->member('email@test.com');
```

## Roadmap

* дописать недостающие методы
* привести названия методов к какому-то общему виду (надо ли?)
* вынести методы в подклассы по группам (модульность)

## Links

- [Документация](https://pro.subscribe.ru/API/API.html) Sendsay API
- [Github](https://github.com/bibimij/sendsay)