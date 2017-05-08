<?php
    require ('simple_html_dom.php');
    set_time_limit(3600);
    function curl($url , $ua = FALSE){
        if($ua == false){
            $ua = $_SERVER['HTTP_USER_AGENT'];
        }
        $ch = curl_init();
        curl_setopt($ch , CURLOPT_URL , $url);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER , true);
        curl_setopt($ch , CURLOPT_FOLLOWLOCATION , true);
        curl_setopt($ch , CURLOPT_USERAGENT , $ua);
        return curl_exec($ch);
    }
    
    function outputCSV($headers, $data, $filename = "import.csv", $delimiter = ",") {
        //$outputBuffer = fopen("imports.csv", 'w');
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        $outputBuffer = fopen("php://output", "w");
        fputcsv($outputBuffer, $headers, $delimiter);
        foreach($data as $val) {
            fputcsv($outputBuffer, $val, $delimiter);
        }
        //fclose($outputBuffer);

    }

    $html = new simple_html_dom();

    function yellowPages($html, $limit, $i = 1, $len = 0, $t = true, $items = array()){
               
        $filter = "";
        $locality = "";

        if(isset($_POST['firstname'])){
            $filter = $filter . $_POST['firstname'];
            $filter = str_replace(" ", "+", $filter);
        }

        if(isset($_POST['lastname'])){
            $filter = $filter . "+" . $_POST['lastname'];
            $filter = str_replace(" ", "+", $filter);
        }

        if(isset($_POST['zipcode'])){
            $locality = $locality . $_POST['zipcode'];
            $locality = str_replace(" ", "+", $locality);
        }

        if(isset($_POST['city'])){
            $locality = $locality . "+" . $_POST['city'];
            $locality = str_replace(" ", "+", $locality);
        }

        if(isset($_POST['state'])){
            $locality = $locality . "+" . $_POST['state'];
            $locality = str_replace(" ", "+", $locality);
        }
            
        

        while($t && $i <= $limit){
            $url = "http://www.yellowpages.com/search?search_terms=".$filter."&geo_location_terms=".$locality."&page=".$i;
            $html->load_file($url);

            $posts = $html->find('div[class=search-results organic]');

            if(count($posts) > 0){

                foreach($posts as $post){
                    
                    foreach($post->find('div[class=info]') as $client){
                        ####Obtain Full Name#####
                        $name = $client->find('h3 a', 0);
    
                        ####Obtain Full Address####
                        $info = $client->find('div[class=info-section info-primary]', 0);
                        $infoAddress = $info->find('p span');
    
                        ####Obtain Telephone####
                        $telephone = $info->find('div[class*=phones]', 0);
    
                        ####Telephone Number####
                        if(isset($telephone)){
                            $item[$len]['Office Phone'] = $telephone->innertext;
                            $item[$len]['Office Phone'] = str_replace(array(' ', '(', ')', '-'), "", $item[$len]['Office Phone']);
                        }else
                            $item[$len]['Office Phone'] = "";
    
                        ####Full Name####
                        $item[$len]['Full Name'] = $name->innertext;
    
                        ####State####
                        if(isset($infoAddress[2])){
                            $item[$len]['Primary Address State'] = $infoAddress[2]->innertext;
                        }else
                            $item[$len]['Primary Address State'] = "";
    
                        ####City####
                        if(isset($infoAddress[1])){
                            $item[$len]['Primary Address City'] = $infoAddress[1]->innertext;
                        }else
                            $item[$len]['Primary Address City'] = "";
    
                        ####Address####
                        if(isset($infoAddress[0])){
                            $item[$len]['Primary Address Street'] = $infoAddress[0]->innertext;
                        }else
                            $item[$len]['Primary Address Street'] = "";
    
                        ####Postal Code####
                        if(isset($infoAddress[3])){
                            $item[$len]['Primary Address Postalcode'] = $infoAddress[3]->innertext;
                        }else
                            $item[$len]['Primary Address Postalcode'] = "";
    
                        $len++;
                    }
                }
                $i++;
    
            }else
                $t = false;
        }
        
        if($len > 0){
            outputCSV(array('Office Phone', 'Full Name', 'Primary Address State', 'Primary Address City', 'Primary Address Street', 'Primary Address Postalcode'), $item);
        }else
            echo "<span>No se encontraron datos con los criterios de busqueda proporcionados!!!</span>";
    }
    
    function whitePages($html, $limit, $i = 0, $len = 0, $t = true, $items = array()){
        $i = 0;
        $url = "http://50states.addresses.com/results.php?ReportType=34";

        if(isset($_POST['firstname']))
            $url = $url . "&qf=" . $_POST['firstname'];
        
        if(isset($_POST['lastname']))
            $url = $url . "&qn=" . $_POST['lastname'];     
        
        if(isset($_POST['city'])){        
            $locality = $_POST['city'];
            $locality = str_replace(" ", "+", $locality);
            $url = $url . "&qc=" . $locality;
        }

        if(isset($_POST['state'])){
            $locality = $_POST['state'];
            $locality = str_replace(" ", "+", $locality);
            $url = $url . "&qs=" . $locality;
        }
        
        $limit *= 10;

        while($t && $i <= $limit){            
            $url2 =  $url . "&qi=$i&qk=10";

            $html->load_file($url2);

            $posts = $html->find('table[class=resultTable]');

            if(count($posts) > 0){

                foreach($posts as $post){
                    foreach($post->find('td[class=nameAndAddress]') as $e){
                        $item[$len]['Office Phone'] = $e->find('div[class=listingInfo] div[class=phone]', 0)->innertext;
                        $item[$len]['Office Phone'] = str_replace(array(' ', '(', ')', '-'), "", $item[$len]['Office Phone']);
                        $item[$len]['Full Name'] = $e->find('div a[class=resultName]', 0)->innertext;                                                                    
                        $direccion = explode("<br>", $e->find('div[class=listingInfo] div', 0)->innertext);
                        $state = explode(" ", $direccion[1]);
                        $direccion[2] = $state[3];
                        $zipcode = explode(" ", $direccion[1]);
                        $direccion[3] = $zipcode[4];
                        $address = explode(",", $direccion[1]);
                        $direccion[1] = $address[0];
                        $item[$len]['Primary Address State'] = $direccion[2];
                        $item[$len]['Primary Address City'] = $direccion[1];
                        $item[$len]['Primary Address Street'] = $direccion[0];
                        $item[$len]['Primary Address Postalcode'] = $direccion[3];                      
                    }   
                    $len++;
                }

                $i += 10;
            }else
                $t = false;                    
        }

        if($len > 0){
            outputCSV(array('Office Phone', 'Full Name', 'Primary Address State', 'Primary Address City', 'Primary Address Street', 'Primary Address Postalcode'), $item);
        }else
            echo "<span>No se encontraron datos con los criterios de busqueda proporcionados!!!</span>";
    }

    //Hispanic yellow pages By Jean Robles
    function HispanicYellowPages($html, $limit, $i = 1, $len = 0, $t = true, $items = array()){

        if(isset($_POST['firstname'])){
            $keyword = $_POST['firstname'];
            $keyword = str_replace(" ", "+", $keyword);
        }

        if(isset($_POST['zipcode'])){
            $zip = $_POST['zipcode'];
            $zip = str_replace(" ", "+", $zip);
        }

        if(isset($_POST['city'])){
            $city = $_POST['city'];
            $city = str_replace(" ", "+", $city);
        }

        if(isset($_POST['state'])){
            $state = $_POST['state'];
            $state = str_replace(" ", "+", $state);
        }
            
        
    
        while($t && $i <= $limit){
            $url = "http://hispanicyellowpagesusa.com/search?zip_code=".$zip."&keyword=".$keyword."&state=".$state."&city=".$city."&page=".$i;

            $html->load_file($url);

            $posts = $html->find('div[id=search_page_listing_results]');

            $posts_alt = $html->find('div[id=search_page_listing_results]');

            if(count($posts) > 0){

                foreach($posts as $post){
                    
                    foreach($post->find('div[id=search_page_listing_item]') as $client){
                        ####Obtain Full Name#####
                        $left = $client->find('div[id=search_page_listing_item_left]', 0);
                        $name = $left->find('p a',0);
                        $right = $client->find('div[id=search_page_listing_item_right]', 0);
                        ####Obtain Full Address####
                        $infoAddress = $right->find('p',1);
                        $infoAux = explode(',',$infoAddress);
                        $stateAddess = explode(' ',$infoAux[1]);
                        $primaryAddress = explode(' ',$infoAux[0]);

                        ####Obtain Telephone####
                        $telfContainer = $right->find('p',2);
                        $aContainer = $telfContainer->find('a',0);
    
                        ####Telephone Number####
                        $urlPhone = "http://hispanicyellowpagesusa.com" . $aContainer->href;
                        $allData = file_get_html($urlPhone);
                        $allDataContent = $allData->find('div[id=listing_view]', 0);
                        $allDataRow = $allDataContent->find('div[class=row]',0);
                        $allDataCol = $allDataRow->find('div[class=col-sm-4]',0);
                        $item[$len]['Office Phone'] = str_replace(array(' ', '(', ')', '-'), "",$allDataCol->find('p',4)->innertext);

                        //$item[$len]['Office Phone'] = "http://hispanicyellowpagesusa.com" . $aContainer->href;
    
                        ####Full Name####
                        $item[$len]['Full Name'] = $name->innertext;
    
                        // ####State####
                        $item[$len]['Primary Address State'] = str_replace(array(' ', '<p>', '</p>', '"'), "", $stateAddess[1]);
    
                        ####City####
                        
                        $item[$len]['Primary Address City'] = str_replace(array(' ', '<p>', '</p>', '"'), "", $infoAux[0]);
    
                        ####Address####
                        $item[$len]['Primary Address Street'] = str_replace('"', "", $right->find('p',0)->innertext);
    
                        ####Postal Code####
                        $item[$len]['Primary Address Postalcode'] = str_replace(array(' ', '<p>', '</p>', '"'), "", $stateAddess[2]);
                       
    
                        $len++;
                    }
                }

                foreach($posts_alt as $post_alt){
                    
                    foreach($post_alt->find('div[id=search_page_listing_item]') as $client){
                        ####Obtain Full Name#####
                        $left = $client->find('div[id=search_page_listing_item_left]', 0);
                        $name = $left->find('p a',0);
                        $right = $client->find('div[id=search_page_listing_item_right]', 0);
                        ####Obtain Full Address####
                        $infoAddress = $right->find('p',1);
                        $infoAux = explode(',',$infoAddress);
                        $stateAddess = explode(' ',$infoAux[1]);
                        $primaryAddress = explode(' ',$infoAux[0]);

                        ####Obtain Telephone####
                        $telfContainer = $right->find('p',2);
                        $aContainer = $telfContainer->find('a',0);
    
                        ####Telephone Number####
                        $urlPhone = "http://hispanicyellowpagesusa.com" . $aContainer->href;
                        $allData = file_get_html($urlPhone);
                        $allDataContent = $allData->find('div[id=listing_view]', 0);
                        $allDataRow = $allDataContent->find('div[class=row]',0);
                        $allDataCol = $allDataRow->find('div[class=col-sm-4]',0);
                        $item[$len]['Office Phone'] = str_replace(array(' ', '(', ')', '-'), "",$allDataCol->find('p',4)->innertext);

                        //$item[$len]['Office Phone'] = "http://hispanicyellowpagesusa.com" . $aContainer->href;
    
                        ####Full Name####
                        $item[$len]['Full Name'] = $name->innertext;
    
                        // ####State####
                        $item[$len]['Primary Address State'] = str_replace(array(' ', '<p>', '</p>', '"'), "", $stateAddess[1]);
    
                        ####City####
                        
                        $item[$len]['Primary Address City'] = str_replace(array(' ', '<p>', '</p>', '"'), "", $infoAux[0]);
    
                        ####Address####
                        $item[$len]['Primary Address Street'] = str_replace('"', "", $right->find('p',0)->innertext);
    
                        ####Postal Code####
                        $item[$len]['Primary Address Postalcode'] = str_replace(array(' ', '<p>', '</p>', '"'), "", $stateAddess[2]);
                       
    
                        $len++;
                    }
                }

                $i++;
    
            }else
                $t = false;
        }
        
        if($len > 0){
            outputCSV(array('Office Phone', 'Full Name', 'Primary Address State', 'Primary Address City', 'Primary Address Street', 'Primary Address Postalcode'), $item);
        }else
            echo "<span>No se encontraron datos con los criterios de busqueda proporcionados!!!</span>";
    }
    
    $i = 1;
    $len = 0;
    $t = true;
    $items = array();
    
    if(isset($_POST['limit']))
        $limit = $_POST['limit'];
    else
        $limit = 10;
    
    if($_POST['origin'] == 0)
        yellowPages($html, $limit, $i, $len, $t, $items);
    if($_POST['origin'] == 1)
        whitePages($html, $limit, $i, $len, $t, $items);
    if($_POST['origin'] == 2)
        HispanicYellowPages($html, $limit, $i, $len, $t, $items);
    
    
?>
