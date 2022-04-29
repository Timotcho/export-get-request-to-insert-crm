<?php

class API_ZOHO {
	public $a_data = array();
	public function __construct(){
	}
	public function APIV2_PointEntree($CONF) {
		#Creation du token par identification
		//$this->APIV2_getTokenIdentification($CONF);
		$this->APIV21_getTokenIdentification($CONF);
		if (!empty($this->access_token)){
			#Appel WS BIHR pour le TicketId
			$this->APIV2_getTicketID($CONF);
            $this->APIV2_getTicketID2($CONF);
            $this->APIV2_getTicketID3($CONF);
			//$this->a_data
			#Decompression du fichier mis a dispo
			$this->DecompressionAndWorkingData($CONF);
			#destruction du fichier origin
			#@unlink($CONF['APIV2_FILEPATH_BIHR'].'/bihrfiles');
		} else {
			echo "Error d'access token";
		}
	}
	
	public function APIV21_getTokenIdentification($CONF){
		#on doit poster un formulaire pour avoir un json en retour composer de : {"access_token":"dkjgd","token_type":"bearer","expires_in":500}
		$params = array(
				"client_id"=>$CONF['APIV2_client_id'],
				"client_secret"=>$CONF['APIV2_client_secret'],
				"grant_type"=>$CONF['Grant_type'],
				#"code"=>$CONF['code'],
				"refresh_token"=>$CONF['refresh_token'],
				#"scope"=>$CONF['scope'],
				#'response_type'=>'code',
				#"access_type"=>$CONF['access_type'],
				#"redirect_uri"=>$CONF['redirect_uri'],
		);
		$CONF['APIV2_URL_Authentification'] = 'https://accounts.zoho.com/oauth/v2/token';
		$string="";
		foreach($params as $key => $val)
			$string.=$key."=".urlencode($val)."&";
			$string = substr($string,0,-1);
			$defaults = array(
					CURLOPT_URL => $CONF['APIV2_URL_Authentification'],
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => $string,
					CURLOPT_RETURNTRANSFER=> true, // Retourner le contenu téléchargé dans une chaine (au lieu de l'afficher directement)
					CURLOPT_HTTPHEADER	=> array('Transfer-Encoding: chunked'),
					CURLOPT_HEADER        => false, // Ne pas inclure l'entête de réponse du serveur dans la chaine retournée,
					CURLOPT_VERBOSE		=> 0,
					CURLOPT_SSL_VERIFYPEER=> false
			);
	
			$ch = curl_init();
			curl_setopt_array($ch, ($defaults));
			$responce =  json_decode(curl_exec($ch),true);
			var_dump($responce);
			$this->access_token="";
			if (empty($responce['error']))
				$this->access_token = $responce['access_token'];
		
				$repertoire = 'Data/';
			$response_json = str_replace("\'","\\'",$response);
			mkdir($repertoire, 0777, true);
			$handle = fopen($repertoire.date("Y-m-d")."token.json",'a+');
			
			fwrite($handle,$this->access_token);
			fclose($handle);
	}
	/**
	 * Methode de creation de l'entete du fil_get_contents
	 */
	public function APIV2_CreateHeaderREST($CONF){
		$opts = array(
				'http'=>array(
						'method'=>'GET',
						'header'=>'Authorization: bearer '.$this->access_token."\r\n"
				),
				"ssl"=>array(
					"cafile"=>"/connecteur.yannr.fr/getrequest/cacert.pem",
					"verify_peer"=>false,
					"verify_peer_name"=>false,
				)
		);
		$context = stream_context_create($opts);
		return $context;
	}
	function curl_get_file_contents($URL)
    {
        $c = curl_init();
		$authorization="Authorization: Bearer ".$this->access_token;
		curl_setopt($c, CURLOPT_HTTPHEADER, array($authorization ));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($c, CURLOPT_HEADER,false);
		
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
            else return FALSE;
    }
	public function APIV2_getTicketID($CONF) {
		#Methode REST
		//création d'un contexte d'appel de type GET
		$context = $this->APIV2_CreateHeaderREST($CONF);
		//print_r($context);
		//Utilisation du contexte dans l'appel
		/*$response = file_get_contents(
				$CONF['APIV2_URL_GetCatalogue'],
				false,
				$context);*/
				//url to get info 
		$response = $this->curl_get_file_contents($CONF['APIV2_URL_GetCatalogue']);

		$a_data = json_decode($response,true);
		
		$this->a_data = $a_data;
	}
    public function APIV2_getTicketID2($CONF) {
		#Methode REST
		//création d'un contexte d'appel de type GET
		$context1 = $this->APIV2_CreateHeaderREST($CONF);
		//print_r($context);
		//Utilisation du contexte dans l'appel
		/*$response = file_get_contents(
				$CONF['APIV2_URL_GetCatalogue'],
				false,
				$context);*/
				//url to get info 
		$response2 = $this->curl_get_file_contents($CONF['APIV2_URL1_GetCatalogue']);

		$a_data1 = json_decode($response2,true);
		#print_r($a_data);
		$this->a_data1 = $a_data1;
	}
    public function APIV2_getTicketID3($CONF) {
		#Methode REST
		//création d'un contexte d'appel de type GET
		$context1 = $this->APIV2_CreateHeaderREST($CONF);
		//print_r($context);
		//Utilisation du contexte dans l'appel
		/*$response = file_get_contents(
				$CONF['APIV2_URL_GetCatalogue'],
				false,
				$context);*/
				//url to get info 
		$response3 = $this->curl_get_file_contents($CONF['APIV2_URL2_GetCatalogue']);

		$a_data2 = json_decode($response3,true);
		#print_r($a_data);
		$this->a_data2 = $a_data2;
	}
	
