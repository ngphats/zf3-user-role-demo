Zend framework 3 demo
==================================================

This sample is based on *User Demo* sample. It shows how to:

 * Implement roles and permissions in your website
 * Organize roles in database into an hierarchy
 * Use Zend\Authenticate to implement login control
 * Use Zend\Permissions\Acl component to implement access control list
 * Use Zend\Mail, Zend\Recapcha component to implement forgot-password

## Installation

You need to have Apache 2.4 HTTP server, PHP v.5.6 or later with `gd` and `intl` extensions, and MySQL 5.6 or later.

Download the sample to some directory (it can be your home dir or `/var/www/html`) and run Composer as follows:

```
php composer install
```

The command above will install the dependencies (Zend Framework and Doctrine).
