Introduction
    This is a simple PHP MVC framework intended to introduce
    the MVC design pattern to new PHP programmers and demonstrate
    how a PHP MVC framework can be implemented. It can also be
    used as a small framework for developing simple applications
    where the features and bloat of a full blown framework is
    not required. 

    As it is designed to be as simple as possible, common features
    like form abstraction layers and object relational mapping are
    not provided. Programming with this framework is exactly
    like programming with plain PHP, with the added benefit of
    the MVC structure, which produces cleaner, more maintainable
    code.

    This framework is released under the GNU General Public Licence
    and is free/open source software. You are free to use, modify
    and redistribute it under the terms of the GNU GPL, please see
    the file ``COPYING'' for more information.

Examples
    A number of usage examples are provided with the framework in
    the app/controllers directory.

An overview of Model-View-Controller
    Model-View-Controller is a design pattern which lends itself
    extremely well to the development of complex web applications.
    MVC specifies that an application is divided into 3 components,
    a model, view and controller.

    The Model defines a representation of the applications data
    and the operations which can be preformed on it, such as the
    typical create, read, update, delete. Models can operate on
    data from any source, the common ones being a database, files
    and internet URL's.

    The View defies the interface for an application. On the web
    the View is the HTML which will be displayed to the user.

    The Controller contains all of the applications busings logic
    and interfaces with the Model to read or store data and the
    View to display an interface to the user.

    MVC allows for large, complicated applications to be developed
    with ease because code is separated into a clean logical
    structure. This also increases the reuseability of code, reducing
    the total volume of code which has to be written.

Framework API documentation
    File structure
        app
            Contains the applications models, views and controllers.

        theme
            Contains the outer template, navigation CSS and any general
            layout images. Look here if you want to change the general
            look of the application.

        src
            Contains the frameworks source code.


    Configuration options
        All of the frameworks configuration options are defined within
        the index.php file, please see this file for documentation.

    Built in callbacks
        framework_error_callback($error_string)
            If a function is defined with the name
            `framework_error_callback' it will be called if an error
            is raised.

    General functions
        make_url($page, (optional)$section, (optional)$id)
            Generates a URL relative to the framework in the correct
            format depending if use_rewrite is enabled or not.

        get_site_index()
            Returns an array of the frameworks URL parameters, page,
            sect and id. These are set to the parameter if it exists
            in the URL or NULL if it does not.

        get_current_path()
            Returns the path to the root of the framework, allows
            applications to be written that will work regardless of
            the frameworks location in the file system.

        raise_error($error_string)
            Instantly stops the execution of the application and
            displays an error message. Takes error message string
            as an argument.

        
        
    Controller

        navigation class
            add_item($text, $target)
                Adds an item to the navigation, the text parameter
                defines the text to display on the page and the
                target defines the URL of the link. This can be
                combined with make_url to link to controllers in the
                framework.

        dispatcher (handler) class
            register_handler($page, $function)
                Registers a function to handle the URL provided by 
                the page parameter.

        make_return($title, $content, (optional)$ajax = false)
            Used with a return statement to return the page title and
            content from a controller. If the optional parameter `ajax'
            is set to true, the framework does not display the outer
            (theme) template and the $title parameter is ignored.

        instance_model($model_name)
            Creates an instance of a model class contained in the models
            directory.

        instance_view($view_name)
            Creates an instance of the view class with the view file
            loaded from the views directory.

    Model

        `database' class methods

            sql_to_array($mysql_resource)
                Converts a mysql resource into an array. 

            query($query_string, (optional)$arg1, ...)
                Variable argument function.
                Takes a query string containing @v tokens and replaces
                the tokens with arguments provided in the argument list
                after running them through mysql_real_escape_string.
                Using this method removes the need to manually escape
                variables used in database queries.
            

    View
        `view' class methods
            parse_to_variable($argument_array)
                Executes a template file as a PHP script then returns the result
                as a string. Arguments can be passed to the template as an associative
                array, where the key is the variable name and the value is the
                value of the variable.

            parse($argument_array)
                Functionally identical to the above, however the output goes directly
                to the client instead of being saved as a variable.
