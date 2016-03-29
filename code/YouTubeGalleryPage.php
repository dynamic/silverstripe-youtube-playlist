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

    private $playlist;

    /**
     * @param ArrayList|null $playlist
     * @return $this
     */
    public function setYoutubePlaylist($playlist = null)
    {
        if ($playlist === null) {
            $this->buildYoutubePlaylist();
        }
        $this->playlist = $playlist;
        return $this;
    }

    /**
     * @return bool|ArrayList
     */
    public function getYoutubePlaylist()
    {
        if (!$this->playlist) {
            $this->buildYoutubePlaylist();
        }
        return $this->playlist;
    }

    /**
     * 
     */
    protected function buildYoutubePlaylist()
    {
        $list = ArrayList::create();

        $client = new Google_Client();
        $client->setApplicationName($this->config()->get('application_name'));
        $client->setDeveloperKey($this->config()->get('api_key'));
        $service = new Google_Service_YouTube($client);
        $results = $service->playlistItems->listPlaylistItems('snippet',
            array('playlistId' => $this->PlaylistID, 'maxResults' => 50));
        $results = $results['items'];

        $pushVideo = function ($video) use (&$list) {
            if ($parsedVideo = YouTubeVideo::parse_video_data($video)) {
                $list->push($parsedVideo);
            }
        };

        foreach ($results as $result) {
            $pushVideo($result);
        }

        $this->setYoutubePlaylist($list);
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
        $list = $this->data()->getYoutubePlaylist();
        return PaginatedList::create($list, $this->request)
            ->setPageLength(($this->data()->VideosPerPage) ? $this->data()->VideosPerPage : 8);
    }

}