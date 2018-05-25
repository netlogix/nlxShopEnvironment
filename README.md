sdShopEnvironment
=================

By using this Shopware plugin you can load or dump some shop configuration to or from database.

This is especially useful for easily privisioning different environment.
For example you can have a file ```shopconfig.prod.yml``` and a ```shopconfig.dev.yml```
in your GIT repository.

You can then use a dump of your production database to setup your development environment.
After inserting the dump into your database,
you can load the settings from ```shopconfig.dev.yml``` file
overriding production critical configuration like smtp servers, hostnames, URLs, etc. 


Usage
-----

First you have to install and activate the plugin into your shopware installation.

Then you can use both commands:

    bin/console sd:environment:config:dump
    bin/console sd:environment:config:load

Use ```help``` command on the commands to get a brief overview of what they do.


License
-------

Please see [License File](LICENSE) for more information.
