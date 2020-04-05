# Hugonette

If you need a PHP micro-framework to easily enliven your static site, try Hugonette. 

[Hugo static site generator](https://gohugo.io) is one of the favourite tools for preparing page design and functionality. It's advantage is a really fast response to change. You simply set your browser to localhost:1313 and when you change any file in your project, you can check it immediately. After debugging Hugo generates a static site, where all files are ready exactly as you see it while browsing. You can manage some easy tasks as a mailbox, using external service, or hosting your files on Netlify. 

But if you see that your site can't be static anymore, and you prefere to use your LAMP hosting, here is the solution! You can easily use Hugonette standalone, with plain old PHP templating. But if you have more complex demands, you can solve it using Hugonette with other advanced tools, including awesome templating engine and debugging tool from [Nette Framework](https://nette.org/en/).

Hugonette micro-framework is designed on [Model-View-Presenter](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter) pattern. By passing attributes to Route object or directly determining in Presenter, you can set several modes of operation: from simple static pages as-is, to a routed web application with JSON Web services. 

## Installation

```
composer require andygrond/hugonette
```

The tutorial will be provided in version 1.0.
