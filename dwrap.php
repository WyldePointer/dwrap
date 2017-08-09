<?php
/*
 * Copyright (c) 2017, Sohrab Monfared <sohrab.monfared@gmail.com>
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * Neither the name of the <organization> nor the
 *      names of its contributors may be used to endorse or promote products
 *      derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

function dwrapd_parse_request($request){
/*
 *  PHP has getopt() but it only takes arguments from command line.
 */

  $command = NULL;
  $result = NULL;

  $request_array = explode(' ', $request);

  /*
   * Removing the white spaces from the options.
   */
  foreach ($request_array as $array_key => $array_value){
    if ($array_value == ''){
      unset($request_array[$array_key]);
    }
  }

  $request_array = array_values($request_array); /* Re-indexing the array */

  if (!is_null($request_array)){

    $command = $request_array[0];

    switch ($command){

      case "get_ip_by_name":
        $result = _dwrapd_get_ip_by_name($request_array);
        break;

      case "get_mx":
        $result = _dwrapd_get_mx($request_array);
        break;


      default:
        return -1; /* invalid command. TODO: Better error codes */
    }

    if (array_search("--json", $request_array)){
      return json_encode($result);
    }

    return $result;
  }

  return 0;
}



function _dwrapd_get_mx($request_array){

  $dns_result = NULL;
  $return_limit = NULL;

  if (!isset($request_array[1])){
    return -1; /* no hostname */
  }

  $return_limit = _dwrapd_syntax_parser_get_limit($request_array);

  $dns_result = dwrapd_do_dns_lookup_mx($request_array[1]);

  if ($return_limit < count($dns_result) && $return_limit != 0){
    return array_slice($dns_result, 0, $return_limit, true);
  }

  return $dns_result;
}


function _dwrapd_syntax_parser_get_limit($request_array){

  $limit_index = NULL;
  $ip_return_limit = 0;

  if ($limit_index = array_search("--limit", $request_array)){

    $ip_return_limit = 1;

    if (isset($request_array[$limit_index+1])){

      /* if it's not an option */
      if (!(substr($request_array[$limit_index+1], 0, 1) == '-' xor substr($request_array[$limit_index+1], 0, 1) == '--')){

        if (intval($request_array[$limit_index+1])){
          return $request_array[$limit_index+1];
        }

      }

    }

  }

  return $ip_return_limit;
}


function _dwrapd_get_ip_by_name($request_array){

  $dns_result = NULL;
  $ip_addresses = array();
  $return_limit = NULL;

  if (!isset($request_array[1])){
    return -1; /* no hostname */
  }

  $return_limit = _dwrapd_syntax_parser_get_limit($request_array);

  $dns_result = dwrapd_do_dns_lookup_a($request_array[1], $return_limit);

  if ($return_limit < count($dns_result) && $return_limit != 0){
    return array_slice($dns_result, 0, $return_limit);
  }

  return $dns_result;
}


function dwrapd_do_dns_lookup($hostname, $record='A'){

  $result = array();
  $php_record_type = DNS_A;

  if ($hostname == ''){
    return -1;
  }

  switch ($record){
    case "txt":
    case "TXT":
      $php_record_type = DNS_TXT;
      break;

    case 'a':
    case 'A':
      $php_record_type = DNS_A;
      break;

    default:
      return -13; /* DWRAPD_LOOKUP_NOT_IMPLEMENTED_FOR_RECORD */
  }

  $result = dns_get_record($hostname, $php_record_type);

  if (isset($result[0]["type"])){

    switch ($result[0]["type"]){

      case "TXT":
        return $result[0]["txt"];
        break;

      case 'A':
        return $result[0]["ip"];
        break;

    }

  }

  return 0;
}

function dwrapd_do_dns_lookup_a($hostname, $limit=0){

  $ips = NULL;

  /*
   *  TODO: Timeout for gethostbyname functions.
   */
  if ($limit == 1){
    $ips = gethostbyname($hostname);
  }else{
    $ips = gethostbynamel($hostname);
  }

  return $ips;  

}


function dwrapd_do_dns_lookup_mx($hostname){

  $mx_records = array();
  $weights = array();
  $formatted = array();

  /*
   *  TODO: Finding the most address-friendly regex
   *        and using it instead of doing the actual lookup.
   */

  if(!filter_var(dwrapd_do_dns_lookup_a($hostname, 1), FILTER_VALIDATE_IP)){
    return -1;
  }

  if (getmxrr($hostname, $mx_records, $weights)){

    if (count($mx_records) == count($weights)){

      foreach ($weights as $key => $value){

        /*
         *  TODO: Making sure the returned address is not spoofed.
         *        (e,g. it's a valid record)
         */

        if (isset($mx_records[$key])){
          $formatted[$value] = $mx_records[$key];
        }

      }

      if (count($formatted)>0){
        return $formatted;
      }

    }

    return $mx_records;
  }


  return 0;
}


function string_has_white_space($string){

  if ($string == ''){
    return 0; /* otherwise this function will return true on empty string. */
  }

  return preg_match("/\s/", $string);
}


function get_url_array(){

  $sorted = array();

  $all = explode('/', $_SERVER["REQUEST_URI"]);
  unset($all[0]);  /* remove the empty index from the beginning of array */

  foreach ($all as $param){
    if ((intval($param)!=0) && ($param == intval($param)) && (strlen($param) == strlen(intval($param))) ){
      $sorted[] = intval($param);
    }else{
      if (!empty($param)){
        $sorted[] = $param;
      }
    }
  }

  return $sorted;
}


function _dwrapd_is_a_rest_request(){

  $url = get_url_array();

  if (isset($url[0])){

    if ($url[0] == "api"){

      return true;

    }

  }

  return false;
}


function _dwrapd_handle_rest_request(){

    $ip_array = array();
    $json = false;
    $limit = 0;
    $limit_index = 0;

    $url = get_url_array();

    if (!_dwrapd_is_a_rest_request()){
      return -1;
    }

    if (!isset($url[1])){
      return -2; /* No action supplied */
    }

    if (!isset($url[2])){
      return -3; /* No hostname supplied */
    }

    $json = array_search("json", $url);

    $limit_index = array_search("limit", $url);

    /* If the word "limit" was found in URL */
    if ($limit_index){
      $limit = 1;
    }

    /* If there was a number next to "limit" in URL */
    if (isset($url[$limit_index+1])){

      if (intval($url[$limit_index+1]) == $url[$limit_index+1]){
        $limit = $url[$limit_index+1];
      }

    }

    $ips = dwrapd_do_dns_lookup_a($url[2], $limit);

    if (is_array($ips)){

      foreach ($ips as $ip){

        if(filter_var($ip, FILTER_VALIDATE_IP)){
          $ip_array[] = $ip;
        }

        if (count($ip_array) >= $limit && $limit > 0){
          break;
        }

      }

    } else {

      if ($ips != ''){
        echo $ips;
      }

    }

    if (count($ip_array)){

      if ($json){

        echo json_encode($ip_array);

      } else {

        foreach ($ip_array as $ip){

          echo $ip, "\n";

        }

      }

    }

  return true;
}


