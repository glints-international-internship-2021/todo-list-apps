# To-do-list Application
> An application that helps you make your own to-do-list with ease and comfortably. This application only provides API routes for mobile application or website.

> This application uses **Laravel v8.54**.

## Features
**User**
* Register a new user account.
* Login with user account.
* Forgot password of the user account.
* Sign out the user account.
* User can create a to-do-list.
* Add photo to the to-do-list.
* Edit the to-do-list (include the photo).
* Delete the to-do-list.

**Admin**
* Login with admin account.
* Admin can see all registered users.

## Roles
| Name*         | Alias         | Description   |
| ------------- | ------------- | ------------- |
| Customer      | User          | This role can do the main tasks such as register, login, create, modify, and delete the to-do-list. |
| User          | Admin         | This role can only do login with admin account and see all registered users. |

*Name that used in this repository for the model and database.

## Database
### ERD
![ERD](https://i.ibb.co/RSk0cjf/todolist-erd.png)

### SQL File
[Download Link](https://drive.google.com/file/d/1W9ckTPpCWHTkyTrHppIOcf7tDpNfyYQO/view?usp=sharing)

## Installation Requirements
* **XAMPP** | [Download here](https://www.apachefriends.org/download.html)
* **Composer** | [Download here](https://getcomposer.org/download/)

## Installation Process
1. After you download and install all the requirements above. Open the XAMPP Control Panel and start the "Apache" and "MySQL".

![XAMPP](https://i.ibb.co/gvCYjcd/xampp.png)

2. Check if the Composer it's installed or not through the CLI.

![Composer](https://i.ibb.co/8g2BfKH/composer.png)

3. Clone this repository to inside the folder `C:\xampp\htdocs`.
4. Open the CLI.
5. Go to the folder that you are already cloned it before.
6. Run this command.
```
composer install
```
7. Make sure the `vendor` folder already exist.
8. Make your own database in `http://localhost/phpmyadmin` named `todolist`. Import the SQL File above through the phpMyAdmin.

![phpMyAdmin](https://i.ibb.co/xznbn8r/phpmyadmin.png)

9. Copy the `.env.example` file and change it to `.env`.
10. Open the CLI. Go to the project folder. Run this command.
```
php artisan db:seed
```
11. You will see a new admin account in your database. This is the credentials of that account.
| Username      | Password      |
| ------------- | ------------- |
| admin         | 1234dmin      |
12. Run this command.
```
php artisan serve
```
13. You can check the detailed API routes in [here](https://github.com/glints-international-internship-2021/todo-list-apps/wiki). You can try those API routes with **Postman** ([link to download](https://www.postman.com/downloads/)).