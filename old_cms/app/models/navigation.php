<?php
class mdl_navigation extends database
{
    function __CONSTRUCT()
    {
        $this->table = "navigation";
        $this->sort_col = "Order";
    }

    function create($title)
    {
        $this->query("
            insert into `navigation` set
                 `Title` = '@v',
                 `Type`  = 'url'",
            $title
        );
    }
}
