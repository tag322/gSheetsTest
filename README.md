# Подготовка

1. Склонируйте проект.
2. Создайте `.env` файл в папке `backend`, скопируйте в него содержимое `.env.example`.
3. Создайте гугл таблицу.
4. Заполните ячейки A1:C1 значениями, например такими:\
 ![alt text](https://i.imgur.com/Uti48kI.png)
5. Сверху в панели инструментов перейдите в Расширения -> Apps Script.
6. Создайте там файл с любым названием и скопируйте туда код из `googleScripts/main.js`.
7. Выполните развертывание. Там будет URL приложения, заполните им переменную `GOOGLE_SCRIPTS_APP_URL` в `.env` файле.
8. Откройте Google Cloud Console (https://console.cloud.google.com/), создайтe проект в левом верхнем углу.
9. В разделе API & Services на главной странице перейдите в подраздел Credentials.
10. Создайте там API Key и OAuth Client ID.
11. Заполните в `.env` значения под ключами `GOOGLE_CLIENT_ID` и `GOOGLE_API_KEY`
12. Перейдите в подраздел OAuth Consent Screen -> Audience -> Test Users. Введите адрес почты тестового юзера, скорее всего - просто вашей почты.

# Запуск

1. `cd backend; php artisan serve`
2. `cd front; npm run serve`