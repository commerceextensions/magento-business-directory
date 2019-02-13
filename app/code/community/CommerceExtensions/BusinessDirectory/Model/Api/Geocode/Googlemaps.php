<?php

class CommerceExtensions_BusinessDirectory_Model_Api_Geocode_Googlemaps extends Mage_Core_Model_Abstract
{
  public function getCoordinates(array $listing = array())
  {
    $isoCountryCode = array_key_exists('listing_country', $listing) ? $listing['listing_country'] : null;
    $stateRegion    = array_key_exists('listing_state', $listing) ? $listing['listing_state'] : null;
    $city           = array_key_exists('listing_city', $listing) ? $listing['listing_city'] : null;
    $postalCode     = array_key_exists('listing_zip_code', $listing) ? $listing['listing_zip_code'] : null;
    $address        = array_key_exists('address_line_one', $listing) ? $listing['address_line_one'] : null;

    if(!array_key_exists('directory_location', $listing)) {
      $search = array(
        'street_address'              => $address,
        'locality'                    => $city,
        'administrative_area_level_1' => $stateRegion,
        'postal_code'                 => $postalCode,
        'country'                     => Mage::helper('businessdirectory')->getCountryName($isoCountryCode),
      );

      // here we remove PO Boxes from the search criteria. you cab add more address text to search for an
      // remove from the array. do not include spaces, like PO Box is actually pobox.
      if(array_key_exists('street_address', $search)) {
        $checkAddress = str_ireplace(' ', '', $address);
        $array        = array('pobox', 'postalbox');
        foreach($array as $value) {
          $isPoBox = (stripos($checkAddress, $value) !== false) ? true : false;
          if($isPoBox) {
            unset($search['street_address']);
            break;
          }
        }
      }
      $search = array_filter($search, 'strlen');
      $search = implode(' ', $search);
      $search = 'address=' . urlencode(trim($search));
    } else {
      $search['address'] = urlencode(trim($listing['directory_location'])); // if we are just submitting a straight query, as in data from a single search box
      $search            = array_filter($search, 'strlen');
      $search            = urldecode(http_build_query($search));
    }

    $service_url = "http://maps.googleapis.com/maps/api/geocode/xml?";
    $sensor      = "&sensor=false";
    $service_url = $service_url . $search . $sensor;
    $handle      = curl_init();
    curl_setopt_array(
      $handle,
      array(
        CURLOPT_URL            => $service_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => array('Accept' => 'application/xml')
      )
    );

    $response = curl_exec($handle);
    curl_close($handle);
    $response = simplexml_load_string($response);

    if((string)$response->status == 'OK') {
      $result['Latitude']  = (string)$response->result->geometry->location->lat;
      $result['Longitude'] = (string)$response->result->geometry->location->lng;
    } else {
      return array();
    }
    return array_filter($result, 'strlen');
  }
}