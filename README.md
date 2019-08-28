# Custom Multi Tier Architectured And Content Management Platform

This platform was built starting in 2008 with PHP 5 and MYSQL 5; it's been through a couple of versions and my last updates to it were in 2015.

It uses a Model View Controller architecture with an Active Record based data model, rudimentary ORM built into the model, and Smarty Template Engine for view templates.

I've built in a few convenience classes and wrappers to handle things like auth, email, caching, data change history loggin, database query and results, form handling, etc.

I have also built and integrated a base CRUD / content management system into the platform with a command line script to auto generate admin CRUD pages for data models.  The base CMS has common site management features such as ACL, reusable content modules, email and email templates, forms, multi language support, navigation, and site tag management.
