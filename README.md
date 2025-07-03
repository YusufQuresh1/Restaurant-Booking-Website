This web application is a restaurant booking system for Lancaster's Restaurant. It allows customers to book tables for services, while staff can manage bookings and services via a dashboard.

Requirements:

This application requires the following: PHP, MySQL, Twig, Composer.
Ensure these are all up to date and all permisions necessary for them to run correctly are set. Regarding Twig, the 'website' folder contains vendor, templates and cache folders as well as composer.lock and composer.jsons so take this into account when installing what is necessary for Twig's functionality e.g. Composer.

To set up the application, follow these steps:

1. Extract Files
Extract the contents of the zip into a directory of your choice. The zip will contain folders: database, html and private so if all these are placed at the same level then the website should work however still make sure that the directory structure matches the one outlined below.

/path/to/extracted/root/
    ├── database/               # Contains SQL scripts for database setup
    │   └── installation.sql    # SQL file to create the database and tables
    │
    ├── html/                   # Web-accessible application files
    │   └── website/            # Web application source code
    │       ├── index.php       # Homepage
    │       ├── login.php       # Login and sign-up functionality
    │       ├── booktable.php   # Booking page
    │       ├── db_connect.php  # Database connection script
    │       ├── vendor/         # Composer dependencies (including Twig)
    │       ├── templates/      # Twig templates
    │       ├── cache/          # Twig cache directory
    │       └── ...             # Other application files
    │
    └── private/                # Contains non-web-accessible files
        └── config.ini          # Configuration file for database credentials

2. Configure the Web Server
Place the html/website/ directory in your web root (e.g., /var/www/html/website/ for Apache) or set your web server's document root to point to the html directory e.g. DocumentRoot "/path/to/base-directory/html".
Ensure the private/ directory is not publicly accessible. It should reside outside the web root.
IMPORTANT: Ensure the directory structure remains the same. For example:
            1. /var/www/html/website/
            2. /var/www/private/
           The structure should NOT be:
            1. /var/www/html/html/website/
            2. /var/www/private/
           The private folder and the folder containing the website folder should be at the same level.

3. Set Permissions
Make sure to set all the necessary permissions to access the application files etc. Everything necessary to run MySQL, PHP, Twig etc. is required. Ensure everything that can access the private folder should have access to it.

4. Create the Database
Import the installation.sql file from the database folder into phpMyAdmin while logged in with the credentials from config.ini. If done correctly, this will create the database and all tables within it. If you are using different credentials, alter the config.ini file to reflect those i.e.:

[database]
host = "localhost"
username = "your_db_username"
password = "your_db_password"
dbname = "your_database_name"

5. Access the application
The website was tested using Visual Studio Code with the PHP Server extention installed. In order to ensure correct functionality, use this method or an otherwise equivalent one to access the website. The website has been accessed by:
    1. opening the 'website' folder in VSCode
    2. locating 'index.php'
    3. right clicking on a blank area of the code canvas and clicking 'PHP Server: Serve project'
This will then open 'index.php' in the browser. From here you can now start interacting with the website.

6. Create Staff Account
A staff account is required to manage services etc. so once you have reached 'index.php', click the 'Login' button in the right of the navigation bar. This will direct you to 'login.php'. Here, the user will be greeted with a 'Staff Sign Up' form as there are no accounts in the database so the default account creation is staff. Enter an email address and password, confirm the password and then click 'create account'. This will redirect you to 'login.php' but now a login form will appear instead. Log in with the account you just created. This will be the one and only staff account.

7. Add Services
After logging in, you will be redirected to 'staffdashboard.php'. You will see that there are no services today nor upcoming ones so you must create them. Navigate to the 'Upcoming Services' section and select your desired date using the date picker. A message will appear saying there are no services for this date along with an 'Add Service' button. Click this button and complete the form to your liking. There is a choice between Breakfast, Lunch or Dinner for services and their times and the maximum number of tables for the service can be customised. After filling out the form, press 'Add Service' and the service will be added to the database. Repeat this process for as many services as you would like for various dates. The website is now ready for diners to use.

8. Customer Use Case
Now, one is able to make a reservation for any of the services set as well as create an account to simplify this process. These bookings will appear in the respective customer's 'My Account' page as well as all bookings appearing in the staff dashboard. Test by making customer bookings and then logging into the staff account to see the staff dashboard.