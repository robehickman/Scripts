<?php
/*
A simple demo controller module which implements the classic
``hello world'' program.
 
First the register function is implemented, All controler modules
must implement a register function, the name of which is the file
name prefixed with the string ``register''. When the framework
loads the first thing it does is call all of the controller modules
register functions. The frameworks handler and navigation object
are passed to this function as paramiters.
*/
function register_hello($handler, $navigation)
{
/*
The controllers within the framework are adressed using URL's which
take the format:

    /path_to_framework/page/section/id.html

if mod_rewrite is enabled in index.php and your apache config or:

    /path_to_framework/index.php?page=something&section=something&id=something

if mod_rewrite is not enabled. The generation of URL's in the correct
format is handles automatically by the frameworks make_url function.

The part of the URL which you need to pay attention to here is the page
section. This consists of a text string which is mapped to a controller
function using the register_handler method of the dispatcher object.

Here the string ``Hello World'' is mapped to the controller function
``hello_world". This means that wnenever the framework is sent to
page ``Hello_World'' using ither of the URL formats, the function
``hello_world'' will be called.
*/
    $handler->register_handler(
        "Hello World",
        "hello_world");

/*
Here a link is added to the navigation to link to the controller
function. Items can be added to the navigation using the add_item
method of the navigation object which is passed to the register
function. Add item takes two arguments, the string to display
on the page and the URL to link to. You could, for example do
this to add a link to the navigation which links to google:

    add_item("Google", http://www.google.com");

Within the framework the make_url function is used to generate the
URL's in the correct format.
 */
    $navigation->add_item("Hello, World",
        make_url("Hello World"));
}

/*
This function implements the function which was registered above.
 */
function hello_world()
{
/*
The results displayed on the page need to be returned form the
controller function as an assosiative array. The make_return
function which is part of the frameworks API can be used to
generate an array in the correct format. The first argument
of this funciton sets the page title and second argument sets
the content. Normally the second argument is generated from
a template, but for the sake of simplisity it is inlined
for this example.
*/
    return make_return(
        "Hello, World",
        "<p>This is a simple controller.</p>");
}

/*
As an excersise, try changing the text to make the program
display something else.
*/
