
# CRM-CRUD

Simple web-based Customer Relationship Management (CRM) that allows users
to manage customer information. The system should include the ability to add, update,
delete, and view customer details. The users should register and login to the CRM in order
to use it.


## Environment Variables

To run this project, first you will need to copy example.env with default values to .env

You can leave them as is:

`MYSQL_USER=cmsuser`

`MYSQL_PASSWORD=pass`

`MYSQL_DB=cms`

`MYSQL_PORT=3306`

`BACKEND_PORT=3000`

`FRONTEND_PORT=4000`

`JWK_KEY=tT4RCuHHrQpwaABxWjXJO5nBPKwGsgxNraexAQdnh6f4qAeLB9XwHwafsJuP1em`


## Installation

Create CRM-CRUD with docker, run from root

```bash
docker-compose up -d
```

Execute init queries from file

```bash
mysql/init.sql
```

### Default URL's

Backend
```bash
http://localhost:3000
```
Frontend
```bash
http://localhost:4000
```
    
## API Reference

#### Login

```http
  POST /users/login
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `username` | `string` | **Required**.|
| `password` | `string` | **Required**.|

#### Registration

```http
  POST /users/register
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `username` | `string` | **Required**.|
| `password` | `string` | **Required**.|

### All ```/customers``` requests required ```Authorization``` header

#### Get all customers

```http
  GET /customers
```

#### Get customer by ID

```http
  GET /customers/{id}
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `id` | `int` | **Required**.|


#### Create new customer

```http
  POST /customers/create
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**.|
| `email` | `string` | **Required**.|
| `address` | `string` | **Required**.|
| `phone` | `string` | **Required**.|

#### Update customer

```http
  POST /customers/update/{id}
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `id` | `int` | **Required**.|
| `name` | `string` | **Required**.|
| `email` | `string` | **Required**.|
| `address` | `string` | **Required**.|
| `phone` | `string` | **Required**.|

#### Delete customer

```http
  DELETE /customers/delete/{id}
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `id` | `int` | **Required**.|
