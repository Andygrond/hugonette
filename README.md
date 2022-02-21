# Hugonette

This is a PHP micro-framework to easily enliven your static site.

You can manage with some easy tasks as a contact mailbox, using external service, or hosting your static files on Netlify. But if you want to host your static pages independently, try Hugonette.

Hugonette works well in more challenging scenarios also. If the increase in requirements means that your site can no longer be pure static, here is the solution! On the other hand, if you prefer to use a static site generator for more sophisticated purposes, you can build now all your UX work with a graphical design, JS functionality and PHP microservices without leaving [Hugo](https://gohugo.io).

You can easily use Hugonette standalone, with plain old PHP templating made on Notepad, or you can steam it up with [Nette Framework](https://nette.org/en/) utilizing a powerful templating engine and awesome debugging tool.

Hugonette is flexible to meet your needs, also when you are going to develop an existing project. The recommended solution is to build new functionality on Hugonette, allowing to run old code in parallel, and consecutively transfering all that mess to the new technology.   

Whatever your needs, start adventure with Hugonette to bring simplicity and order to your project. Now you can build a real app on your page, utilizing the most popular hosting service -- LAMP stack without any special needs.


## Warnings

* This tutorial is in the process of being written.

* Use of static site generators other than Hugo with this project was not tested yet. You can try.


## Technologies

PHP version 7.1 or higher

[Hugo static site generator](https://gohugo.io) is one of the favourite tools for preparing page design and functionality. It's advantage is a really fast response to change during development. Simply set your browser to localhost:1313 and when you save changes to any file in your project, you see results immediately on your browser. After debugging, Hugo generates a static site, where all files are ready exactly as you see it while browsing.

[Nette](https://nette.org/en/) is a very flexible and useful PHP framework and I would recomend to use it if your project requires something more than simple mailbox.


## Installation

You must have [Composer](https://getcomposer.org/) installed on your local machine. Then create a new folder for your project. From a command tool go into the project folder and run:

```
composer require andygrond/hugonette
```

If you plan to use Nette in your project, install it similarly: `composer require nette/nette`. Thats it.


## Usage

Hugonette micro-framework is designed on [Model-View-Presenter](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter) pattern. By passing attributes to Route object you can set several modes of operation: from simple static pages as-is, to a routed web application with JSON microservices.


#### Simple static pages

You will find example files soon in the doc section of this project.
To be continued.
