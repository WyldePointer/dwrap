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
  $opts = array();
  $ip_addresses = array();
  $ip_return_limit = 0;
  $limit_index = NULL;

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

    if ($command == "get_ip_by_name"){

      if (!isset($request_array[1])){
        return -1; /* no hostname */
      }


      if ($limit_index = array_search("--limit", $request_array)){

        $ip_return_limit = 1;

        if (isset($request_array[$limit_index+1])){

          /* if it's not an option */
          if (!(substr($request_array[$limit_index+1], 0, 1) == '-' xor substr($request_array[$limit_index+1], 0, 1) == '--')){

            if (intval($request_array[$limit_index+1])){
              $ip_return_limit = $request_array[$limit_index+1];
            }else{
              return -2; /* non-digit limit */
            }

          }

        }

      }


      $ip_addresses = dwrapd_get_ip_by_name($request_array[1], $ip_return_limit);


      if (array_search("--json", $request_array)){
        return json_encode($ip_addresses);
      }

      return $ip_addresses;
    }

  }

}


function dwrapd_get_ip_by_name($hostname, $limit=0){

  $dns_result = NULL;

  $dns_result = dwrapd_do_dns_lookup($hostname, $limit);

  if ($limit < count($dns_result) && $limit != 0){
    return array_slice($dns_result, 0, $limit);
  }

  return $dns_result;
}


function dwrapd_do_dns_lookup($hostname, $limit=0){

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


function dwrapd_do_mx_lookup($hostname){

  $mx_records = array();
  $weights = array();
  $formatted = array();

  /*
   *  TODO: Finding the most address-friendly regex
   *        and using it instead of doing the actual lookup.
   */

  if(!filter_var(dwrapd_get_ip_by_name($hostname, 1), FILTER_VALIDATE_IP)){
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


