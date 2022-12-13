clone directory

composer install

<!-- FICHIER ENV.DEV -->
add .env.dev


create database
make migrations


<!-- MAILER -->
DATABASE_URL="LINKTODATABASESQL"
HOST_SMTP="SMTP.SERVEUR.FR"
COMPTE_SMTP="SMTP_EMAIL"
PASSWORD_SMTP="SMTP_PASSWORD"

MAILER_DSN=smtp://${COMPTE_SMTP}:${PASSWORD_SMTP}@${HOST_SMTP}:${PORT_SMTP}

<!-- MISE EN ROUTE DU SITE -->
