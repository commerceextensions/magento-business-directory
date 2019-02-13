<?php

class CommerceExtensions_BusinessDirectory_Model_Api_Geocode_Ipinfodb extends Mage_Core_Model_Abstract
{
  public function getCoordinates()
  {
    if($key = Mage::getStoreConfig('businessdirectory/geocode/ipinfodb_api_key')) {

      $session = Mage::getSingleton('core/session');
      if($result = $session->getData('user_location')) {
        return $result;
      }

      $ip = Mage::helper('core/http')->getRemoteAddr();

      if(!$ip || $ip == '127.0.0.1') {
        return array();
      }
      $service_url = "http://api.ipinfodb.com/v3/ip-city/?key={$key}&ip={$ip}";

      $handle = curl_init();
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
      $response            = explode(';', $response);
      $result['Latitude']  = (string)$response[8];
      $result['Longitude'] = (string)$response[9];

      $result = array_filter($result, 'strlen');
      if(empty($result)) {
        return array();
      }
      $session->setData('user_location', $result);
      return in_array(0, $result) ? array() : $result;
    } else {
      return array();
    }
  }
}