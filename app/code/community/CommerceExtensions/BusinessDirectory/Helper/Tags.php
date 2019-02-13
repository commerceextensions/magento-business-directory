<?php

class CommerceExtensions_BusinessDirectory_Helper_Tags extends Mage_Core_Helper_Abstract
{
  public $tags = array(
    'name'          => 'name',
    'address'       => 'streetAddress',
    'streetaddress' => 'streetAddress',
    'region'        => 'addressRegion',
    'state'         => 'addressRegion',
    'locality'      => 'addressLocality',
    'city'          => 'addressLocality',
    'postalcode'    => 'postalCode',
    'zipcode'       => 'postalCode',
    'country'       => 'addressCountry',
    'employee'      => 'employee',
    'phone'         => 'telephone',
    'fax'           => 'faxNumber',
    'url'           => 'url',
    'email'         => 'email',
    'latitude'      => 'latitude',
    'longitude'     => 'longitude',
    'img'           => 'image'

  );

  public function processCustomTags($string, array $arguments = array())
  {
    return $this->replaceMatches($string, $arguments);
  }

  public function replaceMatches($string, array $arguments = array())
  {
    $filterModel  = Mage::getModel('businessdirectory/directory_staticfilter');
    $filterFields = array_keys($filterModel->ALLOWABLE_FILTERS);
    $params       = Mage::app()->getRequest()->getParams();

    $replacements                   = array();
    $replacements['[[store_name]]'] = Mage::getStoreConfig('general/store_information/name');

    if(!empty($arguments)) {
      foreach($arguments as $placeholder => $value) {
        if($placeholder == 'listing_state') {
          $replacements['[[' . $placeholder . ']]'] = trim(Mage::helper('businessdirectory')->convertStateAbbreviation(trim($value), 'US'));
        } else {
          $replacements["[[" . $placeholder . "]]"] = trim($value);
        }
      }
    }

    foreach($filterFields as $field) {
      if(array_key_exists($field, $params)) {
        if($field == 'listing_state') {
          $replacements['[[' . $field . ']]'] = trim(Mage::helper('businessdirectory')->convertStateAbbreviation(trim($params[$field]), 'US'));
        } else {
          $replacements['[[' . $field . ']]'] = trim($params[$field]);
        }
      }
    }
    return str_ireplace(array_keys($replacements), array_values($replacements), $string);
  }

  public function addSchemaTag($tagType, $content = null)
  {
    $tags    = $this->tags;
    $content = !is_null($content) ? 'content="' . $content . '"' : null;
    return ' itemprop="' . $tags[$tagType] . '"';
  }

  public function wrapWithSchemaTag($string, $tagType, $content = null)
  {
    if(!is_null($string) && $string != '') {
      $tags = $this->tags;
      if(!$content) {
        return '<span itemprop="' . $tags[$tagType] . '">' . $string . '</span>';
      } else {
        return '<span itemprop="' . $tags[$tagType] . '" content="' . $content . '">' . $string . '</span>';
      }
    }
    return null;
  }

  public function addSchemaTagMeta($content, $tagType)
  {
    if(!is_null($content) && $content != '') {
      return '<meta itemprop="' . $this->tags[$tagType] . '" content="' . $content . '" />';
    }
    return null;
  }

}