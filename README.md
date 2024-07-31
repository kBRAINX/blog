## Blog Platform
### Description
This project is a blog platform where users can share posts, manage comments, and view various blog entries. The platform includes features for both regular users and administrators, with a range of functionalities such as user registration, login, post creation, and content management.

### Table of Contents
    - Features
    - Technologies Used
    - Installation
    - Configuration
    - Usage 
        |- Home Page
        |- User Management
        |- Administrator Management
        |- Services Management
        |- Blog Post Management
        |- Comments Management
        |- Authentication
    - Interfaces


### Features
- User Authentication: Register, login, and logout functionality.


- Service Management: create, edit, delete, and filter posts


- Post Management: Create, edit, delete, and view blog posts.


- Comment System: Add, remove, edit and manage comments on blog posts.


- Role-based Access Control: Admins have elevated permissions to manage content and users.


- Pagination: Supports paginated views of posts and users.


- SVG Icons: Custom SVG icons for actions like edit, delete, logout, and more.

### Technologies Used

- Framework: Symfony 7


- Templating Engine: Twig


- CSS: Custom styles with modern CSS practices


- JavaScript: For interactive elements


- Database: MySQL (or any other database supported by Symfony)

### Installation
##### Prerequisites
PHP >= 8.2 if you want to use symfony 7
Composer
MySQL (or another supported database)

##### Steps Clone the repository:

```bash
git clone https://github.com/kBRAINX/blog.git
cd blog
```

##### Install dependencies:

```bash
composer install
```

##### Set up the environment variables:
Copy the .env file and configure your database connection and other settings:

```bash
cp .env .env.local
```

### configure the messenger
in /config/packages/messenger.yaml edit to have this configuration

```yaml
            transports:
            # https://symfony.com/doc/current/messenger.html#transport-configuration
            async:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                options:
                    use_notify: true
                    check_delayed_interval: 60000
                retry_strategy:
                    max_retries: 3
                    multiplier: 2
            failed: 'doctrine://default?queue_name=failed'
            sync: 'sync://'

        default_bus: messenger.bus.default

        buses:
            messenger.bus.default: []

        routing:
            Symfony\Component\Mailer\Messenger\SendEmailMessage: sync
            Symfony\Component\Notifier\Message\ChatMessage: sync
            Symfony\Component\Notifier\Message\SmsMessage: sync

```

and in .env modify the last step to have:

```dotenv
###> symfony/mailer ###
 MAILER_DSN=smtp://localhost:1025
###< symfony/mailer ###
```

##### Create the database:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

##### Run the Symfony server:

```bash
symfony server:start
````
Or use:
```bash
php -S localhost:8000 -t public
```
#### Run server mail(Mailpit)

```bash
./bin/mailpit
```

### Configuration

#### Routes Configuration
The application uses several routes for user management,service management, post management, comment management, and authentication.

- Logout : /logout, handled by SecurityController::logout


- Register : /register, handled by SecurityController::register


- Login : /login, handled by SecurityController::login


- Service : /admin/category/_path, handled by CategoryController for Administrator 


- Service : /category/_path, handled by CategoryController for User interaction


- Post : /admin/post/_path, handled by PostController for the Administrator interaction


- Post : /post/_path, handled by PostController for the User interaction


- Comment : /admin/comment/_path, handled by CommentController for the Administrator interaction


- Comment : /comment/_path, handled by CommentController for the User interaction


- User Management: /admin/users, handled by UserController 

#### Remark
    
    And All the method Delete or method we have a tag " #[Is_granted('ROLE_ADMIN')] " means the method use by the Administrator
    
    And All the method we have a tag " #[Is_granted('ROLE_USER')] " means the method use by the User

#### Environment Configuration
Update the .env.local file with your database credentials, mailer settings, and other environment-specific variables.

```dotenv
DATABASE_URL="mysql://username:password@127.0.0.1:3306/db_name"
APP_ENV=dev
APP_SECRET=your_secret_key
```

### Usage
- Home Page: 

The home page displays the latest blog posts with a "How It Works" section and an "About Us" page. Users can view a list of blog posts, filtered by categories.

- User Management:

Admins can manage users by viewing a list of users in a table format, with columns for username, email, role, and actions. Each row has options for editing or deleting users.

- Blog Post Management:

Users with the appropriate permissions can create, edit, and delete blog posts. The post list is displayed in a card layout, with each post showing its title, author, category, and a brief content preview.

- Comments Management

Users can add comments to blog posts, and admins can manage these comments, including deleting inappropriate ones.

- Authentication

Users can register, login, and logout. The authentication pages are styled to match the rest of the site's theme and include SVG icons for ease of use.

### Interfaces
on the first start with project we have these interfaces:

#### HomePage
![Home](./public/images/home.png)

#### Filter Posts with service

![Home](./public/images/filter1.png)

#### Blog Post

![Home](./public/images/blog1.png)

#### Register

![Home](./public/images/register.png)

#### Login

![Home](./public/images/login.png)

#### Contact



After user have connect two page have a new design: 

#### Blog Post

![Home](./public/images/blog2.png)

#### Filter Posts with service

![Home](./public/images/filter2.png)

And the last we have a admin view to Admins

#### Dashboard

![Home](./public/images/dash.png)

#### Service Page

![service](./public/images/service.png)

#### User Page

![Home](./public/images/user.png)
