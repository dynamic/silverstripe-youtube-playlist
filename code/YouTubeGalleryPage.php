<?php

/**
 * Class YouTubeGalleryPage
 */
class YouTubeGalleryPage extends Page
{

    /**
     * @var string
     */
    private static $singular_name = 'YouTube Gallery Page';
    /**
     * @var string
     */
    private static $plural_name = 'YouTube Gallery Pages';
    /**
     * @var string
     */
    private static $description = 'A page that shows a gallery of videos from a playlist';

    /**
     * @var
     */
    private static $api_key;

    /**
     * @var
     */
    private static $application_name;

    /**
     * @var array
     */
    private static $db = array(
        'PlaylistID' => 'Varchar(255)',
        'VideosPerPage' => 'Int',
    );

    /**
     * @var array
     */
    private static $indexes = array(
        'PlaylistID' => true,
    );

    private static $playlistItems = array();
    private static $playlist;

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab(
            'Root.YoutubeSettings',
            TextField::create('PlaylistID')
                ->setTitle('YouTube Playlist ID')
        );

        $fields->addFieldToTab(
            'Root.YoutubeSettings',
            NumericField::create('VideosPerPage')
                ->setTitle('Videos Per Page')
        );

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    /**
     * @return ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();

        if ($exists = YouTubeGalleryPage::get()->filter('PlaylistID', $this->PlaylistID)->exclude('ID',
            $this->ID)->first()
        ) {
            $result->error("That playlist is already used on page: {$exists->Title}.");
        }

        return $result;
    }

    /**
     * @param null $playlistID
     * @return bool
     */
    protected static function getBasePlaylistData($playlistID = null)
    {
        if ($playlistID === null) {
            return false;
        }

        $key = self::getAPIKey();
        $client = new Google_Client();
        $client->setApplicationName('Dynamic Miller STN Test');
        $client->setDeveloperKey($key);
        $service = new Google_Service_YouTube($client);
        //todo allow for customization of the call (max, multiple ID's, etc)
        //see https://developers.google.com/youtube/v3/docs/playlistItems/list
        $results = $service->playlistItems->listPlaylistItems('snippet',
            array('playlistId' => $playlistID, 'maxResults' => 50));
        $results = $results['items'];
        foreach ($results as $result) {
            $snippet = $result['snippet'];
            $video = YouTubeVideo::create();
            $video->Title = $snippet['title'];
            $video->Thumbnail = $snippet['thumbnails']['default']['url'];
            $video->ThumbnailWidth = $snippet['thumbnails']['default']['width'];
            $video->ThumbnailHeight = $snippet['thumbnails']['default']['height'];
            $video->URL = self::generateYouTubeLink($snippet['resourceId']['videoId']);
            self::$playlistItems[] = $video;
        }

    }

    /**
     * @param null $items
     * @return bool
     */
    protected static function getAdditionalVideoInformation($items = null)
    {
        if ($items === null) {
            return false;
        }

        //todo add options for additional info to be queried per video

        self::$playlist = $items;
    }

    /**
     * @param null $playlistID
     * @return bool
     */
    public static function getPlaylistVideos($playlistID = null)
    {
        if ($playlistID === null) {
            return false;
        }

        self::getBasePlaylistData($playlistID);
        self::getAdditionalVideoInformation(self::$playlistItems);

        return self::$playlist;
    }

    /**
     * @return mixed
     */
    public function getVideos()
    {
        return ArrayList::create(self::getPlaylistVideos($this->PlaylistID));
    }

    /**
     * @param null $url
     * @return bool|string
     */
    protected static function generateYouTubeLink($url = null)
    {
        return ($url === null) ? false : '//www.youtube.com/watch?v=' . $url;
    }

    /**
     * @return array|scalar
     */
    protected static function getAPIKey()
    {
        return Config::inst()->get('YouTubeGalleryPage', 'api_key');
    }

}

/**
 * Class YouTubeGalleryPage_Controller
 */
class YouTubeGalleryPage_Controller extends Page_Controller
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
        $list = $this->data()->getVideos();
        return PaginatedList::create($list, $this->request)
            ->setPageLength(($this->data()->VideosPerPage) ? $this->data()->VideosPerPage : 8);
    }

}