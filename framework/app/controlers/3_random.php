<?php
/*
This file displays a random number, unlike the previous
examples it makes use of a model and a view.
*/
function register_random($handler, $navigation)
{
    $handler->register_handler(
        "Random",
        "display_random");

    $navigation->add_item("Random number",
        make_url("Random"));
}

function display_random()
{
/*
The instance_model function is used to create an instance
of a model class. This class containes one method which
returns a pseudo random number. From this point, plese
switch to the file: models/random_number.php
*/
    $random_model = instance_model("random_number");
    $random = $random_model->random();

/*
Now the random number which was generated by the random
model is displayed in a template. Templates form the
``view'' part of the MVC framework, they contain the
HTML which gets displayed to the browser. Views live
in the ``views'' directory and can be loaded using the
instance_view function. This takes the name of the view
file, minus the extension as an argument and returns
an instance of the frameworks view class with the
template file loaded.

To convert a template into a string the parse_to_variable
method is used, this executes the template as a PHP script
and returns the result as a string. Variables can be passed
along to the template by passing an assisiative array to
this method. The key part of the array is used to set the
name of the variable and the value is used to pass a value.
In the template these are scoped as regular PHP variables.

Pleese take a look at the file views/random_number.tpl
*/
    $content = instance_view("random_number");
    $content = $content->parse_to_variable(array(
        'random' => $random));

/*
Here the parsed template is displayed onto the page.
*/
    return make_return("Random number", $content);
}

/*
Try changing the tamplate to make it display something
different.
*/