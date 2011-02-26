<?php
/*
This is the model class for the random.php controller, it
contains one method which returns a random number. This
is an excessivily simple example.
*/
class random_number
{
/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Generate a random number
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function random()
    {
        return rand();
    }
}
