# Hugonette

This is a PHP micro-framework to easily enliven your static site.

You can manage with some easy tasks as a contact mailbox, using external service, or hosting your static files on Netlify. But if you want to host your static pages independently, try Hugonette.

Hugonette works well in more challenging scenarios also. If the increase in requirements means that your site can no longer be pure static, here is the solution! On the other hand, if you prefer to use a static site generator for more sophisticated purposes, you can build now all your UX with a graphical design, JS functionality and PHP microservices without leaving [Hugo](https://gohugo.io).

You can easily use Hugonette almost standalone, with plain old PHP templating made on Notepad, or you can steam it up with [Nette](https://nette.org/en/) utilizing a powerful templating engine and/or awesome debugging tool.

Hugonette is flexible to meet your needs, also when you are going to develop an existing project. The recommended solution is to build new functionality on Hugonette, allowing to run old code in parallel, and consecutively transfering all that mess to the new technology.   

Whatever your needs, start adventure with Hugonette to bring simplicity and order to your project. Now you can build a real app on your page, utilizing the most popular hosting service -- LAMP stack without any special needs.


## Cautions

* This tutorial is in the process of being written.

* Use of static site generators other than Hugo with this project was not tested yet. You can try.


## Technologies

Hugonette runs on PHP version 7.1 or higher.

[Hugo static site generator](https://gohugo.io) is one of the favourite tools for preparing page design and functionality. It's advantage is a really fast response to change during development. Simply set your browser to localhost:1313 and when you save changes to any file in your project, you see results immediately on your browser. After debugging, Hugo generates a static site, where all files are ready exactly as you see it while browsing.

[Nette Web Framework](https://nette.org/en/) is a flexible, feature rich, modular PHP framework. Hugonette is a little and lighter friend of Nette, sharing it's project structure and using several Nette awesome components. Thanks to Nette guys, our work on Hugonette projects will be much more nicer & faster.


## Installation

You must have [Composer](https://getcomposer.org/) installed on your local machine. Then choose a name for your project folder (let it be  `system`). Common practice is to place it outside DocumentRoot for security. From a command tool run:

```
composer create-project nette/web-project system
```

Then go into the project folder and install Hugonette:

```
cd system
composer require andygrond/hugonette
```

If you are working on Linux or macOS, make `log` and `temp` folders writable.


## Basic usage

Hugonette micro-framework is designed on [Model-View-Presenter](https://en.wikipedia.org/wiki/Model-view-presenter) pattern. Using Route object you can set several modes of operation: from simple static pages, to a routed web application with JSON microservices.


#### Simple static pages

You will find example files in the `Docs` folder of this project. Put those files into the project folder:

* `.env.php` configuration file

* `routes.php` with route definitions

Create two folders inside the DocumentRoot: `static` folder, where you will put the static pages and the folder where your starting page will be placed (let it be `homepage`), with two files inside. You will want to check/edit their contents.

* `gate.php` file: check the relative path to `.env.php` file in `Require ../.env.php` line

* `.htaccess` file: check the path to your static pages in the last line: `RewriteRule ^(.*)$ /static/homepage/$1`

TO BE CONTINUED...
