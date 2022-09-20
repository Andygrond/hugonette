# Hugonette PHP micro-framework

Lightweight, fast, and easy to use. Provides everything you need to build not only your blog but also a powerful web application. Ready for new projects, but flexible enough to use in existing scenarios. Scalable as your project grows.

You can use [Hugo static site generator](https://gohugo.io) to prepare a web design and JavaScript UX. Then use Hugonette to mix it with the power of [Nette PHP framework](https://nette.org/en/): an intuitive templating engine and awesome debugging tool. With their help, writing code will be fast, pleasant, and well done.

You can prepare your templates in any way. However, if you have more sophisticated purposes, don't do it by hand. Try Hugo. It is the fastest engine in the world to help you with templates. You can build now all your UX with a graphical design, JS functionality, and AJAX with PHP microservices without leaving Hugo.

#### Hugonette as an engine for your static site

If you have already a static site, you probably manage with some easy tasks, such as a contact mailbox, using an external service. Few are hosting their static files on Netlify. But if you want to host your static pages independently, try Hugonette. Hugonette works well in more challenging scenarios also. If the increase in requirements means that your site can no longer be pure static, here is the solution!

#### Hugonette customization to existing projects

Hugonette is flexible to meet your needs, also when you are going to develop an existing project. The recommended solution is to build new functionality on Hugonette, allowing to run old code in parallel, and consecutively transferring all that mess to the new technology.

If you have a website on WordPress and you can't find any WordPress plugin to meet your needs, don't try to write the next WordPress plugin! Hugonette will perfectly fit to patch up the hole, giving you seamless integration and ease of use.

Whatever your needs, start an adventure with Hugonette to bring simplicity and order to your project. With its help you can build a real app on your page, utilizing the most popular hosting service -- LAMP stack without any special needs.


## Cautions

* This tutorial is in the process of being written.

* Hugonette perfectly fits Hugo. Usage of static site generators other than Hugo is not tested yet.


## Technologies

Hugonette runs on PHP version 7.1 or higher.

[Hugo static site generator](https://gohugo.io) is one of the favorite tools for preparing page design and functionality. Its advantage is a really fast response to change during development. Simply set your browser to localhost:1313 and when you save changes to any file in your project, you see results immediately on your browser. After debugging, Hugo generates a static site, where all files are ready exactly as you see them while browsing.

[Nette Web Framework](https://nette.org/en/) is a flexible, feature-rich, modular PHP framework. Hugonette is a little friend of Nette, sharing its project structure and using several Nette awesome components. Thanks to Nette guys, our work on Hugonette projects will be much nicer & faster.


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

You will also need the Hugo environment to prepare some static pages for your project. If you wish, you can take advantage of another tool or even use an existing template, making some necessary changes in a text editor. Your choice.

If you decide to give Hugo a chance, please head over to the [Hugo documentation](https://gohugo.io/documentation/) for details. The first step after installation:

```
hugo new site myblog
cd myblog
```

You will probably want to build your own theme. Use any html template for a good start.

```
hugo new theme ...
```

Hugo is a powerful tool, with some difficulties on start, but when your project is bigger than a blog I recommend you to go this way. You will find some useful hints when you look into `hugonette/doc/hugo` folder.

Start a Web Server: `hugo server` and watch your changes at `http://localhost:1313/`. At the end of the design process, you will issue `hugo` command. After a while your static site will be ready to publish in the `public` folder. Place it inside the `static` folder and rename it to `myblog`. That's all.


## Basic usage

Hugonette micro-framework is designed on the [Model-View-Presenter](https://en.wikipedia.org/wiki/Model-view-presenter) pattern. Your task is to determine the view, calculate model (that is the array of data), and pass it to the presenter.

You can set several view types: JSON microservices, upload or redirect are helpful. But the main view type will be [Latte](https://latte.nette.org/), which is a template engine from Nette framework. Be sure to review their documentation to see what you can do with this powerful tool.

To see famous 'hello world', we will use Latte view. First prepare a basic template. You don't need Hugo to do this. Edit any `index.html` file or make a fresh one, placing inside `{$hello}` code. This is the command to print the value of variable `$hello`. Put the file in `www/static/myblog` folder.

Preparing your application you will work inside the `app` folder of your project. Some useful files are already there:

* `Bootstrap.php` -- initial configuration of the environment

* `routes.php` -- you will need to define some routes here, but now you have what you need:

```
Env::set('view', 'latte');  // Latte view
$route->get('/', 'Examples'); // route to 'default' method of 'Examples' presenter class
$route->get('/login/.*', 'Examples:login'); // route to 'login' method of 'Examples' presenter class
```

Go to `app/presenters` folder and see `Examples.php` class. The `default` method of this class is expected to return a model. Each key of this array will be seen as a template variable. In this example we return `'hello'` key to have it inside the template as `$hello` variable.

```
protected function default()
{
  return [
    'hello' => 'Hello world',
  ];
}
```

Now type the address into your browser: `http://localhost/myblog` -- and you will see `Hello world`. But if you made any mistake, Tracy debugging tool would show you what to fix. So... make a mistake now to see Tracy in action.


## To be continued...

I will continue writing this tutorial. But if you don't have time to wait, please look at the code to see all possibilities.
