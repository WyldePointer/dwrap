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
