<?php
Class Dossiersmodel extends CI_Model 
{
	function __Construct()
	{
		parent::__construct();		
	}
	
	function save($type, $data, $edit = false){
		try {
			if ($edit){
				switch ($edit) {								
					case 'dossier':
						$where = array('_id' => new mongoID($data['dossier_id']));
					break;
				}				
				$this->mongo_db->where($where)->update($type, $data);
			}else {
				if ($type=="bijlagen"){					
					$filename = $_FILES['bijlage']['name'];
					$uploaded = $this->mongo_db->gridFs_storeUpload('bijlages', 'bijlage', $_FILES['bijlage']['name']);
					if ($uploaded){
						return $uploaded;
					} else {
						return FALSE;
					}
				} else {	
					$this->mongo_db->insert($type, $data);
				}
			}
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
		return true;		
	}
	
	function dossier_selectie()
	{
		$dossiers = $this->mongo_db->order_by(array('dossier' => 1))->get('dossiers');
		$arr = array();
		foreach ($dossiers as $pkey => $pname){
			$arr[$dossiers[$pkey]['dossier']] = $dossiers[$pkey]['dossier'];
		}
		return $arr;
	}
	
	function dossier_info($type = null, $dossier_id = array(), $rapport = array(), $order_by = array())
	{
		if ($type == 'view'){
			$dossier = $this->mongo_db->where(array('dossier' => str_replace('_', '/', $dossier)))->get('dossiers');
			return $dossier;
		} else if ($type == 'list') {
			$type = array(
				' ' => '--selecteer--',
				'email_kabinet' => 'EMAILKAB',
				'fietspaden' => 'MPF', 
				'wegen' => 'Wegen',
				'parlementaire_vragen' => 'PV',
				'kabinetsnotas' => 'KAB'
			);
			$dossier_list = $this->mongo_db->where($dossier_id)->order_by(array('dossiernummer' => -1))->get('dossiers');
			$this->load->library('table');
			$tmpl = array (
	           	'table_open' => '<table id="dossiers" border="0" cellpadding="4" cellspacing="0" style="width: auto; min-width: 720px; font-size: 12px;">',
	        );
			$this->table->set_template($tmpl);	
			$this->table->set_heading('Dossier', 'Omschrijving', 'Ontvangen op', 'Te Herrineren op', 'Antwoord tegen', 'Uitgeschreven op');		
			foreach ($dossier_list as $key => $value){
				if($dossier_list[$key]['datum_melding']->sec == 0){
					$dossier_list[$key]['datum_melding'] = '';
				} else {
					$dossier_list[$key]['datum_melding'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], date('d-m-Y', $dossier_list[$key]['datum_melding']->sec), 'class="link"');
				}
				if (array_key_exists('te_herinneren_op', $dossier_list[$key]))
				if($dossier_list[$key]['te_herinneren_op']->sec == 0){
					$dossier_list[$key]['te_herinneren_op'] = '';
				} else {
					$dossier_list[$key]['te_herinneren_op'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], date('d-m-Y', $dossier_list[$key]['te_herinneren_op']->sec), 'class="link"');
				}
				if($dossier_list[$key]['antwoord_tegen']->sec == 0){
					$dossier_list[$key]['antwoord_tegen'] = '';
				} else {
					$dossier_list[$key]['antwoord_tegen'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], date('d-m-Y', $dossier_list[$key]['antwoord_tegen']->sec), 'class="link"');
				}
				if($dossier_list[$key]['uitgeschreven_op']->sec == 0){
					$dossier_list[$key]['uitgeschreven_op'] = '';
				} else {
					$dossier_list[$key]['uitgeschreven_op'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], date('d-m-Y', $dossier_list[$key]['uitgeschreven_op']->sec), 'class="link"');
				}
				$dossier_nr = $type[$dossier_list[$key]['type']].'/'.substr($dossier_list[$key]['dossiernummer'], 0, 4).'/'.substr($dossier_list[$key]['dossiernummer'], 4, 7);
				
				$this->table->add_row(
					anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], $dossier_nr, 'class="link"'), 
					anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], substr($dossier_list[$key]['omschrijving'], 0, 45).'...', 'class="link"'),  
					$dossier_list[$key]['datum_melding'],  
					$dossier_list[$key]['te_herinneren_op'], 
					$dossier_list[$key]['antwoord_tegen'], 
					$dossier_list[$key]['uitgeschreven_op']);
			}
			$content = $this->table->generate();
			return $content;
		} else if ($type == 'data')	{
			$dossier = $this->mongo_db->where($dossier_id)->order_by(array('dossiernummer' => -1))->get('dossiers');
			/*$next = $this->mongo_db->where_lt('dossiernummer', (int)$dossier[0]['dossiernummer'])->order_by(array('dossiernummer' => -1))->limit(1)->get('dossiers');
			$prev = $this->mongo_db->where_gt('dossiernummer', (int)$dossier[0]['dossiernummer'])->order_by(array('dossiernummer' => 1))->limit(1)->get('dossiers');
			return array('dossier' => $dossier, 'prev' => $prev, 'next' => $next);*/
			return $dossier;
		} else if ($type == 'rapport')	{
			$dossier = $this->mongo_db->where($rapport)->order_by($order_by)->get('dossiers');
			return $dossier;
		} else if ($type == 'nummer'){
			$dossier = $this->mongo_db->where(array('type' => $dossier_id))->order_by(array('dossiernummer' => -1))->limit(1)->get('dossiers');
			return $dossier;
		} else if ($type == 'oorzaken'){
			$dossier = $this->mongo_db->where(array('_id' => $dossier_id))->get('oorzaken');
			return $dossier;
		}
	}

	function filesearch($id){
		$file =  $this->mongo_db->gridFs_getFile('bijlages', $id);
		return $file;
	}
	
	function filter($search, $filter = ''){
		try {
			$search = new MongoRegex('/'.str_replace('_', '/', $search).'/i');
			$dossiers = $this->mongo_db->where(array('dossier' => array('$regex' => $search, '$options' => 'i')))->get('dossiers');
			return $dossiers;
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}
	
	function check($dossier, $originele_id)
	{
		$dossier = $this->mongo_db->where(array('dossier' => array('$regex' => $dossier, '$options' => 'i')))->get('dossiers');
		if(empty($dossier[0]))
		{
			return array('result' => true, 'dossier' => $dossier);
		} else {
			if ($originele_id != 'undefined')
			{
				$origineel = $this->mongo_db->where(array('_id' => $originele_id))->get('dossiers');
				if ($dossier[0]['dossier'] == $origineel[0]['dossier']){
					return array('result' => true, 'dossier' => $dossier);
				} else {
					return array('result' => false, 'dossier' => $dossier);
				}
			} else {
				return array('result' => false, 'dossier' => $dossier);
			}			
		}
	}	
}
?>