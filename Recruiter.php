<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recruiter extends CI_Controller {

  private $admin_id_session = null;
  private $admin_type_session = null;

  public function __construct(){
    parent::__construct();
    $session_vareables = $this->session->userdata();
    $this->admin_id_session = $this->session->userdata('admin_id');
    $this->admin_type_session = $this->session->userdata('type');
    if($this->admin_id_session==''){
      redirect(base_url('adminmedia/home'));
    }

    // Deletes cache for the currently requested URI
    $this->output->delete_cache();
  }


  public function index(){

    $data['title'] = SITE_NAME.': Manage Recruiter';
    $data['msg'] = '';
    $data['result_email_template'] = $this->email_model->get_all_recruiter_records();



    $obj_result = $this->recruiter_model->get_all_records();

    if($obj_result){
      foreach($obj_result as $row){

        $obj_recruiter_all_jobs = $this->posted_jobs_model->get_all_job_by_recruiter_id($row->ID);


        $obj_recruiter_open_jobs = $this->posted_jobs_model->get_open_job_by_recruiter_id($row->ID);
        $row->total_jobs_posted = 0;
        $row->total_open_jobs_posted = 0;
        $row->total_cv_searched = 0;

        if(!empty($obj_recruiter_all_jobs)){
          $row->total_jobs_posted = count($obj_recruiter_all_jobs);
        }
        if(!empty($obj_recruiter_open_jobs)){
          $row->total_open_jobs_posted = count($obj_recruiter_open_jobs);
        }
      }
    }


    $data['result_curency'] = $this->posted_jobs_model->get_all_active_curency();
    $data['result_cites'] = $this->cities_model->get_all_active_records();
    $data['result_countries'] = $this->countries_model->get_all_active_records();
    $data['result_company_type'] = $this->company_type_model->get_all_records();
    $data['result_industries'] = $this->industries_model->get_all_active_records();

    $data["total_rows"] = count($obj_result);
    $data['result'] = $obj_result;
    //echo "hello"; exit;
    $this->load->view('adminmedia/recruiter_view', $data);
    return;
  }



  public function details_view($id=''){


    $data['title'] = SITE_NAME.': recruiter Details ';
    $data['msg'] = '';
    $data['result_email_template'] = $this->email_model->get_all_recruiter_records();

    $obj_result = $this->recruiter_model->get_record_by_id($id);
    $data["total_rows"] = count($obj_result);
    $data['row'] = $obj_result;

    $obj_recruiter_all_jobs = $this->posted_jobs_model->get_all_job_by_recruiter_id($id);
    $obj_recruiter_open_jobs = $this->posted_jobs_model->get_open_job_by_recruiter_id($id);


    $data["total_jobs_posted"] = 0;
    $data["total_open_jobs_posted"] = 0;
    $data["total_skills_set"] = 0;
    $data["total_cv_searched"] = 0;


    if(!empty($obj_recruiter_all_jobs)){
      $data["total_jobs_posted"] = count($obj_recruiter_all_jobs);
    }
    if(!empty($obj_recruiter_open_jobs)){
      $data["total_open_jobs_posted"] = count($obj_recruiter_open_jobs);
    }

    $data['recruiter_jobs'] = $obj_recruiter_all_jobs;

    // Recruiter ratings
    $employer_rating = $this->ratings_model->calculateAvgRatingOfRecruiter($id, 'company');
    $data['employer_rating'] = round($employer_rating, 1);

    $candidate_rating = $this->ratings_model->calculateAvgRatingOfRecruiter($id, 'jobseeker');
    $data['candidate_rating'] = round($candidate_rating, 1);

    //echo "hello"; exit;
    $this->load->view('adminmedia/recruiter_details_view', $data);
    return;

  }

  public function add(){

    if($this->admin_id_session==''){
      edirect(base_url().'adminmedia','');
    }

    $data['ads_row'] = '';
    $data['title'] = SITE_NAME.': Add New Recruiter';
    $data['msg']='';
    $data['result_curency'] = $this->posted_jobs_model->get_all_active_curency();
    $data['result_cites'] = $this->cities_model->get_all_active_records();
    $data['result_countries'] = $this->countries_model->get_all_active_records();
    $data['result_company_type'] = $this->company_type_model->get_all_records();
    $data['result_industries'] = $this->industries_model->get_all_active_records();


    $this->form_validation->set_rules('recruiter_email', 'Email', 'trim|required|valid_email|is_unique[mm_recruiter.recruiter_email]|strip_all_tags');
    $this->form_validation->set_message('is_unique','This email address is already taken. Please try again');
    $this->form_validation->set_rules('recruiter_password', 'Password', 'trim|required|min_length[6]|strip_all_tags');
    $this->form_validation->set_rules('crecruiter_password', 'Confirm password', 'trim|required|matches[recruiter_password]|strip_all_tags');
    $this->form_validation->set_rules('recruiter_website', 'Website', 'trim|strip_all_tags');
    $this->form_validation->set_rules('recruiter_name', 'Name', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('recruiter_phone', 'Phone ', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('recruiter_cell', 'Mobile ', 'trim|strip_all_tags');
    $this->form_validation->set_rules('recruiter_phone', 'Phone ', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('recruiter_fax', 'Fax ', 'trim|strip_all_tags');
    $this->form_validation->set_rules('recruiter_country', 'Country', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('recruiter_description', 'Description', 'trim|required');
    $this->form_validation->set_rules('recruiter_address', 'Address ', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('account_val_date', 'Account Validity', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('account_type', 'Account Type ', 'trim|required|strip_all_tags');
    /*
    $this->form_validation->set_rules('company_location', 'Company address', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('company_description', 'Company Description', 'trim|required|strip_all_tags|secure');
    $this->form_validation->set_rules('company_phone', 'Company Phone', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('company_fax', 'Company Phone', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('company_type', 'Company Phone', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('no_of_employees', 'No of Employees', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('company_website', 'Company Website', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('ownership_type', 'Company Website', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('captcha', 'Verification code', 'trim|required|validate_ml_spam');
    */

    if (isset($_FILES['recruiter_profile_image']) && !empty($_FILES['recruiter_profile_image']['name'])){
      $this->form_validation->set_rules('recruiter_profile_image', 'Image', 'callback_upload_image');
    }

    $this->form_validation->set_error_delimiters('<span style="padding-left:2px;" class="err">', '</span>');

    if ($this->form_validation->run() === FALSE) {
      //$data['cpt_code'] = create_ml_captcha();
      $this->load->view('adminmedia/add_recruiter_view',$data);
      return;
    }

    $current_date = date("Y-m-d");

    $slug = make_slug($this->input->post('recruiter_email'));
    $is_slug = $this->recruiter_model->check_slug($slug);

    if($is_slug>0){
      $slug.='-'.time();
    }

    $unique_time = $this->input->post('unique_time');
    $recruiter_email = $this->input->post('recruiter_email');
    $recruiter_password =  $this->input->post('recruiter_password');
    $account_type = $this->input->post('account_type');
    $account_val_date = $this->input->post('account_val_date');
    $recruiter_name =  $this->input->post('recruiter_name');
    $recruiter_website = $this->input->post('recruiter_website');
    $recruiter_address =  $this->input->post('recruiter_address');
    $recruiter_fax =  $this->input->post('recruiter_fax');
    $recruiter_phone =  $this->input->post('recruiter_phone');
    $recruiter_cell =  $this->input->post('recruiter_cell');
    $recruiter_country =  $this->input->post('recruiter_country');
    $recruiter_description = $this->input->post('recruiter_description');
    $sts = 'active';

    $account_val_date = date("Y-m-d", strtotime($account_val_date));

    $data_array = array(
      'recruiter_email' => $recruiter_email,
      'password' => $recruiter_password,
      'account_type' => $account_type,
      'account_val_date' => $account_val_date,
      'recruiter_name' => $recruiter_name,
      'recruiter_website' => $recruiter_website,
      'recruiter_address' => $recruiter_address,
      'recruiter_fax' => $recruiter_fax,
      'recruiter_phone' => $recruiter_phone,
      'recruiter_cell' => $recruiter_cell,
      'recruiter_country' => $recruiter_country,
      'recruiter_description' => $recruiter_description,
      'sts' => $sts,
      'recruiter_slug' => $slug,
      'dated_last_modified' => '',
      'account_val_date' => '',
      'dated_created' => $current_date,
      'role_id' => RECRUITER,
    );

    $unique_time =  $_POST['unique_time'];

    if (isset($_FILES['recruiter_profile_image']) && !empty($_FILES['recruiter_profile_image']['name'])){
      $image = $_FILES['recruiter_profile_image']['name'];
      $image = str_replace(' ', '_', $image);
$image =  preg_replace("/(\.)(?=\S+\.)/", "_", $image);
      $img_array = array(
        'recruiter_profile_image' => $unique_time.$image
      );
      $data_array = array_merge($data_array,$img_array);
    }

    $recruiter_id = $this->recruiter_model->add($data_array);

    /********************************************************************** Email Start Here*************************************************************/
    $email    = $recruiter_email;
    $password = $recruiter_password;


    //Getting Basic Email Template From Back End Email Module
    $mail_email_template = $this->email_model->get_record_by_id(3);
    $mail_body    = $mail_email_template->mail_body;
    $subject      = $mail_email_template->subject;


    $mail_body = str_replace('{USERNAME}',$email,$mail_body);
    $mail_body = str_replace('{PASSWORD}',$password,$mail_body);
    //Getting Basic Email Template From Back End Email Module



    $mail_body_message = '';
    if($subject==''){
      $subject = " Welcome To | Your account is been created Successfully in  WHR SOLUTION";
    }

    //Replacing emails in email tempalte
    $row_email = $this->static_template_model->get_records_by_id(2);
    $email_template = $row_email->messages_template_body;

    $mail_body_message = replace_string('{email_subject}',$subject,$email_template);
    $mail_body_message = replace_string('{mail_body}',$mail_body,$mail_body_message);


    // Uncomment when you want to send mail to New Clients
    $config = array();
    $config['protocol'] = 'smtp';
    $config['smtp_host'] = 'mail.whr-solution.com'; //change this
    $config['smtp_port'] = '25';
    $config['smtp_user'] = 'no-reply@whr-solution.com'; //change this
    $config['smtp_pass'] = '~$meXJghdz?x'; //change this

    $config['mailtype'] = 'html';
    $config['charset'] = 'utf-8';
    $config['wordwrap'] = TRUE;
    $config['newline'] = "\r\n"; //use double quotes to comply with RFC 822 standard


    $this->email->initialize($config);
    $this->email->clear(TRUE);
    $this->email->from("no-reply@whr-solution.com",'Welcome To  WHR SOLUTION ');
    $this->email->to($email);
    //$this->email->cc('rrehan@maximagroup.ae');
    $this->email->subject($subject);
    $this->email->message($mail_body_message);
    $this->email->send();

    /********************************************************************** Email Start Here*************************************************************/
   
  
	
		
		if($this->email->send()) {  
		 $this->session->set_flashdata('update_action', true);
    redirect(base_url('adminmedia/recruiter'));
  
  
        } 
       else{  
           
          // echo $this->email->print_debugger();exit;
			 $this->session->set_flashdata('update_action', true);
    redirect(base_url('adminmedia/recruiter'));
  
  
        } 
	
  
  
  
  }
  
  
  

  public function update($id=''){
    if($this->admin_id_session=='' || $id==''){
      edirect(base_url().'adminmedia','');
    }

    $data['result_curency'] = $this->posted_jobs_model->get_all_active_curency();
    $data['result_cites'] = $this->cities_model->get_all_active_records();
    $data['result_countries'] = $this->countries_model->get_all_active_records();
    $data['result_company_type'] = $this->company_type_model->get_all_records();
    $data['result_industries'] = $this->industries_model->get_all_active_records();


    $data['title'] = SITE_NAME.': Edit Employer Details';
    $data['msg'] = '';
    $data['result_email_template'] = $this->email_model->get_all_recruiter_records();

    if($this->input->post('password')!=''){
      $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|strip_all_tags');
      $this->form_validation->set_rules('cpassword', 'Confirm password', 'trim|required|matches[password]|strip_all_tags');
    }

    //$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_check_email_in_jobseeker|is_unique[pp_employers.email]|strip_all_tags');
    //$this->form_validation->set_message('check_email_in_jobseeker','This email address is already taken. Please try again exist please choose onther one');
    $this->form_validation->set_message('is_unique','This email address is already taken. Please try again');


    $this->form_validation->set_rules('recruiter_website', 'Website', 'trim|strip_all_tags');
    $this->form_validation->set_rules('recruiter_name', 'Name', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('recruiter_phone', 'Phone ', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('recruiter_cell', 'Mobile ', 'trim|strip_all_tags');
    $this->form_validation->set_rules('recruiter_phone', 'Phone ', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('recruiter_fax', 'Fax ', 'trim|strip_all_tags');
    $this->form_validation->set_rules('recruiter_country', 'Country', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('recruiter_description', 'Description', 'trim|required');
    $this->form_validation->set_rules('recruiter_address', 'Address ', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('account_val_date', 'Account Validity', 'trim|required|strip_all_tags');
    $this->form_validation->set_rules('account_type', 'Account Type ', 'trim|required|strip_all_tags');


    if (isset($_FILES['recruiter_profile_image']) && !empty($_FILES['recruiter_profile_image']['name'])){
      $this->form_validation->set_rules('recruiter_profile_image', 'Image', 'callback_upload_image');
    }

    $this->form_validation->set_error_delimiters('<span style="padding-left:2px;" class="err">', '</span>');

    if ($this->form_validation->run() === FALSE) {
      $data['row'] = $this->recruiter_model->get_record_by_id($id);
      $data['title'] = SITE_NAME.': Edit Recruiter ';
      $data['msg'] = '';
      $data['result_email_template'] = $this->email_model->get_all_recruiter_records();
      $data['form_action'] = 'update';

      $this->load->view('adminmedia/edit_recruiter_view',$data);
      return;

    }


    $current_date = date("Y-m-d");


    $recruiter_email = $this->input->post('recruiter_email');
    $recruiter_password =  $this->input->post('recruiter_password');
    $account_type = $this->input->post('account_type');
    $account_val_date = $this->input->post('account_val_date');
    $recruiter_name =  $this->input->post('recruiter_name');
    $recruiter_website = $this->input->post('recruiter_website');
    $recruiter_address =  $this->input->post('recruiter_address');
    $recruiter_fax =  $this->input->post('recruiter_fax');
    $recruiter_phone =  $this->input->post('recruiter_phone');
    $recruiter_cell =  $this->input->post('recruiter_cell');
    $recruiter_country =  $this->input->post('recruiter_country');
    $recruiter_description = $this->input->post('recruiter_description');
    $sts = 'active';

    $account_val_date = date("Y-m-d", strtotime($account_val_date));

    $data_array = array(
      'recruiter_email' => $recruiter_email,
      'password' => $recruiter_password,
      'account_type' => $account_type,
      'account_val_date' => $account_val_date,
      'recruiter_name' => $recruiter_name,
      'recruiter_website' => $recruiter_website,
      'recruiter_address' => $recruiter_address,
      'recruiter_fax' => $recruiter_fax,
      'recruiter_phone' => $recruiter_phone,
      'recruiter_cell' => $recruiter_cell,
      'recruiter_country' => $recruiter_country,
      'recruiter_description' => $recruiter_description,
      'sts' => $sts,
      'dated_last_modified' => $current_date,
      'dated_created' => ''
    );

    if($this->input->post('recruiter_password')!=''){
      $data_array['password'] = $this->input->post('recruiter_password');
    }


    $unique_time =  $_POST['unique_time'];
    if(isset($_FILES['recruiter_profile_image']) && !empty($_FILES['recruiter_profile_image']['name'])){
      $old_pic_name = $this->input->post('old_image');
      $real_path = realpath(APPPATH . '../public/uploads/recruiter/');
      @unlink($real_path . "/" . $old_pic_name);
      @unlink($real_path . "/thumb/" . $old_pic_name);

      $image = $_FILES['recruiter_profile_image']['name'];
      $image = str_replace(' ', '_', $image);
$image =  preg_replace("/(\.)(?=\S+\.)/", "_", $image);

      $data_array['recruiter_profile_image'] = $unique_time.$image;
    }


    $last_id = $this->recruiter_model->update($id,$data_array);
    //$employer_array['company_ID'] = $company_id;
    //$employer_id = $this->employers_model->add_employer($employer_array);


    //$this->session->set_userdata($user_data);

    //Sending email to the user
    /*
    $row_email = $this->email_model->get_records_by_id(3);
    $from_email = replace_string('{SITE_EMAIL_URL}',SITE_EMAIL_URL,$row_email->from_email);
    $from_name = replace_string('{SITE_NAME}',SITE_NAME,$row_email->from_name);
    $subject = replace_string('{SITE_NAME}',SITE_NAME,$row_email->subject);

    $config = array();
    $config['wordwrap'] = TRUE;
    $config['mailtype'] = 'html';

    $this->email->initialize($config);
    $this->email->clear(TRUE);
    $this->email->from($from_email, $from_name);
    $this->email->to($this->input->post('email'));
    $this->email->bcc('babar@medialinkers.com');
    $this->email->subject($subject);
    $mail_message = $this->email_drafts_model->employer_signup($row_email->content, $employer_array);
    $this->email->message($mail_message);
    $this->email->send();
    */

    $this->session->set_flashdata('update_action', true);

    redirect(base_url('adminmedia/recruiter'));
  }

  public function upload_image(){

    $newfile_name_without_space = $image = str_replace(' ', '_', $_FILES['recruiter_profile_image']['name']);
$newfile_name_without_space =  preg_replace("/(\.)(?=\S+\.)/", "_", $newfile_name_without_space);

    $unique_time =  $_POST['unique_time'];
    $config = array();
    if (isset($_FILES['recruiter_profile_image']) && !empty($_FILES['recruiter_profile_image']['name'])){
      $real_path = realpath(APPPATH . '../public/uploads/recruiter/');
      $config['upload_path'] = $real_path;
      $config['allowed_types'] = 'gif|jpg|png|jpeg';
      /*$config['overwrite'] = true;*/
      $config['max_size']	= '6000';
      $config['max_width'] = '2024';
      $config['max_height'] = '1768';
      $config['file_name'] = $unique_time.$newfile_name_without_space;

      $this->upload->initialize($config);
      if ($this->upload->do_upload('recruiter_profile_image')){
        $image = array('upload_data' => $this->upload->data());
        $image_name = $image['upload_data']['file_name'];
        $_POST['recruiter_profile_image']=$image_name;
        $thumb_config['image_library'] = 'gd2';
        $thumb_config['source_image']	= $real_path.'/'.$image_name;
        $thumb_config['new_image']	= $real_path.'/thumb/'.$image_name;
        $thumb_config['maintain_ratio'] = TRUE;
        $thumb_config['height']	= 200;
        $thumb_config['width']	 = 250;

        $this->image_lib->initialize($thumb_config);
        $this->image_lib->resize();

        return true;

      }
      else{
        // possibly do some clean up ... then throw an error
        $this->form_validation->set_message('upload_image', $this->upload->display_errors());
        return false;
      }
    }
    else
    {
      return false;
    }
  }


  public function search(){
    $data['title'] = SITE_NAME.': Manage recruiter | Search';
    $data['msg'] = '';
    $data['result_email_template'] = $this->email_model->get_all_recruiter_records();

    $data['result_curency'] = $this->posted_jobs_model->get_all_active_curency();
    $data['result_cites'] = $this->cities_model->get_all_active_records();
    $data['result_countries'] = $this->countries_model->get_all_active_records();
    $data['result_company_type'] = $this->company_type_model->get_all_records();
    $data['result_industries'] = $this->industries_model->get_all_active_records();


    $search_key = $this->input->post('search');
    $obj_result = $this->recruiter_model->get_all_records_by_serach($search_key);



    if($obj_result){
      foreach($obj_result as $row){

        $obj_recruiter_all_jobs = $this->posted_jobs_model->get_all_job_by_recruiter_id($row->ID);


        $obj_recruiter_open_jobs = $this->posted_jobs_model->get_open_job_by_recruiter_id($row->ID);
        $row->total_jobs_posted = 0;
        $row->total_open_jobs_posted = 0;
        $row->total_cv_searched = 0;

        if(!empty($obj_recruiter_all_jobs)){
          $row->total_jobs_posted = count($obj_recruiter_all_jobs);
        }
        if(!empty($obj_recruiter_open_jobs)){
          $row->total_open_jobs_posted = count($obj_recruiter_open_jobs);
        }
      }
    }



    $data["total_rows"] = count($obj_result);

    $data['result'] = $obj_result;
    $this->load->view('adminmedia/recruiter_view', $data);
    return;

    /*
    //Pagination starts
    $total_rows = $this->cms_model->record_count('mm_users');
    $config = pagination_configuration(base_url("adminmedia/manage_users"), $total_rows, 50, 3, 5, true);

    $this->pagination->initialize($config);
    $page = ($this->uri->segment(2)) ? $this->uri->segment(3) : 0;
    $page_num = $page-1;
    $page_num = ($page_num<0)?'0':$page_num;
    $page = $page_num*$config["per_page"];
    $data["links"] = $this->pagination->create_links();
    //Pagination ends

    $obj_result = $this->user_model->get_all_records_limit($config["per_page"], $page);
    */

  }
  public function search_by_status($sts=''){
    $data['title'] = SITE_NAME.': Manage recruiter | Search';
    $data['msg'] = '';
    $data['result_email_template'] = $this->email_model->get_all_recruiter_records();

    $obj_result = $this->recruiter_model->get_all_records_by_sts($sts);

    if($obj_result){
      foreach($obj_result as $row){

        $obj_recruiter_all_jobs = $this->posted_jobs_model->get_all_job_by_recruiter_id($row->ID);


        $obj_recruiter_open_jobs = $this->posted_jobs_model->get_open_job_by_recruiter_id($row->ID);
        $row->total_jobs_posted = 0;
        $row->total_open_jobs_posted = 0;
        $row->total_cv_searched = 0;

        if(!empty($obj_recruiter_all_jobs)){
          $row->total_jobs_posted = count($obj_recruiter_all_jobs);
        }
        if(!empty($obj_recruiter_open_jobs)){
          $row->total_open_jobs_posted = count($obj_recruiter_open_jobs);
        }
      }
    }

    $data['result_curency'] = $this->posted_jobs_model->get_all_active_curency();
    $data['result_cites'] = $this->cities_model->get_all_active_records();
    $data['result_countries'] = $this->countries_model->get_all_active_records();
    $data['result_company_type'] = $this->company_type_model->get_all_records();
    $data['result_industries'] = $this->industries_model->get_all_active_records();


    $data["total_rows"] = count($obj_result);
    $data['result'] = $obj_result;
    $this->load->view('adminmedia/recruiter_view', $data);
    return;

  }

  public function status($id='', $current_staus=''){

    if($id==''){
      echo 'error';
      exit;
    }
    if($current_staus==''){
      echo 'invalid current status provided.';
      exit;
    }

    if($current_staus=='active')
    $new_status= 'de-active';
    else
    $new_status= 'active';

    $data = array (
      'sts' => $new_status
    );

    $this->recruiter_model->update($id, $data);
    echo $new_status;
    exit;
  }

  public function delete($id='',$image_name=''){

    if($id==''){
      echo 'error';
      exit;
    }

    $real_path = realpath(APPPATH . '../public/uploads/recruiter/');
    @unlink($real_path."/".$image_name);
    @unlink($real_path."/thumb/".$image_name);

    //echo $real_path."/".$banner_name;
    $obj_row = $this->recruiter_model->delete($id);
    echo 'done';
    exit;
  }


  public function advance_search(){

    $data['result_curency'] = $this->posted_jobs_model->get_all_active_curency();
    $data['result_cites'] = $this->cities_model->get_all_active_records();
    $data['result_countries'] = $this->countries_model->get_all_active_records();
    $data['result_company_type'] = $this->company_type_model->get_all_records();
    $data['result_industries'] = $this->industries_model->get_all_active_records();


    $data['title'] = SITE_NAME.': Manage Recruiter | Search';
    $data['msg'] = '';
    $data['result_email_template'] = $this->email_model->get_all_recruiter_records();





    $search_data = $this->input->post();
    if(empty($search_data)){
      redirect(base_url('adminmedia/recruiter'));
      return;
    }



    /********************************************* For multifle country Start ********************************/


    if(isset($_POST['recruiter_country'])!=''){
      $country_name_var = '';
      foreach ($_POST['recruiter_country'] as $selectedOption){
        $country_name_var.= $selectedOption.',';

      }
      $search_data['recruiter_country'] = rtrim($country_name_var,',');
    }

    /**************************************************** For multifle country End********************************/



    //print'<pre>';print_r($search_data);exit;


    $is_filed_empty = 0;

    unset($search_data['submit']);

    foreach($search_data as $key=>$value){
      if($value!=''){
        $is_filed_empty = 1;
      }
    }
    if($is_filed_empty==0){
      redirect(base_url('adminmedia/recruiter'));
      return;
    }



    foreach($search_data as $key=>$val){
      if($val=='')
      unset($search_data[$key]);
    }


    // print'<pre>';print_r($search_data);exit;

    //count
    //$total_rows = $this->job_seekers_model->advance_search_record_count($search_data);
    //Records
    $obj_result = $this->recruiter_model->get_all_advance_searched_records($search_data);

    // print'<pre>';print_r($obj_result);exit;
    if($obj_result){
      foreach($obj_result as $row){

        $obj_recruiter_all_jobs = $this->posted_jobs_model->get_all_job_by_recruiter_id($row->ID);


        $obj_recruiter_open_jobs = $this->posted_jobs_model->get_open_job_by_recruiter_id($row->ID);
        $row->total_jobs_posted = 0;
        $row->total_open_jobs_posted = 0;
        $row->total_cv_searched = 0;

        if(!empty($obj_recruiter_all_jobs)){
          $row->total_jobs_posted = count($obj_recruiter_all_jobs);
        }
        if(!empty($obj_recruiter_open_jobs)){
          $row->total_open_jobs_posted = count($obj_recruiter_open_jobs);
        }
      }
    }


    if(!empty($obj_result)){
      $data["total_rows"] = count($obj_result);}else{
        $data["total_rows"] = 0;
      }



      $data['result'] = $obj_result;
      $data['s_type'] = 'search';
      $this->load->view('adminmedia/recruiter_view', $data);
      return;
    }
  }
