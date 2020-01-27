<?php
class mdl_members extends database
{
    function __CONSTRUCT()
    {
        $this->table = "members";
    }

    function get_in_category($id)
    {

        $m_set = instance_model('gallery_set');
        $m_members = instance_model('members');

    // Get sets in category
        $set = $m_set->get_by_category($id);

        $listed_users = array();
        $cat_contents = array();
        
        foreach($set as $itm)
        {
            $i_member = $m_members->get_by_id($itm['Owner']);

        // Merge in members
            if(count($i_member) > 0 && !in_array($i_member[0]['ID'], $listed_users))
                $cat_contents = array_merge($cat_contents, $i_member);
        }

        return $cat_contents;
    }

    function get_by_title($title)
    {
        return $this->sql_to_array(
            $this->query("select * from `members` where `Title` = '@v'", $title));
    }

    function get_by_clean_title($title)
    {
        return $this->sql_to_array(
            $this->query("select * from `members` where `Clean_title` = '@v'", $title));
    }

    function create($title, $clean_title)
    {
        $query = "insert into `members` set
            `Title` = '@v',
            `Clean_title` = '@v'";
        $this->query($query, $title, $clean_title);
    }
}
