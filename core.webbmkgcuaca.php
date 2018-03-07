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
		/*$userAgent = "Mozilla/5.0 Gecko/20100101 Firefox/43.0";*/
		$userAgent = "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36";
		
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

	/* Get Inner HTML from DOMDocument Node */
	function DOMinnerHTML(DOMNode $element) { 
	    $innerHTML = ""; 
	    $children  = $element->childNodes;

	    foreach ($children as $child) 
	    { 
	        $innerHTML .= $element->ownerDocument->saveHTML($child);
	    }

	    return $innerHTML; 
	} 
	
    /*START THE FUNC*/
    corsSinTaskAPI();
    headerSinTaskAPI();
	
    /*EXEC*/
    if($_GET['url']!="" && $_GET['type']==1) {
        $finalURL = $_GET['url'];

        $html = curlDownload($finalURL);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        
        /*INITIATE*/
        $urlFixed = "";
        $title = $desc = $image = $keywords = $sitename = $hostname = [];
        $data = [];
        /*END INITIATE*/
        
        /*GET URL*/
        $urlFixed = urlFixed($finalURL);
        /*END GET URL*/
        
        header("Content-Type: application/json");

    	$urutanIndex = 0;
    	$tdLen = $doc->getElementsByTagName('td')->length;

    	/* GET PROV */
    	$thisProv = "";
    	foreach ($doc->getElementsByTagName('div') as $meta) {
    		foreach ($meta->getElementsByTagName('h2') as $meta2) {
    			$tab = $meta2->getAttribute('class');
    			if(strcasecmp($tab, "blog-grid-title-lg")==0) {
    				$thisProv = strip_tags(DOMinnerHTML($meta2));
    			}
        	}
    	}

    	/* GET TOTAL ELEMENT TABLE HEAD */
    	$index1 = 0;
    	$index2 = 0;
    	$totalElement = []; 
     	foreach ($doc->getElementsByTagName('table') as $meta) {
    		foreach ($meta->getElementsByTagName('thead') as $meta2) {
    			$elementCount = 0;
    			foreach ($meta2->getElementsByTagName('th') as $meta3) {
    				$elementCount = $elementCount+1;
    			}
    			$totalElement[$index2] = $elementCount;
    			$index2 = $index2+1;
			}
			$index1 = $index1+1;
    	}

    	/* GET TOTAL ELEMENT TABLE BODY */
    	$index3 = 0;
    	$totalData = [];
    	foreach ($doc->getElementsByTagName('table') as $meta) {
    		$dataCount = 0;
    		foreach ($meta->getElementsByTagName('tr') as $meta2) {
        		$dataCount = $dataCount+1;
        	}

        	$totalData[$index3] = $dataCount-2;
        	$index3 = $index3+1;
    	}

    	/* GET TOTAL ELEMENT TABLE DATE + DATA */
    	$index4 = 0;
    	$totalDate = [];
    	foreach ($doc->getElementsByTagName('li') as $meta) {
    		$dataCount = 0;
    		foreach ($meta->getElementsByTagName('a') as $meta2) {
    			$tab = $meta2->getAttribute('data-toggle');
    			if(strcasecmp($tab, "tab")==0) {
    				$totalDate[$index4] = strip_tags(DOMinnerHTML($meta2));
        			$index4 = $index4+1;
    			}
        	}
    	}

    	/* GET DATA */
    	$index5 = 0;
    	$saveData = [];
    	foreach ($doc->getElementsByTagName('table') as $meta) {
    		foreach ($meta->getElementsByTagName('td') as $meta2) {
        		array_push($saveData, strip_tags(DOMinnerHTML($meta2)));
        	}

        	$index5 = $index5+1;
        }

    	/* RUN */
    	$index6 = 0;
    	$totalElementLen = count($totalElement);
    	$dataResult = [];
    	$dataArray = [];
    	for ($i = 0; $i < $totalElementLen; $i++) {
    		$totalDataLen = $totalData[$i];
    		$totalTime = $totalElement[$i];
    		$totalDataLoop = $totalDataLen*($totalTime-1);
    		$urutanIndex = 0;
    		if($totalTime == 8) {
				$urutan = [
	        		"Kota",
	        		"Pagi",
	        		"Siang",
	        		"Malam",
	        		"Dini Hari",
	        		"Suhu (Celcius)",
	        		"Kelembaban (%)"
	        	];
	        } else if($totalTime == 7) {
	        	$urutan = [
	        		"Kota",
	        		"Siang",
	        		"Malam",
	        		"Dini Hari",
	        		"Suhu (Celcius)",
	        		"Kelembaban (%)"
	        	];
	        } else if($totalTime == 6) {
	        	$urutan = [
	        		"Kota",
	        		"Malam",
	        		"Dini Hari",
	        		"Suhu (Celcius)",
	        		"Kelembaban (%)"
	        	];
	        } else if($totalTime == 5) {
	        	$urutan = [
	        		"Kota",
	        		"Dini Hari",
	        		"Suhu (Celcius)",
	        		"Kelembaban (%)"
	        	];
			}

			$indexData = -1;
    		for ($j = 0; $j < $totalDataLoop; $j++) { 
    			$indexing = $urutanIndex%($totalTime-1);
    			if($indexing == 0) {
        			$indexData = $indexData+1;
        		}

    			$dataArray[$indexData][$urutan[$indexing]] = $saveData[$index6];

    			$urutanIndex = $urutanIndex+1;
    			$index6 = $index6+1;
    		}

    		$tempResult = [];
    		$tempResult["tanggal"] = $totalDate[$i];
    		$tempResult["data"] = $dataArray;

    		$dataResult[$i] = $tempResult; 
    		$dataArray = [];
    	}

    	$response = [
			"status"        => 200,
			"datafrom"		=> $thisProv,
			"content"		=> $dataResult,
		];

		echo json_encode($response, JSON_PRETTY_PRINT);
    } else if($_GET['type']==2) {
    	$finalURL = "http://www.bmkg.go.id/cuaca/prakiraan-cuaca-indonesia.bmkg";

    	if($_GET['search']!=null && $_GET['search']!="" && !ctype_space($_GET['search'])) {
    		$searchKey = strtolower($_GET['search']);
    		$areaList = [
    			"aceh" => 1,
    			"bali" => 2,
    			"bangka belitung" => 3,
    			"banten" => 4,
    			"bengkulu" => 5,
    			"di yogyakarta" => 6, "jogja" => 6, "yogya" => 6, "yogyakarta" => 6,
    			"jakarta" => 7, "dki" => 7, "dki jakarta" => 7,
    			"gorontalo" => 8,
    			"jambi" => 9,
    			"jabar" => 10, "jawa barat" => 10,
    			"jateng" => 11, "jawa tengah" => 11,
    			"jatim" => 12, "jawa timur" => 12,
    			"kalbar" => 13, "kalimantan barat" => 13,
    			"kalsel" => 14, "kalimantan selatan" => 14,
    			"kalteng" => 15, "kalimantan tengah" => 15,
    			"kaltim" => 16, "kalimantan timur" => 16,
    			"kaltara" => 17, "kalimantan utara" => 17,
    			"kepri" => 18, "kep riau" => 18, "kepulauan riau" => 18,
    			"lampung" => 19,
    			"maluku" => 20,
    			"malut" => 21, "maluku utara" => 21,
    			"ntb" => 22, "nusa tenggara barat" => 22,
    			"ntt" => 23, "nusa tenggara timur" => 23,
    			"papua" => 24,
    			"pabar" => 25, "papua barat" => 25, 
    			"riau" => 26,
    			"sulbar" => 27, "sulawesi barat" => 27,
    			"sulsel" => 28, "sulawesi selatan" => 28,
    			"sulteng" => 29, "sulawesi tengah" => 29,
    			"sultra" => 30, "sulawesi tenggara" => 30,
    			"sulut" => 31, "sulawesi utara" => 31,
    			"sumbar" => 32, "sumatera barat" => 32,
    			"sumsel" => 33, "sumatera selatan" => 33,
    			"sumut" => 34, "sumatera utara" => 34,
    		];

    		$resultKey = 0;
			foreach($areaList as $key => $value) {
			    similar_text($key, $searchKey, $percent);
			    if($percent > 90) {
			        $resultKey = $value;
			    }
			}

			if($resultKey != 0) {
				$finalURL .= "?Prov=".$resultKey;
			}
    	}

        $html = curlDownload($finalURL);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        
        /*INITIATE*/
        $urlFixed = "";
        $title = $desc = $image = $keywords = $sitename = $hostname = [];
        $data = [];
        /*END INITIATE*/
        
        /*GET URL*/
        $urlFixed = urlFixed($finalURL);
        /*END GET URL*/
        
        header("Content-Type: application/json");

    	$urutanIndex = 0;
    	$tdLen = $doc->getElementsByTagName('td')->length;

    	/* GET PROV */
    	$thisProv = "";
    	foreach ($doc->getElementsByTagName('div') as $meta) {
    		foreach ($meta->getElementsByTagName('h2') as $meta2) {
    			$tab = $meta2->getAttribute('class');
    			if(strcasecmp($tab, "blog-grid-title-lg")==0) {
    				$thisProv = strip_tags(DOMinnerHTML($meta2));
    			}
        	}
    	}

    	/* GET TOTAL ELEMENT TABLE HEAD */
    	$index1 = 0;
    	$index2 = 0;
    	$totalElement = []; 
     	foreach ($doc->getElementsByTagName('table') as $meta) {
    		foreach ($meta->getElementsByTagName('thead') as $meta2) {
    			$elementCount = 0;
    			foreach ($meta2->getElementsByTagName('th') as $meta3) {
    				$elementCount = $elementCount+1;
    			}
    			$totalElement[$index2] = $elementCount;
    			$index2 = $index2+1;
			}
			$index1 = $index1+1;
    	}

    	/* GET TOTAL ELEMENT TABLE BODY */
    	$index3 = 0;
    	$totalData = [];
    	foreach ($doc->getElementsByTagName('table') as $meta) {
    		$dataCount = 0;
    		foreach ($meta->getElementsByTagName('tr') as $meta2) {
        		$dataCount = $dataCount+1;
        	}

        	$totalData[$index3] = $dataCount-2;
        	$index3 = $index3+1;
    	}

    	/* GET TOTAL ELEMENT TABLE DATE + DATA */
    	$index4 = 0;
    	$totalDate = [];
    	foreach ($doc->getElementsByTagName('li') as $meta) {
    		$dataCount = 0;
    		foreach ($meta->getElementsByTagName('a') as $meta2) {
    			$tab = $meta2->getAttribute('data-toggle');
    			if(strcasecmp($tab, "tab")==0) {
    				$totalDate[$index4] = strip_tags(DOMinnerHTML($meta2));
        			$index4 = $index4+1;
    			}
        	}
    	}

    	/* GET DATA */
    	$index5 = 0;
    	$saveData = [];
    	foreach ($doc->getElementsByTagName('table') as $meta) {
    		foreach ($meta->getElementsByTagName('td') as $meta2) {
        		array_push($saveData, strip_tags(DOMinnerHTML($meta2)));
        	}

        	$index5 = $index5+1;
        }

    	/* RUN */
    	$index6 = 0;
    	$totalElementLen = count($totalElement);
    	$dataResult = [];
    	$dataArray = [];
    	for ($i = 0; $i < $totalElementLen; $i++) {
    		$totalDataLen = $totalData[$i];
    		$totalTime = $totalElement[$i];
    		$totalDataLoop = $totalDataLen*($totalTime-1);
    		$urutanIndex = 0;
    		if($totalTime == 8) {
				$urutan = [
	        		"Kota",
	        		"Pagi",
	        		"Siang",
	        		"Malam",
	        		"Dini Hari",
	        		"Suhu (Celcius)",
	        		"Kelembaban (%)"
	        	];
	        } else if($totalTime == 7) {
	        	$urutan = [
	        		"Kota",
	        		"Siang",
	        		"Malam",
	        		"Dini Hari",
	        		"Suhu (Celcius)",
	        		"Kelembaban (%)"
	        	];
	        } else if($totalTime == 6) {
	        	$urutan = [
	        		"Kota",
	        		"Malam",
	        		"Dini Hari",
	        		"Suhu (Celcius)",
	        		"Kelembaban (%)"
	        	];
	        } else if($totalTime == 5) {
	        	$urutan = [
	        		"Kota",
	        		"Dini Hari",
	        		"Suhu (Celcius)",
	        		"Kelembaban (%)"
	        	];
			}

			$indexData = -1;
    		for ($j = 0; $j < $totalDataLoop; $j++) { 
    			$indexing = $urutanIndex%($totalTime-1);
    			if($indexing == 0) {
        			$indexData = $indexData+1;
        		}

    			$dataArray[$indexData][$urutan[$indexing]] = $saveData[$index6];

    			$urutanIndex = $urutanIndex+1;
    			$index6 = $index6+1;
    		}

    		$tempResult = [];
    		$tempResult["tanggal"] = $totalDate[$i];
    		$tempResult["data"] = $dataArray;

    		$dataResult[$i] = $tempResult; 
    		$dataArray = [];
    	}

    	$response = [
			"status"        => 200,
			"datafrom"		=> $thisProv,
			"content"		=> $dataResult,
		];

		echo json_encode($response, JSON_PRETTY_PRINT);
    } else {
    	header("Content-Type: application/json");
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