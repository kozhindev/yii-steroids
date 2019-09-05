# Структура приложения

Чтобы разработка и поддержка приложений были простыми, структура приложения должна быть идентичная от проекта к проекту.
Мы выработали наиболее подходящую для нас структуру проекта, которой необходимо придерживаться для полноценного
использования Yii Steroids.

## Иерархия файлов

Структура не похожа на Yii Basic и Yii Advanced шаблоны. В основе лежит правило, что в все приложение разделено на модули.
Невозможно создать контроллер/модель/представление вне модуля.

Весь код лежит в папке `app`, где каждая папка - это модуль (за исключением папки `config`).

```
app/ - тут только модули + config
	config/
		env/
			development.php
			stage.php
			production.php
		main.php
		web.php
		console.php
	core/ - макеты, базовые и полезные вещи. Не пихаем сюда controllers и views!
		base/ - базовые классы
			FakeApplication.php - для phpdoc
			Yii.php
		components/ - компоненты приложения, которые используют все модули
			ContextUser.php
		exceptions/
			AppException.php
			NotFoundException.php
        layouts/
            main.php - макет
		validators/ - валидаторы, которые можно переиспользовать в разных модулях
			StateFlowValidator.php
	site/ - пример модуля, все содержимое - специфичное для проекта.
		commands
			SiteCommand.php - не *Controller, а *Command!
		controllers/
			admin/ - админку держим в подмодулях
				controllers/
			SiteAdminController.php
			SiteController.php - никаких Default* контроллеров!
		enums/
		exceptions/
		forms/ - ModelForm
		mail/ - вьюшки для писем
		models/ - ActiveRecord и модели из MVC
		migrations/ - миграции модуля
		validators/
		views/
		widgets/
			MyWidget - React-виджет. Билдится автоматически вебпаком
				MyWidget.php
				MyWidget.js
				MyWidget.scss
		SiteModule.php - класс модуля, всегда содержит имя модуля
dev/ - различных скрипты, файлы, примеры для разработчиков
files/ - файлы, динамически добавляющиеся в приложения (логи, upload, runtime, tmp…), их нет в git
	log/
	public/ - файлы, доступные напрямую через http
		avatars/
		upload/
node_modules/ - npm пакеты
public/ - webroot для apache/nginx
	assets/ - css и js файлы, сгенерированные через вебпак, исключены из git
		bundle.*.js
		bundle.*.css
	fonts/
	images/
	.htaccess
	favicon.ico
	index.php - входная точка для доступа в приложение через браузер
	robots.txt
tests/ - тесты приложения
	unit/
vendor/ - composer пакеты
.gitignore
config.php - кастомная конфигурация сервера или разработчика, исключен из гита
config.sample.php - пример кастомной конфигурации
config.js - по аналогии с config.php
config.sample.js - по аналогии с config.sample.php
webpack.js - универсальная входная точка для запуска вебпака
yii - входная точка для доступа в приложение через командную строку
```

## Конфигурация

Папка `config` лежит в `app` для того, чтобы она включалась в поиск по исходникам, когда нужно что-то найти, потому
что это тоже часть исходного кода. Конфигурация приложения собирается из нескольких файлов (массивов) и рекурсивно
мержится.

Под кастомными конфигами проекта понимаются конфиги (php файлы с массивом), лежищие в корне проекта и исключенные из
git репозитория. Сделано это для того, чтобы в них определялись настройки, специфичные для конкретной машины, на которой
запускается проекта (машины разработчиков, сервера), а так же стоит прописывать в них пароли и ключи (чтобы не хранить в git'е).

В общем виде мерж происходит в таком порядке (чем ниже конфиг, тем он приоритетнее значения в нем):

```php
$config = ArrayHelper::merge(
    "app/config/main.php",
    "app/config/web.php", // или console.php для консольного приложения
    "app/config/env/$env.php", // $env указывается в кастомном config.php
    "config.php",
);
```

## Модуль `core`

Не смотря на то, что вне модулей запрещено размещать контроллеры, модели и представления, необходимость в общих компонентах
все равно может быть. Это могут быть компоненты, валидаторы, поведения, трейты, макеты и так далее. Все то, что может использоваться
в нескольких модулях - можно и нужно размещать в модуле `core`.

Зачастую такие компоненты переносятся потом на уровень библиотеки и используются в разных проектах.

## Папка `files`

Обычно на серверах эта папка выносится из проекта и хранится отдельно от исходников. В ней будут все файлы, создаваемые
в процессе работы приложения (логи, временные, загруженные файлы и т.п.). Доступ к файлам в этой папке осуществялется
отдельными правилами apache/nginx.