# Hugonette

This is a PHP micro-framework to easily enliven your static site.

You can manage with some easy tasks, such as a contact mailbox, using an external service, or hosting your static files on Netlify. But if you want to host your static pages independently, try Hugonette.

Hugonette works well in more challenging scenarios also. If the increase in requirements means that your site can no longer be pure static, here is the solution! On the other hand, if you prefer to use a static site generator for more sophisticated purposes, you can build now all your UX with a graphical design, JS functionality, and PHP microservices without leaving [Hugo](https://gohugo.io).

You can easily use Hugonette almost standalone, with plain old PHP templating made on Notepad, or you can steam it up with [Nette](https://nette.org/en/) utilizing a powerful templating engine and/or awesome debugging tool.

Hugonette is flexible to meet your needs, also when you are going to develop an existing project. The recommended solution is to build new functionality on Hugonette, allowing to run old code in parallel, and consecutively transferring all that mess to the new technology.   

Whatever your needs, start an adventure with Hugonette to bring simplicity and order to your project. Now you can build a real app on your page, utilizing the most popular hosting service -- LAMP stack without any special needs.


## Cautions

* This tutorial is in the process of being written.

* The usage of static site generators other than Hugo with this project was not tested yet. You can try.


## Technologies

Hugonette runs on PHP version 7.1 or higher.

[Hugo static site generator](https://gohugo.io) is one of the favorite tools for preparing page design and functionality. Its advantage is a really fast response to change during development. Simply set your browser to localhost:1313 and when you save changes to any file in your project, you see results immediately on your browser. After debugging, Hugo generates a static site, where all files are ready exactly as you see them while browsing.

[Nette Web Framework](https://nette.org/en/) is a flexible, feature-rich, modular PHP framework. Hugonette is a little and lighter friend of Nette, sharing its project structure and using several Nette awesome components. Thanks to Nette guys, our work on Hugonette projects will be much nicer & faster.


## Installation

You must have [Composer](https://getcomposer.org/) installed on your local machine. Then choose a name for your project folder (let it be  `myblog`). Common practice is to place it outside DocumentRoot for security. From a command tool run:

```
composer create-project nette/web-project myblog
```

Then go into the project folder. Install Hugonette:

```
cd myblog
composer require andygrond/hugonette
```

#### Adaptations for Hugonette

After that take a look into your project folder. If you are working on Linux or macOS, make `log` and `temp` folders writable. Now find some example files in the `vendor/Andygrond/hugonette/doc/install` folder of Hugonette project. Replace entire `app` folder with `install/app` folder of Hugonette and entire `www` with `install/www`. The second one will be your DocumentRoot. Here you will find 2 subfolders:

* `myblog` will be your entry point. Modifying 2 files there: `.htaccess` and `gate.php` you can change the project name, path and do whatever pops into your head to adapt Hugonette to your existing project. It's very flexible, so if you have a question like "will it be possible" the answer is probably: YES!

* `static` -- you will put your static files here.

#### Hugo project

You will also need the Hugo environment in order to prepare some static pages for your project. If you wish, you can take advantage of another tool or even use existing template, making some necessary changes in a text editor. Your choice.

If you decide to give Hugo a chance, please head over to the [Hugo documentation](https://gohugo.io/documentation/) for details. It is a little more difficult then doing it by text editor, but when your project is bigger than blog I recommend you to go this way. At the end of the design process you will issue `hugo` command, and all needed files will be ready in the `public` folder of your Hugo project. Place this folder inside `static` folder and rename it to `myblog`. That's it.


## Basic usage

Hugonette micro-framework is designed on the [Model-View-Presenter](https://en.wikipedia.org/wiki/Model-view-presenter) pattern. Using Route object you can set several modes of operation: from simple static pages to a routed web application with JSON microservices.

You will work inside the `app` folder of your project. Some useful files:

* `.env.php` -- basic configuration server aware

* `routes.php` -- here you will need to define some routes


#### Simple static pages

TO BE CONTINUED...
