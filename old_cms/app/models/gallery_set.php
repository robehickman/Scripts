<?php
class mdl_gallery_set extends database
{
    function __CONSTRUCT()
    {
        $this->table = "gallery_set";
    }

    function get_by_user($user)
    {
        return $this->sql_to_array(
            $this->query("select * from `gallery_set` where `Owner` = '@v'", $user));
    }

    function get_by_title($title, $user)
    {
        return $this->sql_to_array(
            $this->query("select * from `gallery_set` where `Title` = '@v' and `Owner` = '@v'", $title, $user));
    }

    function get_by_category($category)
    {
        return $this->sql_to_array(
            $this->query("select * from `gallery_set` where `Category` = '@v'", $category));
    }

    function get_by_category_and_user($category, $user)
    {
        return $this->sql_to_array(
            $this->query("select * from `gallery_set` where
                `Category` = '@v' and
                `Owner` = '@v'",
                $category, $user));
    }

    function create($owner, $title, $clean_title)
    {
        $query = "insert into `gallery_set` set
            `Owner` = '@v',
            `Title` = '@v',
            `Clean_title` = '@v'";
        $this->query($query, $owner, $title, $clean_title);
    }

    function delete_set($id, $clean_user_name)
    {
        $m_gallery = instance_model('gallery');
        $m_gallery->delete_gallery($id, $clean_user_name);

        $this->delete_by_id($id);
    }

/*

    function get_page_by_clean_title($title)
    {
        return $this->sql_to_array(
            $this->query("select * from `pages` where `Clean_title` = '@v'", $title));
    }

*/
}
