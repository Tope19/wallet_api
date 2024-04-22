# Laravel Wallet API

This is a RESTful API built with Laravel for managing user wallets and transactions. The API allows users to perform various actions such as creating wallets, funding wallets using Paystack, and viewing transaction history.

## Table of Contents

- [Project Overview](#project-overview)
- [Setup Instructions](#setup-instructions)
- [API Documentation](#api-documentation)

## Project Overview

The Laravel Wallet API provides endpoints to perform the following tasks:

- User registration and authentication using JWT
- Assign currencies to wallets
- Wallet management (create, view, fund, and debit)
- Paystack integration for funding wallets
- Transaction history for wallets

## Setup Instructions

To run this project locally, follow these steps:

1. **Clone the Repository**

   ```bash
   git clone https://github.com/Tope19/wallet_api.git
   cd wallet_api

2. **Install Dependencies**

    ```bash
    composer install

3. **Copy Environment File**

    ```bash
    cp .env.example .env

4. **Generate JWT Secret and Update the JWT_SECRET variable in your .env file:**

    ```bash
    php artisan jwt:secret

5. **Obtain your Paystack API keys (PAYSTACK_PUBLIC_KEY and PAYSTACK_SECRET_KEY) and update the corresponding variables in your .env file.**

6. **Run Migrations**

    ```bash
    php artisan migrate

7. **Run the Database Seeder**

    ```bash
    php artisan db:seed

8. **Start the Development Server**

    ```bash
    php artisan serve


## API Documentation

Explore the API endpoints using Postman by importing the provided collection:

<a target="_blank" href="https://documenter.getpostman.com/view/10180177/2sA3Bq3Awf">Postman Documentation</a>


## Usage
After completing the setup instructions, your Laravel application should be up and running.
