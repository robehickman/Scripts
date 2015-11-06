<?php
class mdl_files extends database
{
    function __CONSTRUCT()
    {
        $this->table = "files";
    }

    function get_by_title($title)
    {
        return $this->sql_to_array(
            $this->query("select * from `files` where `Title` = '@v'", $title));
    }

    function create($title, $size, $mime, $is_image)
    {
        $this->query("
            insert into files set
                `Title`  = '@v',
                `Size`   = '@v',
                `Mime`   = '@v',
                `Is_img` = '@v'",
             $title, $size, $mime, $is_image);
    }

/*
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
*/
}
