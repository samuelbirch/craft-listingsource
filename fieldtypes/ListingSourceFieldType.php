<?php
/**
 * Listing Source plugin for Craft CMS
 *
 * ListingSource FieldType
 *
 * @author    Kurious Agency
 * @copyright Copyright (c) 2018 Kurious Agency
 * @link      https://kurious.agency
 * @package   ListingSource
 * @since     1.0.0
 */

namespace Craft;

class ListingSourceFieldType extends BaseFieldType
{
    /**
     * @return mixed
     */
    public function getName()
    {
        return Craft::t('ListingSource');
    }

    /**
     * @return mixed
     */
    public function defineContentAttribute()
    {
        return AttributeType::Mixed;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return string
     */
    public function getInputHtml($name, $value)
    {
        if (!$value)
            $value = new ListingSourceModel();

        $id = craft()->templates->formatInputId($name);
        $namespacedId = craft()->templates->namespaceInputId($id);

/* -- Include our Javascript & CSS */

        craft()->templates->includeCssResource('listingsource/css/fields/ListingSourceFieldType.css');
        craft()->templates->includeJsResource('listingsource/js/fields/ListingSourceFieldType.js');
        craft()->templates->includeJs('new ListingSource("'.craft()->templates->namespaceInputId($id).'");');

        // Settings
    	$settings = $this->getSettings();

    	// Types
		$availableTypes = $this->_getTypes();
		$types = array('' => Craft::t('Source...'));

       	if(is_array($settings['types']))
    	{
			foreach($settings['types'] as $type)
			{
				$types[$type] = $availableTypes[$type];
			}
    	}
    	else
    	{
	    	$types = $types + $availableTypes;
        }

        
        $channels = [];
        $entries = [];
        $sections = craft()->sections->getAllSections();
        foreach($sections as $section){
            if($section->type == 'channel'){
                if($settings->channelSources == '*' || in_array($section->id, $settings->channelSources)){
                    $channels[] = [
                        'label' => $section->name,
                        'value' => $section->handle,
                    ];
                }
            }
            if($section->type == 'structure'){
                if($settings->entrySources == '*' || in_array($section->id, $settings->entrySources)){
                    $entries[] = 'section:'.$section->id;
                }
            }
        }
        
        // Element Select Options
        $elementSelectSettings = array(
            'entry' => array(
                'elementType' => new ElementTypeVariable( craft()->elements->getElementType(ElementType::Entry) ),
                'elements' => $value && $value->entry ? array($value->entry) : null,
                'sources' => $entries,
                'criteria' => array(
                    'status' => null,
                ),
                'sourceElementId' => null,
                'limit' => 1,
                'addButtonLabel' => Craft::t($settings->entrySelectionLabel),
                'storageKey' => 'field.'.$this->model->id,
            ),
            'channel' => array(
                'options' => $channels,
            ),
        );

/* -- Variables to pass down to our field.js */

        $jsonVars = array(
            'id' => $id,
            'name' => $name,
            'namespace' => $namespacedId,
            'prefix' => craft()->templates->namespaceInputId(""),
            );

        $jsonVars = json_encode($jsonVars);
        //craft()->templates->includeJs("$('#{$namespacedId}-field').ListingSourceFieldType(" . $jsonVars . ");");

/* -- Variables to pass down to our rendered template */

        $variables = array(
            'id' => $id,
            'name' => $name,
            'namespaceId' => $namespacedId,
            'value' => $value,
            'settings' => $settings,
            'types' => $types,
            'elementSelectSettings' => $elementSelectSettings,
        );

        return craft()->templates->render('listingsource/fields/input.twig', $variables);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function prepValueFromPost($value)
    {
        if( is_array($value) && $value['type'] != '' )
        {
    		return json_encode($value);
        }
        else
        {
            return '';
        }
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function prepValue($value)
    {
        return $this->_valueToModel($value);
    }


    public function validate($value)
    {
        if(is_array($value) && $value['type'] != '')
        {
            $sourceModel = $this->_valueToModel($value);
            $validated = $sourceModel->validate();
            return $validated ? true : $sourceModel->getAllErrors();
        }
        parent::validate($value);
    }


    public function getSettingsHtml()
    {
        return craft()->templates->render('listingsource/fields/settings', array(
            'settings'                  => $this->getSettings(),
            'types'                     => $this->_getAvailableTypes(),
            'elementSources'            => craft()->listingSource->getElementSources(),
        ));
    }


    protected function getSettingsModel()
    {
        return new ListingSource_SettingsModel();
    }



    private function _getAvailableTypes()
    {
        $types = $this->_getTypes();
        $sources = craft()->listingSource->getElementSources();
        if(!$sources['entry'])
        {
            unset($types['entry']);
        }
        if(!$sources['channel'])
        {
            unset($types['channel']);
        }
        return $types;
    }

    private function _getTypes()
    {
        return array(
			'channel' => Craft::t('Channel'),
			'entry' => Craft::t('Entry'),
        );
        
    }

    private function _valueToModel($value, $settings = false)
    {
        if( is_array($value) && $value['type'] != '' )
        {
            $settings = $settings ? $settings : $this->getSettings();

            $source = new ListingSourceModel;

            $source->type = isset($value['type']) && $value['type'] != '' ? $value['type'] : false;
            $source->value = $source->type ? (is_array($value[$source->type]) ? $value[$source->type][0] : $value[$source->type]) : false;

            return $source;
        }

        return '';
    }
}