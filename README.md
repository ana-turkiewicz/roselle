# Roselle

## Como executar

1. Crie o `.env` a partir do arquivo `.env.example`

2. No `.env`, configure o banco:

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=roselle
    DB_USERNAME=roselle
    DB_PASSWORD=xxxxxxx
    ```

3. Suba todos os serviços o comando `docker compose up --build`

4. Acesse http://localhost:8000.

Na primeira subida o backend instala as dependências, gera a `APP_KEY` e roda as migrations.

Para popular o banco com os seeders:

```bash
docker compose exec back php artisan db:seed
```

5. Para parar `docker compose down`

6. Build da imagem de produção: ``docker build -f Dockerfile.back.prod -t roselle .`
# Roselle

## Como executar

1. Crie o `.env` a partir do arquivo `.env.example`

2. No `.env`, configure o banco:

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=roselle
    DB_USERNAME=roselle
    DB_PASSWORD=xxxxxxx
    ```

3. Suba todos os serviços o comando `docker compose up --build`

4. Acesse http://localhost:8000.

5. Para parar `docker compose down`

6. Build da imagem de produção: ``docker build -f Dockerfile.back.prod -t roselle .`