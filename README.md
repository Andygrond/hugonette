# Hugonette
If you need a PHP micro-framework to easily enliven your static site, try Hugonette. 

I choose [Hugo generator](https://gohugo.io) as my tool for preparing page design and functionality. It's main advangate for me is really fast response for change. So when I change any html or css in my project, or debugging js file, I can check it in my browser immediately on localhost:1313. As a result Hugo generates a static page, where all files are ready exactly as you see it while browsing.

You can help yourself with some easy tasks as mailbox on page, using external service, or hosting your files on Netlify. But when you see that your site can't be static anymore, and you prefer to use your LAMP hosting, here is the solution! Equipped with Nette templating engine and awesome Tracy debugging tool you can solve even more complex demands.

Hugonette is designed on popular Model-View-Presenter pattern. You can use on your site several modes of routing:
1. Simple static pages with plain old PHP templating
2. Routed pages with plain old PHP templating
3. Routed pages with Nette templating engine
4. JSON Web services

