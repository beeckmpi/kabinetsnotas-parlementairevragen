<?php
Class Beherenmodel extends CI_Model 
{
	function __Construct()
	{
		parent::__construct();
		
	}
	function save($type, $data, $edit = false){
		try {
			if ($edit){
				switch ($type) {					
					case 'provincies':
						$where = array('provincie' => $data['provincie']);
						break;
					case 'districten':
						$where = array('_id' => $data['_id']);
						unset($data['_id']);
						break;
					case 'rollen':
						$where = array('rol' => $data['rol']);
						break;
					case 'te_behandelen':
						$where = array('_id' => $data['_id']);
						unset($data['_id']);
						break;
					case 'parlementairen':
						$where = array('_id' => $data['_id']);
						unset($data['_id']);
						break;
					case 'wegen':
						$where = array('_id' => $data['_id']);
						unset($data['_id']);
						break;
				}
				$this->mongo_db->where($where)->update($type, $data);
			}else {
				$this->mongo_db->insert($type, $data);				
			}
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
		return true;		
	}
	
	function districten_lijst($type = 'list')
	{
		$provincies = $this->mongo_db->where(array('actief' => 'ja'))->order_by(array('provincie' => 1))->get('provincies');
		if (empty($provincies)){
				return false; // als er geen provincies worden gevonden, dan zal er geen lijst getoond worden.
		} else {
			$locatie = array();
			foreach ($provincies as $pkey => $pname){
				$provincie = $provincies[$pkey]['provincie'];
				$districten = $this->mongo_db->where(array('provincie' => $provincie))->order_by(array('district' => 1))->get('districten');
				foreach ($districten as $dkey => $dname){
					$code = $districten[$dkey]['code'];
					$district = $districten[$dkey]['district'];
					$locatie[$provincie][$district] = $districten[$dkey];
					$select[$code] = array('class' => str_replace(" ", "_",$provincie), 'value' => $code, 'district' => $district);
				}
			}
			if ($type == 'list'){
				return $locatie;	//Elke provincie krijgt de lijst met gegevens van de districten.
			} else if ($type == 'select'){
				return $select;
			}	
		}
	}

	function provincies_lijst()
	{
		$provincies = $this->mongo_db->where(array('actief' => 'ja'))->get('provincies');
		$provincie_arr = array();
		foreach ($provincies as $pkey => $pname){
			$p = $provincies[$pkey]['provincie'];
			$provincie_arr[$p] = $p;			
		}
		return $provincie_arr;	
	}
	
	function getProvincie($afkorting){
		$provincie = $this->mongo_db->where(array('afkorting' => $afkorting))->get('provincies');
		return $provincie;
	}
	
	function getDistricten($provincie = ''){
	    if ($provincie == ''){
	        $districten = $this->mongo_db->order_by(array('district' => 1))->get('districten');
	    } else {
	        $districten = $this->mongo_db->where(array('provincie' => $provincie))->order_by(array('district' => 1))->get('districten');
	    }		
		return $districten;
	}
	
	function rollen_lijst()
	{
		$rollen = $this->mongo_db->where(array('actief' => 'ja'))->get('rollen');
		$rol_arr = array();
		$rol_arr[] = array(array('data' => form_checkbox(), 'style' => 'width: 20px'), 'Rollen', array('data' => 'Actief', 'style' => 'width: 60px'));
		foreach ($rollen as $pkey => $pname){
			$p = $rollen[$pkey]['rol'];
			$a = $rollen[$pkey]['actief'];
			$rol_arr[] = array(form_checkbox(), $p, $a);			
		}
		return $rol_arr;	
	}
	
	function te_behandelen_lijst($type="ja", $data = 'lijst')
	{
		if ($type== "beide"){
			$where = array();
		} else {
			$where = array('actief' => $type);
		}
			
		$te_behandelen = $this->mongo_db->where($where)->order_by(array('naam' => 1))->get('te_behandelen');
		$tb_arr = array();
		if ($data == 'lijst'){
			$tb_arr[] = array('Te behandelen', array('data' => 'Actief', 'style' => 'width: 60px'), array('data' => 'Deactiveren', 'style' => 'width: 60px'));
		}
		foreach ($te_behandelen as $pkey => $pname){
			if ($data == 'lijst'){
			if(array_key_exists('naam', $te_behandelen[$pkey])){
					$p = '<div contentEditable="true" class="te_bepalen_edit" data-id="'.$te_behandelen[$pkey]['_id'].'">'.$te_behandelen[$pkey]['naam'].'</div><div class="te_bepalen_orig">'.$te_behandelen[$pkey]['naam'].'</div>';
					if (array_key_exists('email', $te_behandelen[$pkey])){
						$p .= '<div contentEditable="true" class="te_bepalen_email_edit" data-id="'.$te_behandelen[$pkey]['_id'].'">'.$te_behandelen[$pkey]['email'].'</div><div class="te_bepalen_email_orig">'.$te_behandelen[$pkey]['email'].'</div><button class="te_bepalen_save">Bewaren</button><button class="te_bepalen_cancel">Annuleren</button>';
					} else {
						$p .= '<div contentEditable="true" class="te_bepalen_email_edit" data-id="'.$te_behandelen[$pkey]['_id'].'" style="width: 200px;" placeholder="emailadressen">&nbsp;</div><div class="te_bepalen_email_orig"></div><button class="te_bepalen_save">Bewaren</button><button class="te_bepalen_cancel">Annuleren</button>';
					}
				} else {
					$p = '';
				}
				$a = '<span class="janee">'.$te_behandelen[$pkey]['actief'].'</span>';
				if ($te_behandelen[$pkey]['actief'] == 'ja'){
					$activatie = 'deactiveren';
				} else {
					$activatie = 'activeren';
				}
				$d = '<a href='.site_url('ajax/beheren/te_behandelen/'.$activatie.'/'.$te_behandelen[$pkey]['_id']).' class="tb_deactive">'.$activatie.'</a>';
				$tb_arr[] = array($p, $a, $d);	
			} else if ($data == 'data'){
				$tb_arr[$te_behandelen[$pkey]['naam']] = $te_behandelen[$pkey]['naam'];
			}		
		}
		return $tb_arr;
	}
	
	function doorsturen_lijst($behandelen_door, $type = 'default'){
		if ($type == 'default'){
			$users = array();
			$districten = array();
			if (!is_array($behandelen_door) && $behandelen_door != ''){
				$behandelen_door = explode(', ', $behandelen_door);
			}		
			foreach($behandelen_door as $key => $value){
				$users[$value] = $this->mongo_db->where(array('location.afkorting' => $value))->order_by(array('name' => 1))->get('users');
				$provincie = $this->mongo_db->where(array('afkorting' => $value))->get('provincies');			
				$districten[$value] = $this->mongo_db->where(array('provincie' => $provincie[0]['provincie']))->order_by(array('name' => 1))->get('districten');
			}
			return array('users' => $users, 'districten' => $districten);
		} else if ($type == 'data') {
			$user_arr = array();
			if (user_access(array('Administrators', 'Stafdienst'))){
				$search = array();
			} else {
				$search = array('location.afkorting' => $behandelen_door);
			}
			$users = $this->mongo_db->where($search)->order_by(array('name' => 1))->get('users');
			foreach ($users as $key => $user){
				if($user['first_name'] != ''){
					$user_arr[$user['username']] = $user['name'].' '.$user['first_name'];
				}
			}
			return $user_arr;
		}
	}
	function te_behandelen($naam){
		$naam = explode(', ', $naam);
		foreach($naam as $key => $value){
			$namen[$value] = $this->mongo_db->where(array('naam' => $value))->get('te_behandelen');
		}
		return $namen;
	}
	function parlementairen_lijst($type="ja", $data = 'lijst')
	{
		if ($type== "beide"){
			$where = array();
		} else {
			$where = array('actief' => $type);
		}
			
		$parlementairen = $this->mongo_db->where($where)->order_by(array('naam' => 1))->get('parlementairen');
		$tb_arr = array();
		if ($data == 'lijst'){
			$tb_arr[] = array('Parlementairen', array('data' => 'Actief', 'style' => 'width: 60px'), array('data' => 'Deactiveren', 'style' => 'width: 60px'));
		}
		foreach ($parlementairen as $pkey => $pname){
			if ($data == 'lijst'){
			if(array_key_exists('naam', $parlementairen[$pkey])){
					$p = '<div contentEditable="true" class="parlementarier_edit" data-id="'.$parlementairen[$pkey]['_id'].'">'.$parlementairen[$pkey]['naam'].'</div><div class="parlementarier_orig">'.$parlementairen[$pkey]['naam'].'</div><button class="parlementarier_save">Bewaren</button><button class="parlementarier_cancel">Annuleren</button>';
				} else {
					$p = '';
				}
				$a = '<span class="janee">'.$parlementairen[$pkey]['actief'].'</span>';
				if ($parlementairen[$pkey]['actief'] == 'ja'){
					$activatie = 'deactiveren';
				} else {
					$activatie = 'activeren';
				}
				$d = '<a href='.site_url('ajax/beheren/parlementairen/'.$activatie.'/'.$parlementairen[$pkey]['_id']).' class="tb_deactive">'.$activatie.'</a>';
				$tb_arr[] = array($p, $a, $d);	
			} else if ($data == 'data'){
				$tb_arr[$parlementairen[$pkey]['naam']] = $parlementairen[$pkey]['naam'];
			}		
		}
		return $tb_arr;
	}
	function wegen_lijst($type="ja", $data = 'lijst')
	{
		if ($type== "beide"){
			$where = array();
		} else {
			$where = array('actief' => $type);
		}
			
		$wegen = $this->mongo_db->where($where)->order_by(array('code' => 1))->get('wegen');
		$tb_arr = array();
		if ($data == 'lijst'){
			$tb_arr[] = array('wegen', array('data' => 'Actief', 'style' => 'width: 60px'), array('data' => 'Deactiveren', 'style' => 'width: 60px'));
		}
		foreach ($wegen as $pkey => $pname){
			if ($data == 'lijst'){
			if(array_key_exists('naam', $wegen[$pkey])){
					$p = '<div contentEditable="true" class="weg_edit weg_edit_code" data-id="'.$wegen[$pkey]['_id'].'">'.$wegen[$pkey]['code'].'</div><div contentEditable="true" class="weg_edit weg_edit_naam" data-id="'.$wegen[$pkey]['_id'].'" style="display:inline">'.$wegen[$pkey]['naam'].'</div><div class="weg_orig_code">'.$wegen[$pkey]['code'].'</div><div class="weg_orig_naam">'.$wegen[$pkey]['naam'].'</div><button class="weg_save">Bewaren</button><button class="weg_cancel">Annuleren</button>';
				} else {
					$p = '';
				}
				$a = '<span class="janee">'.$wegen[$pkey]['actief'].'</span>';
				if ($wegen[$pkey]['actief'] == 'ja'){
					$activatie = 'deactiveren';
				} else {
					$activatie = 'activeren';
				}
				$d = '<a href='.site_url('ajax/beheren/wegen/'.$activatie.'/'.$wegen[$pkey]['_id']).' class="tb_deactive">'.$activatie.'</a>';
				$tb_arr[] = array($p, $a, $d);	
			} else if ($data == 'data'){
				$tb_arr[$wegen[$pkey]['naam']] = $wegen[$pkey]['naam'];
			}		
		}
		return $tb_arr;
	}
	function selectielijsten($type)
	{
		if($type == "la"){
			$la = $this->mongo_db->where(array('type' => 'la'))->order_by(array('naam' => 1))->get('lijsten');
			$arr = array();
			foreach ($la as $pkey => $pname){
				$arr[$la[$pkey]['titel'].' '.$la[$pkey]['voornaam'].' '.$la[$pkey]['naam']] = $la[$pkey]['titel'].' '.$la[$pkey]['voornaam'].' '.$la[$pkey]['naam'];
			}
		} else if($type == "dossierbeheerders"){
			$la = $this->mongo_db->where(array('type' => 'dossierbeheerder'))->order_by(array('naam' => 1))->get('lijsten');
			$arr = array();
			foreach ($la as $pkey => $pname){
				$arr[$la[$pkey]['voornaam'].' '.$la[$pkey]['naam']] = $la[$pkey]['voornaam'].' '.$la[$pkey]['naam'];
			}
		} else if($type == "delegatie"){
			$la = $this->mongo_db->where(array('type' => 'delegatie'))->order_by(array('delegatie' => 1))->get('lijsten');
			$arr = array();
			foreach ($la as $pkey => $pname){
				$arr[$la[$pkey]['delegatie']] = $la[$pkey]['delegatie'];
			}
		}
		return $arr;
	}
	
	function oorzaken_lijst($type = 'list', $list_name = null, $oorzaak_id = null, $suboorzaak = null)
	{
	
		if ($type == 'list'){
			$oorzaken = array();
			$oorzaken['top'] = $this->mongo_db->where(array('parent' => ''))->order_by(array('naam' => 1))->get('oorzaken');
			foreach ($oorzaken['top'] as $key => $value){
				$naam = $oorzaken['top'][$key]['naam'];
				$oorzaken['middle'][$naam] = $this->mongo_db->where(array('parent' => $naam))->order_by(array('naam' => 1))->get('oorzaken');
				foreach ($oorzaken['middle'][$naam] as $middle => $name){
					$oorzaken['bottom'][$oorzaken['middle'][$naam][$middle]['naam']] = $this->mongo_db->where(array('topparent' => $oorzaken['middle'][$naam][$middle]['parent'], 'parent' => $oorzaken['middle'][$naam][$middle]['naam']))->order_by(array('naam' => 1))->get('oorzaken');
				}
			}
		} else {
			$where = array();
			$oorzaken = array();
			if ($list_name == 'oorzaken') {				
				$where = array('parent' => '');
				$oorzaken_list = $this->mongo_db->where($where)->order_by(array('naam' => 1))->get('oorzaken');				
				foreach ($oorzaken_list as $key => $value)
				{
					$oorzaken[(string)$oorzaken_list[$key]['_id']] = ucfirst($oorzaken_list[$key]['naam']);
				}
				if (isset($oorzaken)){
					array_unshift($oorzaken, '--selecteer--');
				} else {
					$oorzaken = array("" => 'selecteren');
				}
			} else if ($list_name == 'suboorzaken') {		
				$where = array('parent' => $oorzaak_id);			
				$oorzaken_list = $this->mongo_db->where($where)->order_by(array('naam' => 1))->get('oorzaken');
				foreach ($oorzaken_list as $key => $value)
				{
					$oorzaken[(string)$oorzaken_list[$key]['_id']] = ucfirst($oorzaken_list[$key]['naam']);
				}		
				if (isset($oorzaken)){
					array_unshift($oorzaken, '--selecteer--');
				} else {
					$oorzaken = "";
				}				
			} else if ($list_name == 'suboorzaken2') {		
				$where = array('topparent' => $oorzaak_id, 'parent' => $suboorzaak);			
				$oorzaken_list = $this->mongo_db->where($where)->order_by(array('naam' => 1))->get('oorzaken');
				foreach ($oorzaken_list as $key => $value)
				{
					$oorzaken[(string)$oorzaken_list[$key]['_id']] = $oorzaken_list[$key]['naam'];
				}		
				if (isset($oorzaken)){
					array_unshift($oorzaken, '--selecteer--');
				} else {
					$oorzaken = "";
				}				
			}
		}
		return $oorzaken;
	}
	
	
	function lijsten_lijst($type = 'list', $list_name = null)
	{
		if ($type == 'list'){
			
		} else {
			if ($list_name == 'wegen') {
				$where = array('actief' => 'ja');
				$lijst = array();
				$wegen_list = $this->mongo_db->where($where)->order_by(array('code' => 1))->get('wegen');
				
				foreach ($wegen_list as $key => $value)
				{
					$lijst[$wegen_list[$key]['code']] = $wegen_list[$key]['naam'];
				}
				if (isset($lijst)){
					
				} else {
					$lijst = "";
				}
			}
		}	
		return $lijst;
	}
	
	function district_gegevens($code = Null, $district = NULL){
		if ($code != Null || $district !=  Null){
			$where = array();
			if ($code != Null){
				$where['code'] = $code ;
			}
			if ($district != null){
				$where['district'] = $district;
			}
			$districten = $this->mongo_db->where($where)->get('districten');
			return $districten;
		} else {
			return false;
		}		
	}
	
	function oorzaken_id($type = '_id', $naam, $parent = '', $topparent = ''){
		if ($type == '_id'){
			$oorzaken = $this->mongo_db->where(array('_id' => $naam))->get('oorzaken');
		} else {
			$oorzaken = $this->mongo_db->where(array('naam' => $naam, 'parent' => $parent, 'topparent' => $topparent))->get('oorzaken');
		}
		return $oorzaken;
	}
}
?>