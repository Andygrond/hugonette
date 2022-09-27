# Hugonette: PHP micro-framework for static pages

Lightweight, fast, and easy to use. Provides everything you expect from a framework, to build not only your blog but also a powerful web application. Ready for new projects, but flexible enough to use in existing scenarios. Scalable as your project grows.

I like to use [Hugo static site generator](https://gohugo.io) to prepare a web design and JavaScript UX for my projects. So I wrote Hugonette to mix it with the power of [Nette PHP framework](https://nette.org/en/): an intuitive templating engine and awesome debugging tool.


## Technologies

Hugonette runs on PHP version 7.1 or higher.

[Hugo static site generator](https://gohugo.io) is one of the favorite tools for preparing page design and functionality. Its advantage is a really fast response to change during development. After debugging, Hugo generates a static site, where all files are ready exactly as you see them while browsing.

[Nette PHP framework](https://nette.org/en/) is a flexible, feature-rich, modular PHP framework. Hugonette is a little friend of Nette, sharing its project structure and using several Nette awesome components. Thanks to Nette guys, our work on Hugonette projects will be nice & fast.


## Tutorial

Go to [Hugonette GitBook](https://andygrond.gitbook.io/hugonette/). You will find there the Hugonette documentation as well as some useful advices on how to use Hugo.


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

Take a look at your project folder. If you are working on Linux or macOS, make `log` and `temp` folders writable. Now find some example files in the `vendor/Andygrond/hugonette/doc/install` folder of the Hugonette project. Replace the entire `app` folder with `install/app` folder of Hugonette and the entire `www` with `install/www`. The second one will be your DocumentRoot. Here you will find 2 subfolders:

* `myblog` will be your entry point. Modifying 2 files there: `.htaccess` and `gate.php` you can change the project name, path and do whatever pops into your head to adapt Hugonette to your existing project. It's very flexible, so if you have a question like "will it be possible" the answer is probably: YES!

* `static` -- you will put your static files here.

#### Hugo project

My suggestion is to use Hugo to prepare static pages for your project. But if you wish, you can take advantage of another tool or even use an existing template, making necessary changes in a text editor. Your choice.



## Next steps

Head to [Hugonette GitBook](https://andygrond.gitbook.io/hugonette/) for useful advices.


## License

Hugonette is available as open source under the terms of the [MIT License](http://opensource.org/licenses/MIT).
