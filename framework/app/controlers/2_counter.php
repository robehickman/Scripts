<?php
/*
This file implements a basic reload counter. Like the
``hello world'' example the first thing to define is the
controller modules register function. You should be able
to work out what is going on here.
*/
function register_counter($handler, $navigation)
{
    $handler->register_handler(
        "Counter",
        "counter");

    $navigation->add_item("Reload counter",
        make_url("Counter"));
}

/*
This function implements a controller function which counts
the number of times the page has bean reloaded. It is not
a hit counter becouse it uses session variables to store
the counter, so will reset whenever the browser is reloaded.
*/
function counter()
{

/*
As already stated, a session variable will be used to store
the counter. Session variables are a basic persistent data
store which remain across page reloads. Howeaver the data is
lost if the browser is closed.

The first thing the function needs to do is check if the
session variable has bean created, and if it hasent set
it to -1.
*/
    if(!isset($_SESSION['counter']))
        $_SESSION['counter'] = -1;

/*
Now that the session variable exists for sure, it is incremented
to register a page reload. This is the reason why the counter
is initialised to -1 instead of 0. The first time the page
loads it is incremented so the value 0 is displayed.
*/
    $_SESSION['counter'] ++;

/*
Finally the reload count is displayed on the page.
*/
    return make_return("Reload counter", 
        "Page has bean reloaded ".$_SESSION['counter']." times.");
}

/*
Try adding a second controller which starts at 100 then counts down
every time the page is reloaded.
*/
