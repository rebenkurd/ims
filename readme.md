
# Invertory Managment System

A modern web application built with Laravel 12 (Backend API) and Vue.js 3 (Frontend).

## Project Structure

```
├── backend/          # Laravel 12 API
├── frontend/         # Vue.js 3 Application
└── README.md         # This file

```

## Prerequisites

Before you begin, ensure you have the following installed on your system:

-   **PHP** >= 8.2
-   **Composer** (latest version)
-   **Node.js** >= 18.x
-   **npm** or **yarn**
-   **MySQL** or **PostgreSQL** (or your preferred database)
-   **Git**

## Backend Setup (Laravel 12)

### Installation

1.  Navigate to the backend directory:

```bash
cd backend

```

2.  Install PHP dependencies:

```bash
composer install

```

3.  Create environment file:

```bash
cp .env.example .env

```

4.  Generate application key:

```bash
php artisan key:generate

```


### Database Setup

1.  Create a new database in phpMyAdmin with name -> ims
2. then import ims.sql file into the ims database
3.  Update the database credentials in `.env`


### Running the Backend

Start the Laravel development server:

```bash
php artisan serve

```

The API will be available at: `http://127.0.0.1:8000`


## Frontend Setup (Vue.js 3)

### Installation

1.  Navigate to the frontend directory:

```bash
cd frontend

```

2.  Install Node.js dependencies:

```bash
npm install
# or
yarn install

```

3.  Create environment file:

```bash
cp .env.example .env

```


### Running the Frontend

Start the Vue.js development server:

```bash
npm run dev
# or
yarn dev

```
