<?php
define('ERROR_404_MESSAGE','<div class="error-div well well-large">
							<p class="text-error">Ошибка в запросе.</p>
							<p class="text-error">Страница с адресом http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].' не существует или была перемещена.</p>
							<p class="text-error">Обратитесь к администратору за подробностями.</p>
							</div>');
define('ERROR_500_MESSAGE','<div class="error-div well well-large">
							<p class="text-error">Ой! У нас случилась ошибка.</p>
							<p class="text-error">Но не переживайте администратору было отправлено автоматическое уведомление об этом.</p>
							<p class="text-error">Возможно ошибка незначительна и он уже работает над её исправлением.</p>
							</div>');
define('REDIRECT_MESSAGE','<p>Вы будете переадресованы на главную страницу через <span class="time">5</span> секунд. <a href='.SUB_DIR.'">Перейти сразу</a></p>');
define('REGISTRATION_EMAIL_TITLE','Email');
define('REGISTRATION_EMAIL_ERROR','Ошибка при заполнении мыла');
define('REGISTRATION_NAME_TITLE','Имя');
define('REGISTRATION_NAME_ERROR','Ошибка при заполнении имени');
define('REGISTRATION_PASSWORD_TITLE','Пароль');
define('REGISTRATION_PASSWORD_ERROR','Ошибка при заполнении пароля');
define('REGISTRATION_AGREEMENT_TITLE','Согласен на <a href="'.SUB_DIR.'user/eula">все</a>');
define('REGISTRATION_AGREEMENT_ERROR','Ошибка при акцептировании условий');
define('REGISTRATION_BUTTON_TITLE','Регистрация');
define('LOGIN_EMAIL_TITLE','Email');
define('LOGIN_PASSWORD_TITLE','Пароль');
define('LOGIN_ERROR','Ошибка при вводе связки логина/пароля');
define('FOGOTTEN_PASSWORD','Забыли пароль?');
define('LOGIN_REMEMBER_TITLE','Remember me');
define('LOGIN_BUTTON_TITLE','Войти');
define('LOGOUT_BUTTON_TITLE','Выйти');
define('SAVE_BUTTON_TITLE','Сохранить');
define('ADD_BUTTON_TITLE','Добавить');
define('BACK_BUTTON_TITLE','Назад');
define('YES','Да');
define('NO','Нет');
define('ERROR_ACCESS_DENY','Доступ запрещен');
define('ERROR_READING_CONFIG','Ошибка чтения конфигурационного файла');
define('ERROR_CONECTING_TO_DB','Ошибка соединения с базой данных');
define('ERROR_STANDART_MESSAGE','Ой! У нас проблема...');
define('ERROR_IN_ERROR_CATCHER','Параметр, переданный от '.__METHOD__.' должен быть числом');
define('ERROR_SHOULD_BE_STRING','Сообщение об ошибке должно быть строкой');
define('ERROR_IN_PATH','Указан неверный путь для перенаправлении');
define('ERROR_SESSION_SECTION','Неверно указан путь к секции сессии');
define('ERROR_SESSION_SUBSECTION','Неверно указан путь к под-секции сессии');
define('ERROR_SESSION_VALUE','Неверно указано значение секции сессии');
define('ERROR_SESSION_USER','Неверно указан параметр пользователя в сессии');
define('ERROR_SESSION_MESSAGE','Отсутствует сообщение об ошибке');
define('ERROR_SQL_INSERT','Ошибкка вставки данных в таблицу ');
define('ERROR_SQL_UPDATE','Ошибкка обновления данных в таблице ');
define('ERROR_SQL_SELECT','Ошибкка выборки данных из таблицы ');
define('ERROR_VIEW_URL','Неверный URL. Ошибка вызова метода ');
define('ADMIN_MENU_TITLE_CONFIG','Конфигурация');
define('ADMIN_MENU_TITLE_MENU','Меню');
define('ADMIN_MENU_TITLE_ACL','Контроль доступа');
define('ADMIN_MENU_TITLE_USERS','Пользователи');
define('ADMIN_MENU_TITLE_PAGES','Страницы');
define('ADMIN_MENU_TITLE_SHOP','Магазин');
define('ADMIN_MENU_TITLE_PRODUCTS','Товары');
define('ADMIN_PRODUCT_TITLE_GENERAL','Главное');
define('ADMIN_PRODUCT_TITLE_PARAMS','Параметры');
define('ADMIN_PRODUCT_TITLE_IMAGES','Изображения');
define('ADMIN_PRODUCT_TITLE_VISIBLE','Видимый');
define('ADMIN_PRODUCT_TITLE_NAME','Название');
define('ADMIN_PRODUCT_TITLE_MANUFACTURER','Производитель');
define('ADMIN_PRODUCT_TITLE_CATEGORIES','Категории');
define('ADMIN_PRODUCT_TITLE_PRICE','Цена');
define('ADMIN_PRODUCT_TITLE_AMOUNT','Количество');
define('ADMIN_PRODUCT_TITLE_SHORT_DESCRIPTION','Короткое описание');
define('ADMIN_PRODUCT_TITLE_DESCRIPTION','Описание');
define('MISSING_PRODUCT_DESCRIPTION','Отсутствует описание для товара');
define('ADMIN_PRODUCT_TITLE_MANUFACTURERS','Производители');
define('ADMIN_PRODUCT_TITLE_COUNTRIES','Страны');
define('ADMIN_PRODUCT_TITLE_REGIONS','Области');
define('ADMIN_PRODUCT_TITLE_CITIES','Города');
define('ERROR_MODEL_UNNOWN_CLASS','Имя класса не было передано');
define('ERROR_WRONG_URI','Неверный URI');
define('ERROR_FORM_SENDED','Неверный URL. Форма было отправлена с другого сайта');
define('ADMIN_USER_TITLE_GENERAL','Главное');
define('ADMIN_USER_TITLE_ORDERS','Заказы');
define('ADMIN_USER_TITLE_IMAGES','Изображения');
define('ADMIN_USER_TITLE_VISIBLE','Видимый');
define('ADMIN_USER_TITLE_ADMIN','Админ');
define('ADMIN_USER_TITLE_FIRST_NAME','Имя');
define('ADMIN_USER_TITLE_LAST_NAME','Фамилия');
define('ADMIN_USER_TITLE_EMAIL','Email');
define('ADMIN_USER_TITLE_PHONE','Телефон');
define('ADMIN_USER_TITLE_NEW_PASSWORD','Новый пароль');
define('ADMIN_USER_TITLE_PASSWORD','Пароль');
define('ADMIN_USER_TITLE_ADDRESS','Адрес');
define('ADMIN_USER_TITLE_COUNTRY','Страна');
define('ADMIN_USER_TITLE_REGION','Область');
define('ADMIN_USER_TITLE_CITY','Город');
define('ADMIN_PAGE_TITLE_GENERAL','Главное');
define('ADMIN_PAGE_TITLE_IMAGES','Изображения');
define('ADMIN_PAGE_TITLE_VISIBLE','Видимая');
define('ADMIN_PAGE_TITLE_ALIAS','URI имя');
define('ADMIN_PAGE_TITLE_NAME','Заголовок');
define('ADMIN_PAGE_TITLE_CONTENT','Контент');