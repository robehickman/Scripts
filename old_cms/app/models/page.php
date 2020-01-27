<?php
class mdl_page extends database
{
    function __CONSTRUCT()
    {
        $this->table = "pages";
    }

    function get_page_by_title($title)
    {
        return $this->sql_to_array(
            $this->query("select * from `pages` where `Title` = '@v'", $title));
    }

    function get_page_by_clean_title($title)
    {
        return $this->sql_to_array(
            $this->query("select * from `pages` where `Clean_title` = '@v'", $title));
    }

    function create($title, $clean_title)
    {
        $query = "insert into `pages` set
            `Title` = '@v',
            `Clean_title` = '@v'";
        $this->query($query, $title, $clean_title);
    }
}
