<?php
/*
This example implements a simple application which adds
two numbers together. It bilds on the concepts demonstrated
in the previous examples.

Two controllers are used, one displays the HTML form alowing
the user to input two numbers and the outher adds the numbers
and displays the result.
*/

function register_add($handler, $navigation)
{
    $handler->register_handler(
        "Display add", "display_add_numbers");

    $handler->register_handler(
        "Calculate add", "add_numbers");

    $navigation->add_item("Add Two Numbers",
        make_url("Display add"));
}

/*
This controller function simply displays the form. The URL
which the form will send its data to is passed as an argument
to the template.
*/
function display_add_numbers()
{
    $content = instance_view("add_numbers_form");
    $content = $content -> parse_to_variable(array(
        'target' => make_url("Calculate add")
        ));

    return make_return("Add Two Numbers", $content);
}

/*
This is the more interesting part of this application, this
function retreaves the two numbers from the form, adds
them together and displays them in a template.
*/
function add_numbers()
{

/*
Here the two numbers are retreaved from the form and are
saved into two variables.
*/
    $num1 = $_POST['num1'];
    $num2 = $_POST['num2'];
	
/*
The data is validated using is_numeric to make sure that
the values provided by the user actually are numbers.
If they are not an error is raised, this instantly
stops execution of the function and displays an error
message to the user.
*/
    if(!is_numeric($num1) || !is_numeric($num2))
        raise_error("Values must be numeric");


/*
After the data has bean validated, the numbers are added
and the result is displayed in a template.
*/
	$result = $num1 + $num2;
	
    $content = instance_view("add_numbers_result");
    $content = $content -> parse_to_variable(array(
        'result' => $result));

    return make_return("Result", $content);
}

/*
Try adding a 3rd form so that 3 numbers can be added together.
*/
