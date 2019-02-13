<?php

class CommerceExtensions_BusinessDirectory_Model_Api_Geocode_Bingmaps extends Mage_Core_Model_Abstract
{
  public function getCoordinates(array $listing = array())
  {
    if(Mage::getStoreConfig('businessdirectory/geocode/bingmaps_api_key')) {
      $baseURL = "http://dev.virtualearth.net/REST/v1/Locations";

      $parameters = array(
        'output'     => 'xml',
        'key'        => Mage::getStoreConfig('businessdirectory/geocode/bingmaps_api_key'),
        'maxResults' => 1
      );

      $isoCountryCode = array_key_exists('listing_country', $listing) ? $listing['listing_country'] : null;
      $stateRegion    = array_key_exists('listing_state', $listing) ? $listing['listing_state'] : null;
      $city           = array_key_exists('listing_city', $listing) ? $listing['listing_city'] : null;
      $postalCode     = array_key_exists('listing_zip_code', $listing) ? $listing['listing_zip_code'] : null;
      $address        = array_key_exists('address_line_one', $listing) ? $listing['address_line_one'] : null;

      // the purpose of this if statement is to reorder the $search array depending on whether we are sending the url through as a query or structured url
      if(!array_key_exists('directory_location', $listing)) {
        $search = array(
          'addressLine'   => $address,
          'locality'      => $city,
          'adminDistrict' => $stateRegion,
          'postalCode'    => $postalCode,
          'countryRegion' => Mage::helper('businessdirectory')->getCountryName($isoCountryCode),
        );

        // here we remove PO Boxes from the search criteria. you cab add more address text to search for an
        // remove from the array. do not include spaces, like PO Box is actually pobox.
        if(array_key_exists('addressLine', $search)) {
          $checkAddress = str_ireplace(' ', '', $address);
          $array        = array('pobox', 'postalbox');
          foreach($array as $value) {
            $isPoBox = (stripos($checkAddress, $value) !== false) ? true : false;
            if($isPoBox) {
              unset($search['addressLine']);
              break;
            }
          }
        }

      } else {
        $search = $listing; // if we are just submitting a straight query, as in data from a single search box
      }
      $search = array_filter($search, 'strlen');

      $findURL = $baseURL . '?query=' . rawurlencode(trim(implode(', ', $search))) . '&' . http_build_query($parameters);

      $handle = curl_init();
      curl_setopt_array(
        $handle,
        array(
          CURLOPT_URL            => $findURL,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HEADER         => false,
          CURLOPT_HTTPHEADER     => array('Accept' => 'application/xml')
        )
      );

      $response = curl_exec($handle);
      curl_close($handle);
      $response = simplexml_load_string($response);

      /*	  these are other pieces of response data. not needed, just left here for convenience
          echo $response->Copyright;
          echo $response->BrandLogoUri;
          echo $response->StatusCode;
          echo $response->StatusDescription;
          echo $response->AuthenticationResultCode;
          echo $response->TraceId;
          echo $response->ResourceSets->ResourceSet->EstimatedTotal;*/

      if((int)$response->StatusCode == 200) {
        $result['Latitude']  = (string)$response->ResourceSets->ResourceSet->Resources->Location->Point->Latitude;
        $result['Longitude'] = (string)$response->ResourceSets->ResourceSet->Resources->Location->Point->Longitude;
      } else {
        return array();
      }
      return array_filter($result, 'strlen');
    } else
      return array();
  }

}