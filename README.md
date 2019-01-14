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


Adding functionality
--------------------

For each root node in the `yaml` files there is an own loader that processes the configuration
and an own dumper that generates these information.

To add a new root node you have to do the following:

* Provide a new `Loader` that implements the `LoaderInterface` that is registered in `Resources/services/loaders.xml`.
* Provide a new `Dumper` that implements the `DumperInterface` that is registered in `Resources/services/dumpers.xml`.
* Provide a new `DataType` that implements the `DataTypeInterface` 
  that is registered in `Resources/services/data_types.xml` and tagged with `sd.data_type`.
  In the tag you can define the concrete root node key you want to use.
  If you are lazy you can omit creating an own class implementing `DataTypeInterface` by using `GenericDataType`.


License
-------

Please see [License File](LICENSE) for more information.
