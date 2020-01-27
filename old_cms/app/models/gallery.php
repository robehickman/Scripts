<?php
class mdl_gallery extends database
{
    function __CONSTRUCT()
    {
        $this->table = "gallery";
    }

    function get_in_set($set_id)
    {
        return $this->sql_to_array(
            $this->query("select * from `gallery` where `Set` = '@v'", $set_id));
    }

    function create($set_id, $file)
    {
        $this->query("
            insert into `gallery` set
                 `Set` = '@v',
                 `File` = '@v'",
            $set_id, $file
        );
    }

    function delete_gallery($set_id, $clean_user_name)
    {
        $gallery = $this->get_in_set($set_id);

        $root_path = 'res/gallery/';

        foreach($gallery as $image)
        {
            unlink($root_path . $clean_user_name . '/' . $image['File']);
            unlink($root_path . $clean_user_name . '/thumbs/' . $image['File']);
            $this->delete_by_id($image['ID']);
        }
    }
}
