<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

class Dashboard extends BaseController {

    /**
     * This is default constructor of the class
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('menu_model');
        $this->load->model('member_model');
        $this->isLoggedIn();

        $menu_key = 'home';

        $baseID = $this->input->get('baseID', TRUE);

        $baseID = isset($baseID) ? 1 : $baseID;


        $result = $this->loadThisForAccess($this->role, $baseID, $menu_key);
        if ($result != true) {
            redirect('access');
        }
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index() {
        
        
        
        
        //5-2-2021
        
//        $this->db->trans_start();
//
//        $this->db->select('*');
//        $this->db->from('tbl_lookup_details');
//        $this->db->where('display_order',0);
//        $this->db->where('id<=', 438);
//        $query = $this->db->get();
//        $table_one_data = $query->result();
//
//        foreach ($table_one_data as $data_one) {
//            $this->db->from('tbl_lookup_details_testing');
//            $this->db->where('id', $data_one->id);
//            $this->db->where('lookup_master_id', $data_one->lookup_master_id);
//            $query = $this->db->get();
//            $exists = $query->row();
//            if ($exists == true) {
//                
////                echo $data_one->name_old . ' matched<br/>';
//                $update_data = array(
//                    "display_order" => $exists->display_order
//                );
//
//                $this->db->where('id', $data_one->id);
//                $this->db->update('tbl_lookup_details', $update_data);
//            }
//            
//        }
////        
//         $this->db->trans_complete();
//        
//        echo 'done!!!!!';
//
//        exit();
//
//
//
////        echo "<pre/>";
////        print_r($table_one_data);
////        exit();
//
//        foreach ($table_one_data as $data_one) {
//
//            $insert_data = array(
//                "name" => $data_one->name_old,
//                "lookup_master_id" => $data_one->lookup_master_id_old,
//                "code" => $data_one->code_old,
//                "internal_code" => $data_one->internal_code_old,
//                "description" => $data_one->description_old,
//                "active" => 1,
//                'insertedBy' => $this->vendorId,
//                'insertedOn' => date('Y-m-d H:i:s')
//            );
//
//            $this->db->insert('tbl_lookup_details', $insert_data);
////                    echo "<pre/>";
////                    print_r($insert_data);
////                    exit();
//            $insert_id = $this->db->insert_id();
//
//            $update_data = array(
//                "new_id" => $insert_id
//            );
//
//            $this->db->where('id', $data_one->id);
//            $this->db->update('tbl_lookup_details', $update_data);
//        }
//
//        $this->db->trans_complete();
//        
//        echo 'okkkkk';
//
//        exit();
//
//        //---------------------------------------------------------------   
//        $this->db->trans_start();
//
//        $query1 = "SET IDENTITY_INSERT tbl_lookup_details ON ";
//        $this->db->query($query1);
//
//        $this->db->select('*');
//        $this->db->from('tbl_lookup_details_testing');
//        $this->db->where('id!=', 27);
//        $this->db->where('id!=', 57);
//        $this->db->where('id!=', 177);
//        $this->db->where('id!=', 181);
//        $this->db->where('id!=', 182);
//        $query = $this->db->get();
//        $table_one_data = $query->result();
//
////        $this->db->select('*');
////        $this->db->from('tbl_lookup_master');
////        $query = $this->db->get();
////        $table_two_data=$query->result();
//
//        foreach ($table_one_data as $data_one) {
//            $this->db->from('tbl_lookup_details');
//            $this->db->where('id', $data_one->id);
//            $this->db->where('name', $data_one->name);
//            $this->db->where('lookup_master_id', $data_one->lookup_master_id);
//            $query = $this->db->get();
//            $exists = $query->row();
////            if($exists==false)                echo $data_one->id.' '.$data_one->name.' not match<br/>';
//            if ($exists == false) {
//                $this->db->from('tbl_lookup_details');
//                $this->db->where('id', $data_one->id);
//                $query = $this->db->get();
//                $id_exists = $query->row();
//                if ($id_exists) {
//                    $update_data = array(
//                        "name" => $data_one->name,
//                        "code" => $data_one->code,
//                        "internal_code" => $data_one->internal_code,
//                        "description" => $data_one->description,
//                        "lookup_master_id" => $data_one->lookup_master_id,
//                        "name_old" => $id_exists->name,
//                        "lookup_master_id_old" => $id_exists->lookup_master_id,
//                        "code_old" => $id_exists->code,
//                        "internal_code_old" => $id_exists->internal_code,
//                        "description_old" => $id_exists->description
//                    );
//
//                    $this->db->where('id', $id_exists->id);
//                    $this->db->update('tbl_lookup_details', $update_data);
//                } else {
//                    $insert_data = array(
//                        "id" => $data_one->id,
//                        "name" => $data_one->name,
//                        "lookup_master_id" => $data_one->lookup_master_id,
//                        "code" => $data_one->code,
//                        "internal_code" => $data_one->internal_code,
//                        "description" => $data_one->description,
//                        'insertedBy' => $this->vendorId,
//                        'insertedOn' => date('Y-m-d H:i:s')
//                    );
//
//                    $this->db->insert('tbl_lookup_details', $insert_data);
//                }
//            }
//        }
//
//        $query2 = "SET IDENTITY_INSERT tbl_lookup_details OFF ";
//        $this->db->query($query2);
//        $this->db->trans_complete();
//
//        exit();
        //-------------------------------------------------
        $baseID = $this->input->get('baseID', TRUE);
        $this->load->model('menu_model');

        $this->global['menu'] = $this->menu_model->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : Dashboard';


        // echo date("ymdis");
        //echo CI_VERSION; 
        $this->load->view('includes/header', $this->global);
        $this->load->view('dashboard');
        $this->load->view('includes/footer');
    }

    function logout() {
        $this->session->sess_destroy();

        redirect('login');
    }

}

?>