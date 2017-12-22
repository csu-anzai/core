
PHP Web Application Core Framework
==========

This is a fork of the open source CMS Kajona. 
The project was forked to decouple the backend framework in order to transform it into a web application framework.
> This is still work in progress.


Bugtracker / Issues
---
Please feel free to report issues, ideas an general feedback using the GitHub issue pages
https://github.com/artemeon/core/issues

Build-System
---
We currently provide various build-scripts in order to test, clean, build and package a project out of the sources.
Please have a look at the ant-scripts located at `_buildfiles`: `build_jenkins.xml`, `build_project.xml`

Documentation
---
Documentation is provided per module, thus have a look at the `docs` folder per module.
A automtically generated overview is provided at:
[_buildfiles/docs/overview.md](_buildfiles/docs/overview.md)

Quickstart
---
You only have to follow a few steps in order to build a project out of the sources:

* Create a folder in your webroot, used to store the later Kajona project, e.g. `project`
* Create a folder named `core` within the folder created before, e.g. `project/core`
* Clone the Git-repo inside the core-folder: Change to the new directory and use the following command:
`git clone https://github.com/artemeon/core.git .`
* The folder `project/core` should now be filled with a structure similar to: 
* Open the file `project/core/setupproject.php` using the webbrowser of your choice (btw, you could run this script on the command line, too)
* After a few log-outputs, your `project` folder is now setup like a real Kajona project, so there should be a structure similar to
```
    /core (as created manually)
    /files
    /project
    .htaccess
    debug.php
    image.php
    index.php
    installer.php
    xml.php
```

> Have a look at the end of the results: If you see some red lines (composer messages) the setupproject could NOT run the composer commands successfully! 
Please run the commands manually from the command line or a terminal window.

Done! All you have to do is to fire up your browser, opening the file `project/installer.php` and the installer will guide you through the process.
Whenever you make changes to s.th. below /core, don't forget to create a pull-request with all those changes - and be sure to earn the glory!

