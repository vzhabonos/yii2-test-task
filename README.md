# Yii2 Test Task

A Yii2 application for importing shipment data from files into MongoDB and synchronizing it with OpenSearch for advanced querying.

## Requirements

Before starting the project, ensure you have the following installed on your local machine:

*   **Docker** (Desktop or Engine)
*   **Docker Compose** (usually bundled with Docker Desktop)

## First Start

Follow these steps to get the application up and running:

1. Clone the repository and prepare environment
2. Initialize `.env` file:
```shell
cp .env.example .env
```
3. Change the configuration of the `.env` file to your liking and needs.
4. Start the app using docker compose in detached mode:
```shell
docker compose up -d
```
5. Connect to the app docker container:
```shell
docker compose exec app bash
```
6. Run migrations to MongoDB:
```shell
php yii migrate-mongodb
```
7App could be accessed using this link (if default `APP_CONTAINER_EXTERNAL_PORT` wasn't changed): http://127.0.0.1:8080
