# Hugonette

If you need a PHP micro-framework to easily enliven your static site, try Hugonette. 

[Hugo static site generator](https://gohugo.io) is one of the favourite tools for preparing page design and functionality. It's advantage is a really fast response to change. Simply set your browser to localhost:1313 and when you save changes to any file in your project, you see results immediately on your browser. After debugging, Hugo generates a static site, where all files are ready exactly as you see it while browsing. 

You can manage some easy tasks as a mailbox, using external service, or hosting your static files on Netlify. But with more complex demands, when your site can't be pure static anymore, here is the solution! You can easily use Hugonette standalone, with plain old PHP templating, or you can solve it using Hugonette with other advanced tools, including awesome templating engine and debugging tool from [Nette Framework](https://nette.org/en/).

Hugonette micro-framework is designed on [Model-View-Presenter](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter) pattern. By passing attributes to Route object or directly determining in Presenter, you can set several modes of operation: from simple static pages as-is, to a routed web application with JSON Web services. 

## Requirements

PHP version 7.1 or higher

## Installation

```
composer require andygrond/hugonette
```

The tutorial will be provided in version 1.0.
