# Custom Multi Tier Architectured Framework And Content Management Platform

This platform was built starting in 2008 using the LAMP stack (PHP 5, MYSQL 5); it's been through a couple of versions and my last updates to it were in 2015.

It uses a Model View Controller architecture with an Active Record based data model, Smarty Template Engine for view templates, and rudimentary ORM built into the model.

I've built in a few convenience classes and wrappers to handle things like auth, email, caching, data change history loggin, database query and results, form handling, etc.

I have also built and integrated a base CRUD / content management system into the platform with a command line script to auto generate admin CRUD pages for data models.  The base CMS has common site management features such as ACL, reusable content modules, email and email templates, forms, multi language support, navigation, and site tag management.

I've learned many lesson from building this platform, including the all important lesson of not building your own framework.

The platform is currently used on ~10 live client web sites.

# Installation

1) Run SQL setup file to setup DB.
2) Update .htaccess in the platform root directory with domain and subdomain details
3) Update conf.php in each application conf folder (DB and Email details)
4) Point apache to application web root

# Folder Structure

- Platform Root
  - Applications
    - Admin
      - Build (CRUD auto generation script)
      - Conf
      - Controllers
      - Templates
        - Source (Template source files)
      - WWW (Web root)
        - CSS
        - JS
        - Images
    - Main
  - Base Classes (MVC and general functions)
  - Classes
  - Data Views (Admin table view classes)
  - Fonts
  - Library (Vendor libraries)
  - Models
  - SQL (Database files)
