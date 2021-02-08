<?php

namespace Dynamic\YouTubePlaylist\Page;

use SilverStripe\ORM\PaginatedList;

/**
 * Class YouTubeGalleryPage_Controller
 */
class YouTubeGalleryPageController extends \PageController
{

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'Playlist',
    );

    /**
     *
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return PaginatedList
     */
    public function Playlist()
    {
        $list = $this->data()->getYoutubePlaylist();
        return PaginatedList::create($list, $this->request)
            ->setPageLength(($this->data()->VideosPerPage) ? $this->data()->VideosPerPage : 8);
    }

}
