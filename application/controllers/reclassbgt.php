<?php
	class reclassbgt extends DBController{
		
		function __construct(){
			// deskripsikan model yg dipakai
			parent::__construct('reclassbgt_model');
			$this->set_page_title('RECLASS BUDGET');
			$this->default_limit = 30;
			$this->template_dir = 'project/reclassbgt';
			$session_id = $this->UserLogin->isLogin();
			$this->user = $session_id['username'];
			$this->pt = $session_id['id_pt'];
			$this->divisi = $session_id['divisi_id'];
		}
		
		protected function setup_form($data=false){
			
												
		}
		

	function get_json(){
		$this->set_custom_function('tgl_bgtproj','indo_date');
		$this->set_custom_function('jan12','currency');
		//~ $this->set_custom_function('mei14','currency');
		parent::get_json();
		
		
	}
		
		
		function index(){
			$this->set_grid_column('id_flag','',array('width'=>50,'hidden'=>true,'align'=>'center'));
			$this->set_grid_column('id_reclass','',array('width'=>50,'hidden'=>true,'align'=>'center'));
			$this->set_grid_column('tgl_bgtproj','Date',array('width'=>50,'align'=>'center','formatter' => 'cellColumn'));
			$this->set_grid_column('id_user','Requestor',array('width'=>100,'formatter' => 'cellColumn'));
			$this->set_grid_column('divisi_nm','Divisi',array('width'=>50,'align'=>'left','formatter' => 'cellColumn'));
			
		
			$this->set_jqgrid_options(array('width'=>900,'height'=>200,'caption'=>'RECLASS BUDGET','rownumbers'=>true));
			if($this->user!="")parent::index();
			else die("The Page Not Found");
		}
		
		function approved($id){
		$data['sql1'] = $this->db->select('TOP 1 *')
								->where('id_reclass',$id)	
								->order_by('id_bgtproj_update','asc')
								->get('db_bgtproj_update_temp')->row();
								
		$row = 	$this->db->select('TOP 1 *')
								->where('id_reclass',$id)	
								->order_by('id_bgtproj_update','asc')
								->get('db_bgtproj_update_temp')->row();
		$row->id_subproject;
		
		
		$data['sql2'] = $this->db->select('TOP 1 *')
								->where('id_reclass',$id)	
								->order_by('id_bgtproj_update','desc')
								->get('db_bgtproj_update_temp')->row();
			
		$this->load->view('project/reclassbgt-input',$data);	
		
		}
		
		
		
		function savereclass(){
			
			extract(PopulateForm());
		
			$session_id = $this->UserLogin->isLogin();
			$this->user = $session_id['username'];
			$user = $this->user;
		
			
			
			
			
			if(@$klik == 1){
				
				
				
				
				
				
			$sql =	$this->db->query("SP_ReclassBgtProj '".$idcostproj."','".$kodebgtproj."','".$nmbgtproj."','".$nilaibgtproj."','".$tglbgtproj."',
						         '".$inputdate."','".$iddivisi."','".$idpt."','".$idsubproject."','".$user."','".$coano."','".$adj."','".$remark."',
						         '".$idcostproj2."','".$kodebgtproj2."','".$nmbgtproj2."','".$nilaibgtproj2."','".$tglbgtproj2."',
						         '".$inputdate2."','".$iddivisi2."','".$idpt2."','".$idsubproject2."','".$coano2."','".$adj2."','".$remark2."','".$idreclass."'");
			
			
			
						$message = "Kepada Bpk. Erick\n
				
Dengan hormat,\n
Permohonan RECLASS BUDGET SEBESAR ".number_format($nilaibgtproj).",\n
dari BUDGET '".$nmbgtproj."', ke BUDGET '".$nmbgtproj2."', \n
Telah mendapat PERSETUJUAN \n
Demikian Informasi persetujuan RECLASS BUDGET ini kami sampaikan.\n
Terimakasih 
PT.GMI System Application";			

//die($message);
			$this->email->from($this->from_app_propbgt, $this->displayname_app_probgt);
			$listpro =  array('erick@bsu.co.id','rochmad@bsu.co.id','ali@bsu.co.id');
			$this->email->to($listpro);
			$this->email->subject($this->subject_app_reclass);
			$this->email->message($message);	
			$this->email->send();
	
			

			
			
			
			
			
			#if($sql) die("sukses");
			$this->UserLogin->deleteLogin();
			redirect('user/login');
		
			}
			else if(@$batal == 1){
				#echo "<script>alert('batal');</script>";
				#$this->load->view('gl/print/print_excel_tb');
				$sql = $this->db->query("SP_ReclassBgtProj_void '".$idreclass."'");
				
							$message = "Kepada Bpk. Erick\n
				
Dengan hormat,\n
Permohonan RECLASS BUDGET SEBESAR ".number_format($nilaibgtproj).",\n
dari BUDGET '".$nmbgtproj."', ke BUDGET '".$nmbgtproj2."', \n
Belum dapat diSETUJUI \n
Demikian Informasi persetujuan RECLASS BUDGET ini kami sampaikan.\n
Terimakasih 
PT.GMI System Application";			

//die($message);
			$this->email->from($this->from_app_propbgt, $this->displayname_app_probgt);
			$listpro =  array('erick@bsu.co.id','setiono@bsu.co.id','ali@bsu.co.id','rochmad@bsu.co.id');
			$this->email->to($listpro);
			$this->email->subject($this->subject_app_reclass);
			$this->email->message($message);	
			$this->email->send();
	
			

			
				
				
				
				
				#if($sql) die("sukses");
				
				$this->UserLogin->deleteLogin();
				redirect('user/login');
		
			}
		
		}
		
				
		
	
	}

