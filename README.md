# Hugonette PHP micro-framework

Lightweight, fast, and easy to use. Provides everything you need to build not only your blog but also a powerful web application. Ready for new projects, but flexible enough to use in existing scenarios. Scalable as your project grows.

I like to use [Hugo static site generator](https://gohugo.io) to prepare a web design and JavaScript UX for my projects. So I wrote Hugonette to mix it with the power of [Nette PHP framework](https://nette.org/en/): an intuitive templating engine and awesome debugging tool. With their help, writing code will be fast and pleasant for me.

You can prepare your templates in any way. However, if you have more sophisticated purposes, don't do it by hand. Try Hugo. It is the fastest web development engine in the world.


## Tutorial

You can find some useful advices [on GitBook](https://andygrond.gitbook.io/hugonette/). This tutorial is still in the process of being written.


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
hugo new theme your-theme-name
```

Hugo is a powerful tool, but you don't need to use all its power with Hugonette. Please check [Hugonette tutorial](https://andygrond.gitbook.io/hugonette/) to easily make Hugo your friend. You will find some useful hints when you look into `hugonette/doc/hugo` folder.


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

Head to [Hugonette GitBook Documentation](https://andygrond.gitbook.io/hugonette/) for useful advices. Set the star if you like Hugonette.


## License

Hugonette is available as open source under the terms of the [MIT License](http://opensource.org/licenses/MIT).
