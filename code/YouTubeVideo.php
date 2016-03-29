<?php

/**
 * Class YouTubeVideo
 */
class YouTubeVideo extends ViewableData
{

    /**
     * @var string
     */
    private static $singular_name = 'YouTube Video';
    /**
     * @var string
     */
    private static $plural_name = 'YouTube Videos';
    /**
     * @var string
     */
    private static $description = 'A YouTube video fetched via the YouTube API v3';

    /**
     * @var array
     */
    private static $casting = array(
        'Title' => 'Text',
        'Thumbnail' => 'Text',
        'ThumbnailWidth' => 'Int',
        'ThumbnailHeight' => 'Int',
        'PlayerURL' => 'Text'
    );

}