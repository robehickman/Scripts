<?php

    function display_navigation()
    {
        $m_navi = instance_model('navigation');
        $navi = $m_navi->get_all('Order');

        $m_page = instance_model('page');

        $output = array();
        foreach($navi as $row)
        {
            $out_title = '';
            $out_url   = '';

            if($row['Type'] == 'page')
            {
                $page = $m_page->get_by_id($row['Data']);

                if($page == array())
                {
                    $out_title = '[Not Found]';
                    $out_url   = '#';
                }
                else
                {
                    $out_title = $row['Title'];
                    $out_url   = make_url('page', $page[0]['Clean_title']);
                
                }
            }
            else if($row['Type'] == 'url')
            {
                $out_title = $row['Title'];
                $out_url   = $row['Data'];
            }

            $output []= array(
                'title' => $out_title,
                'url'   => $out_url);
        }

        $view = instance_view('navigation');
        $view->parse(array(
            'navi' => $output
        ));
    }
