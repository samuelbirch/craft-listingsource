<?php
/**
 * Listing Source plugin for Craft CMS
 *
 * ListingSource Service
 *
 * @author    Kurious Agency
 * @copyright Copyright (c) 2018 Kurious Agency
 * @link      https://kurious.agency
 * @package   ListingSource
 * @since     1.0.0
 */

namespace Craft;

class ListingSourceService extends BaseApplicationComponent
{
    protected $plugin;
    protected $pluginHandle;
    
    public function __construct()
    {
        $this->pluginHandle = 'listingSource';
        $this->plugin = craft()->plugins->getPlugin($this->pluginHandle);
    }

    public function getElementSources()
    {
        return array(
            'entry' => $this->_getSections('structure'),
            'channel' => $this->_getSections('channel'),
        );
    }

    private function _getSections($type)
    {
        $channels = craft()->sections->getAllSections();
        $sources = array();

        foreach ($channels as $source)
        {
            if (!isset($source->heading) && $source->type == $type)
            {
                $sources[] = array(
                    'label' => $source->name,
                    'value' => $source->id,
                );
            }
        }
        return $sources;
    }

}