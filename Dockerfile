FROM php:8.2-cli

WORKDIR /app
COPY . .

# Extensiones necesarias para MySQL (mysqli / pdo_mysql)
RUN docker-php-ext-install mysqli pdo_mysql

# Railway usa la variable PORT; fallback a 8080
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /app/src/pages"]
