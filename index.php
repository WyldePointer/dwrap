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

require "dwrap.php";

function show_api_help(){
  echo <<<HELP
<h4>dwrap API examples:</h4>
<pre>GET /api/get_ip_by_name/www.google.com</pre>
<pre>GET /api/get_ip_by_name/www.google.com/limit/3</pre>
<pre>GET /api/get_ip_by_name/www.google.com/json</pre>
<pre>GET /api/get_ip_by_name/www.google.com/json/limit/2</pre>
HELP;
}

$ip_array = array();
$json = false;
$limit = 0;
$limit_index = 0;
$url = get_url_array();

if (isset($url[0])){

  if ($url[0] == "api"){

    if (!isset($url[1])){
      die(show_api_help());
    }

    $json = array_search("json", $url);
    $limit_index = array_search("limit", $url);

    if (isset($url[$limit_index+1])){

      if (intval($url[$limit_index+1]) == $url[$limit_index+1]){
        $limit = $url[$limit_index+1];
      }

    }

    $ips = dwrapd_do_dns_lookup($url[1], $limit);

    if (is_array($ips)){

      foreach ($ips as $ip){

        if(filter_var($ip, FILTER_VALIDATE_IP)){
          $ip_array[] = $ip;
        }

        if (count($ip_array) >= $limit){
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

    die();
  }

}

$request_one = "get_ip_by_name www.google.com --json --limit 3";

var_dump(dwrapd_parse_request($request_one));

