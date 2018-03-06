<?php
	ini_set("display_errors", "Off");
	ini_set("default_charset", "utf-8");
	
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
	
	function toSingleLine($output) {
		$output = str_replace(array("\r\n", "\r"), "\n", $output);
		$lines = explode("\n", $output);
		$new_lines = array();

		foreach ($lines as $i => $line) {
			if(!empty($line))
				$new_lines[] = trim($line);
		}
		return implode($new_lines);
	}
	function corsSinTaskAPI() {
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
			// you want to allow, and if so:
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

			exit(0);
		}
	}
	function headerSintaskAPI() {
		header('Content-Type: text/html; charset=utf-8');
		header('API-Info: SinTaskAPI v1.1 (WebPreview)');
	}
	function regexType($type) {
		$output = "";
		if($type==1) {
			$output = "%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%siu";
		} else if($type==2) {
			$output = "#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si";
		} else if($type==3) {
			$output = '#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i';
		} else if($type==4) {
			$output = "/(?:http|https)?(?:\:\/\/)?(?:www.)?(([A-Za-z0-9-]+\.)*[A-Za-z0-9-]+\.[A-Za-z]+)(?:\/.*)?/im";
		} else if($type==5) {
			$output = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';
		} else {
			$output = "@(https?|ftp)://(-\.)?([^\s/?\.#-]+\.?)+(/[^\s]*)?$@iS";
		}
		return $output;
	}
	function curlDownload($Url) {
		$output = "";
		if (!function_exists('curl_init')){
			$output = "cURL is not installed";
		}
		
		$ch = curl_init();
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		/*$userAgent = "SinTaskComot/1.0";*/
		$userAgent = "Mozilla/5.0 Gecko/20100101 Firefox/43.0";
		
		$options = array(
			CURLOPT_URL            => $Url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_AUTOREFERER    => true,
			CURLOPT_CONNECTTIMEOUT => 120,
			CURLOPT_TIMEOUT        => 120,
			CURLOPT_MAXREDIRS      => 10,
			//CURLOPT_REFERER        => "http://api.sintask.com",
			CURLOPT_USERAGENT      => $userAgent,
		);
		curl_setopt_array( $ch, $options );
		$output = curl_exec($ch);
		curl_close($ch);
	 
		return $output;
	}
	function curlDownloadInfo($Url) {
		if (!function_exists('curl_init')){
			die('cURL is not installed');
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $Url);
		curl_exec($ch);
		$output = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return $output;
	}
	function getDomainURL($url, $type) {
		if($type=="scheme") {
			$return = parse_url($url, PHP_URL_SCHEME);
		} else if($type=="host") {
			$return = parse_url($url, PHP_URL_HOST);
		} else if($type=="port") {
			$return = parse_url($url, PHP_URL_PORT);
		} else if($type=="user") {
			$return = parse_url($url, PHP_URL_USER);
		} else if($type=="pass") {
			$return = parse_url($url, PHP_URL_PASS);
		} else if($type=="path") {
			$return = parse_url($url, PHP_URL_PATH);
		} else if($type=="query") {
			$return = parse_url($url, PHP_URL_QUERY);
		} else if($type=="fragment") {
			$return = parse_url($url, PHP_URL_FRAGMENT);
		}
		return $return;
	}
	function urlFixed($url) {
		$urlFixed = parse_url($url, PHP_URL_HOST);
		if($urlFixed=="" || $urlFixed==null) {
			$urlExplode = explode("/", $url);
			$urlFixed = $urlExplode[0];
		}
		return $urlFixed;
	}
	function searchLinkCurl($input) {
		$regexType = 5;
		$regex = regexType($regexType);
		if(preg_match($regex, $input)) {
			return true;
		} else {
			return false;
		}
	}
	function urlCheckerv2($url) {
		$regexType = 5;
		$regex = regexType($regexType);
		if(preg_match($regex, $url)) {
			return true;
		} else {
			if(strcasecmp($url, "localhost")!=0) {
				$urlf = "http://".$url;
				if(preg_match($regex, $urlf)) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		}
	}
	function fixedTheURL($url, $addhost) {
		$output;
		if(searchLinkCurl($url) == false) {
			if(urlCheckerv2($url) == false) {
				$thispath = getDomainURL($url, "path");
				$four = getDomainURL($url, "query");

				$explodethepath = explode("/", $thispath);
				if($explodethepath[0]!="") {
					if($four!=null || $four!="") {
						$output = "//".$addhost."/".$thispath."?".$four;
					} else {
						$output = "//".$addhost."/".$thispath;
					}
				} else {
					if($four!=null || $four!="") {
						$output = "//".$addhost.$thispath."?".$four;
					} else {
						$output = "//".$addhost.$thispath;
					}
				}
			} else {
				$one = getDomainURL($url, "host");
				$two = getDomainURL($url, "port");
				$three = getDomainURL($url, "path");
				$four = getDomainURL($url, "query");
				if($two!=null || $two!="") {
					if($four!=null || $four!="") {
						$output = "//".$one.":".$two.$three."?".$four;
					} else {
						$output = "//".$one.":".$two.$three;
					}
				} else {
					if($four!=null || $four!="") {
						$output = "//".$one.$three."?".$four; 
					} else {
						$output = "//".$one.$three; 
					}    
				}   
			}
		} else {
			$one = getDomainURL($url, "host");
			$two = getDomainURL($url, "port");
			$three = getDomainURL($url, "path");
			$four = getDomainURL($url, "query");
			if($two!=null || $two!="") {
				if($four!=null || $four!="") {
					$output = "//".$one.":".$two.$three."?".$four;
				} else {
					$output = "//".$one.":".$two.$three;
				}
			} else {
				if($four!=null || $four!="") {
					$output = "//".$one.$three."?".$four; 
				} else {
					$output = "//".$one.$three; 
				}   
			}
		}
		return $output;
	}
	function addslashesCustom($input) {
		$input = addslashes($input);
		$input = str_replace("\'", "'", $input);
		return $input;
	}
	function getOriginalURL($url) {
		$ch = curl_init($url);
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		//$userAgent = "SinTaskComot/1.0";
		$userAgent = "Mozilla/5.0 Gecko/20100101 Firefox/43.0";
		
		$options = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_BINARYTRANSFER => true,
			CURLOPT_HEADER         => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING       => "UTF-8",
			CURLOPT_AUTOREFERER    => true,
			CURLOPT_CONNECTTIMEOUT => 120,
			CURLOPT_TIMEOUT        => 120,
			CURLOPT_MAXREDIRS      => 10,
			//CURLOPT_REFERER        => "http://api.sintask.com",
			CURLOPT_USERAGENT      => $userAgent,
		);
		curl_setopt_array( $ch, $options );
		
		$header = curl_exec($ch);
		$redir = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		
		return $redir;
	}
	/* 	Function Norm And Fix JSON */
	function fixStJson($input) {
		$input = str_replace(",]", "]", $input);
		$input = str_replace("[,", "]", $input);
		$input = str_replace(",,", ",", $input);
		
		$result = $input;
		return $result;
	}
	/* Function Normalize and Fix JSON v2 */
	function fixStJsonTwo($input) {
		$input = preg_replace("/,{2,}/", ",", $input);
		$input = preg_replace("/,\s+\]/", "]", $input);

		$result = fixStJson($input);
		return $result;
	}
	
    /*START THE FUNC*/
    corsSinTaskAPI();
    headerSinTaskAPI();
	header("Content-Type: application/json");
    /*EXEC*/
    if($_GET['url']!="" && $_GET['type']==1) {
        $finalURL = $_GET['url'];

        $html = curlDownload($finalURL);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        
        /*INITIATE*/
        $urlFixed = "";
        $title = $desc = $image = $keywords = $sitename = $hostname = [];
        /*END INITIATE*/
        
        /*GET URL*/
        $urlFixed = urlFixed($finalURL);
        /*END GET URL*/
        
        /*GET BY PROPERTY*/
        foreach( $doc->getElementsByTagName('meta') as $meta ) { 
            $property = $meta->getAttribute('property');
            if  (
                    (strcasecmp($property, "og:title")==0)              ||
                    (strcasecmp($property, "twitter:title")==0)
                ) 
                    {
                        $c = $meta->getAttribute('content');
                        array_push($title, $c);
                    }
            if  (
                    (strcasecmp($property, "og:description")==0)        ||
                    (strcasecmp($property, "twitter:description")==0)
                ) 
                    {
                        $c = $meta->getAttribute('content');
                        array_push($desc, $c);
                    }
            if  (
                    (strcasecmp($property, "og:image")==0)          ||
                    (strcasecmp($property, "twitter:image:src")==0) ||
                    (strcasecmp($property, "twitter:image")==0)
                ) 
                    {
                        $c = $meta->getAttribute('content');
                        array_push($image, $c);
                    }
            if  (
                    (strcasecmp($property, "og:keywords")==0)       ||
                    (strcasecmp($property, "twitter:keywords")==0)
                ) 
                    {
                        $c = $meta->getAttribute('content');
                        array_push($keywords, $c);
                    }
            if  (
                    (strcasecmp($property, "og:site_name")==0)      ||
                    (strcasecmp($property, "twitter:site_name")==0) ||
                    (strcasecmp($property, "twitter:site")==0)
                ) 
                    {
                        $c = $meta->getAttribute('content');
                        array_push($sitename, $c);
                    }
        }
        /*END GET BY PROPERTY*/
        
        /*GET BY NAME*/
        foreach( $doc->getElementsByTagName('meta') as $meta ) { 
            $property = $meta->getAttribute('name');
            if(strcasecmp($property, "title")==0) {
                $c = $meta->getAttribute('content');
                array_push($title, $c);
            }
            if(strcasecmp($property, "description")==0) {
                $c = $meta->getAttribute('content');
                array_push($desc, $c);
            }
            if(strcasecmp($property, "image")==0) {
                $c = $meta->getAttribute('content');
                array_push($image, $c);
            }
            if(strcasecmp($property, "keywords")==0) {
                $c = $meta->getAttribute('content');
                array_push($keywords, $c);
            }
            if(
                (strcasecmp($property, "site_name")==0) ||
                (strcasecmp($property, "site")==0)
            ) {
                $c = $meta->getAttribute('content');
                array_push($sitename, $c);
            }
            if(strcasecmp($property, "hostname")==0) {
                $c = $meta->getAttribute('content');
                array_push($hostname, $c);
            }
        }
        /*END GET BY NAME */
        
        /*TITLE BY TAG*/
        $nodes = $doc->getElementsByTagName('title');
        $c = $nodes->item(0)->nodeValue;
        array_push($title, $c);
        /*END TITLE BY TAG*/
        
        /*DESC BY TAG*/
        $bqnodes = $doc->getElementsByTagName('blockquote');
        $c = $bqnodes->item(0)->nodeValue;
        array_push($desc, $c);
        
        $pnodes = $doc->getElementsByTagName('h1');
        $pnodeslen = $pnodes->length;
        for ($i = 0; $i < $pnodeslen; $i++) {
            $c = $pnodes->item($i)->nodeValue;
            if($c!="" || $c!=null) {
                array_push($desc, $c);
            }
        }
        /*END DESC BY TAG*/
        
        /*GET IMAGE BY TAG*/
        $imgtag = $doc->getElementsByTagName('img');
        $imglen = $imgtag->length;
        for ($i = 0; $i < $imglen; $i++) {
            $alterimage = $imgtag->item($i);
            $c = $alterimage->getAttribute('src');
            if($c!="" || $c!=null) {
                array_push($image, $c);
            }
        }
        /*END GET IMAGE*/
        
        /*PUSH BY TITLE URL*/
        array_push($title, $urlFixed);
        /*END PUSH*/

        /*ALL_LENGTH*/
        $titleleng = count($title);
        $descleng = count($desc);
        $imageleng = count($image);
        $keywordsleng = count($keywords);
        $sitenameleng = count($sitename);
        $hostnameleng = count($hostname);
        /*END_ALL_LENGTH*/

        if($titleleng>5) {
            $titleleng = 5;
        }
        if($descleng>5) {
            $descleng = 5;
        }
        if($imageleng>5) {
            $imageleng = 5;
        }

        if(urlCheckerv2($urlFixed)==1 || urlCheckerv2($urlFixed)==true) {
            $rr = " [
                        {\"content\":[
                            {";
                            for($i = 0; $i < $imageleng; $i++) {
                                if($image[$i]!="") {
                                    if((searchLinkCurl($image[$i]) == false) && (strcasecmp($urlFixed, "localhost") != 0)) {
                                        $randomId = getRandomPlusDate(22);
                                        $pictCurlImg = fixedTheURL($image[$i], $urlFixed);

                                        $rr .= "\"image\":\"".$pictCurlImg."\",";
                                    } else {
                                        $randomId = getRandomPlusDate(22);
                                        $pictCurlImg = $image[$i];

                                        $rr .= "\"image\":\"".$pictCurlImg."\",";
                                    }
                                    $i = $imageleng;
                                } else {
                                    if($i==$imageleng-1) {
                                        $rr .= "\"image\":\"null\",";
                                    }
                                }
                            }
                            if($imageleng==0) {
                                $rr .= "\"image\":\"null\",";
                            }
                            for($i = 0; $i < $titleleng; $i++) {
                                if($title[$i]!="") {
                                    $rr .= "\"title\":\"".toSingleLine(addslashesCustom($title[$i]))."\",";
                                    $i = $titleleng;
                                } else {
                                    if($i==$titleleng-1) {
                                        $rr .= "\"title\":\"\",";
                                    }
                                }
                            }
                            for($i = 0; $i < $descleng; $i++) {
                                if($desc[$i]!="") {
                                    $rr .= "\"desc\":\"".toSingleLine(addslashesCustom($desc[$i]))."\",";
                                    $i = $descleng;
                                } else {
                                    if($i==$descleng-1) {
                                        $rr .= "\"desc\":\"\",";
                                    }
                                }
                            }
                            $rr .= "\"real_url\":\"".getOriginalURL($finalURL)."\",";
                            $rr .= "\"url\":\"".toSingleLine(addslashes(strtoupper($urlFixed)))."\",";
                            $rr .= "\"full_url\":\"".$finalURL."\"";
                $rr .= "      }";
                $rr .= "  ]},
                        {\"sts\":200},
                        {\"inst\":\"null\"},
                        {\"msg\":\"OK\"}
                    ]";
            echo fixStJsonTwo($rr);
        } else {
            $rr = " [
                        {\"content\":[
                            {
                                \"image\":\"\",
                                \"title\":\"Not an URL\",
                                \"desc\":\"\",
                                \"url\":\"getcontent.sintask.com\",
                                \"full_url\":\"getcontent.sintask.com\"
                            }
                        ]},
                        {\"sts\":201},
                        {\"inst\":\"null\"},
                        {\"msg\":\"Not an URL\"}
                    ]";
            echo fixStJsonTwo($rr);
        }
    } else {
		$rr = " [
					{\"content\":[
						{
							\"image\":\"\",
							\"title\":\"Invalid parameter\",
							\"desc\":\"\",
							\"url\":\"getcontent.sintask.com\",
							\"full_url\":\"getcontent.sintask.com\"
						}
					]},
					{\"sts\":202},
					{\"inst\":\"null\"},
					{\"msg\":\"Invalid parameter\"}
				]";
		echo fixStJsonTwo($rr);
	}

?>