# Church Community API

##  About the Project

This project is a RESTful API built with Laravel for managing content in a church community platform.

It allows administrators to publish and manage important information such as news, announcements, and events, which are then displayed to the public through a frontend application.

The system is designed to be simple, structured, and scalable, focusing on real-world usage for church communication and organization.

---

##  Main Features

*  Authentication system (Admin access)
*  News management (create, update, delete, publish)
*  Announcements system with priority levels
*  Events management with date and location
*  Image upload support for posts and events
*  Role-based access (Admin vs Public users)
*  Public content access (read-only for visitors)

---

##  System Roles

### Admin

* Full access to create, edit, and delete content
* Manages all sections (news, announcements, events)

### Public Users

* Can view published content only
* No authentication required

---

##  Technologies Used

* Laravel (PHP Framework)
* MySQL / PostgreSQL (Database)
* REST API architecture
* Eloquent ORM
* Laravel Validation & Middleware

---

##  Project Structure

/app
/routes
/database
/storage
/config

---

##  API Endpoints Overview

### Authentication

* POST `/api/login`
* POST `/api/logout`
* GET `/api/user`

---

### News

* GET `/api/news`
* GET `/api/news/{id}`
* POST `/api/news`
* PUT `/api/news/{id}`
* DELETE `/api/news/{id}`

---

### Announcements

* GET `/api/announcements`
* POST `/api/announcements`
* PUT `/api/announcements/{id}`
* DELETE `/api/announcements/{id}`

---

### Events

* GET `/api/events`
* POST `/api/events`
* PUT `/api/events/{id}`
* DELETE `/api/events/{id}`

---

##  Access Control

* Public endpoints: GET requests (news, announcements, events)
* Protected endpoints: POST, PUT, DELETE (Admin only)

Authentication is required to access protected routes.

---

##  Installation

Clone the repository:

```bash
git clone https://github.com/your-username/church-community-api.git
cd church-community-api
```

Install dependencies:

```bash
composer install
```

Copy environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Configure your database in `.env`, then run:

```bash
php artisan migrate
```

Start the server:

```bash
php artisan serve
```

---

##  Environment Variables

Make sure to configure:

* DB_DATABASE
* DB_USERNAME
* DB_PASSWORD
* APP_URL

---

##  Future Improvements

* Pagination for large datasets
* Search and filtering functionality
* Email notifications for announcements
* User registration for members
* Mobile API optimization

---

##  Project Purpose

This API was developed as a real-world solution for managing church communication and keeping members informed in a structured and accessible way.

---

##  Author

Developed by Dayana Rojas

