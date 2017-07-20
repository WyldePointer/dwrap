<?php

function dwrapd_parse_request($request){
/*
 *  PHP has getopt() but it only takes arguments from command line.
 */

  $command = NULL;
  $opts = array();
  $ip_addresses = array();
  $ip_return_limit = 0;
  $limit_index = NULL;

  $request_array = explode(" ", $request);

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

    if ($command == "get_ip_by_name"){

      if (!isset($request_array[1])){
        return -1; /* no hostname */
      }


      if ($limit_index = array_search("--limit", $request_array)){

        if (isset($request_array[$limit_index+1])){

          if (!string_has_white_space($request_array[$limit_index+1])){
            $ip_return_limit = $request_array[$limit_index+1];
          } else {
            $ip_return_limit = 1;
          }

        }

      }

      $ip_addresses = dwrapd_get_ip_by_hostname($request_array[1], $ip_return_limit);

      if (array_search("--json", $request_array)){
        return json_encode($ip_addresses);
      }

      return $ip_addresses;
    }

  }

}


function dwrapd_get_ip_by_hostname($hostname, $limit=0){

  $dns_result = NULL;

  $dns_result = dwrapd_do_dns_lookup($hostname);

  if ($limit < count($dns_result) && $limit != 0){
    return array_slice($dns_result, 0, $limit);
  }

  return $dns_result;
}


function dwrapd_do_dns_lookup($hostname){

  $ips = NULL;

  /*
   *  TODO: 
   *    - gethostbyname() instead of dummy array.
   */

  $ips = array(
    "173.194.32.148",
    "173.194.32.144",
    "173.194.32.147",
    "173.194.32.146",
    "173.194.32.145"
  );

  return $ips;  
}

function string_has_white_space($string){

  if ($string == ''){
    return 0; /* otherwise this function will return true on empty string. */
  }

  return preg_match('/\s/', $string);
}



$request_one = "get_ip_by_name www.google.com --limit 2 --json";


var_dump(dwrapd_parse_request($request_one));
