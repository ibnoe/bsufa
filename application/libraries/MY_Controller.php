<?
	defined('BASEPATH') or die('Access Denied');
	
	class Application extends Controller{
		var $parameters = array();
		var $pageCaption;
		
		function Application(){
			parent::Controller();
		}
		
		protected function initModule(){	
		}	
		
		protected function loadTemplate($template_file){
			$var = $this->initModule();
			if($var) $this->parameters = array_merge($this->parameters,$var);
			
			$session_id = $this->UserLogin->isLogin();			
			$this->parameters['session_id'] =  $session_id['username'];
			
			$this->parameters['site_id'] =  $this->UserLogin->getSitesID();
			
			$this->parameters['pageCaption'] = $this->pageCaption;
			$this->load->view($template_file,$this->parameters);
		}		
		
				
		protected function set_jqgrid_options_ceklist($custom_options=array()){
			$this->grid_options = array(
					'datatype' => 'json',
					'jsonReader' => array("repeatitems"=>false),
					'url' => site_url($this->module_url.'/get_json'),
					'mtype' => 'POST',
					'colNames' => $this->grid_column_captions,
					'colModel' => $this->grid_columns,
					'rowNum' => $this->default_limit,
					'rowList' => array(10,20,30),
					'pager' => '#pager',					
					'viewrecords' => true,	
					'multiselect' => true,					
					'imgpath' => 'themes/basic/images',
					'width' => 600,
					'height' => 400,
					'editurl' => site_url('unidentified/crud')
				);
			if(count($custom_options) > 0){
				foreach($custom_options as $k => $v){
					$this->grid_options[$k] = $v;
				}
			}
		}
		
		function setSites($id) {		
			if($id!="")
				$this->UserLogin->setSites($id);
			else
				$this->UserLogin->setSites("0");
		}
		
		function _setPagging($url,$totalRows,$perPage=10,$setLimit=true){
			$this->load->library('pagination');			
			$config = array(
				'base_url' => site_url($url),
				'total_rows' => $totalRows,
				'per_page' => $perPage,
				'uri_segment' => count(explode('/',$url))
			);
			$this->pagination->initialize($config);

			$currentPage = intval($this->input->get('per_page'));
			$this->parameters['pagging'] = array(
				'links' => $this->pagination->create_links(),
				'limit' => $config['per_page'],
				'page' => $currentPage
			);
			if($setLimit) $this->db->limit($perPage,$currentPage);
		}	
	}
	
	include_once(APPPATH.'libraries/adminpage.php');
	include_once(APPPATH.'libraries/admindatabase.php');
	include_once(APPPATH.'libraries/publicsite.php');
	

	class DBController extends Controller{
		private $grid_columns;
		private $grid_column_captions;
		private $grid_options;
		private $page_title;
		private $main_template = 'main_template';
		private $head_scripts = array();
		protected $message = '';
		#cuti
		protected $email_form = 'mis@bsu.co.id';
		protected $person_form = 'MIS Department';
		protected $person_to = '';
		protected $subject = '';
		protected $module_url = '';
		protected $template_dir = '';
		protected $resource_dir = '';
		protected $user_account = '';
		protected $user_id = '';
		protected $pt_id = '';
		#proposed budget
		protected $from_app_propbgt = 'mis@bsu.co.id';
		protected $displayname_app_probgt = 'GMI System Application';
		protected $to_app_probgt = 'donsadat@gmail.com';
		protected $subject_app_probgt = 'Need Your Approval for PROPOSED BUDGET PROJECT';
		protected $subject_app_reclassbgt = 'Need Your Approval for RECLASS BUDGET';
		protected $subject_app_addbgt = 'Need Your Approval for ADD BUDGET';
		#approve proposed budget
		protected $from_app = 'mis@bsu.co.id';
		protected $from_cs = 'customerservice@bsu.co.id';
		protected $displayname_app = 'GMI System Application';
		protected $displayname_cs = 'Notification CS';
		
		protected $to_app = '';
		protected $subject_app = 'Confirmation Approval Proposed Budget Project';
		protected $subject_cs = 'Notification from Customer Service';
		protected $subject_app_reclass = 'Confirmation Approval RECLASS BUDGET';
		protected $subject_app_readdbgt = 'Confirmation Approval ADD BUDGET';
		protected $subject_app_readcjc = 'Confirmation Approval CERTIFIED JOB TO COMPLISH';
		
		#approve tender project
		protected $from_tender_propbgt = 'mis@bsu.co.id';
		protected $displayname_tender_probgt = 'GMI System Application';
		protected $to_tender_probgt = 'donsadat@gmail.com';
		protected $subject_tender_probgt = 'Need Your Approval fot Tender Project';
		
		
		protected $default_limit = 10;
		protected $parameters = array();
		protected $custom_functions = array();
		
		
		function __construct($model_name=false,$module_url=false){
			parent::Controller();
			if($model_name){
				$this->load->model($model_name,'data');
			}			
			$this->module_url = $module_url ? $module_url : strtolower(get_class($this));
			$this->template_dir = $this->module_url;
			$this->resource_dir = base_url() . 'assets/';
			
			$this->load->helper(array('url','html'));
			$this->add_head_script(link_tag($this->resource_dir.'css/main.css'));
			$this->add_js('jquery-1.7.2.min.js');
			#$this->add_js('jquery-1.4.2.min.js');
			#$this->add_js('jquery-1.6.min.js');
			#$this->add_js('jquery-1.8.1.js');
			$this->parameters['module_url'] = $this->module_url;
		}
		
		protected function add_js($js_name){
			$this->add_head_script('<script language="javascript" src="'.$this->resource_dir.'js/'.$js_name.'"></script>');
		}

		protected function set_custom_function($key,$function){
			$this->custom_functions[$key] = $function;
		}
		
		protected function set_jqgrid_options_ceklist($custom_options=array()){
			$this->grid_options = array(
					'datatype' => 'json',
					'jsonReader' => array("repeatitems"=>false),
					'url' => site_url($this->module_url.'/get_json'),
					'mtype' => 'POST',
					'colNames' => $this->grid_column_captions,
					'colModel' => $this->grid_columns,
					'rowNum' => $this->default_limit,
					'rowList' => array(10,20,30),
					'pager' => '#pager',					
					'viewrecords' => true,	
					'multiselect' => true,					
					'imgpath' => 'themes/basic/images',
					'width' => 600,
					'height' => 400,
					'editurl' => site_url('unidentified/crud')
				);
			if(count($custom_options) > 0){
				foreach($custom_options as $k => $v){
					$this->grid_options[$k] = $v;
				}
			}
		}
				
		protected function set_grid_column($key,$caption,$options=array()){
			$default_opts = array(
					'name' => $key,
					'index' => $key,	
					'editable' => true				
				);
			if(count($options) > 0){
				foreach($options as $k => $v){
					$default_opts[$k] = $v;
				}
			}
			$this->grid_columns[] = $default_opts;
			$this->grid_column_captions[] = $caption;
		}
		
		protected function set_jqgrid_options($custom_options=array()){
			$this->grid_options = array(
					'datatype' => 'json',
					'jsonReader' => array("repeatitems"=>false),
					'url' => site_url($this->module_url.'/get_json'),
					'mtype' => 'POST',
					'colNames' => $this->grid_column_captions,
					'colModel' => $this->grid_columns,
					'rowNum' => $this->default_limit,
					'rowList' => array(10,20,30),
					'pager' => '#pager',					
					'viewrecords' => true,					
					'imgpath' => 'themes/basic/images',
					'width' => 600,
					'height' => 400
				);
			if(count($custom_options) > 0){
				foreach($custom_options as $k => $v){
					$this->grid_options[$k] = $v;
				}
			}
		}
		
		function piutang($id){
			$data = $this->data->get_detail_print($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : cetak');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			//$this->load_view('cetak',true);
			redirect('print/print_kartu_piutang/kartu_piutang/'.$id.'');
			}	
			
		function piutang_leasing($id){
			$data = $this->data->get_detail_print($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : cetak');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			//$this->load_view('cetak',true);
			redirect('print/print_kartu_piutang_lease/kartu_piutang/'.$id.'');
			}	

		function cetakpiutang($id){
			$data = $this->data->get_detail_print($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : cetak');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			//$this->load_view('cetak',true);
			redirect('print/print_history_payment/history_payment/'.$id.'');
		}
		
		function cetakpiutang_leasing($id){
			$data = $this->data->get_detail_print($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : cetak');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			//$this->load_view('cetak',true);
			redirect('print/print_history_payment_leasing/history_payment/'.$id.'');
		}
			
		function groupcapcha(){
			$capcha = @$this->base_model->capcha();
			return @$capcha;
		}	
				
		function get_json(){
			$param = array();
			if(isset($this->data)){
				$page = intval($this->input->post('page'));
				$paging_limit = $this->input->post('rows') ? $this->input->post('rows') : $this->default_limit;
				if($this->input->post('searchField')){
					$this->data->set_search($this->input->post('searchField'),$this->input->post('searchOper'),$this->input->post('searchString'));
				}
				$count = $this->data->count_rows();
				
				$param['page'] = $page ? $page : 1;
				$param['total'] = ceil( $count / $paging_limit );
				$param['records'] = $count;
				
				$sort_by = $this->input->post('sidx');
				$this->db->limit($paging_limit,($page - 1) * $paging_limit);
				if($sort_by)
				  $this->db->order_by($sort_by,$this->input->post('sord'));
				
				if($this->input->post('searchField')){
					$this->data->set_search($this->input->post('searchField'),$this->input->post('searchOper'),$this->input->post('searchString'));
				}
				$grid_rows = (array) $this->data->get();
				if(count($this->custom_functions) > 0){					
					$new_grid_rows = array();
					foreach($grid_rows as $_rows){
						foreach($this->custom_functions as $k=>$v){
							if(isset($_rows->$k)){
								$_rows->$k = $v($_rows->$k);
							}
						}
						$new_grid_rows[] = $_rows;
					}
					$grid_rows = $new_grid_rows;
				}
				$param['rows'] = $grid_rows;
			}
			echo json_encode($param);
		}	
		
		protected function set_page_title($page_title){
			$this->page_title = $page_title;
		}
		
		protected function user_account($user_account){
			$this->user_account = $user_account;
		}

		protected function user_id($user_id){
			$this->user_id = $user_id;
		}

		protected function pt_id($pt_id){
			$this->pt_id = $pt_id;
		}
	
		protected function add_head_script($head_script){
			$this->head_scripts[] = $head_script;
		}
		
		protected function setup_form($data=false){
					
		}
		
		function load_view($view_name = 'index',$noheader = false){
			if($noheader){
				$this->load->view($this->template_dir.'-'.$view_name,$this->parameters);
			}else{
				$this->parameters['page_title'] = $this->page_title;
				$this->parameters['head_scripts'] = implode("\n",$this->head_scripts);
				$this->parameters['content'] = $this->load->view($this->template_dir.'-'.$view_name,$this->parameters,true);
				$this->load->view($this->main_template,$this->parameters);
			}
		}

		# ini load buat menu yang baru, isi nya dari adminpage.php -> By Abas
		protected function initModule(){
			$var = array();
			$sql = 'SELECT m.* FROM modules m 
					left JOIN user_menu u ON m.id_module = u.module_id 
					left join db_site_modules sm on m.id_module = sm.id_module
					WHERE u.group_id = \''.$this->UserLogin->getClass().'\' and (sm.id_sites = \''. $this->UserLogin->getSitesID() .'\' or sm.id_sites = \'0\' or sm.id_sites is null)
					ORDER BY m.module_index , m.parent_module_id';
			//echo "ID Sites = ".$this->UserLogin->getSitesID();
			$modules = $this->ado->GetAll($sql);
			if($modules){
				$data = array();
				foreach($modules as $mod){
					$data[$mod->parent_module_id][] = $mod;
				}
				$var['modules_menu'] = $this->loadMenu($data,0);
			}else $var['modules_menu'] = false;
			
			$var['sites_menu'] = $this->loadSites();
			return $var;
		}
		
		protected function loadMenu($data,$parent){
			if(isset($data[$parent])){
			   $str = ($parent==0?'':'<div>').'<ul'.($parent==0?' class="menu-drop"':'').'>'; //parent_module_id
			   foreach($data[$parent] as $row){
				 $str .= '<li>'.($row->module_path?anchor($row->module_path,$row->module_name):'<a href="javascript:;">'.$row->module_name.'</a>');			 
				 $str .= $this->loadMenu($data,$row->id_module);
				 $str .= '</li>';
			   }
			   $str .= '</ul>'.($parent==0?'':'</div>');
			   return $str;
			}else return '';	
		}

		protected function loadSites() {
			//$sql = "SELECT ISNULL(c.id_sites, 0) as id_sites, ISNULL(c.sites_name, 'Back To Home') as sites_name, ISNULL(c.sites_img, 'global_32.png') as sites_img FROM db_sites c";
			$sql = "SELECT ISNULL(d.id_sites, 0) as id_sites, ISNULL(c.sites_name, 'Back To Home') as sites_name, ISNULL(c.sites_img, 'global_32.png') as sites_img FROM user_menu a left join modules b on b.id_module = a.module_id left join db_site_modules d on b.id_module = d.id_module right join db_sites c on c.id_sites = d.id_sites where a.group_id = '". $this->UserLogin->getClass() ."' group by d.id_sites, c.sites_name, c.sites_img";
			$res_sites = false;
			$sites = $this->ado->GetAll($sql);
			$sites2 = false;
			$idSites2 = "";
			$res_sites .= "<nav id=nav>
				<ul>";
			if($sites){
				foreach($sites as $s){
					$sql2 = "SELECT ISNULL(d.id_sites, 0) as id_sites, ISNULL(c.sites_name, 'Back To Home') as sites_name, ISNULL(c.sites_img, 'global_32.png') as sites_img FROM user_menu a left join modules b on b.id_module = a.module_id left join db_site_modules d on b.id_module = d.id_module right join db_sites c on c.id_sites = d.id_sites where a.group_id = '". $this->UserLogin->getClass() ."' and d.id_sites = '". $s->id_sites ."' group by d.id_sites, c.sites_name, c.sites_img";
					$sites2 = $this->ado->GetAll($sql2);
					$idSites2 = $s->id_sites;
					/*if($sites2) {
						foreach($sites2 as $s2){
							$idSites2 = $s2->id_sites;
						}
					}*/
					$res_sites .= "<li>
                                                
						<a ".($idSites2!=""?"":"style='-moz-opacity: .75;filter: alpha(opacity=50);opacity: 0.5;'")." ".($idSites2!=""?"href=\"".base_url()."user/home/sites/".$s->id_sites:"#")."\" id=".$s->sites_img."></a>
						</li>";
					$idSites2 = "";
					//var_dump($res_sites);
				}
			}
			$res_sites .= "</ul>
					</nav>";
			
			return $res_sites;			
		}		
		# ini akhir load buat menu yang baru, isi nya dari adminpage.php --> By Abas
		
		function index(){
			#$this->add_head_script(link_tag($this->resource_dir.'css/jquery-ui-1.8.7.custom.css'));
			$this->add_head_script(link_tag($this->resource_dir.'css/redmond/jquery-ui-1.8.2.custom.css'));
			$this->add_head_script(link_tag($this->resource_dir.'css/ui.jqgrid.css'));
			$this->add_head_script(link_tag($this->resource_dir.'css/thickbox.css'));
			$this->add_head_script(link_tag($this->resource_dir.'css/validationEngine.jquery.css'));
			$this->add_js('i18n/grid.locale-en.js');
			$this->add_js('jquery.jqGrid.min.js');
			$this->add_js('grid.generator.js');
			$this->add_js('jquery.easyui.min.js');
			$this->add_js('thickbox.js');
			$this->add_js('jquery.validationEngine-en.js');
			$this->add_js('jquery.validationEngine.js');
			#$this->add_js('jquery.tabs.js');
			$this->parameters['grid_data'] = json_encode($this->grid_options);
			#var_dump($this->parameters['grid_data']);
			$this->load_view();
		}
		
		
		function xjquery_js(){
			$this->parameters['grid_data'] = json_encode($this->grid_options);
			var_dump($this->parameters['grid_data']);exit;
		}		
		
		function save(){
			$data = $_POST;
			$id = $this->input->post($this->data->primary_key);
			echo $this->data->save($data,$id) ? 'success' : 'failed';
		}

		function approve($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : Update');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			$this->load_view('app',true);
		}
		function tender($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : Update');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			$this->load_view('add',true);
		}
		

		function posting($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : Update');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			$this->load_view('posting',true);
		}
		
		function appr($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : Update');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			$this->load_view('approve',true);
		}
		


		function deletedata($id){
			$dt = array
			(
				'flag_id' => 10
			);
			echo $this->data->deletedata($id,$dt) ? 'success' : 'failed';
		}


		function reclass(){
			$this->set_page_title($this->page_title . ' : Reclass');
			$this->setup_form();
			$this->load_view('reclass',true);
		}


		function adj(){
			$this->set_page_title($this->page_title . ' : Reclass');
			$this->setup_form();
			$this->load_view('adj',true);
		}




		
		function add(){
			$this->set_page_title($this->page_title . ' : Tambah');
			$this->setup_form();
			$this->load_view('input',true);
		}
		
		function tam(){
			$session_id = $this->UserLogin->isLogin();
			$this->user = $session_id['username'];
			$pt	= $session_id['id_pt'];
			
			if ($pt==44){
				//die('gmi');
			$this->set_page_title($this->page_title . ' : Tambah');
			$this->setup_form();
			$this->load_view('tam',true);
			}else{
			$this->set_page_title($this->page_title . ' : Tambah');
			$this->setup_form();
			$this->load_view('tamx',true);
			}
		}

		function form(){
			$this->set_page_title($this->page_title . ' : Tambah');
			$this->setup_form();
			$this->load_view('form',true);
		}
		
		/*Cetak Bill*/
		function cetakbill($id){
			$data = $this->data->get_detail_print($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : cetak');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			//$this->load_view('cetak',true);
			redirect('print/print_jadwal_pembayaran_unit/jadwal_pembayaran_unit/'.$id.'');
			}
			
		function cetakbill_leasing($id){
			$data = $this->data->get_detail_print($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : cetak');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			//$this->load_view('cetak',true);
			redirect('print/print_jadwal_pembayaran_unit_leasing/jadwal_pembayaran_unit/'.$id.'');
			}

		function reprint_sp($id){
			$data = $this->data->get_detail_print($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : print');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			//$this->load_view('print',true);
			redirect('reprint/sp'.$id.'.pdf');
		}		


		function update_form($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');


			
			$this->set_page_title($this->page_title . ' : Update');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			$this->load_view('form',true);
		}

		
		function update($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');


			$this->parameters['grid_data'] = json_encode($this->grid_options);	
			#var_dump($this->parameters['grid_data']);exit;
			
			$this->set_page_title($this->page_title . ' : Update');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			$this->load_view('form',true);
		}
		
		function mapping($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');


			$this->parameters['grid_data'] = json_encode($this->grid_options);	
			#var_dump($this->parameters['grid_data']);exit;
			
			$this->set_page_title($this->page_title . ' : Update');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			$this->load_view('mapp',true);
		}
		
		
		function app($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : Update');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			$this->load_view('app',true);
		}
		
		function view($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : Update');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			$this->load_view('view',true);
		}
		function edit($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : Update');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			$this->load_view('edit',true);
		}

		function reprint($id){
			$data = $this->data->get_detail_print($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : print');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			//$this->load_view('print',true);
			redirect('reprint/Budget'.$id.'.pdf');
		}
		
		function printkwt($id){
			$data = $this->data->get_detail($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : print');
			$this->setup_form($data);
			$this->parameters['data'] = $data;			
			#$this->load_view('print',true);
			#var_dump($id);exit;
			redirect('kwitansi/kwitansi_view/'.$id.'');
		}		

		function reprint_app($id){
			$data = $this->data->get_detail_print($id);
			if(!$data) die('Data not found');
			
			$this->set_page_title($this->page_title . ' : print');
			$this->setup_form($data);
			$this->parameters['data'] = $data;
			//$this->load_view('print',true);
			redirect('reprint/Approved Budget'.$id.'.pdf');
		}		


		
		function flag($id){
			$dt = array
			(
				'flag'=> 1
			);
			
			$date = date("d-m-Y");
			$time = date("H:i:s");
			#$session_id = $this->UserLogin->isLogin();
			#$user = $session_id('id');
			#var_dump($user);exit;
			$data = array
			(
				'hstusr_tgl' => $date,
				'hstusr_jam' => $time,
				'table' => $this->data->table_name(),
				'id_table' => $id,
				#'user_id'=>$user,
				'statusr_id' => 4
			);
			
			$this->data->sethistory('hstusr',$data);					
			echo $this->data->update($id,$dt)?'success' : 'failed';
	}
			
		
		function delete($id){
			echo $this->data->delete($id) ? 'success' : 'failed';
		}
		
		protected function printdata(){
			if($this->input->get('field')){
				$this->data->set_search($this->input->get('field'),$this->input->get('oper'),$this->input->get('str'));
			}
			$data = $this->data->get();
			if($data){
				$html = "<table border='1'><tr bgcolor='#CCCCCC'>";
				$isrownum = isset($this->grid_options['rownumbers']) && $this->grid_options['rownumbers'];
				if($isrownum){
					$html .= "<th>No</th>";
				}
				foreach($this->grid_column_captions as $cap){
					$html .= "<th>$cap</th>";
				}
				$html .= "</tr>";
				$rownum = 1;
				foreach($data as $row){
					$html .= "<tr>";
					if($isrownum){
						$html .= "<td>$rownum</td>";
						$rownum++;
					}
					foreach($this->grid_columns as $col){
						$field = $col['name'];
						$html .= "<td>".$row->$field."</td>";
					}
					$html .= "</tr>";
				}
				$html .= "</table>";
				require 'fpdf/fpdf.php';
			    $pdf=new FPDF('L','mm','A4');
			    $pdf->AliasNbPages();
			    $pdf->AddPage();
				$pdf->Cell(25,4,'Printing line number '.$i,1);
				$pdf->Output();
			}
		}
	}
	
	
?>
