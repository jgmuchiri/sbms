# Small Business Management System (SBMS)

![dashboard](https://snag.gy/Hxdty4.jpg "SBMS Dashboard")

![projects](https://snag.gy/RNZbMC.jpg "SBMS Projects Dashboard")

## About Small Business Management System

Small Business Management System is a lightweight platform based on Laravel framework that suitable for both small businesses and freelancers to manage invoices, projects, track expenses, and contacts.

## Requirements
- Composer (https://getcomposer.org)
- MySQL >= 5.6
- PHP >= 7.0.*
- SSH (if installing on remote server)

## Features
- Invoicing system
    - Create quick invoices
    - Add items which can also be pre entered as inventory
    - Log manual payments
    - Allow your clients to pay via Stripe using their credit cards
- Expenses tracker
    - Easily view add your expenses and you can sort them by year.
    - Track project tasks payments as expenses
    - Associate an expense with a registered user
- Project management
    - Create a project and assign it to a client (user must have a client role to be available in projects)
    - Create milestones
    - Add tasks to milestones
    - Assign individual tasks to team members
    - Assign team members to the project
    - Communicate easily with clients and team members within the project
    - Share files within the project
- Contacts
    - Manage business contacts in a central location
    - Contacts are available to users as business directory
- User management
- Roles and permissions
	- Complete access control for each module in the application
    Create roles and assign permissions on who should should read, create, update or delete records
    
## INSTALLATION

This is a Laravel based application. Please review Laravel documentation to understand how to configure it.

If you need assistance with installation, please send a support ticket at https://amdtllc.com/support. 

- Extract contents to your desired root directory
- Create a MySQL database
- Edit .env file to match your desired configuration including database connection
- Open terminal inside root directory and run `composer install`
- Once composer completes installing vendors, run `php artisan migrate --seed`
- Default user account is admin@app.com / password

## Support

Technical support is not included in this application. Please contact Evanto Team for support. Bugs can be reported at https://amdtllc.com/support and we will fix them with the next update. We are continually working to make this application better. Suggestions are also welcome.

- We offer installation and application support at a small fee. Please visit https://amdtllc.com/#support for more information.

## Security Vulnerabilities

If you discover a security vulnerability within the application, submit a ticket at https://amdtllc.com/support and/or submit a pull request with a fix. All security vulnerabilities will be promptly addressed.