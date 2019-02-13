<?php

class CommerceExtensions_BusinessDirectory_Helper_Data extends Mage_Core_Helper_Abstract
{

  public $states = array(
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AS' => 'American Samoa',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'DC' => 'District of Columbia',
    'FM' => 'Federated States of Micronesia',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'GU' => 'Guam',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MH' => 'Marshall Islands',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'MP' => 'Northern Mariana Islands',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PW' => 'Palau',
    'PA' => 'Pennsylvania',
    'PR' => 'Puerto Rico',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'AE' => 'U.S. Armed Forces Europe',
    'AA' => 'U.S. Armed Forces Americas',
    'AP' => 'U.S. Armed Forces Pacific',
    'UM' => 'U.S. Minor Outlying Islands',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VI' => 'Virgin Islands',
    'VA' => 'Virginia',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming',
  );

  public function getImageUrl($filepath)
  {
    return Mage::getBaseUrl('media') . DS . $filepath;
  }

  public function getCountryName($countryCode)
  {
    if($countryCode)
      return Mage::app()->getLocale()->getCountryTranslation($countryCode);
    else
      return null;
  }

  public function renderAddress($_listing, $type = null, $showCountry = false, $useSchemaTags = false)
  {
    $tagsHelper = Mage::helper('businessdirectory/tags');

    $addressElements = array();
    $streetAddress   = array();

    if($type != 'short') {
      if(!is_null($_listing->getAddressLineOne())) {
        if($useSchemaTags == true) {
          if(!is_null($_listing->getAddressLineTwo())) {
            $streetAddress[] = '<span ' . $tagsHelper->addSchemaTag('streetaddress') . '>' . $_listing->getAddressLineOne();
            $streetAddress[] = '<br />' . $_listing->getAddressLineTwo() . '</span>';
          } else {
            $streetAddress[] = $tagsHelper->wrapWithSchemaTag($_listing->getAddressLineOne(), 'address');
          }
        } else {
          if(!is_null($_listing->getAddressLineTwo())) {
            $streetAddress[] = $_listing->getAddressLineOne();
            $streetAddress[] = '<br />' . $_listing->getAddressLineTwo();
          } else {
            $streetAddress[] = $tagsHelper->wrapWithSchemaTag($_listing->getAddressLineOne(), 'address');
          }
        }
      }
      $streetAddress = array_filter($streetAddress, 'strlen');
    }

    $locale = array();
    if($_listing->getListingCity() && $_listing->getListingState()) {
      if($useSchemaTags == true) {
        $locale[] = $tagsHelper->wrapWithSchemaTag($_listing->getListingCity(), 'city') . ', ' . $tagsHelper->wrapWithSchemaTag($_listing->getListingState(), 'region');
      } else {
        $locale[] = $_listing->getListingCity() . ', ' . $_listing->getListingState();
      }
    } else {
      if($useSchemaTags == true) {
        if(!is_null($_listing->getListingCity())) {
          $locale[] = $tagsHelper->wrapWithSchemaTag($_listing->getListingCity(), 'city');
        }
        if(!is_null($_listing->getListingState())) {
          $locale[] = $tagsHelper->wrapWithSchemaTag($_listing->getListingState(), 'region');
        }
      } else {
        $locale[] = $_listing->getListingCity();
        $locale[] = $_listing->getListingState();
      }
    }

    if($type != 'short') {
      if($useSchemaTags == true) {
        $locale[] = !is_null($_listing->getListingZipCode()) ? $tagsHelper->wrapWithSchemaTag($_listing->getListingZipCode(), 'postalcode') : null;
      } else {
        $locale[] = $_listing->getListingZipCode();
      }
      if($showCountry) {
        if($useSchemaTags == true) {
          if(!is_null($_listing->getListingCountry())) {
            $locale[] = $tagsHelper->wrapWithSchemaTag($this->getCountryName($_listing->getListingCountry()), 'country', $_listing->getListingCountry());
          }
        } else {
          $locale[] = $this->getCountryName($_listing->getListingCountry());
        }
      }
    }
    $locale = array_filter($locale, 'strlen');

    if(!empty($streetAddress)) {
      $addressElements[] = implode(' ', $streetAddress);
    }

    if(!empty($locale)) {
      $addressElements[] = implode(' ', $locale);
    }
    $addressElements = array_filter($addressElements, 'strlen');

    $schemaBeginTag = $useSchemaTags == true ? '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">' : null;
    $schemaEndTag   = $useSchemaTags == true ? '</span>' : null;

    if(!empty($addressElements)) {
      return $schemaBeginTag . trim('<p>' . implode('<br />', $addressElements) . '</p>') . $schemaEndTag;
    } else {
      return null;
    }
  }

  public function renderUrl($_url)
  {
    if($_url) {

      $_url = trim($_url, '/');
      if(!preg_match('#^http(s)?://#', $_url)) {
        $_url = 'http://' . $_url;
      }
      $_urlParts = parse_url($_url);
      return $_urlParts['host'];

    } else {
      return null;
    }
  }

  public function shortenDecimal($decimal)
  {
    return number_format((float)$decimal, 1, '.', ',');
  }

  public function convertStateAbbreviation($abbreviation, $country)
  {
    if(array_key_exists($abbreviation, $this->states) && $country == 'US') {
      return $this->states[$abbreviation];
    } else {
      return $abbreviation;
    }
  }

  public function abbreviateStateName($stateName)
  {
    $states       = array_map('strtoupper', $this->states);
    $state        = strtoupper($stateName);
    $abbreviation = array_search($state, $states);
    return $abbreviation != false ? $abbreviation : $stateName;
  }

  public function isVersionEqualOrHigher($versionNumber)
  {
    $magentoVersion = Mage::getVersion();
    if(version_compare($magentoVersion, $versionNumber, '>=')) {
      return true;
    }
    return false;
  }

  public function nl2p($string, $line_breaks = true, $xml = true)
  {
    // converts line breaks into paragraphs
    $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

    // It is conceivable that people might still want single line-breaks
    // without breaking into a new paragraph.
    if($line_breaks == true)
      return '<p>' . preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '$1<br' . ($xml == true ? ' /' : '') . '>$2'), trim($string)) . '</p>';
    else
      return '<p>' . preg_replace(
        array("/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i"),
        array("</p>\n<p>", "</p>\n<p>", '$1<br' . ($xml == true ? ' /' : '') . '>$2'),

        trim($string)) . '</p>';
  }

  public function formatPhoneForUs($phoneNumber)
  {
    $phoneNumber = preg_replace("/[^0-9,.]/", "", $phoneNumber);
    return "(" . substr($phoneNumber, 0, 3) . ") " . substr($phoneNumber, 3, 3) . "-" . substr($phoneNumber, 6);
  }

}