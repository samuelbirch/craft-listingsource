<?php
/**
 * Listing Source plugin for Craft CMS
 *
 * ListingSource Model
 *
 * @author    Kurious Agency
 * @copyright Copyright (c) 2018 Kurious Agency
 * @link      https://kurious.agency
 * @package   ListingSource
 * @since     1.0.0
 */

namespace Craft;

class ListingSourceModel extends BaseModel
{
    private $_entry;
    private $_channel;
    private $_data;
    private $_criteria;
    
    /**
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'type' => array(AttributeType::String, 'default' => false),
            'value' => array(AttributeType::String, 'default' => false),
        ));
    }

    public function getElement()
    {

        switch ($this->type)
        {
            case('entry'):
                $element = $this->entry;
                break;
            case('channel'):
                $element = $this->channel;
                break;
            default:
                $element = false;
        }
        return $element;
    }

    public function getEntry()
    {
        if($this->type != 'entry')
        {
            return false;
        }

        if(!$this->_entry)
        {
            $id = is_array($this->value) ? $this->value[0] : false;
            if( $id && $entry = craft()->entries->getEntryById($id) )
            {

                $this->_entry = $entry;
            }
        }
        return $this->_entry;
    }

    public function getChannel()
    {
        if($this->type != 'channel')
        {
            return false;
        }

        if(!$this->_channel)
        {
            $id = is_array($this->value) ? $this->value[0] : false;
            if( $id && $channel = craft()->sections->getSectionByHandle($id) )
            {

                $this->_channel = $channel;
            }
        }
        return $this->_channel;
    }

    public function validate($attributes = null, $clearErrors = true)
    {
        switch($this->type)
        {
            case('entry'):
                if($this->value == '')
                {
                    $this->addError('value', Craft::t('Please select an entry.'));
                }
                break;
            case('channel'):
                if($this->value == '')
                {
                    $this->addError('value', Craft::t('Please select a channel.'));
                }
                break;
        }

        return !$this->hasErrors();
    }

    public function getCriteria()
    {
        if(!$this->_criteria){

            $criteria = craft()->elements->getCriteria(ElementType::Entry);

            switch($this->type)
            {
                case('entry'):
                    $criteria->descendantOf = $this->value;
                    $criteria->descendantDist = 1;
                    break;
                case('channel'):
                    $criteria->section = $this->value;
                    break;
            }
            $this->_criteria = $criteria;
        }

        return $this->_criteria;
    }

    public function getData()
    {
        if(!$this->_data){

            $this->_data = $this->getCriteria()->find();
        }

        return $this->_data;
    }
}