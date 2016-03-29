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

    /**
     * @param array $data
     * @return bool
     */
    public static function parse_video_data($data = array())
    {
        if (empty($data)) {
            return false;
        }

        $snippet = $data['snippet'];
        $video = YouTubeVideo::create();
        $video->Title = $snippet['title'];
        $video->Thumbnail = $snippet['thumbnails']['default']['url'];
        $video->ThumbnailWidth = $snippet['thumbnails']['default']['width'];
        $video->ThumbnailHeight = $snippet['thumbnails']['default']['height'];
        $video->URL = self::generateYouTubeLink($snippet['resourceId']['videoId']);

        return $video;
    }

    /**
     * @param null $url
     * @return bool|string
     */
    protected static function generateYouTubeLink($url = null)
    {
        return ($url === null) ? false : '//www.youtube.com/watch?v=' . $url;
    }

}