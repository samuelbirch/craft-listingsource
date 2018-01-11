<?php
/**
 * Listing Source plugin for Craft CMS
 *
 * ListingSource_Settings Model
 *
 * @author    Kurious Agency
 * @copyright Copyright (c) 2018 Kurious Agency
 * @link      https://kurious.agency
 * @package   ListingSource
 * @since     1.0.0
 */

namespace Craft;

class ListingSource_SettingsModel extends BaseModel
{
    /**
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'types' => AttributeType::Mixed,

            'entrySources' => AttributeType::Mixed,
            'entrySelectionLabel' => array(AttributeType::String, 'default' => Craft::t('Select an entry')),

            'channelSources' => AttributeType::Mixed,
            'channelSelectionLabel' => array(AttributeType::String, 'default' => Craft::t('Select a channel')),
        ));
    }

    public function validate($attributes = null, $clearErrors = true)
    {
        parent::validate($attributes, $clearErrors);

        if(is_array($this->types))
        {
            if( in_array('entry', $this->types) && $this->entrySources == '')
            {
                $this->addError('entrySources', Craft::t('Please select at least 1 entry source.'));
            }

            if( in_array('channel', $this->types) && $this->channelSources == '')
            {
                $this->addError('channelSources', Craft::t('Please select at least 1 channel.'));
            }
        }
        else
        {
            $this->addError('types', Craft::t('Please select at least 1 source type.'));
        }

        return !$this->hasErrors();
    }
}