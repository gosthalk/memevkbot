<?php
declare(strict_types=1);

namespace App;

class Utility
{
    public function getPostsIds($posts)
    {
        $ids = [];
        for($i=0;$i<count($posts['response']['items']);$i++){
            $ids[] = $posts['response']['items'][$i]['id'];
        }

        return $ids;
    }
}