	public function Cfct_callCurl($url,$typedenvoi="GET"){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $typedenvoi);         
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$header[] = 'Content-Type: application/json';
		$header[] = 'Accept: */*';
		$header[] = 'Cache-Control:no-cache';
		$header[] = 'Origin: https://www.zohoapis.com/';
		if (!empty($this->data)) $header[] = "Content-Length: ".strlen($this->data);
		$header[] = "Connection: Keep-Alive";
		$header[] = "Accept-Encoding: gzip, deflate, br";
		$header[] = "User-Agent: Apache-HttpClient/4.1.1";
		if (!empty($this->header)){
			foreach($this->header as $key => $val)
				$header[] = $val;
		}
		if (!empty($this->data)){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data);
			curl_setopt($curl, CURLOPT_VERBOSE, true);
			curl_setopt($curl, CURLOPT_FAILONERROR, true);
		}
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$this->response = curl_exec($curl);
		$this->error = curl_error($curl);
		$this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($this->debug) echo "ERROR:".print_r($this->error,true)."\n";
		if ($this->debug) echo "STATUS:".print_r($this->status,true)."\n";
		if ($this->debug) echo "RESPONSE:".print_r($this->response,true)."\n";
		curl_close($curl);
	}
	/**
	 * 
	 * Mise à jour des nouvelles activités
	 */
	public function updatebdd($data){
		$con=mysqli_connect ("mysql-maps-bdd.alwaysdata.net", 'maps-bdd', '5PbM40S^aP7i','maps-bdd_location');
		if (!$con) {
			die('Not connected : ' . mysqli_connect_error());
		}
		$query = "update locations set location_status = $confirmed WHERE id = $id ";
	
	$result = mysqli_query($con,$query);
    echo "Inserted Successfully";
    if (!$result) {
        die('Invalid query: ' . mysqli_error($con));
    }
}
	/**
	*	Traitement json
	*/
	public function DecompressionAndWorkingData($CONF){
		
		$array_page1 = $this->a_data["data"];
        $array_page2 = $this->a_data1["data"];
        $array_page3 = $this->a_data2["data"];
	
        $array = array_merge($array_page1,$array_page2,$array_page3);
		
		$access_token = $this->access_token;
		$client=array();
		$cpt=1;
        $array_count = array();
		$array_count1 = array();
		#echo "<pre>".print_r($array["data"],true)."</pre>";
		 foreach($array as $items) {
            $date =substr($items["End_DateTime"],0,10);
            $oneYearOn = date('Y-m-d',strtotime(date("Y-m-d", time()) . " - 182 day"));
            $date_actually = date('Y-m-d');
			array_push($array_count1,$items["What_Id"]["id"]);
        #Data in array for 6month 
            if($date_actually>=$date && $date>=$oneYearOn) {
                array_push($array_count,$items["What_Id"]["id"]);
            }   
            $count_values=array_count_values($array_count);
			$count_array_all=array_count_values($array_count1);
			#echo "<pre>".print_r($items,true)."</pre>";
			$date =substr($items["End_DateTime"],0,10);
            #IF description = NUll change to ""
            if(isset($items["Description"])) {
               
            }else{
                $items["Description"]=" ";
            }
			$array_traitement["data"] = array(["id"=>$items["What_Id"]["id"], "Nom_du_commercial" => $items["Owner"]["name"], "Date_visite_".$count_values[$items["What_Id"]["id"]].""=>$date,"Description_visite_".$count_values[$items["What_Id"]["id"]].""=>$items["Description"],"Nombre_visites"=>$count_array_all[$items["What_Id"]["id"]],"Nombre_visites_dans_les_6_mois"=>$count_values[$items["What_Id"]["id"]]]);
			$format_envoi = json_encode($array_traitement);
			var_dump($format_envoi);
			if($items["$se_module"] = "Accounts"){
				$url_post = "https://www.zohoapis.com/crm/v2/Accounts/upsert";
			}else {
				$url_post = "https://www.zohoapis.com/crm/v2/Leads/upsert";
			}
			$ch = curl_init();
            curl_setopt ( $ch, CURLOPT_URL, $url_post );
            curl_setopt ( $ch, CURLOPT_POST, 1 );
            curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS,$format_envoi);
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Zoho-oauthtoken '. $access_token,
                'Content-Type: application/x-www-form-urlencoded'
                 ) );
       
            $response = curl_exec( $ch );
            $response = json_decode ( $response );
			var_dump($response);
			
		
	}
}
}
$CONF = array(
'APIV2_client_id'=>'1000.JWPGVEF4C71IINANN0OM85B1PGJJ8E',
'APIV2_client_secret'=>'d51dc65a8150af929c3e5c3473888ed7202f27fbf3',
'Grant_type'=>'refresh_token',
'code'=>'1000.875b4a944bf6abe33bb2b6216c64b2e7.53806bc2a7859073c5c1079c8a653724',
'refresh_token'=>'1000.286a6b4af5c543b81e4d791a042798e3.4454608f043d414474462bf09b0fc2ee',
'APIV2_URL_GetCatalogue'=>'https://www.zohoapis.com/crm/v2/Events?page=1',
'APIV2_URL1_GetCatalogue'=>'https://www.zohoapis.com/crm/v2/Events?page=2',
'APIV2_URL2_GetCatalogue'=>'https://www.zohoapis.com/crm/v2/Events?page=3',
'access_type'=>'online',
'scope'=> 'ZohoCRM.modules.ALL,ZohoCRM.settings.ALL,ZohoCRM.users.ALL,ZohoCRM.org.ALL,aaaserver.profile.ALL,ZohoCRM.settings.functions.all,ZohoCRM.notifications.all,ZohoCRM.coql.read,ZohoCRM.files.create,ZohoCRM.bulk.all',
'redirect_uri'=>'https://www.zoho.com',
);

$obj_zoho = new API_ZOHO();

$obj_zoho->APIV2_PointEntree($CONF);


