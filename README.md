# Dating Website | Foxy Farmers
College Project, Feb-May 2020

Currently hosted [here](http://hive.csis.ul.ie/cs4116/group17/pages/login/landing-page.php).

### Developed Using: 
- **Front-End** - HTML / CSS (Bootstrap) / JavaScript
- **Back-End** - PHP
- **Database** - MySQL
- **Editor** - VSCode
- **Development Server** - MAMP

---

You can register/login from the landing page. Once registered you need to complete your profile information. 

You are then taken to a home page which shows suggested users based on your preferences. These preferences can be changed anytime from your profile page. 

You can only see who is waiting for you (who has liked you) if you sign up for a premium account, this can be done in your profile page (dummy confirmation box used in place of a payment process).

You can search all other users (of your preferred gender). This search excludes users who have disliked you and users you have already liked or that you have already matched with.

Once you match with a user you can them see their email address and can contact them via email.

Admins have the ability to suspend reported users, edit/delete user accounts, add/delete events and add/edit/delete interests.

--- 

### Team Members:
- Enda McCarthy
- Richard Moloney
- Griselda Williams
- Natalie Nic An Ghaill

### Instructions for team members:

1. Under **Clone or download**, click Download ZIP.
2. Save the contents of the ZIP file to the **htdocs** folder in your MAMP/WAMP application folder.
3. Open your MAMP/WAMP app.
4. Navigate to local host page (http://localhost:8888).
5. From there navigate to http://localhost:8888/DatingWebsite/database/ and click on **dbSetup.php**.
6. This should create a new database called dating_website and all the tables in the database (if something has gone wrong try changing username and password used for the db connection in setup.php from 'root' to '').
7. If DB was correctly set up, click on the **Go To Landing Page** Link.
8. You can now register as a new user. If you want to login as an existing user or as an admin, use the following login credentials:

    **Normal User:**
    - **email:** test@email.com
    - **password:** password

    **Admin User:**
    - **email:** admin@email.com
    - **password:** password

9. To view the database go into phpMyAdmin from the MAMP/WAMP homepage (or http://localhost:8888/phpMyAdmin/?lang=en).

--- 

## Screenshots
![Landing Page Screenshot](screenshots/LandingPage.png)
![Home Page Screenshot](screenshots/HomePage.png)
![Profile Page Screenshot](screenshots/ProfilePage.png)
![Search Page Screenshot](screenshots/SearchPage.png)
![Admin Home Page Screenshot](screenshots/AdminHomePage.png)
