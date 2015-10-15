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
				$data = $this->mongo_db->where($where)->update($type, array('$set' => $data));
				return $data;
			}else {
				if ($type=="bijlagen"){					
					$filename = $_FILES['bijlage']['name'];
					$metadata = array('filename' => $_FILES['bijlage']['name'], 'locatie' => $_POST['locatie'],'opmerking' => $_POST['opmerking'], 'user' => $_POST['user']);
					$uploaded = $this->mongo_db->gridFs_storeUpload('bijlages', 'bijlage', $metadata);
					if ($uploaded){
						return $uploaded;
					} else {
						return FALSE;
					}
				} else {	
					$data = $this->mongo_db->insert($type, $data);
					return $data;
				}
			}
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
		return true;		
	}
	function add_notificatie($id, $notificatie){
		try {
			$this->mongo_db->where('_id', $id)->push('notificaties', $notificatie)->update('dossiers');
			//$this->mongo_db->where(array('blog_id'=>123))->push('comments', array('text'=>'Hello world'))->update('blog_posts');
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}
	function beantwoord($id){
		try {
			$dossier = $this->mongo_db->where('_id', $id)->get('dossiers');
			if (array_key_exists('beantwoord', $dossier[0])){
				if ($dossier[0]['beantwoord'] == 'true'){
					$dossier[0]['beantwoord'] = 'false';
				} else {
					$dossier[0]['beantwoord'] = 'true';
				}
			} else {
				$dossier[0]['beantwoord'] = 'true';
			}			
			$this->mongo_db->where('_id', $id)->update('dossiers', $dossier[0]);
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}	
		return true;
	}
	
	function remove($id) {
		if ($this->mongo_db->where(array('_id' => $id))->delete('dossiers')){
			return true;
		}
	}
	function removeFile($file_ID, $dossier_ID) {
		if($dossier_ID != null){
			$this->mongo_db->where(array('_id'=>new mongoID($dossier_ID)))->pull('bijlagen', array('_id' => $file_ID))->update('dossiers');
		}
		$this->mongo_db->gridFS_deleteFile('bijlages', $file_ID);
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
	
	function force_update() {
		$dossiers = $this->mongo_db->get('dossiers');
		foreach ($dossiers as $key => $dossier) {
			if (!array_key_exists('secretariaat', $dossier)){
				$secretariaat = array();
				$te_behandelen_door = explode(', ', $dossier['te_behandelen_door']);
				foreach ($te_behandelen_door as $key => $value){
					$secretariaat[$value] = array('doorsturen_naar' => '', 'doorsturen_naar_namen' => '', 'datum_secretariaat' => '', 'datum_antwoord_binnen' => '', 'secretariaat_opmerking' => '');
				}
				$dossier['secretariaat'] = $secretariaat;
			}			
		}
		$this->mongo_db->update('dossiers', $dossiers);
	}
	function dossier_info($type = null, $dossier_id = array(), $rapport = array(), $order_by = array(), $user = array())
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
			if (array_key_exists(0, $dossier_list)){
				$this->load->library('table');
				$tmpl = array (
		           	'table_open' => '<table id="dossiers" border="0" cellpadding="4" cellspacing="0" class="">',
		        );
				$this->table->set_template($tmpl);
				$pv = '';	
				if($dossier_list[0]['type']=='parlementaire_vragen'){
					$pv = 'PV';
				} elseif ($dossier_list[0]['type']=='email_kabinet') {
					$pv = 'KAB';
				}
				if (!user_access(array('Administrators', 'Stafdienst'))){
					$doorgestuurd_door = 'OK voor';
				} else {
					$doorgestuurd_door = 'Goedgekeurd';
				} 
                if (user_access(array('Secretariaat')) && !user_access(array('Administrators', 'Stafdienst'))){
				    $this->table->set_heading($pv, 'Onderwerp', 'Te herinneren op', 'Naar staf tegen', 'Naar kabinet tegen', 'Naar Stafdienst', $doorgestuurd_door);
                } else {
                    $this->table->set_heading($pv, 'Onderwerp', 'Te herinneren op', 'Naar staf tegen', 'Naar kabinet tegen', $doorgestuurd_door);
                }		
				foreach ($dossier_list as $key => $value){
						$nummer_pv = '';
						if($dossier_list[$key]['nummer_pv'] != ''){
							$nummer_pv = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], $dossier_list[$key]['nummer_pv']);
						}
						if (array_key_exists('nummer_kab', $dossier_list[$key])){
							if($dossier_list[$key]['nummer_kab'] != ''){
								$nummer_pv = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], $dossier_list[$key]['nummer_kab']);
							}
						} 
						if($dossier_list[$key]['datum_melding']->sec == 0){
							$dossier_list[$key]['datum_melding'] = '';
						} else {
							$dossier_list[$key]['datum_melding'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], date('d-m-Y', $dossier_list[$key]['datum_melding']->sec), 'class="link"');
						}				
                        if (is_object($dossier_list[$key]['herinnering_op'])){
    						if($dossier_list[$key]['herinnering_op']->sec == 0){
    							$dossier_list[$key]['herinnering_op'] = '';
    						} else {
    							$dossier_list[$key]['herinnering_op'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], date('d-m-Y', $dossier_list[$key]['herinnering_op']->sec), 'class="link"');
    						}
                        } else {
                            if ($dossier_list[$key]['herinnering_op'] != ''){
                                $dossier_list[$key]['herinnering_op'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], $dossier_list[$key]['herinnering_op'], 'class="link"');
                            }
                        }				
						if (is_object($dossier_list[$key]['naar_staf_tegen'])){
							if($dossier_list[$key]['naar_staf_tegen']->sec == 0){
								$dossier_list[$key]['naar_staf_tegen'] = '';
							} else {
								$dossier_list[$key]['naar_staf_tegen'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], date('d-m-Y', $dossier_list[$key]['naar_staf_tegen']->sec), 'class="link"');
							}
						} else {
							if ($dossier_list[$key]['naar_staf_tegen'] != ''){
								$dossier_list[$key]['naar_staf_tegen'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], $dossier_list[$key]['naar_staf_tegen'], 'class="link"');
							}
						}
                        if (is_object($dossier_list[$key]['uitgeschreven_op'])){
    						if($dossier_list[$key]['uitgeschreven_op']->sec == 0){
    							$dossier_list[$key]['uitgeschreven_op'] = '';
    						} else {
    							$dossier_list[$key]['uitgeschreven_op'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], date('d-m-Y', $dossier_list[$key]['uitgeschreven_op']->sec), 'class="link"');
    						}
    					} else {
                            if ($dossier_list[$key]['uitgeschreven_op'] != ''){
                                $dossier_list[$key]['herinnering_op'] = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], $dossier_list[$key]['herinnering_op'], 'class="link"');
                            }
                        }
						$dossier_nr = $type[$dossier_list[$key]['type']].'/'.substr($dossier_list[$key]['dossiernummer'], 0, 4).'/'.substr($dossier_list[$key]['dossiernummer'], 4, 7);
						if (strlen($dossier_list[$key]['onderwerp'])>45){
							$onderwerp = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], mb_substr($dossier_list[$key]['onderwerp'], 0, 45).'...', 'class="link"');
						} else {
							$onderwerp = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], $dossier_list[$key]['onderwerp'], 'class="link"');
						}
                        if (user_access(array('Secretariaat')) && !user_access(array('Administrators', 'Stafdienst'))){
                           $location = $this->session->userdata('location');
                           $datum_antwoord_binnen = '';
                           if (array_key_exists('secretariaat', $dossier_list[$key])){
                               if (array_key_exists($location, $dossier_list[$key]['secretariaat'])){
                                   if ($dossier_list[$key]['secretariaat'][$location]['datum_antwoord_binnen'] != ''){
                                       if($dossier_list[$key]['secretariaat'][$location]['datum_antwoord_binnen']->sec == 0){
                                           $datum_antwoord_binnen = '';
                                       } else {
                                           $datum_antwoord_binnen = anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], date('d-m-Y', $dossier_list[$key]['secretariaat'][$location]['datum_antwoord_binnen']->sec), 'class="link"');
                                       }
                                   }
                               }
                           }                                   
                        }     
						if (user_access(array('Administrators', 'Stafdienst'))){ 
							if(array_key_exists('beantwoord', $dossier_list[$key])){
								if($dossier_list[$key]['beantwoord'] != "true"){
									$beantwoord = anchor('ajax/dossiers/beantwoord/'.$dossier_list[$key]['_id'],'&nbsp;', 'class="onbeantwoord btn glyphicon glyphicon-thumbs-down '.$dossier_list[$key]['_id'].'_b" title="de pv is niet beantwoord" style="font-size:14px; padding: 2px 5px"');
								} else {
									$beantwoord =array('data' => anchor('ajax/dossiers/beantwoord/'.$dossier_list[$key]['_id'],'&nbsp;', 'class="beantwoord btn glyphicon glyphicon-thumbs-up '.$dossier_list[$key]['_id'].'_b" title="de pv is beantwoord" style="font-size:14px; padding: 2px 5px"'), 'class' => 'beantwoord');
								}
							} else {
								$beantwoord = anchor('ajax/dossiers/beantwoord/'.$dossier_list[$key]['_id'],'&nbsp;', 'class="onbeantwoord btn glyphicon glyphicon-thumbs-down '.$dossier_list[$key]['_id'].'_b" title="de pv is niet beantwoord" style="font-size:14px; padding: 2px 5px"');
							}
						} else {
							if(array_key_exists('beantwoord', $dossier_list[$key])){
								if($dossier_list[$key]['beantwoord'] != "true"){
									$beantwoord = array('data' => '<i class="onbeantwoord"></i>', 'class' => 'onbeantwoord');
									if(array_key_exists('secretariaat', $dossier_list[$key])){
										if (array_key_exists($rapport['location'], $dossier_list[$key]['secretariaat'])){
											if (array_key_exists('datum_antwoord_binnen', $dossier_list[$key]['secretariaat'][$rapport['location']])){
												if ($dossier_list[$key]['secretariaat'][$rapport['location']]['datum_antwoord_binnen'] != ''){
													$beantwoord = array('data' => '<i class="secretariaat_beantwoord">Secretariaat</i>', 'class' => 'secretariaat_beantwoord');
												}
											}
										}
									}
								} else {
									$beantwoord = array('data' => '<i class="beantwoord">Stafdienst</i>', 'class' => 'beantwoord');								
								}
							} 
						}
						if (user_access(array('Secretariaat')) && !user_access(array('Administrators', 'Stafdienst'))){ 						
    						$this->table->add_row(
    							//anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], $dossier_nr, 'class="link"'), 
    							$nummer_pv,
    							$onderwerp,  
    							$dossier_list[$key]['herinnering_op'],  
    							$dossier_list[$key]['naar_staf_tegen'], 
    							$dossier_list[$key]['uitgeschreven_op'],
    							$datum_antwoord_binnen,
    							$beantwoord
    						);
                        } else {
                            $this->table->add_row(
                                //anchor('dossier/dossiers/view/'.$dossier_list[$key]['_id'], $dossier_nr, 'class="link"'), 
                                $nummer_pv,
                                $onderwerp,  
                                $dossier_list[$key]['herinnering_op'],  
                                $dossier_list[$key]['naar_staf_tegen'], 
                                $dossier_list[$key]['uitgeschreven_op'],
                                $beantwoord
                            );
                        }
					}				
				    $content = $this->table->generate();
				} else {
					$content = '<h5>Geen inhoud gevonden</h5>Het is aan te raden de filter (rechts) aan te passen. U kan best de datum aanpassen.';
				}
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
		} else if ($type == 'count'){
			$dossier = $this->mongo_db->where($dossier_id)->order_by(array('dossiernummer' => -1))->count('dossiers');
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
	
	public function zoeken($tekst){
		try {
			return $this->mongo_db->fullTextSearch('dossiers', '\"'.$tekst.'\"');
		} catch (MongoConnectionException $e) {
			die('Error connecting to MongoDB server');
		} catch (MongoException $e) {
			die('Error: ' . $e->getMessage());
		}
	}
}
?>