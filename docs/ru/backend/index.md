# Yii Steroids (Бекенд часть)

Бекенд часть библиотеки представляет из себя набор базовых классов, валидаторов, поведений, модулей и других компонентов,
расширяющих базовый функционал PHP фреймворка [Yii](https://www.yiiframework.com/) версии 2.

Каждый новый функционал или компонент создается с максимальным приближением к идеологии Yii 2. Тем не менее, сейчас
набор компонентов уже настолько велик, что без отдельной документации для Стероидов не обойтись.

## Обзор

- [Структура приложения](application_structure.md)
- [Инициализация приложения](bootstrap.md)
- [Права доступа](permissions.md)
    - [Методы `can...()` в моделях](permissions_model.md)
- Model
    - Form Model
        - Вложенные модели
    - Search Model
    - Model (ActiveRecord)
    - Meta information (MetaTrait)
    - Nested load, validate and save data (RelationSaveTrait)
- Enum
- Schema
- Types
- Site map
- Module
- Validators
- Documentation (swagger)
- Modules
    - Gii
    - File
    - Docs
