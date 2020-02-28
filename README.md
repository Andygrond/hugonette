# Hugonette
If you need a PHP micro-framework to easily enliven your static site, try Hugonette. 

I choose [Hugo generator](https://gohugo.io) as my tool for preparing page design and functionality. It's main advangate for me is really fast response for change. So when I change any html or css in my project, or debugging js file, I can check it in my browser immediately on localhost:1313. As a result Hugo generates a static page, where all files are ready exactly as you see it while browsing.

When you see that your site can't be static anymore, you can help yourself with some easy tasks as mailbox on page, using external service, or hosting your files on Netlify. But if you prefer to use your LAMP hosting, here is the solution! You can easily use Hugonette standalone, with plain old PHP templating. But if you have more complex demands, you can solve it using Hugonette with other advanced tools, including awesome templating engine and debugging tool from [Nette Framework](https://nette.org/en/).

Hugonette micro-framework is designed on [Model-View-Presenter](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter) pattern. Passing attributes to Route object you can set several modes of operation, from simple static pages as-is, to routed web application with JSON Web services. Tutorial will be provided in version 1.0.
