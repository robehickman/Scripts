<?php
class mdl_member_categories extends database
{
    function __CONSTRUCT()
    {
        $this->table = "member_category";
        $this->sort_col = "Order";
    }

    function get_by_name($name)
    {
        return $this->sql_to_array(
            $this->query("select * from `member_category` where `Name` = '@v'", $name));
    }

    function get_by_clean_name($name)
    {
        return $this->sql_to_array(
            $this->query("select * from `member_category` where `Clean_name` = '@v'", $name));
    }

    function create($title, $clean_title)
    {
        $query = "insert into `member_category` set
            `Name` = '@v',
            `Clean_name` = '@v'";
        $this->query($query, $title, $clean_title);
    }
}
