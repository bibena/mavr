<?php
define('ERROR_404_MESSAGE','<div class="error-div well well-large">
							<p class="text-error">Ошибка в запросе.</p>
							<p class="text-error">Страница с адресом http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].' не существует или была перемещена.</p>
							<p class="text-error">Обратитесь к администратору за подробностями.</p>
							</div>');
define('ERROR_500_MESSAGE','<div class="error-div well well-large">
							<p class="text-error">Ой! У нас случилась ошибка.</p>
							<p class="text-error">Но не переживайте администратору было отправлено автоматическое уведомление об этом.</p>
							<p class="text-error">Возможно ошибка незначительна и он уже работает над её исправлением.</p>
							</div>');
define('REGISTRATION_EMAIL_TITLE','Email');
define('REGISTRATION_EMAIL_ERROR','Ошибка при заполнении мыла');
define('REGISTRATION_NAME_TITLE','Имя');
define('REGISTRATION_NAME_ERROR','Ошибка при заполнении имени');
define('REGISTRATION_PASSWORD_TITLE','Пароль');
define('REGISTRATION_PASSWORD_ERROR','Ошибка при заполнении пароля');
define('REGISTRATION_AGREEMENT_TITLE','Согласен на <a href="'.SUB_DIR.'/user/eula">все</a>');
define('REGISTRATION_AGREEMENT_ERROR','Ошибка при акцептировании условий');
define('REGISTRATION_BUTTON_TITLE','Регистрация');
define('LOGIN_EMAIL_TITLE','Email');
define('LOGIN_PASSWORD_TITLE','Пароль');
define('LOGIN_ERROR','Ошибка при вводе связки логина/пароля');
define('FOGOTTEN_PASSWORD','Забыли пароль?');
define('LOGIN_REMEMBER_TITLE','Remember me');
define('LOGIN_BUTTON_TITLE','Войти');
