<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

class Reports extends BaseController {

    /**
     * This is default constructor of the class
     */
    public $controller = "reports";
    public $pageTitle = 'Report Management';
    public $pageShortName = 'Report';

    public function __construct() {
        parent::__construct();
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        ini_set('sqlsrv.ClientBufferMaxKBSize', '5624288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '5624288'); // Setting to 512M - for pdo_sqlsrv

        $this->load->model('Reports_model', 'modelName');
        $this->load->model('menu_model', 'menuModel');
        $this->load->library('pagination');
        $this->isLoggedIn();
        $menu_key = 'report';
        $baseID = $this->input->get('baseID', TRUE);
        $result = $this->loadThisForAccess($this->role, $baseID, $menu_key);
        if ($result != true) {
            redirect('access');
        }
    }

    /**
     * This function used to load the first screen of the user
     */
    public function conception() {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = $this->pageTitle;
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'conception';
        $data['editMethod'] = 'edit_conception';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

        //$data['conception_info'] = $this->modelName->all_conception_info($data['round_no'], $this->config->item('conceptionTable'), 0);
//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['conception_info']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/conception', $data);
        $this->load->view('includes/footer');
    }

    public function show_conception() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'conception_date',
            2 => 'DOB',
            3 => 'HHNO',
            4 => 'member_code',
            5 => 'fk_conception_order_code',
            6 => 'PREGPLAN',
            7 => 'fk_conception_followup_status_code',
            8 => 'fk_conception_result_code',
            9 => 'insertedDate',
            10 => 'insertedTime',
            11 => 'insertedBy_name',
            12 => 'updatedDate',
            13 => 'updatedTime',
            14 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("conception_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("conception_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_conception/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->conception_date,
                    $rows->DOB,
                    $rows->HHNO,
                    $rows->member_code,
                    $rows->fk_conception_order_code,
                    $rows->PREGPLAN,
                    $rows->fk_conception_followup_status_code,
                    $rows->fk_conception_result_code,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_conception();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_conception() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("conception_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("conception_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_conception($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Conception";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_conception';
        $data['shortName'] = "Conception";
        $data['boxTitle'] = 'List';

        $data['conception_info'] = $this->modelName->conception_info($id, $this->config->item('conceptionTable'));

//        echo "<pre/>";
//        print_r($data['conception_info']); exit();

        $data['conception_plan'] = $this->modelName->getLookUpList($this->config->item('conception_plan'));
        $data['conception_order'] = $this->modelName->getLookUpList($this->config->item('conception_order'));
        $data['consp_follow_up_status'] = $this->modelName->getLookUpList($this->config->item('consp_follow_up_status'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_conception', $data);
        $this->load->view('includes/footer');
    }

    public function edit_internal_in($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Internal in";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_internal_in';
        $data['shortName'] = "Internal in";
        $data['boxTitle'] = 'List';

        $data['internal_in_info'] = $this->modelName->internal_in_info($id, $this->config->item('migrationInTable'));

        $data['memberexittyp'] = $this->modelName->getLookUpListSpecific($this->config->item('mementrytyp'), array('intin'));
        $data['internal_movement_cause'] = $this->modelName->getLookUpList($this->config->item('internal_movement_cause'));
        $data['slumlist'] = $this->modelName->getListType($this->config->item('slumTable'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_internal_in', $data);
        $this->load->view('includes/footer');
    }

    function update_internal_in() {

        $migrationID = $this->input->post('migrationID', true);
        $member_master_id = $this->input->post('member_master_id', true);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('movement_date', 'Movement/migration Date', 'trim|required');

        $getCurrentRound = $this->modelName->getCurrentRound();
        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_internal_in/' . $migrationID . '?baseID=' . $baseID);
        } else {

            if ($getCurrentRound->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_internal_in/' . $migrationID . '?baseID=' . $baseID);
            }

            $movement_date = $this->input->post('movement_date', true);
            $remarks = $this->input->post('remarks', true);


            $fk_internal_cause = 0;
            $slumID = 0;
            $slumAreaID = 0;
            $househodID = 0;


            $fk_internal_cause = $this->input->post('fk_internal_cause', true);
            $slumID = $this->input->post('slumID', true);
            $slumAreaID = $this->input->post('slumAreaID', true);
            $househodID = $this->input->post('househodID', true);


            $new_movement_date = NULL;

            if (!empty($movement_date)) {
                $parts5 = explode('/', $movement_date);
                $new_movement_date = $parts5[2] . '-' . $parts5[1] . '-' . $parts5[0];
            }



            $this->db->trans_start();

            try {

                $IdInfo = array(
                    'movement_date' => $new_movement_date,
                    'fk_internal_cause' => $fk_internal_cause,
                    'slumIDFrom' => $slumID,
                    'slumAreaIDFrom' => $slumAreaID,
                    'household_master_id_move_from' => $househodID,
                    'remarks' => $remarks,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($IdInfo, $migrationID, $this->config->item('migrationInTable'));

                // member household

                $whereMigout = array('id' => $member_master_id);
                $member_household_id_last = $this->db->select('member_household_id_last')->from($this->config->item('memberMasterTable'))->where($whereMigout)->get()->row()->member_household_id_last;


                $memberHouseholdUpdate = array(
                    'entry_date' => $new_movement_date,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($memberHouseholdUpdate, $member_household_id_last, $this->config->item('memberHouseholdTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating movement/Internal in.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Movement/Internal in updated successfully.');

            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/internal_in' . '?baseID=' . $baseID);
            }

            redirect($this->controller . '/edit_internal_in/' . $migrationID . '?baseID=' . $baseID);
        }
    }

    public function edit_migration_in($id) {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Migration in";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_migration_in';
        $data['shortName'] = "Migration in";
        $data['boxTitle'] = 'List';

        $data['migration_in_info'] = $this->modelName->migration_in_info($id, $this->config->item('migrationInTable'));

//        echo "<pre/>";
//        print_r($data['migration_in_info']); exit();

        $data['memberexittyp'] = $this->modelName->getLookUpListSpecific($this->config->item('mementrytyp'), array('min'));
        $data['outside_cause'] = $this->modelName->getLookUpList($this->config->item('migReason'));
        $data['countrylist'] = $this->modelName->getListType($this->config->item('countryTable'));
        $data['divisionlist'] = $this->modelName->getListType($this->config->item('divTable'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_migration_in', $data);
        $this->load->view('includes/footer');
    }

    function update_migration_in() {

        $migrationID = $this->input->post('migrationID', true);
        $member_master_id = $this->input->post('member_master_id', true);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('movement_date', 'Movement/migration Date', 'trim|required');

        $getCurrentRound = $this->modelName->getCurrentRound();

        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_migration_in/' . $migrationID . '?baseID=' . $baseID);
        } else {

            if ($getCurrentRound->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_migration_in/' . $migrationID . '?baseID=' . $baseID);
            }


            $movement_date = $this->input->post('movement_date', true);
            $remarks = $this->input->post('remarks', true);

            $fk_migration_cause = 0;
            $countryID = 0;
            $divisionID = 0;
            $districtID = 0;
            $thanaID = 0;

            $fk_migration_cause = $this->input->post('fk_migration_cause', true);
            $countryID = $this->input->post('countryID', true);

            if ($this->config->item('bangladesh') == $countryID) { // bangldesh 
                $divisionID = ($this->input->post('divisionID', true)) ? $this->input->post('divisionID', true) : 0;
                $districtID = ($this->input->post('districtID', true)) ? $this->input->post('districtID', true) : 0;
                $thanaID = ($this->input->post('thanaID', true)) ? $this->input->post('thanaID', true) : 0;
            }



            if (!empty($movement_date)) {
                $parts5 = explode('/', $movement_date);
                $new_movement_date = $parts5[2] . '-' . $parts5[1] . '-' . $parts5[0];
            }



            $this->db->trans_start();

            try {
                $IdInfo = array(
                    'movement_date' => $new_movement_date,
                    'fk_migration_cause' => $fk_migration_cause,
                    'countryIDMoveFrom' => $countryID,
                    'divisionIDMoveFrom' => $divisionID,
                    'districtIDMoveFrom' => $districtID,
                    'thanaIDMoveFrom' => $thanaID,
                    'remarks' => $remarks,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($IdInfo, $migrationID, $this->config->item('migrationInTable'));


                // update member household info

                $whereMigout = array('id' => $member_master_id);
                $member_household_id_last = $this->db->select('member_household_id_last')->from($this->config->item('memberMasterTable'))->where($whereMigout)->get()->row()->member_household_id_last;


                $memberHouseholdUpdate = array(
                    'entry_date' => $new_movement_date,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($memberHouseholdUpdate, $member_household_id_last, $this->config->item('memberHouseholdTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating movement/migration in.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Movement/Migration in updated successfully.');

            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/migration_in' . '?baseID=' . $baseID);
            }

            redirect($this->controller . '/edit_migration_in/' . $migrationID . '?baseID=' . $baseID);
        }
    }

    public function member_master() {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = $this->pageTitle;
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'member_master';
        $data['editMethod'] = 'edit_member_master';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';
        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['district'] = $this->modelName->getListType($this->config->item('districtTable'));

        $data['district_id'] = '';
        $data['thana_id'] = '';
        $data['slum_id'] = '';
        $data['slumarea_id'] = '';
        $data['round_no'] = '';

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('district_id');
            $this->session->unset_userdata('thana_id');
            $this->session->unset_userdata('slum_id');
            $this->session->unset_userdata('slumarea_id');
            $this->session->unset_userdata('round_no');
            $data['district_id'] = '';
            $data['thana_id'] = '';
            $data['slum_id'] = '';
            $data['slumarea_id'] = '';
            $data['round_no'] = '';
        }


        $district_id = $this->input->post('district_id');
        $data['district_id'] = $this->session->userdata('district_id');
        $thana_id = $this->input->post('thana_id');
        $data['thana_id'] = $this->session->userdata('thana_id');
        $slum_id = $this->input->post('slum_id');
        $data['slum_id'] = $this->session->userdata('slum_id');
        $slumarea_id = $this->input->post('slumarea_id');
        $data['slumarea_id'] = $this->session->userdata('slumarea_id');
        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('district_id', $district_id);
            $data['district_id'] = $this->session->userdata('district_id');
            $this->session->set_userdata('thana_id', $thana_id);
            $data['thana_id'] = $this->session->userdata('thana_id');
            $this->session->set_userdata('slum_id', $slum_id);
            $data['slum_id'] = $this->session->userdata('slum_id');
            $this->session->set_userdata('slumarea_id', $slumarea_id);
            $data['slumarea_id'] = $this->session->userdata('slumarea_id');
            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

//        $data['list_fields'] = $this->modelName->all_member_info($data['district_id'], $data['thana_id'], $data['slum_id'], $data['slumarea_id'], 1, $this->config->item('memberMasterTable'), 1);
//
//                foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//                                    exit();
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/member', $data);
        $this->load->view('includes/footer');
    }

    public function show_member_master() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'household_master_id_hh',
            2 => 'member_code',
            3 => 'birth_date',
            4 => 'father_code',
            5 => 'mother_code',
            6 => 'spouse_code',
            7 => 'national_id',
            8 => 'birth_registration_date',
            9 => 'afterYear',
            10 => 'contactNoOne',
            11 => 'contactNoTwo',
            12 => 'marital_status_code',
            13 => 'fk_sex_code',
            14 => 'fk_religion_code',
            15 => 'fk_relation_with_hhh_code',
            16 => 'fk_mother_live_birth_order_code',
            17 => 'fk_birth_registration_code',
            18 => 'fk_why_not_birth_registration_code',
            19 => 'fk_additionalChild_code',
            20 => 'insertedDate',
            21 => 'insertedTime',
            22 => 'insertedBy_name',
            23 => 'updatedDate',
            24 => 'updatedTime',
            25 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $district_id = $this->session->userdata('district_id') ? $this->session->userdata('district_id') : 0;
        $thana_id = $this->session->userdata('thana_id') ? $this->session->userdata('thana_id') : 0;
        $slum_id = $this->session->userdata('slum_id') ? $this->session->userdata('slum_id') : 0;
        $slumarea_id = $this->session->userdata('slumarea_id') ? $this->session->userdata('slumarea_id') : 0;
        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        $this->db->select('*');
        $this->db->from("member_view");


        if ($district_id > 0) {
            $this->db->where(array('fk_district_id' => $district_id));
        }
        if ($thana_id > 0) {
            $this->db->where(array('fk_thana_id' => $thana_id));
        }
        if ($slum_id > 0) {
            $this->db->where(array('fk_slum_id' => $slum_id));
        }
        if ($slumarea_id > 0) {
            $this->db->where(array('fk_slum_area_id' => $slumarea_id));
        }
        if ($round_no > 0) {
            $this->db->where(array('round_master_id_entry_round' => $round_no));
        }

        $all_data_list = $this->db->get();


        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "reports/edit_member_master/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->household_master_id_hh,
                    $rows->member_code,
                    $rows->birth_date,
                    $rows->father_code,
                    $rows->mother_code,
                    $rows->spouse_code,
                    $rows->national_id,
                    $rows->birth_registration_date,
                    $rows->afterYear,
                    $rows->contactNoOne,
                    $rows->contactNoTwo,
                    $rows->marital_status_code,
                    $rows->fk_sex_code,
                    $rows->fk_religion_code,
                    $rows->fk_relation_with_hhh_code,
                    $rows->fk_mother_live_birth_order_code,
                    $rows->fk_birth_registration_code,
                    $rows->fk_why_not_birth_registration_code,
                    $rows->fk_additionalChild_code,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_member_master();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_member_master() {

        $district_id = $this->session->userdata('district_id') ? $this->session->userdata('district_id') : 0;
        $thana_id = $this->session->userdata('thana_id') ? $this->session->userdata('thana_id') : 0;
        $slum_id = $this->session->userdata('slum_id') ? $this->session->userdata('slum_id') : 0;
        $slumarea_id = $this->session->userdata('slumarea_id') ? $this->session->userdata('slumarea_id') : 0;
        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        $this->db->select("COUNT(*) as num");
        $this->db->from("member_view");


        if ($district_id > 0) {
            $this->db->where(array('fk_district_id' => $district_id));
        }
        if ($thana_id > 0) {
            $this->db->where(array('fk_thana_id' => $thana_id));
        }
        if ($slum_id > 0) {
            $this->db->where(array('fk_slum_id' => $slum_id));
        }
        if ($slumarea_id > 0) {
            $this->db->where(array('fk_slum_area_id' => $slumarea_id));
        }
        if ($round_no > 0) {
            $this->db->where(array('round_master_id_entry_round' => $round_no));
        }

        $query = $this->db->get();


        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_member_master($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $household_master_id = $this->input->get('household_master_id', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Member master";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_member_master';
        $data['shortName'] = "Member master";
        $data['boxTitle'] = 'List';

        $data['member_master_info'] = $this->modelName->member_master_info($id, $this->config->item('memberMasterTable'));


        $data['district'] = $this->modelName->getListType($this->config->item('districtTable'));
        $data['district2'] = $this->modelName->getListType($this->config->item('districtTable'));
        $data['country'] = $this->modelName->getListType($this->config->item('countryTable'));

        $data['entryType'] = $this->modelName->getLookUpListSpecific($this->config->item('mementrytyp'), array('bls'));
        $data['membersextype'] = $this->modelName->getLookUpList($this->config->item('membersextype'));
        $data['relationhhh'] = $this->modelName->getLookUpList($this->config->item('relationhhh'));
        $data['religion'] = $this->modelName->getLookUpList($this->config->item('religion'));
        $data['maritalstatustyp'] = $this->modelName->getLookUpList($this->config->item('maritalstatustyp'));
        $data['educationtyp'] = $this->modelName->getLookUpList($this->config->item('educationtyp'));
        $data['secularedutyp'] = $this->modelName->getLookUpList($this->config->item('secularedutyp'));
        $data['religiousedutype'] = $this->modelName->getLookUpList($this->config->item('religiousedutype'));
        $data['occupationtyp'] = $this->modelName->getLookUpList($this->config->item('occupationtyp'));
        $data['birthregistration'] = $this->modelName->getLookUpList($this->config->item('yes_no'));
        $data['additionChild'] = $this->modelName->getLookUpList($this->config->item('yes_no'));
        $data['whynotbirthreg'] = $this->modelName->getLookUpList($this->config->item('whynotbirthreg'));
        $data['education_year'] = $this->modelName->getLookUpList($this->config->item('education_year'));
        $data['child_after_year'] = $this->modelName->getLookUpList($this->config->item('child_after_year'));

        $data['motherList'] = $this->modelName->getMemberMasterPresentListByHouseholdIds($household_master_id, $this->config->item('femaleSexCode'));
        $data['fatherList'] = $this->modelName->getMemberMasterPresentListByHouseholdIds($household_master_id, $this->config->item('femaleSexCodeMale'));
        $data['spouseList'] = $this->modelName->getMemberMasterPresentList($household_master_id);

//        echo "<pre/>";
//        print_r($data['motherList']); exit();

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_member_master', $data);
        $this->load->view('includes/footer');
    }

    function update_member_master() {

        $this->load->library('form_validation');

        $member_master_id = $this->input->post('id', true);

        $household_master_id = $this->input->post('household_master_id', true);
        $fk_member_relation_id_last = $this->input->post('fk_member_relation_id_last', true);
        $fk_education_id_last = $this->input->post('fk_education_id_last', true);
        $fk_occupation_id_last = $this->input->post('fk_occupation_id_last', true);
        $member_household_id_last = $this->input->post('member_household_id_last', true);


        $baseID = $this->input->get('baseID', TRUE);

        $this->form_validation->set_rules('sexType', 'Sex Type', 'trim|required|numeric');
        $this->form_validation->set_rules('relationheadID', 'Relation with Head', 'trim|required|numeric');
        $this->form_validation->set_rules('maritalStatusType', 'Marital Status', 'trim|required|numeric');

        $this->form_validation->set_rules('memberName', 'Member Name', 'trim|required|max_length[255]|xss_clean');
        // $this->form_validation->set_rules('fatherCode','Father Code','trim|required|max_length[11]|xss_clean');
        // $this->form_validation->set_rules('motherCode','Mother Code','trim|required|max_length[11]|xss_clean');
        //  $this->form_validation->set_rules('spouseCode','Spouse Code','trim|required|max_length[11]|xss_clean');
        $this->form_validation->set_rules('nationalID', 'National ID', 'trim|max_length[50]|xss_clean');

        //$this->form_validation->set_rules('entryType','Entry Type','trim|required|numeric');
        $this->form_validation->set_rules('entryDate', 'Entry Date', 'trim|required');
        $this->form_validation->set_rules('birthdate', 'Birth Date', 'trim|required');

        // $this->form_validation->set_rules('contactNumber','Contact Number','trim|required|max_length[100]|xss_clean');
        $this->form_validation->set_rules('religionType', 'Religion', 'trim|required|numeric');
        $this->form_validation->set_rules('educationType', 'Education Type', 'trim|required|numeric');
        // $this->form_validation->set_rules('secularEduType','Secular Education','trim|required|numeric');
        // $this->form_validation->set_rules('religiousEduType','Religious Education','trim|required|numeric');
        $this->form_validation->set_rules('occupationType', 'Occupation', 'trim|required|numeric');
        $this->form_validation->set_rules('birstRegiType', 'Birth Registration', 'trim|required|numeric');
        $this->form_validation->set_rules('additionalChild', 'additional Child', 'trim|required|numeric');

        $this->form_validation->set_rules('contactNoOne', 'Contact Number One', 'trim|max_length[11]|xss_clean');
        $this->form_validation->set_rules('contactNoTwo', 'Contact Number Two', 'trim|max_length[11]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_member_master/' . $member_master_id . '?baseID=' . $baseID . '&household_master_id=' . $household_master_id);
        } else {
            $memberName = $this->input->post('memberName', true);
            // $entryType = $this->input->post('entryType',true);
            $entryDate = $this->input->post('entryDate', true);
            $sexType = $this->input->post('sexType', true);
            $birthdate = $this->input->post('birthdate', true);

            $contactNoOne = $this->input->post('contactNoOne', true);
            $contactNoTwo = $this->input->post('contactNoTwo', true);

            $fatherCode = $this->input->post('fatherCode', true);
            $motherCode = $this->input->post('motherCode', true);
            $spouseCode = $this->input->post('spouseCode', true);
            $nationalID = $this->input->post('nationalID', true);
            $relationheadID = $this->input->post('relationheadID', true);
            $hhdate = $this->input->post('hhdate', true);

            $maritalStatusType = $this->input->post('maritalStatusType', true);
            $religionType = $this->input->post('religionType', true);
            $educationType = $this->input->post('educationType', true);
            $yearOfEdu = $this->input->post('yearOfEdu', true);
            //  $secularEduType = $this->input->post('secularEduType',true);
            // $religiousEduType = $this->input->post('religiousEduType',true);
            $occupationType = $this->input->post('occupationType', true);
            $birstRegiType = $this->input->post('birstRegiType', true);
            $birthRegidate = $this->input->post('birthRegidate', true);
            $whyNotRegi = $this->input->post('whyNotRegi', true);
            $additionalChild = $this->input->post('additionalChild', true);
            $afterManyYear = $this->input->post('afterManyYear', true);
            $main_occupation_oth = $this->input->post('main_occupation_oth', true);

            // print_r($memberMaster); die();

            if ($maritalStatusType == 41) {
                $additionalChild = $this->input->post('additionalChild', true);
            } else {
                $additionalChild = 0;
            }
            if ($additionalChild == 1) {
                $afterManyYear = $this->input->post('afterManyYear', true);
            } else {
                $afterManyYear = 0;
            }




            $hhcode = $this->getLookUpDetailCode($relationheadID)[0]->internal_code;

            if ($hhcode == $this->config->item('household_head_code')) {
                $head = $this->getActiveHeadDetails($household_master_id, $relationheadID);

                if (!empty($head)) {

                    if ($head[0]->member_master_id != $member_master_id) {

                        $this->session->set_flashdata('error', 'An active head of this household already exist. Plz select something different as relation to head.');
                        redirect($this->controller . '/edit_member_master/' . $member_master_id . '?baseID=' . $baseID . '&household_master_id=' . $household_master_id);
                    }
                }
            }


            $round_master_id = $this->getCurrentRound()[0]->id;


            if (!empty($birthdate)) {
                $parts2 = explode('/', $birthdate);
                $new_birthdate = $parts2[2] . '-' . $parts2[1] . '-' . $parts2[0];
            }


            if (!empty($entryDate)) {
                $parts1 = explode('/', $entryDate);
                $new_entryDate = $parts1[2] . '-' . $parts1[1] . '-' . $parts1[0];
            }

            $new_hhdate = null;

            if (!empty($hhdate)) {
                $parts3 = explode('/', $hhdate);
                $new_hhdate = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $new_birthRegidate = null;

            if (!empty($birthRegidate)) {
                $parts5 = explode('/', $birthRegidate);
                $new_birthRegidate = $parts5[2] . '-' . $parts5[1] . '-' . $parts5[0];
            }

            if ($educationType == 45) {
                $yearOfEdu = 0;
            }

            if ($educationType == 120) {
                $yearOfEdu = 0;
            }

            if ($birstRegiType == 2) {
                $whyNotRegi = 0;
            }

            if ($additionalChild == 2) {
                $afterManyYear = 0;
            }


            $this->db->trans_start();

            try {



                $memberMaster = array(
                    'birth_date' => $new_birthdate,
                    'member_name' => $memberName,
                    'fk_marital_status' => $maritalStatusType,
                    'fk_sex' => $sexType,
                    'fk_religion' => $religionType,
                    'fk_relation_with_hhh' => $relationheadID,
                    'father_code' => $fatherCode,
                    'fk_mother_id' => $motherCode,
                    'fk_spouse_id' => $spouseCode,
                    'national_id' => $nationalID,
                    'fk_birth_registration' => $birstRegiType,
                    'birth_registration_date' => $new_birthRegidate,
                    'fk_why_not_birth_registration' => $whyNotRegi,
                    'fk_additionalChild' => $additionalChild,
                    'contactNoTwo' => $contactNoTwo,
                    'contactNoOne' => $contactNoOne,
                    'afterYear' => $afterManyYear,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                // member master

                $this->modelName->UpdateInfo($memberMaster, $member_master_id, $this->config->item('memberMasterTable'));


                // member household

                $memberHousehold = array(
                    'entry_date' => $new_entryDate,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($memberHousehold, $member_household_id_last, $this->config->item('memberHouseholdTable'));


                $whereHouseholdMaster = array('id' => $household_master_id);
                $member_master_id_last_head = $this->db->select('member_master_id_last_head')->from($this->config->item('householdMasterTable'))->where($whereHouseholdMaster)->get()->row()->member_master_id_last_head;


                // household head

                if ($hhcode == $this->config->item('household_head_code')) {


                    if ($member_master_id_last_head != $member_master_id) {

                        $householdMasterNew = array(
                            'member_master_id_last_head' => $member_master_id,
                            'transfer_complete' => 'No',
                            'updateBy' => $this->vendorId,
                            'updatedOn' => date('Y-m-d H:i:s')
                        );

                        $this->modelName->UpdateInfo($householdMasterNew, $household_master_id, $this->config->item('householdMasterTable'));

                        $householdHeadUpdate = array(
                            'is_last_head' => 'No',
                            'transfer_complete' => 'No',
                            'updateBy' => $this->vendorId,
                            'updatedOn' => date('Y-m-d H:i:s')
                        );

                        $this->db->where('household_master_id', $household_master_id);
                        $this->db->where('is_last_head', 'Yes');
                        $this->db->update($this->config->item('memberHeadTable'), $householdHeadUpdate);


                        $whereHouseholdhead = array('household_master_id' => $household_master_id, 'round_master_id' => $round_master_id);

                        $countRow = $this->db->select('count(id) as countRow')->from($this->config->item('memberHeadTable'))->where($whereHouseholdhead)->get()->row()->countRow;

                        if ($countRow > 0) {

                            $headID = $this->db->select('id')->from($this->config->item('memberHeadTable'))->where($whereHouseholdhead)->get()->row()->id;

                            $householdHeadup = array(
                                'member_master_id' => $member_master_id,
                                'change_date' => $new_hhdate,
                                'is_last_head' => 'Yes',
                                'transfer_complete' => 'No',
                                'updateBy' => $this->vendorId,
                                'updatedOn' => date('Y-m-d H:i:s')
                            );

                            $this->modelName->UpdateInfo($householdHeadup, $headID, $this->config->item('memberHeadTable'));
                        } else {

                            $householdHeadNew = array(
                                'member_master_id' => $member_master_id,
                                'household_master_id' => $household_master_id,
                                'round_master_id' => $round_master_id,
                                'change_date' => $new_hhdate,
                                'is_last_head' => 'Yes',
                                'transfer_complete' => 'No',
                                'insertedBy' => $this->vendorId,
                                'insertedOn' => date('Y-m-d H:i:s')
                            );

                            $this->modelName->addNewList($householdHeadNew, $this->config->item('memberHeadTable'));
                        }
                    }
                } else if ($member_master_id_last_head == $member_master_id) {

                    //update household_head

                    $householdHeadUpdate = array(
                        'is_last_head' => 'No',
                        'transfer_complete' => 'No',
                        'updateBy' => $this->vendorId,
                        'updatedOn' => date('Y-m-d H:i:s')
                    );

                    $this->db->where('household_master_id', $household_master_id);
                    $this->db->where('is_last_head', 'Yes');
                    $this->db->update($this->config->item('memberHeadTable'), $householdHeadUpdate);


                    // delete household head
                    $this->db->where('household_master_id', $member_master_id_last_head);
                    $this->db->where('round_master_id', $round_master_id);
                    $this->db->delete($this->config->item('memberHeadTable'));


                    $householdMaster = array(
                        'member_master_id_last_head' => 0,
                        'transfer_complete' => 'No',
                        'updateBy' => $this->vendorId,
                        'updatedOn' => date('Y-m-d H:i:s')
                    );

                    $this->modelName->UpdateInfo($householdMaster, $household_master_id, $this->config->item('householdMasterTable'));
                }

                // occupation
                $occupation = array(
                    'fk_main_occupation' => $occupationType,
                    'main_occupation_oth' => $main_occupation_oth,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($occupation, $fk_occupation_id_last, $this->config->item('memberOccupationTable'));

                // Education

                $education = array(
                    // 'fk_religious_edu'=>$religiousEduType, 
                    //  'fk_secular_edu'=>$secularEduType, 
                    'fk_education_type' => $educationType,
                    'year_of_education' => $yearOfEdu,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($education, $fk_education_id_last, $this->config->item('memberEducationTable'));


                // Relation

                $relation = array(
                    'fk_relation' => $relationheadID,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($relation, $fk_member_relation_id_last, $this->config->item('memberRelationTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating member.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Member info updated successfully');

            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/member_master' . '?baseID=' . $baseID);
            }

            redirect($this->controller . '/edit_member_master/' . $member_master_id . '?baseID=' . $baseID . '&household_master_id=' . $household_master_id);
        }
    }

    public function pregnancy() {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "pregnancy";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'pregnancy';
        $data['editMethod'] = 'edit_pregnancy';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';
        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));

        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_pregnancy_info(1, $this->config->item('pregnancyTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//        }
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/pregnancy', $data);
        $this->load->view('includes/footer');
    }

    public function show_pregnancy() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'pregnancy_outcome_date',
            2 => 'breast_milk_day',
            3 => 'induced_abortion',
            4 => 'spontaneous_abortion',
            5 => 'live_birth',
            6 => 'still_birth',
            7 => 'milk_hours',
            8 => 'milk_day',
            9 => 'keep_follow_up',
            10 => 'routine_anc_chkup_mother_times',
            11 => 'anc_first_visit_months',
            12 => 'anc_second_visit_months',
            13 => 'anc_third_visit_months',
            14 => 'anc_fourth_visit_months',
            15 => 'anc_fifth_visit_months',
            16 => 'totalnumbertab',
            17 => 'pnc_chkup_mother_times',
            18 => 'pnc_first_visit_days',
            19 => 'pnc_second_visit_days',
            20 => 'remarks',
            21 => 'birth_date',
            22 => 'household_code',
            23 => 'member_code',
            24 => 'fk_litter_size_code',
            25 => 'fk_delivery_methodology_code',
            26 => 'fk_delivery_assist_type_code',
            27 => 'fk_delivery_term_place_code',
            28 => 'fk_colostrum_code',
            29 => 'fk_first_milk_code',
            30 => 'fk_facility_delivery_code',
            31 => 'fk_preg_complication_code',
            32 => 'fk_delivery_complication_code',
            33 => 'fk_preg_violence_code',
            34 => 'fk_health_problem_code',
            35 => 'fk_high_pressure_code',
            36 => 'fk_diabetis_code',
            37 => 'fk_preaklampshia_code',
            38 => 'fk_lebar_birth_code',
            39 => 'fk_vomiting_code',
            40 => 'fk_amliotic_code',
            41 => 'fk_membrane_code',
            42 => 'fk_malposition_code',
            43 => 'fk_headache_code',
            44 => 'fk_routine_anc_chkup_mother_code',
            45 => 'fk_anc_first_assist_code',
            46 => 'fk_anc_second_assist_code',
            47 => 'fk_anc_second_visit_code',
            48 => 'fk_anc_third_assist_code',
            49 => 'fk_anc_third_visit_code',
            50 => 'fk_anc_fourth_assist_code',
            51 => 'fk_anc_fourth_visit_code',
            52 => 'fk_anc_fifth_assist_code',
            53 => 'fk_anc_fifth_visit_code',
            54 => 'fk_anc_supliment_code',
            55 => 'fk_supliment_received_way_code',
            56 => 'fk_how_many_tab_code',
            57 => 'fk_anc_weight_taken_code',
            58 => 'fk_anc_blood_pressure_code',
            59 => 'fk_anc_urine_code',
            60 => 'fk_anc_blood_code',
            61 => 'fk_anc_denger_sign_code',
            62 => 'fk_anc_nutrition_code',
            63 => 'fk_anc_birth_prepare_code',
            64 => 'fk_anc_delivery_kit_code',
            65 => 'fk_anc_soap_code',
            66 => 'fk_anc_care_chix_code',
            67 => 'fk_anc_dried_code',
            68 => 'fk_anc_bathing_code',
            69 => 'fk_anc_breast_feed_code',
            70 => 'fk_anc_skin_contact_code',
            71 => 'fk_anc_enc_code',
            72 => 'fk_suspecred_infection_code',
            73 => 'fk_baby_antibiotics_code',
            74 => 'fk_prescribe_antibiotics_code',
            75 => 'fk_seek_treatment_code',
            76 => 'fk_anc_vaginal_bleeding_code',
            77 => 'fk_anc_convulsions_code',
            78 => 'fk_anc_severe_headache_code',
            79 => 'fk_anc_fever_code',
            80 => 'fk_anc_abdominal_pain_code',
            81 => 'fk_anc_diff_breath_code',
            82 => 'fk_anc_water_break_code',
            83 => 'fk_anc_vaginal_bleed_aph_code',
            84 => 'fk_anc_obstructed_labour_code',
            85 => 'fk_anc_convulsion_code',
            86 => 'fk_anc_sepsis_code',
            87 => 'fk_anc_severe_headache_delivery_code',
            88 => 'fk_anc_consciousness_code',
            89 => 'fk_anc_vaginal_bleeding_post_code',
            90 => 'fk_anc_convulsion_eclampsia_post_code',
            91 => 'fk_anc_high_feaver_post_code',
            92 => 'fk_anc_smelling_discharge_post_code',
            93 => 'fk_anc_severe_headache_post_code',
            94 => 'fk_anc_consciousness_post_code',
            95 => 'fk_anc_inability_baby_code',
            96 => 'fk_anc_baby_small_baby_code',
            97 => 'fk_anc_fast_breathing_baby_code',
            98 => 'fk_anc_convulsions_baby_code',
            99 => 'fk_anc_drowsy_baby_code',
            100 => 'fk_anc_movement_baby_code',
            101 => 'fk_anc_grunting_baby_code',
            102 => 'fk_anc_indrawing_baby_code',
            103 => 'fk_anc_temperature_baby_code',
            104 => 'fk_anc_hypothermia_baby_code',
            105 => 'fk_anc_central_cyanosis_baby_code',
            106 => 'fk_anc_umbilicus_baby_code',
            107 => 'fk_anc_labour_preg_code',
            108 => 'fk_anc_excessive_bld_pre_code',
            109 => 'fk_anc_severe_headache_preg_code',
            110 => 'fk_anc_obstructed_preg_code',
            111 => 'fk_anc_convulsion_preg_code',
            112 => 'fk_anc_placenta_preg_code',
            113 => 'fk_anc_breath_child_code',
            114 => 'fk_anc_suck_baby_code',
            115 => 'fk_anc_hot_cold_child_code',
            116 => 'fk_anc_blue_child_code',
            117 => 'fk_anc_convulsion_child_code',
            118 => 'fk_anc_indrawing_child_code',
            119 => 'fk_supliment_post_code',
            120 => 'fk_post_natal_visit_code',
            121 => 'fk_pnc_chkup_mother_code',
            122 => 'fk_pnc_first_visit_assist_code',
            123 => 'fk_pnc_first_visit_code',
            124 => 'fk_pnc_second_visit_assist_code',
            125 => 'fk_pnc_second_visit_code',
            126 => 'insertedDate',
            127 => 'insertedTime',
            128 => 'insertedBy_name',
            129 => 'updatedDate',
            130 => 'updatedTime',
            131 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("pregnancy_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("pregnancy_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_pregnancy/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->pregnancy_outcome_date,
                    $rows->breast_milk_day,
                    $rows->induced_abortion,
                    $rows->spontaneous_abortion,
                    $rows->live_birth,
                    $rows->still_birth,
                    $rows->milk_hours,
                    $rows->milk_day,
                    $rows->keep_follow_up,
                    $rows->routine_anc_chkup_mother_times,
                    $rows->anc_first_visit_months,
                    $rows->anc_second_visit_months,
                    $rows->anc_third_visit_months,
                    $rows->anc_fourth_visit_months,
                    $rows->anc_fifth_visit_months,
                    $rows->totalnumbertab,
                    $rows->pnc_chkup_mother_times,
                    $rows->pnc_first_visit_days,
                    $rows->pnc_second_visit_days,
                    $rows->remarks,
                    $rows->birth_date,
                    $rows->household_code,
                    $rows->member_code,
                    $rows->fk_litter_size_code,
                    $rows->fk_delivery_methodology_code,
                    $rows->fk_delivery_assist_type_code,
                    $rows->fk_delivery_term_place_code,
                    $rows->fk_colostrum_code,
                    $rows->fk_first_milk_code,
                    $rows->fk_facility_delivery_code,
                    $rows->fk_preg_complication_code,
                    $rows->fk_delivery_complication_code,
                    $rows->fk_preg_violence_code,
                    $rows->fk_health_problem_code,
                    $rows->fk_high_pressure_code,
                    $rows->fk_diabetis_code,
                    $rows->fk_preaklampshia_code,
                    $rows->fk_lebar_birth_code,
                    $rows->fk_vomiting_code,
                    $rows->fk_amliotic_code,
                    $rows->fk_membrane_code,
                    $rows->fk_malposition_code,
                    $rows->fk_headache_code,
                    $rows->fk_routine_anc_chkup_mother_code,
                    $rows->fk_anc_first_assist_code,
                    $rows->fk_anc_second_assist_code,
                    $rows->fk_anc_second_visit_code,
                    $rows->fk_anc_third_assist_code,
                    $rows->fk_anc_third_visit_code,
                    $rows->fk_anc_fourth_assist_code,
                    $rows->fk_anc_fourth_visit_code,
                    $rows->fk_anc_fifth_assist_code,
                    $rows->fk_anc_fifth_visit_code,
                    $rows->fk_anc_supliment_code,
                    $rows->fk_supliment_received_way_code,
                    $rows->fk_how_many_tab_code,
                    $rows->fk_anc_weight_taken_code,
                    $rows->fk_anc_blood_pressure_code,
                    $rows->fk_anc_urine_code,
                    $rows->fk_anc_blood_code,
                    $rows->fk_anc_denger_sign_code,
                    $rows->fk_anc_nutrition_code,
                    $rows->fk_anc_birth_prepare_code,
                    $rows->fk_anc_delivery_kit_code,
                    $rows->fk_anc_soap_code,
                    $rows->fk_anc_care_chix_code,
                    $rows->fk_anc_dried_code,
                    $rows->fk_anc_bathing_code,
                    $rows->fk_anc_breast_feed_code,
                    $rows->fk_anc_skin_contact_code,
                    $rows->fk_anc_enc_code,
                    $rows->fk_suspecred_infection_code,
                    $rows->fk_baby_antibiotics_code,
                    $rows->fk_prescribe_antibiotics_code,
                    $rows->fk_seek_treatment_code,
                    $rows->fk_anc_vaginal_bleeding_code,
                    $rows->fk_anc_convulsions_code,
                    $rows->fk_anc_severe_headache_code,
                    $rows->fk_anc_fever_code,
                    $rows->fk_anc_abdominal_pain_code,
                    $rows->fk_anc_diff_breath_code,
                    $rows->fk_anc_water_break_code,
                    $rows->fk_anc_vaginal_bleed_aph_code,
                    $rows->fk_anc_obstructed_labour_code,
                    $rows->fk_anc_convulsion_code,
                    $rows->fk_anc_sepsis_code,
                    $rows->fk_anc_severe_headache_delivery_code,
                    $rows->fk_anc_consciousness_code,
                    $rows->fk_anc_vaginal_bleeding_post_code,
                    $rows->fk_anc_convulsion_eclampsia_post_code,
                    $rows->fk_anc_high_feaver_post_code,
                    $rows->fk_anc_smelling_discharge_post_code,
                    $rows->fk_anc_severe_headache_post_code,
                    $rows->fk_anc_consciousness_post_code,
                    $rows->fk_anc_inability_baby_code,
                    $rows->fk_anc_baby_small_baby_code,
                    $rows->fk_anc_fast_breathing_baby_code,
                    $rows->fk_anc_convulsions_baby_code,
                    $rows->fk_anc_drowsy_baby_code,
                    $rows->fk_anc_movement_baby_code,
                    $rows->fk_anc_grunting_baby_code,
                    $rows->fk_anc_indrawing_baby_code,
                    $rows->fk_anc_temperature_baby_code,
                    $rows->fk_anc_hypothermia_baby_code,
                    $rows->fk_anc_central_cyanosis_baby_code,
                    $rows->fk_anc_umbilicus_baby_code,
                    $rows->fk_anc_labour_preg_code,
                    $rows->fk_anc_excessive_bld_pre_code,
                    $rows->fk_anc_severe_headache_preg_code,
                    $rows->fk_anc_obstructed_preg_code,
                    $rows->fk_anc_convulsion_preg_code,
                    $rows->fk_anc_placenta_preg_code,
                    $rows->fk_anc_breath_child_code,
                    $rows->fk_anc_suck_baby_code,
                    $rows->fk_anc_hot_cold_child_code,
                    $rows->fk_anc_blue_child_code,
                    $rows->fk_anc_convulsion_child_code,
                    $rows->fk_anc_indrawing_child_code,
                    $rows->fk_supliment_post_code,
                    $rows->fk_post_natal_visit_code,
                    $rows->fk_pnc_chkup_mother_code,
                    $rows->fk_pnc_first_visit_assist_code,
                    $rows->fk_pnc_first_visit_code,
                    $rows->fk_pnc_second_visit_assist_code,
                    $rows->fk_pnc_second_visit_code,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_pregnancy();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_pregnancy() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("pregnancy_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("pregnancy_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_pregnancy($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Pregnancy";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_pregnancy';
        $data['shortName'] = "Pregnancy";
        $data['boxTitle'] = 'List';

        $data['pregnancy_info'] = $this->modelName->pregnancy_info($id, $this->config->item('pregnancyTable'));

//        echo "<pre/>";
//        print_r($data['conception_info']); exit();
        $data['pregnancy_result'] = $this->modelName->getLookUpList($this->config->item('pregnancy_result'));
        $data['delivery_methodology'] = $this->modelName->getLookUpList($this->config->item('delivery_methodology'));
        $data['preg_term_assist'] = $this->modelName->getLookUpList($this->config->item('preg_term_assist'));
        $data['preg_term_place'] = $this->modelName->getLookUpList($this->config->item('preg_term_place'));
        $data['yes_no_miss_not_app'] = $this->modelName->getLookUpList($this->config->item('yes_no_miss_not_app'));


        $data['onlyYesNo'] = $this->modelName->getLookUpList($this->config->item('yes_no'));
        $data['ancPncVisit'] = $this->modelName->getLookUpList($this->config->item('ancPncVisit'));
        $data['litter_size'] = $this->modelName->getLookUpList($this->config->item('litter_size'));

        $data['yes_no'] = $this->modelName->getLookUpList($this->config->item('yes_no'));
        $data['yes_no_com'] = $this->modelName->getLookUpList($this->config->item('yes_no'));
        $data['facility_delivery'] = $this->modelName->getLookUpList($this->config->item('facility_delivery'));
        $data['fast_milk_birth'] = $this->modelName->getLookUpList($this->config->item('fast_milk_birth'));
        $data['anc_assist_typ'] = $this->modelName->getLookUpList($this->config->item('anc_assist_typ'));
        $data['anc_assist_typ1'] = $this->modelName->getLookUpList($this->config->item('anc_assist_typ'));
        $data['anc_assist_typ2'] = $this->modelName->getLookUpList($this->config->item('anc_assist_typ'));
        $data['anc_assist_typ3'] = $this->modelName->getLookUpList($this->config->item('anc_assist_typ'));
        $data['anc_assist_typ4'] = $this->modelName->getLookUpList($this->config->item('anc_assist_typ'));
        $data['pnc_assist_typ'] = $this->modelName->getLookUpList($this->config->item('anc_assist_typ'));
        $data['pnc_assist_typ1'] = $this->modelName->getLookUpList($this->config->item('anc_assist_typ'));

        $data['prescribe_antibiotics'] = $this->modelName->getLookUpList($this->config->item('anc_assist_typ'));

        $data['go_for_treatment'] = $this->modelName->getLookUpList($this->config->item('ancPncVisit'));

        $data['ifa_supliment_source'] = $this->modelName->getLookUpList($this->config->item('ifa_supliment_source'));
        $data['how_many_tablet'] = $this->modelName->getLookUpList($this->config->item('how_many_tablet'));
        $data['yes_no_not_applicable'] = $this->modelName->getLookUpList($this->config->item('yes_no_not_applicable'));
        $data['knowledgebehavior'] = $this->modelName->getLookUpList($this->config->item('knowledge_behavior'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_pregnancy', $data);
        $this->load->view('includes/footer');
    }

    function update_pregnancy() {

        $member_master_id = $this->input->post('member_master_id', true);
        $conceptionID = $this->input->post('conceptionID', true);
        $pregnancyID = $this->input->post('pregnancyID', true);

        $this->load->library('form_validation');



        $this->form_validation->set_rules('pregnancy_outcome_date', 'pregnancy outcome Date', 'trim|required');
        $this->form_validation->set_rules('spontaneous_abortion', 'spontaneous abortion', 'trim|required|numeric');
        $this->form_validation->set_rules('induced_abortion', 'induced abortion', 'trim|required|numeric');
        $this->form_validation->set_rules('still_birth', 'still birth', 'trim|required|numeric');

        $this->form_validation->set_rules('live_birth', 'live birth', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_conception_result', 'conception result', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_delivery_methodology', 'Delivery methodology', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_delivery_assist_type', 'Delivery assist type', 'trim|required|numeric');

        $this->form_validation->set_rules('fk_delivery_term_place', 'Delivery Termination Place', 'trim|required|numeric');
        // $this->form_validation->set_rules('given_six_hour_birth','given_six_hour_birth','trim');
        // $this->form_validation->set_rules('breast_milk_day','breast milk day','trim|required|numeric');
        // $this->form_validation->set_rules('breast_milk_hour','breast milk hour','trim|required|numeric');
        // $this->form_validation->set_rules('fk_health_problem_id','Health Problem','trim|required|numeric');
        // $this->form_validation->set_rules('fk_high_pressure_id','High Pressure','trim|required|numeric');
        // $this->form_validation->set_rules('fk_diabetis_id','Diabetis','trim|required|numeric');
        // $this->form_validation->set_rules('fk_preaklampshia_id','Pre aklampshia','trim|required|numeric');
        // $this->form_validation->set_rules('fk_lebar_birth_id','Pre term Laber','trim|required|numeric');
        // $this->form_validation->set_rules('fk_vomiting_id','Vomiting','trim|required|numeric');
        // $this->form_validation->set_rules('fk_amliotic_id','Amliotic','trim|required|numeric');
        // $this->form_validation->set_rules('fk_membrane_id','Membrane','trim|required|numeric');
        // $this->form_validation->set_rules('fk_malposition_id','Malposition','trim|required|numeric');
        // $this->form_validation->set_rules('fk_headache_id','Headache','trim|required|numeric');
        // $this->form_validation->set_rules('keep_follow_up','Follow up','trim|required|numeric');
        $this->form_validation->set_rules('conceptionID', 'Conception ID', 'trim|required|numeric');
        $this->form_validation->set_rules('checkupTypeRoutine', 'Routine check-up in pregnancy  for mother', 'trim|required|numeric');
        $this->form_validation->set_rules('checkupType', 'Within 42 days of the birth of the baby you ever had a check-up', 'trim|required|numeric');

        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_pregnancy/' . $pregnancyID . '?baseID=' . $baseID);
        } else {

            if ($this->getCurrentRound()[0]->active == 0) {

                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_pregnancy/' . $pregnancyID . '?baseID=' . $baseID);
            }


            $spontaneous_abortion = $this->input->post('spontaneous_abortion', true);
            $induced_abortion = $this->input->post('induced_abortion', true);
            $still_birth = $this->input->post('still_birth', true);
            $live_birth = $this->input->post('live_birth', true);
            $fk_conception_result = $this->input->post('fk_conception_result', true);
            $fk_delivery_methodology = $this->input->post('fk_delivery_methodology', true);
            $fk_delivery_assist_type = $this->input->post('fk_delivery_assist_type', true);
            $fk_delivery_term_place = $this->input->post('fk_delivery_term_place', true);
            // $given_six_hour_birth = $this->input->post('given_six_hour_birth',true);
            // $breast_milk_day = $this->input->post('breast_milk_day',true);
            // $breast_milk_hour = $this->input->post('breast_milk_hour',true);
            // $fk_health_problem_id = $this->input->post('fk_health_problem_id',true);
            // $fk_high_pressure_id = $this->input->post('fk_high_pressure_id',true);
            // $fk_diabetis_id = $this->input->post('fk_diabetis_id',true);
            // $fk_preaklampshia_id = $this->input->post('fk_preaklampshia_id',true);
            // $fk_lebar_birth_id = $this->input->post('fk_lebar_birth_id',true);
            // $fk_vomiting_id = $this->input->post('fk_vomiting_id',true);
            // $fk_amliotic_id = $this->input->post('fk_amliotic_id',true);
            // $fk_membrane_id = $this->input->post('fk_membrane_id',true);
            // $fk_malposition_id = $this->input->post('fk_malposition_id',true);
            // $fk_headache_id = $this->input->post('fk_headache_id',true);
            // $keep_follow_up = $this->input->post('keep_follow_up',true);
            $conceptionDate = $this->input->post('conceptionDate', true);

            $fk_facility_delivery = $this->input->post('fk_facility_delivery', true);

            if (($fk_conception_result == 95) || ($fk_conception_result == 199)) {
                $fk_colostrum = 0;
                $fk_first_milk = 0;
                $milk_day = 0;
            } else {

                $fk_colostrum = $this->input->post('fk_colostrum', true);
                $fk_first_milk = $this->input->post('fk_first_milk', true);
                $milk_hours = $this->input->post('milk_hours', true);
                $milk_day = $this->input->post('milk_day', true);
            }

            if (($fk_delivery_term_place == 103) || ($fk_delivery_term_place == 104) || ($fk_delivery_term_place == 205)) {
                $fk_facility_delivery = 0;
            } else {
                $fk_facility_delivery = $this->input->post('fk_facility_delivery', true) ? $this->input->post('fk_facility_delivery', true) : 0;
            }


            $fk_preg_complication = $this->input->post('fk_preg_complication', true) ? $this->input->post('fk_preg_complication', true) : 0;
            $fk_delivery_complication = $this->input->post('fk_delivery_complication', true) ? $this->input->post('fk_delivery_complication', true) : 0;
            $fk_preg_violence = $this->input->post('fk_preg_violence', true) ? $this->input->post('fk_preg_violence', true) : 0;
            $fk_litter_size = $this->input->post('fk_litter_size', true) ? $this->input->post('fk_litter_size', true) : 0;




            $pregnancy_outcome_date = $this->input->post('pregnancy_outcome_date', true);

            $new_pregnancy_outcome_date = null;

            if (!empty($pregnancy_outcome_date)) {
                $parts3 = explode('/', $pregnancy_outcome_date);
                $new_pregnancy_outcome_date = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            // anc
            $checkupTypeRoutine = $this->input->post('checkupTypeRoutine', true);

            if ($checkupTypeRoutine == 1) {

                $afterTotalTimesRoutine = $this->input->post('afterTotalTimesRoutine', true) ? $this->input->post('afterTotalTimesRoutine', true) : 0;

                $routineFirstVisitAsist = $this->input->post('routineFirstVisitAsist', true);
                $routineFirstVisit = $this->input->post('routineFirstVisit', true);
                $routineFirstVisitMonthss = $this->input->post('routineFirstVisitMonthss', true) ? $this->input->post('routineFirstVisitMonthss', true) : 0;

                $routineSecondVisitAsist = $this->input->post('routineSecondVisitAsist', true);
                $routineSecondVisit = $this->input->post('routineSecondVisit', true);
                $routineSecondVisitMonths = $this->input->post('routineSecondVisitMonths', true) ? $this->input->post('routineSecondVisitMonths', true) : 0;

                $routineThirdVisitAsist = $this->input->post('routineThirdVisitAsist', true);
                $routineThirdVisit = $this->input->post('routineThirdVisit', true);
                $routineThirdVisitMonths = $this->input->post('routineThirdVisitMonths', true) ? $this->input->post('routineThirdVisitMonths', true) : 0;

                $routineFourthVisitAsist = $this->input->post('routineFourthVisitAsist', true);
                $routineFourthVisit = $this->input->post('routineFourthVisit', true);
                $routineFourthVisitMonths = $this->input->post('routineFourthVisitMonths', true) ? $this->input->post('routineFourthVisitMonths', true) : 0;

                $routineFifthVisitAsist = $this->input->post('routineFifthVisitAsist', true);
                $routineFifthVisit = $this->input->post('routineFifthVisit', true);
                $routineFifthVisitMonths = $this->input->post('routineFifthVisitMonths', true) ? $this->input->post('routineFifthVisitMonths', true) : 0;
            } else {
                $routineFirstVisitAsist = 0;
                $routineSecondVisitAsist = 0;
                $routineThirdVisitAsist = 0;
                $routineFourthVisitAsist = 0;
                $routineFifthVisitAsist = 0;

                $afterTotalTimesRoutine = 0;
                $routineFirstVisit = 0;
                $routineFirstVisitMonthss = 0;
                $routineSecondVisit = 0;
                $routineSecondVisitMonths = 0;
                $routineThirdVisit = 0;
                $routineThirdVisitMonths = 0;
                $routineFourthVisit = 0;
                $routineFourthVisitMonths = 0;
                $routineFifthVisit = 0;
                $routineFifthVisitMonths = 0;
            }

            $fk_supliment = $this->input->post('fk_supliment', true);
            if ($fk_supliment == 1) {
                $fk_supliment_received_way = $this->input->post('fk_supliment_received_way', true) ? $this->input->post('fk_supliment_received_way', true) : 0;
                $fk_how_many_tab = $this->input->post('fk_how_many_tab', true) ? $this->input->post('fk_how_many_tab', true) : 0;

                if ($fk_how_many_tab == 226) {
                    $totalNumberTablet = $this->input->post('totalNumberTablet', true) ? $this->input->post('totalNumberTablet', true) : 0;
                } else {
                    $totalNumberTablet = 0;
                }
            } else {
                $fk_supliment_received_way = 0;
                $fk_how_many_tab = 0;
                $totalNumberTablet = 0;
            }



            // Basic components of ANC
            $fk_anc_weight_taken = $this->input->post('fk_anc_weight_taken', true);
            $fk_anc_blood_pressure = $this->input->post('fk_anc_blood_pressure', true);
            $fk_anc_urine = $this->input->post('fk_anc_urine', true);
            $fk_anc_blood = $this->input->post('fk_anc_blood', true);
            $fk_anc_denger_sign = $this->input->post('fk_anc_denger_sign', true);
            $fk_anc_nutrition = $this->input->post('fk_anc_nutrition', true);
            $fk_anc_birth_prepare = $this->input->post('fk_anc_birth_prepare', true);

            // Newborn care practices

            $fk_anc_delivery_kit = $this->input->post('fk_anc_delivery_kit', true);
            $fk_anc_soap = $this->input->post('fk_anc_soap', true);
            $fk_anc_care_chix = $this->input->post('fk_anc_care_chix', true);

            $fk_anc_dried = $this->input->post('fk_anc_dried', true);
            $fk_anc_bathing = $this->input->post('fk_anc_bathing', true);
            $fk_anc_breast_feed = $this->input->post('fk_anc_breast_feed', true);
            $fk_anc_skin_contact = $this->input->post('fk_anc_skin_contact', true);
            $fk_anc_enc = $this->input->post('fk_anc_enc', true);


            //  sepsis

            $fk_suspecred_infection = $this->input->post('fk_suspecred_infection', true);

            if ($fk_suspecred_infection == 228) {

                $fk_baby_antibiotics = $this->input->post('fk_baby_antibiotics', true) ? $this->input->post('fk_baby_antibiotics', true) : 0;
                if ($fk_baby_antibiotics == 1) {
                    $fk_prescribe_antibiotics = $this->input->post('fk_prescribe_antibiotics', true) ? $this->input->post('fk_prescribe_antibiotics', true) : 0;
                    $fk_seek_treatment = $this->input->post('fk_seek_treatment', true) ? $this->input->post('fk_seek_treatment', true) : 0;
                } else {
                    $fk_prescribe_antibiotics = 0;
                    $fk_seek_treatment = 0;
                }
            } else {
                $fk_baby_antibiotics = 0;
                $fk_prescribe_antibiotics = 0;
                $fk_seek_treatment = 0;
            }



            //  Knowledge and Behavior 

            $fk_anc_vaginal_bleeding = $this->input->post('fk_anc_vaginal_bleeding', true);
            $fk_anc_convulsions = $this->input->post('fk_anc_convulsions', true);
            $fk_anc_severe_headache = $this->input->post('fk_anc_severe_headache', true);
            $fk_anc_fever = $this->input->post('fk_anc_fever', true);
            $fk_anc_abdominal_pain = $this->input->post('fk_anc_abdominal_pain', true);
            $fk_anc_diff_breath = $this->input->post('fk_anc_diff_breath', true);

            // danger signs of delivery
            $fk_anc_water_break = $this->input->post('fk_anc_water_break', true);
            $fk_anc_vaginal_bleed_aph = $this->input->post('fk_anc_vaginal_bleed_aph', true);
            $fk_anc_obstructed_labour = $this->input->post('fk_anc_obstructed_labour', true);
            $fk_anc_convulsion = $this->input->post('fk_anc_convulsion', true);
            $fk_anc_sepsis = $this->input->post('fk_anc_sepsis', true);
            $fk_anc_severe_headache_delivery = $this->input->post('fk_anc_severe_headache_delivery', true);
            $fk_anc_consciousness = $this->input->post('fk_anc_consciousness', true);

            // signs of postnatal period

            $fk_anc_vaginal_bleeding_post = $this->input->post('fk_anc_vaginal_bleeding_post', true);
            $fk_anc_convulsion_eclampsia_post = $this->input->post('fk_anc_convulsion_eclampsia_post', true);
            $fk_anc_high_feaver_post = $this->input->post('fk_anc_high_feaver_post', true);
            $fk_anc_smelling_discharge_post = $this->input->post('fk_anc_smelling_discharge_post', true);
            $fk_anc_severe_headache_post = $this->input->post('fk_anc_severe_headache_post', true);
            $fk_anc_consciousness_post = $this->input->post('fk_anc_consciousness_post', true);

            // signs of newborn baby
            $fk_anc_inability_baby = $this->input->post('fk_anc_inability_baby', true);
            $fk_anc_baby_small_baby = $this->input->post('fk_anc_baby_small_baby', true);
            $fk_anc_fast_breathing_baby = $this->input->post('fk_anc_fast_breathing_baby', true);
            $fk_anc_convulsions_baby = $this->input->post('fk_anc_convulsions_baby', true);
            $fk_anc_drowsy_baby = $this->input->post('fk_anc_drowsy_baby', true);
            $fk_anc_movement_baby = $this->input->post('fk_anc_movement_baby', true);
            $fk_anc_grunting_baby = $this->input->post('fk_anc_grunting_baby', true);
            $fk_anc_indrawing_baby = $this->input->post('fk_anc_indrawing_baby', true);
            $fk_anc_temperature_baby = $this->input->post('fk_anc_temperature_baby', true);
            $fk_anc_hypothermia_baby = $this->input->post('fk_anc_hypothermia_baby', true);
            $fk_anc_central_cyanosis_baby = $this->input->post('fk_anc_central_cyanosis_baby', true);
            $fk_anc_umbilicus_baby = $this->input->post('fk_anc_umbilicus_baby', true);

            //complicated pregnancy

            $fk_anc_labour_preg = $this->input->post('fk_anc_labour_preg', true);
            $fk_anc_excessive_bld_pre = $this->input->post('fk_anc_excessive_bld_pre', true);
            $fk_anc_severe_headache_preg = $this->input->post('fk_anc_severe_headache_preg', true);
            $fk_anc_obstructed_preg = $this->input->post('fk_anc_obstructed_preg', true);
            $fk_anc_convulsion_preg = $this->input->post('fk_anc_convulsion_preg', true);
            $fk_anc_placenta_preg = $this->input->post('fk_anc_placenta_preg', true);


            //newborn and child
            $fk_anc_breath_child = $this->input->post('fk_anc_breath_child', true);
            $fk_anc_suck_baby = $this->input->post('fk_anc_suck_baby', true);
            $fk_anc_hot_cold_child = $this->input->post('fk_anc_hot_cold_child', true);
            $fk_anc_blue_child = $this->input->post('fk_anc_blue_child', true);
            $fk_anc_convulsion_child = $this->input->post('fk_anc_convulsion_child', true);
            $fk_anc_indrawing_child = $this->input->post('fk_anc_indrawing_child', true);


            $remarks = $this->input->post('remarks', true);

            // pnc
            $fk_supliment_post = $this->input->post('fk_supliment_post', true);


            // pnc
            $checkupType = $this->input->post('checkupType', true);


            if ($checkupType == 1) {

                $fk_post_natal_visit = $this->input->post('fk_post_natal_visit', true) ? $this->input->post('fk_post_natal_visit', true) : 0;
                $afterTotalTimes = $this->input->post('afterTotalTimes', true) ? $this->input->post('afterTotalTimes', true) : 0;

                $pncFirstVisitAsist = $this->input->post('pncFirstVisitAsist', true);
                $firstVisit = $this->input->post('firstVisit', true);
                $firstVisitDays = $this->input->post('firstVisitDays', true) ? $this->input->post('firstVisitDays', true) : 0;

                $pncSecondVisitAsist = $this->input->post('pncSecondVisitAsist', true);
                $secondVisit = $this->input->post('secondVisit', true);
                $secondVisitDays = $this->input->post('secondVisitDays', true) ? $this->input->post('secondVisitDays', true) : 0;
            } else {
                $afterTotalTimes = 0;
                $fk_post_natal_visit = 0;
                $pncFirstVisitAsist = 0;
                $pncSecondVisitAsist = 0;
                $firstVisit = 0;
                $firstVisitDays = 0;
                $secondVisit = 0;
                $secondVisitDays = 0;
            }

            //live_birth

            if ($live_birth > 0) {
                if (!empty($conceptionDate)) {
                    $concept = date_create($conceptionDate);
                    $preg = date_create($new_pregnancy_outcome_date);
                    $diff = date_diff($concept, $preg);
                    $days = $diff->format("%a");


                    if ($days <= 160) {
                        $this->session->set_flashdata('error', 'Pregnancy out come date is less than 160 days.');
                        //redirect('memberPregnancy/addEditPregnancy/'. $pregnancyID.'?household_master_id='.$household_master_id.'&&member_master_id='.$member_master_id.'&&baseID='.$baseID.'#pregnancy');
                    }

                    if ($days >= 320) {
                        $this->session->set_flashdata('error', 'Pregnancy out come date is greater than 320 days.');
                        //redirect('memberPregnancy/addEditPregnancy/'. $pregnancyID.'?household_master_id='.$household_master_id.'&&member_master_id='.$member_master_id.'&&baseID='.$baseID.'#pregnancy');
                    }
                }
            }


            $this->db->trans_start();

            try {


                // check same pregnancy date
                $whereHouseholdPregDate = array('pregnancy_outcome_date' => $new_pregnancy_outcome_date, 'member_master_id' => $member_master_id);

                $countRowDate = $this->db->select('count(id) as countRowDate')->from($this->config->item('pregnancyTable'))->where($whereHouseholdPregDate)->get()->row()->countRowDate;

                if ($countRowDate > 1) {
                    $this->session->set_flashdata('error', 'Same pregnancy outcome date already exists. Please select another date.');
                    redirect($this->controller . '/edit_pregnancy/' . $pregnancyID . '?baseID=' . $baseID);
                }


                $IdInfo = array(
                    'pregnancy_outcome_date' => $new_pregnancy_outcome_date,
                    //'breast_milk_day'=>$breast_milk_day, 
                    //'breast_milk_hour'=>$breast_milk_hour,
                    'induced_abortion' => $induced_abortion,
                    'spontaneous_abortion' => $spontaneous_abortion,
                    'live_birth' => $live_birth,
                    'still_birth' => $still_birth,
                    'fk_delivery_methodology' => $fk_delivery_methodology,
                    'fk_delivery_assist_type' => $fk_delivery_assist_type,
                    'fk_delivery_term_place' => $fk_delivery_term_place,
                    'fk_litter_size' => $fk_litter_size,
                    'fk_colostrum' => $fk_colostrum,
                    'fk_first_milk' => $fk_first_milk,
                    'milk_hours' => $milk_hours,
                    'milk_day' => $milk_day,
                    'fk_facility_delivery' => $fk_facility_delivery,
                    'fk_preg_complication' => $fk_preg_complication,
                    'fk_delivery_complication' => $fk_delivery_complication,
                    'fk_preg_violence' => $fk_preg_violence,
                    'fk_anc_first_assist_id' => $routineFirstVisitAsist,
                    'fk_anc_second_assist_id' => $routineSecondVisitAsist,
                    'fk_anc_third_assist_id' => $routineThirdVisitAsist,
                    'fk_anc_fourth_assist_id' => $routineFourthVisitAsist,
                    'fk_anc_fifth_assist_id' => $routineFifthVisitAsist,
                    'fk_anc_supliment' => $fk_supliment,
                    'fk_supliment_received_way' => $fk_supliment_received_way,
                    'fk_how_many_tab' => $fk_how_many_tab,
                    'totalnumbertab' => $totalNumberTablet,
                    'fk_anc_weight_taken' => $fk_anc_weight_taken,
                    'fk_anc_blood_pressure' => $fk_anc_blood_pressure,
                    'fk_anc_urine' => $fk_anc_urine,
                    'fk_anc_blood' => $fk_anc_blood,
                    'fk_anc_denger_sign' => $fk_anc_denger_sign,
                    'fk_anc_nutrition' => $fk_anc_nutrition,
                    'fk_anc_birth_prepare' => $fk_anc_birth_prepare,
                    'fk_anc_delivery_kit' => $fk_anc_delivery_kit,
                    'fk_anc_soap' => $fk_anc_soap,
                    'fk_anc_care_chix' => $fk_anc_care_chix,
                    'fk_anc_dried' => $fk_anc_dried,
                    'fk_anc_bathing' => $fk_anc_bathing,
                    'fk_anc_breast_feed' => $fk_anc_breast_feed,
                    'fk_anc_skin_contact' => $fk_anc_skin_contact,
                    'fk_anc_enc' => $fk_anc_enc,
                    'fk_suspecred_infection' => $fk_suspecred_infection,
                    'fk_baby_antibiotics' => $fk_baby_antibiotics,
                    'fk_prescribe_antibiotics' => $fk_prescribe_antibiotics,
                    'fk_seek_treatment' => $fk_seek_treatment,
                    'fk_anc_vaginal_bleeding' => $fk_anc_vaginal_bleeding,
                    'fk_anc_convulsions' => $fk_anc_convulsions,
                    'fk_anc_severe_headache' => $fk_anc_severe_headache,
                    'fk_anc_fever' => $fk_anc_fever,
                    'fk_anc_abdominal_pain' => $fk_anc_abdominal_pain,
                    'fk_anc_diff_breath' => $fk_anc_diff_breath,
                    'fk_anc_water_break' => $fk_anc_water_break,
                    'fk_anc_vaginal_bleed_aph' => $fk_anc_vaginal_bleed_aph,
                    'fk_anc_obstructed_labour' => $fk_anc_obstructed_labour,
                    'fk_anc_convulsion' => $fk_anc_convulsion,
                    'fk_anc_sepsis' => $fk_anc_sepsis,
                    'fk_anc_severe_headache_delivery' => $fk_anc_severe_headache_delivery,
                    'fk_anc_consciousness' => $fk_anc_consciousness,
                    'fk_anc_vaginal_bleeding_post' => $fk_anc_vaginal_bleeding_post,
                    'fk_anc_convulsion_eclampsia_post' => $fk_anc_convulsion_eclampsia_post,
                    'fk_anc_high_feaver_post' => $fk_anc_high_feaver_post,
                    'fk_anc_smelling_discharge_post' => $fk_anc_smelling_discharge_post,
                    'fk_anc_severe_headache_post' => $fk_anc_severe_headache_post,
                    'fk_anc_consciousness_post' => $fk_anc_consciousness_post,
                    'fk_anc_inability_baby' => $fk_anc_inability_baby,
                    'fk_anc_baby_small_baby' => $fk_anc_baby_small_baby,
                    'fk_anc_fast_breathing_baby' => $fk_anc_fast_breathing_baby,
                    'fk_anc_convulsions_baby' => $fk_anc_convulsions_baby,
                    'fk_anc_drowsy_baby' => $fk_anc_drowsy_baby,
                    'fk_anc_movement_baby' => $fk_anc_movement_baby,
                    'fk_anc_grunting_baby' => $fk_anc_grunting_baby,
                    'fk_anc_indrawing_baby' => $fk_anc_indrawing_baby,
                    'fk_anc_temperature_baby' => $fk_anc_temperature_baby,
                    'fk_anc_hypothermia_baby' => $fk_anc_hypothermia_baby,
                    'fk_anc_central_cyanosis_baby' => $fk_anc_central_cyanosis_baby,
                    'fk_anc_umbilicus_baby' => $fk_anc_umbilicus_baby,
                    'fk_anc_labour_preg' => $fk_anc_labour_preg,
                    'fk_anc_severe_headache_preg' => $fk_anc_severe_headache_preg,
                    'fk_anc_excessive_bld_pre' => $fk_anc_excessive_bld_pre,
                    'fk_anc_convulsion_preg' => $fk_anc_convulsion_preg,
                    'fk_anc_obstructed_preg' => $fk_anc_obstructed_preg,
                    'fk_anc_placenta_preg' => $fk_anc_placenta_preg,
                    'fk_anc_breath_child' => $fk_anc_breath_child,
                    'fk_anc_suck_baby' => $fk_anc_suck_baby,
                    'fk_anc_hot_cold_child' => $fk_anc_hot_cold_child,
                    'fk_anc_blue_child' => $fk_anc_blue_child,
                    'fk_anc_convulsion_child' => $fk_anc_convulsion_child,
                    'fk_anc_indrawing_child' => $fk_anc_indrawing_child,
                    'fk_supliment_post' => $fk_supliment_post,
                    'fk_post_natal_visit' => $fk_post_natal_visit,
                    'fk_pnc_first_visit_assist' => $pncFirstVisitAsist,
                    'fk_pnc_second_visit_assist' => $pncSecondVisitAsist,
                    'remarks' => $remarks,
                    // 'given_six_hour_birth'=>$given_six_hour_birth,
                    // 'fk_health_problem_id'=>$fk_health_problem_id,
                    // 'fk_high_pressure_id'=>$fk_high_pressure_id,
                    // 'fk_diabetis_id'=>$fk_diabetis_id,
                    // 'fk_preaklampshia_id'=>$fk_preaklampshia_id,
                    // 'fk_lebar_birth_id'=>$fk_lebar_birth_id,
                    // 'fk_vomiting_id'=>$fk_vomiting_id,
                    // 'fk_amliotic_id'=>$fk_amliotic_id,
                    // 'fk_membrane_id'=>$fk_membrane_id,
                    // 'fk_malposition_id'=>$fk_malposition_id,
                    // 'fk_headache_id'=>$fk_headache_id,
                    'fk_routine_anc_chkup_mother_id' => $checkupTypeRoutine,
                    'routine_anc_chkup_mother_times' => $afterTotalTimesRoutine,
                    'fk_anc_first_visit_id' => $routineFirstVisit,
                    'anc_first_visit_months' => $routineFirstVisitMonthss,
                    'fk_anc_second_visit_id' => $routineSecondVisit,
                    'anc_second_visit_months' => $routineSecondVisitMonths,
                    'fk_anc_third_visit_id' => $routineThirdVisit,
                    'anc_third_visit_months' => $routineThirdVisitMonths,
                    'fk_anc_fourth_visit_id' => $routineFourthVisit,
                    'anc_fourth_visit_months' => $routineFourthVisitMonths,
                    'fk_anc_fifth_visit_id' => $routineFifthVisit,
                    'anc_fifth_visit_months' => $routineFifthVisitMonths,
                    'fk_pnc_chkup_mother_id' => $checkupType,
                    'pnc_chkup_mother_times' => $afterTotalTimes,
                    'fk_pnc_first_visit_id' => $firstVisit,
                    'pnc_first_visit_days' => $firstVisitDays,
                    'fk_pnc_second_visit_id' => $secondVisit,
                    'pnc_second_visit_days' => $secondVisitDays,
                    //'keep_follow_up'=>$keep_follow_up,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($IdInfo, $pregnancyID, $this->config->item('pregnancyTable'));


                $conceptfo = array(
                    'fk_conception_result' => $fk_conception_result,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );


                $this->modelName->UpdateInfo($conceptfo, $conceptionID, $this->config->item('conceptionTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating pregnancy.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Member pregnancy info updated successfully.');

            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/pregnancy' . '?baseID=' . $baseID);
            }

            redirect($this->controller . '/edit_pregnancy/' . $pregnancyID . '?baseID=' . $baseID);
        }
    }

    public function birth() {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = $this->pageTitle;
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'birth';
        $data['editMethod'] = 'edit_birth';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';
        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';
        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/birth', $data);
        $this->load->view('includes/footer');
    }

    public function show_birth() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'household_master_id_hh',
            2 => 'member_code',
            3 => 'birth_date',
            4 => 'father_code',
            5 => 'mother_code',
            6 => 'spouse_code',
            7 => 'national_id',
            8 => 'birth_registration_date',
            9 => 'afterYear',
            10 => 'contactNoOne',
            11 => 'contactNoTwo',
            12 => 'household_code',
            13 => 'marital_status_code',
            14 => 'fk_sex_code',
            15 => 'fk_religion_code',
            16 => 'fk_relation_with_hhh_code',
            17 => 'fk_mother_live_birth_order_code',
            18 => 'fk_birth_registration_code',
            19 => 'fk_why_not_birth_registration_code',
            20 => 'fk_additionalChild_code',
            21 => 'insertedDate',
            22 => 'insertedTime',
            23 => 'insertedBy_name',
            24 => 'updatedDate',
            25 => 'updatedTime',
            26 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("birth_view", array('round_master_id_entry_round' => $round_no));
        } else {
            $all_data_list = $this->db->get("birth_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_birth/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->household_master_id_hh,
                    $rows->member_code,
                    $rows->birth_date,
                    $rows->father_code,
                    $rows->mother_code,
                    $rows->spouse_code,
                    $rows->national_id,
                    $rows->birth_registration_date,
                    $rows->afterYear,
                    $rows->contactNoOne,
                    $rows->contactNoTwo,
                    $rows->household_code,
                    $rows->marital_status_code,
                    $rows->fk_sex_code,
                    $rows->fk_religion_code,
                    $rows->fk_relation_with_hhh_code,
                    $rows->fk_mother_live_birth_order_code,
                    $rows->fk_birth_registration_code,
                    $rows->fk_why_not_birth_registration_code,
                    $rows->fk_additionalChild_code,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_birth();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_birth() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("birth_view", array('round_master_id_entry_round' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("birth_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_birth($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $household_master_id = $this->input->get('household_master_id', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Birth";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_birth';
        $data['shortName'] = "Birth";
        $data['boxTitle'] = 'List';

        $data['birth_info'] = $this->modelName->birth_info($id, $this->config->item('memberMasterTable'));


        $data['femaleList'] = $this->modelName->getMemberMasterPresentListByHouseholdIds($household_master_id, $this->config->item('femaleSexCode'));
        $data['maleList'] = $this->modelName->getMemberMasterPresentListByHouseholdIds($household_master_id, $this->config->item('femaleSexCodeMale'));

//        
//                echo "<pre/>";
//        print_r($data['femaleList']); exit();


        $data['entryType'] = $this->modelName->getLookUpListSpecific($this->config->item('mementrytyp'), array('bir'));
        $data['maritalstatustyp'] = $this->modelName->getLookUpListSpecific($this->config->item('maritalstatustyp'), array('5'));
        $data['membersextype'] = $this->modelName->getLookUpList($this->config->item('membersextype'));
        $data['relationhhh'] = $this->modelName->getLookUpListNotSpecific($this->config->item('relationhhh'), array('01', '02', '09'));



        $data['religion'] = $this->modelName->getLookUpList($this->config->item('religion'));

        $data['birth_weight_size'] = $this->modelName->getLookUpList($this->config->item('birth_weight_size'));
        $data['mother_live_birth_order'] = $this->modelName->getLookUpList($this->config->item('mother_live_birth_order'));

        $data['educationtyp'] = $this->modelName->getLookUpList($this->config->item('educationtyp'));
        $data['occupationtyp'] = $this->modelName->getLookUpList($this->config->item('occupationtyp'));
        $data['birthregistration'] = $this->modelName->getLookUpList($this->config->item('yes_no'));
        $data['additionChild'] = $this->modelName->getLookUpList($this->config->item('yes_no'));
        $data['whynotbirthreg'] = $this->modelName->getLookUpList($this->config->item('whynotbirthreg'));
        $data['pncassisttyp'] = $this->modelName->getLookUpList($this->config->item('anc_assist_typ'));

//        echo "<pre/>";
//        print_r($data['birth_info']); exit();

        $data['onlyYesNo'] = $this->modelName->getLookUpList($this->config->item('yes_no'));
        $data['ancPncVisit'] = $this->modelName->getLookUpList($this->config->item('ancPncVisit'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_birth', $data);
        $this->load->view('includes/footer');
    }

    function update_birth() {
        $household_master_id = $this->input->post('household_master_id', true);
        $member_master_id = $this->input->post('member_master_id', true);
        $member_household_id_last = $this->input->post('member_household_id_last', true);
        $fk_education_id_last = $this->input->post('fk_education_id_last', true);
        $fk_occupation_id_last = $this->input->post('fk_occupation_id_last', true);
        $fk_member_relation_id_last = $this->input->post('fk_member_relation_id_last', true);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('memberName', 'Child Name', 'trim|required');
        $this->form_validation->set_rules('sexType', 'Sex', 'trim|required|numeric');
        $this->form_validation->set_rules('birth_time', 'Birth Time', 'trim|required');
        $this->form_validation->set_rules('birth_weight', 'Birth weight', 'trim|required');

        $this->form_validation->set_rules('fk_birth_weight_size', 'Birth weight size', 'trim|required|numeric');
//        $this->form_validation->set_rules('fatherCode', 'Father', 'trim|required|numeric');
        $this->form_validation->set_rules('motherCode', 'Mother', 'trim|required|numeric');
        $this->form_validation->set_rules('pregnancy_outcome_id', 'Pregnancy Outcome', 'trim|required|numeric');

        $this->form_validation->set_rules('relationheadID', 'Relation with head', 'trim|required|numeric');
        $this->form_validation->set_rules('entryType', 'entry type', 'trim');
        $this->form_validation->set_rules('entryDate', 'entry date', 'trim|required');
        $this->form_validation->set_rules('maritalStatusType', 'marital status', 'trim|required|numeric');
        $this->form_validation->set_rules('religionType', 'Religion', 'trim|required|numeric');
        $this->form_validation->set_rules('educationType', 'Edication Type', 'trim|required|numeric');
        //$this->form_validation->set_rules('secularEduType','Secular Education','trim|required|numeric');
        // $this->form_validation->set_rules('religiousEduType','Religious Education','trim|required|numeric');
        $this->form_validation->set_rules('occupationType', 'Occupation Type', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_mother_live_birth_order', 'Live birth order', 'trim|required|numeric');
        $this->form_validation->set_rules('keep_follow_up', 'Follow up', 'trim|required|numeric');
        $this->form_validation->set_rules('checkupTypeChild', 'Birth of the child is made to check-up within 42 days', 'trim|required|numeric');


        $getCurrentRound = $this->modelName->getCurrentRound();

        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_birth/' . $member_master_id . '?baseID=' . $baseID . '&household_master_id=' . $household_master_id);
        } else {

            if ($getCurrentRound->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_birth/' . $member_master_id . '?baseID=' . $baseID . '&household_master_id=' . $household_master_id);
            }


            $memberName = $this->input->post('memberName', true);
            $sexType = $this->input->post('sexType', true);
            $birth_time = $this->input->post('birth_time', true);
            $birth_weight = $this->input->post('birth_weight', true);
            $fk_birth_weight_size = $this->input->post('fk_birth_weight_size', true);
            $relationheadID = $this->input->post('relationheadID', true);
            $fatherCode = (!empty($this->input->post('fatherCode', true))) ? $this->input->post('fatherCode', true) : 0;
            $motherCode = $this->input->post('motherCode', true);
            $maritalStatusType = $this->input->post('maritalStatusType', true);
            $religionType = $this->input->post('religionType', true);
            $fk_mother_live_birth_order = $this->input->post('fk_mother_live_birth_order', true);
            $keep_follow_up = $this->input->post('keep_follow_up', true);

            $birstRegiType = $this->input->post('birstRegiType', true);
            $birthRegidate = $this->input->post('birthRegidate', true);
            $whyNotRegi = $this->input->post('whyNotRegi', true);

            $pregnancy_outcome_id = $this->input->post('pregnancy_outcome_id', true);

            $entryDate = $this->input->post('entryDate', true);

            $educationType = $this->input->post('educationType', true);
            //$religiousEduType = $this->input->post('religiousEduType',true);
            // $secularEduType = $this->input->post('secularEduType',true);

            $occupationType = $this->input->post('occupationType', true);


            $new_birthRegidate = null;

            if (!empty($birthRegidate)) {
                $parts5 = explode('/', $birthRegidate);
                $new_birthRegidate = $parts5[2] . '-' . $parts5[1] . '-' . $parts5[0];
            }



            // father code 

            $father_member_code = '';

            if ($fatherCode) {
                $whereFather = array('id' => $fatherCode);
                $father_member_code = $this->db->select('member_code')->from($this->config->item('memberMasterTable'))->where($whereFather)->get()->row()->member_code;
            }



            // mother code 

            $whereMother = array('id' => $motherCode);
            $mother_member_code = $this->db->select('member_code')->from($this->config->item('memberMasterTable'))->where($whereMother)->get()->row()->member_code;

            // get birth date as pregnancy outcome date is equal to birth date

            $wherePregnancyDate = array('id' => $pregnancy_outcome_id);
            $pregnancy_outcome_date = $this->db->select('pregnancy_outcome_date')->from($this->config->item('pregnancyTable'))->where($wherePregnancyDate)->get()->row()->pregnancy_outcome_date;



            $new_entryDate = null;

            if (!empty($entryDate)) {
                $parts3 = explode('/', $entryDate);
                $new_entryDate = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }


            // pnc
            $checkupTypeChild = $this->input->post('checkupTypeChild', true);


            if ($checkupTypeChild == 1) {

                $fk_post_natal_visit = $this->input->post('fk_post_natal_visit', true) ? $this->input->post('fk_post_natal_visit', true) : 0;
                $afterTotalTimesChild = $this->input->post('afterTotalTimesChild', true) ? $this->input->post('afterTotalTimesChild', true) : 0;

                $childSecondVisitAsist = $this->input->post('childSecondVisitAsist', true);
                $childFirstVisit = $this->input->post('childFirstVisit', true);
                $childFirstVisitDays = $this->input->post('childFirstVisitDays', true) ? $this->input->post('childFirstVisitDays', true) : 0;

                $childFirstVisitAsist = $this->input->post('childFirstVisitAsist', true);
                $childSecondVisit = $this->input->post('childSecondVisit', true);
                $childSecondVisitDays = $this->input->post('childSecondVisitDays', true) ? $this->input->post('childSecondVisitDays', true) : 0;
            } else {
                $childSecondVisitAsist = 0;
                $childFirstVisitAsist = 0;
                $fk_post_natal_visit = 0;
                $afterTotalTimesChild = 0;
                $childFirstVisit = 0;
                $childFirstVisitDays = 0;
                $childSecondVisit = 0;
                $childSecondVisitDays = 0;
            }



            $this->db->trans_start();

            try {

                $memberMaster = array(
                    'birth_date' => $pregnancy_outcome_date,
                    'member_name' => $memberName,
                    'fk_marital_status' => $maritalStatusType,
                    'fk_sex' => $sexType,
                    'fk_religion' => $religionType,
                    'fk_relation_with_hhh' => $relationheadID,
                    'father_code' => $father_member_code,
                    'fk_father_id' => $fatherCode,
                    'mother_code' => $mother_member_code,
                    'fk_mother_id' => $motherCode,
                    'fk_mother_live_birth_order' => $fk_mother_live_birth_order,
                    'birth_time' => $birth_time,
                    'birth_weight' => $birth_weight,
                    'fk_birth_weight_size' => $fk_birth_weight_size,
                    'pregnancy_outcome_id' => $pregnancy_outcome_id,
                    'fk_birth_registration' => $birstRegiType,
                    'birth_registration_date' => $new_birthRegidate,
                    'fk_why_not_birth_registration' => $whyNotRegi,
                    'keep_follow_up' => $keep_follow_up,
                    'fk_pnc_chkup_child_id' => $checkupTypeChild,
                    'pnc_chkup_child_times' => $afterTotalTimesChild,
                    'fk_pnc_first_visit_id' => $childFirstVisit,
                    'pnc_first_visit_days' => $childFirstVisitDays,
                    'fk_pnc_second_visit_id' => $childSecondVisit,
                    'pnc_second_visit_days' => $childSecondVisitDays,
                    'fk_child_first_visit_assist' => $childFirstVisitAsist,
                    'fk_child_second_visit_assist' => $childSecondVisitAsist,
                    'fk_post_natal_child_visit' => $fk_post_natal_visit,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                // member master

                $this->modelName->UpdateInfo($memberMaster, $member_master_id, $this->config->item('memberMasterTable'));


                // member household

                $memberHousehold = array(
                    'entry_date' => $new_entryDate,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($memberHousehold, $member_household_id_last, $this->config->item('memberHouseholdTable'));

                // occupation
                $occupation = array(
                    'fk_main_occupation' => $occupationType,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($occupation, $fk_occupation_id_last, $this->config->item('memberOccupationTable'));

                // Education

                $education = array(
                    //'fk_religious_edu'=>$religiousEduType, 
                    //'fk_secular_edu'=>$secularEduType, 
                    'fk_education_type' => $educationType,
                    'year_of_education' => 0,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($education, $fk_education_id_last, $this->config->item('memberEducationTable'));


                // Relation

                $relation = array(
                    'fk_relation' => $relationheadID,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($relation, $fk_member_relation_id_last, $this->config->item('memberRelationTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating birth.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Member Birth updated successfully.');

            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/birth' . '?baseID=' . $baseID);
            }

            redirect($this->controller . '/edit_birth/' . $member_master_id . '?baseID=' . $baseID . '&household_master_id=' . $household_master_id);
        }
    }

    public function internal_in() {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = $this->pageTitle;
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'internal_in';
        $data['editMethod'] = 'edit_internal_in';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';
        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

//        echo $this->session->userdata('round_no'); exit();

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/internal_in', $data);
        $this->load->view('includes/footer');
    }

//    ----------------------------------------------------
    public function show_internal_in() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'movement_date',
            2 => 'REMARKS',
            3 => 'DOB',
            4 => 'HHNO',
            5 => 'member_code',
            6 => 'fk_movement_type_code',
            7 => 'INT_CAU',
            8 => 'SLUMCODEF',
            9 => 'SLUMAREAF',
            10 => 'fk_migration_cause_code',
            11 => 'countryIDMoveFrom_code',
            12 => 'divisionIDMoveFrom_code',
            13 => 'districtIDMoveFrom_code',
            14 => 'thanaIDMoveFrom_code',
            15 => 'HHNOF',
            16 => 'insertedDate',
            17 => 'insertedTime',
            18 => 'insertedBy_name',
            19 => 'updatedDate',
            20 => 'updatedTime',
            21 => 'updateBy_name');

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

        //$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("internal_in_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("internal_in_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "reports/edit_internal_in/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->movement_date,
                    $rows->REMARKS,
                    $rows->DOB,
                    $rows->HHNO,
                    $rows->member_code,
                    $rows->fk_movement_type_code,
                    $rows->INT_CAU,
                    $rows->SLUMCODEF,
                    $rows->SLUMAREAF,
                    $rows->fk_migration_cause_code,
                    $rows->countryIDMoveFrom_code,
                    $rows->divisionIDMoveFrom_code,
                    $rows->districtIDMoveFrom_code,
                    $rows->thanaIDMoveFrom_code,
                    $rows->HHNOF,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_internal_in();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_internal_in() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("internal_in_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("internal_in_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

//    -----------------------------------------------------

    public function edit_internal_out($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Internal out";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_internal_out';
        $data['shortName'] = "Internal out";
        $data['boxTitle'] = 'List';

        $data['internal_out_info'] = $this->modelName->internal_out_info($id, $this->config->item('migrationOutTable'));

        $data['memberexittyp'] = $this->modelName->getLookUpListSpecific($this->config->item('member_exit_typ'), array('intout'));
        $data['internal_movement_cause'] = $this->modelName->getLookUpList($this->config->item('internal_movement_cause'));
        $data['movement_group_typ'] = $this->modelName->getLookUpList($this->config->item('movement_group_typ'));
        $data['outside_cause'] = $this->modelName->getLookUpList($this->config->item('outside_cause'));
        $data['slumlist'] = $this->modelName->getListType($this->config->item('slumTable'));
        $data['countrylist'] = $this->modelName->getListType($this->config->item('countryTable'));
        $data['divisionlist'] = $this->modelName->getListType($this->config->item('divTable'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_internal_out', $data);
        $this->load->view('includes/footer');
    }

    function update_internal_out() {

        $migrationID = $this->input->post('migrationID', true);
        $member_master_id = $this->input->post('member_master_id', true);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('movement_date', 'Movement/migration Date', 'trim|required');

        $getCurrentRound = $this->modelName->getCurrentRound();

        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_internal_out/' . $migrationID . '?baseID=' . $baseID);
        } else {

            if ($getCurrentRound->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_internal_out/' . $migrationID . '?baseID=' . $baseID);
            }

            $movement_date = $this->input->post('movement_date', true);

            $remarks = $this->input->post('remarks', true);


            $fk_internal_cause = 0;
            $slumID = 0;
            $slumAreaID = 0;
            $househodID = 0;

            $fk_internal_cause = $this->input->post('fk_internal_cause', true);
            $slumID = $this->input->post('slumID', true);
            $slumAreaID = $this->input->post('slumAreaID', true);
            $househodID = $this->input->post('househodID', true);



            if (!empty($movement_date)) {
                $parts5 = explode('/', $movement_date);
                $new_movement_date = $parts5[2] . '-' . $parts5[1] . '-' . $parts5[0];
            }


            $this->db->trans_start();

            try {


                $IdInfo = array(
                    'movement_date' => $new_movement_date,
                    'fk_internal_cause' => $fk_internal_cause,
                    'slumIDTo' => $slumID,
                    'slumAreaIDTo' => $slumAreaID,
                    'household_master_id_move_to' => $househodID,
                    'remarks' => $remarks,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($IdInfo, $migrationID, $this->config->item('migrationOutTable'));

                // update member household info

                $whereMigout = array('id' => $member_master_id);
                $member_household_id_last = $this->db->select('member_household_id_last')->from($this->config->item('memberMasterTable'))->where($whereMigout)->get()->row()->member_household_id_last;


                $memberHouseholdUpdate = array(
                    'exit_date' => $new_movement_date,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );
                $this->modelName->UpdateInfo($memberHouseholdUpdate, $member_household_id_last, $this->config->item('memberHouseholdTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating Movement/Internal out Info.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Movement/Internal out Info updated successfully.');

            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/internal_out' . '?baseID=' . $baseID);
            }

            redirect($this->controller . '/edit_internal_out/' . $migrationID . '?baseID=' . $baseID);
        }
    }

    public function migration_in() {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Migration in";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'migration_in';
        $data['editMethod'] = 'edit_migration_in';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';
        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/migration_in', $data);
        $this->load->view('includes/footer');
    }

    public function show_migration_in_view() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'INDATE',
            2 => 'REMARKS',
            3 => 'DOB',
            4 => 'HHNO',
            5 => 'member_code',
            6 => 'INTYPE',
            7 => 'fk_internal_cause_code',
            8 => 'SLUMCODE',
            9 => 'SLUMAREA',
            10 => 'IN_CAUSE',
            11 => 'countryIDMoveFrom_code',
            12 => 'DIVISION',
            13 => 'districtIDMoveFrom_code',
            14 => 'FROM_UPZ',
            15 => 'household_code_move_from',
            16 => 'insertedDate',
            17 => 'insertedTime',
            18 => 'insertedBy_name',
            19 => 'updatedDate',
            20 => 'updatedTime',
            21 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("migration_in_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("migration_in_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_migration_in/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->INDATE,
                    $rows->REMARKS,
                    $rows->DOB,
                    $rows->HHNO,
                    $rows->member_code,
                    $rows->INTYPE,
                    $rows->fk_internal_cause_code,
                    $rows->SLUMCODE,
                    $rows->SLUMAREA,
                    $rows->IN_CAUSE,
                    $rows->countryIDMoveFrom_code,
                    $rows->DIVISION,
                    $rows->districtIDMoveFrom_code,
                    $rows->FROM_UPZ,
                    $rows->household_code_move_from,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_migration_in_view();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_migration_in_view() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("migration_in_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("migration_in_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function internal_out() {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

//        echo "<pre/>";
//        print_r($this->global['menu']); exit();

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Internal out";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'internal_out';
        $data['editMethod'] = 'edit_internal_out';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';
        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/internal_out', $data);
        $this->load->view('includes/footer');
    }

    public function show_internal_out() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'INTRNLDT',
            2 => 'REMARKS',
            3 => 'DOB',
            4 => 'HHNOF',
            5 => 'member_code',
            6 => 'fk_movement_type_code',
            7 => 'INT_CAU',
            8 => 'SLUMCODE',
            9 => 'SLUMAREA',
            10 => 'fk_type_of_group_code',
            11 => 'fk_outside_cause_individual_code',
            12 => 'fk_outside_cause_group_code',
            13 => 'countryIDMoveTo_code',
            14 => 'divisionIDMoveTo_code',
            15 => 'districtIDMoveTo_code',
            16 => 'thanaIDMoveTo_code',
            17 => 'HHNO',
            18 => 'insertedDate',
            19 => 'insertedTime',
            20 => 'insertedBy_name',
            21 => 'updatedDate',
            22 => 'updatedTime',
            23 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("internal_out_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("internal_out_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "reports/edit_internal_out/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->INTRNLDT,
                    $rows->REMARKS,
                    $rows->DOB,
                    $rows->HHNOF,
                    $rows->member_code,
                    $rows->fk_movement_type_code,
                    $rows->INT_CAU,
                    $rows->SLUMCODE,
                    $rows->SLUMAREA,
                    $rows->fk_type_of_group_code,
                    $rows->fk_outside_cause_individual_code,
                    $rows->fk_outside_cause_group_code,
                    $rows->countryIDMoveTo_code,
                    $rows->divisionIDMoveTo_code,
                    $rows->districtIDMoveTo_code,
                    $rows->thanaIDMoveTo_code,
                    $rows->HHNO,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_internal_out();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_internal_out() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("internal_out_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("internal_out_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function migration_out() {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = $this->pageTitle;
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'migration_out';
        $data['editMethod'] = 'edit_migration_out';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';
        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));

        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/migration_out', $data);
        $this->load->view('includes/footer');
    }

    public function show_migration_out_view() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'OUTDT',
            2 => 'REMARKS',
            3 => 'DOB',
            4 => 'HHNO',
            5 => 'member_code',
            6 => 'fk_movement_type_code',
            7 => 'fk_internal_cause_code',
            8 => 'SLUMCODE',
            9 => 'SLUMAREA',
            10 => 'GTYPE',
            11 => 'fk_outside_cause_individual_code',
            12 => 'fk_outside_cause_group_code',
            13 => 'countryIDMoveTo_code',
            14 => 'TO_DIV',
            15 => 'districtIDMoveTo_code',
            16 => 'TO_UPZ',
            17 => 'household_code_move_to',
            18 => 'insertedDate',
            19 => 'insertedTime',
            20 => 'insertedBy_name',
            21 => 'updatedDate',
            22 => 'updatedTime',
            23 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("migration_out_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("migration_out_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_migration_out/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->OUTDT,
                    $rows->REMARKS,
                    $rows->DOB,
                    $rows->HHNO,
                    $rows->member_code,
                    $rows->fk_movement_type_code,
                    $rows->fk_internal_cause_code,
                    $rows->SLUMCODE,
                    $rows->SLUMAREA,
                    $rows->GTYPE,
                    $rows->fk_outside_cause_individual_code,
                    $rows->fk_outside_cause_group_code,
                    $rows->countryIDMoveTo_code,
                    $rows->TO_DIV,
                    $rows->districtIDMoveTo_code,
                    $rows->TO_UPZ,
                    $rows->household_code_move_to,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_migration_out_view();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_migration_out_view() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("migration_out_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("migration_out_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_migration_out($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Migration out";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_migration_out';
        $data['shortName'] = "Migration out";
        $data['boxTitle'] = 'List';

        $data['migration_out_info'] = $this->modelName->migration_out_info($id, $this->config->item('migrationOutTable'));

        $data['memberexittyp'] = $this->modelName->getLookUpListNotSpecific($this->config->item('member_exit_typ'), array('dth', 'ext'));
        $data['internal_movement_cause'] = $this->modelName->getLookUpList($this->config->item('internal_movement_cause'));
        $data['movement_group_typ'] = $this->modelName->getLookUpList($this->config->item('movement_group_typ'));
        $data['outside_cause'] = $this->modelName->getLookUpList($this->config->item('outside_cause'));
        $data['slumlist'] = $this->modelName->getListType($this->config->item('slumTable'));
        $data['countrylist'] = $this->modelName->getListType($this->config->item('countryTable'));
        $data['divisionlist'] = $this->modelName->getListType($this->config->item('divTable'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_migration_out', $data);
        $this->load->view('includes/footer');
    }

    function update_migration_out() {

        $migrationID = $this->input->post('migrationID', true);
        $member_master_id = $this->input->post('member_master_id', true);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('movement_date', 'Movement/migration Date', 'trim|required');


        $getCurrentRound = $this->modelName->getCurrentRound();

        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_migration_out/' . $migrationID . '?baseID=' . $baseID);
        } else {

            if ($getCurrentRound->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_migration_out/' . $migrationID . '?baseID=' . $baseID);
            }

            $movement_date = $this->input->post('movement_date', true);
            $remarks = $this->input->post('remarks', true);

            $fk_type_of_group = 0;
            $fk_outside_cause_individual = 0;
            $fk_outside_cause_group = 0;
            $countryID = 0;
            $divisionID = 0;
            $districtID = 0;
            $thanaID = 0;

            $fk_type_of_group = $this->input->post('fk_type_of_group', true);
            $fk_outside_cause_individual = ($this->input->post('fk_outside_cause_individual', true)) ? $this->input->post('fk_outside_cause_individual', true) : 0;
            $fk_outside_cause_group = ($this->input->post('fk_outside_cause_group', true)) ? $this->input->post('fk_outside_cause_group', true) : 0;
            $countryID = $this->input->post('countryID', true);

            if ($this->config->item('bangladesh') == $countryID) { // bangldesh 
                $divisionID = ($this->input->post('divisionID', true)) ? $this->input->post('divisionID', true) : 0;
                $districtID = ($this->input->post('districtID', true)) ? $this->input->post('districtID', true) : 0;
                $thanaID = ($this->input->post('thanaID', true)) ? $this->input->post('thanaID', true) : 0;
            }



            if (!empty($movement_date)) {
                $parts5 = explode('/', $movement_date);
                $new_movement_date = $parts5[2] . '-' . $parts5[1] . '-' . $parts5[0];
            }


            $this->db->trans_start();

            try {
                $IdInfo = array(
                    'movement_date' => $new_movement_date,
                    'fk_type_of_group' => $fk_type_of_group,
                    'fk_outside_cause_individual' => $fk_outside_cause_individual,
                    'fk_outside_cause_group' => $fk_outside_cause_group,
                    'countryIDMoveTo' => $countryID,
                    'divisionIDMoveTo' => $divisionID,
                    'districtIDMoveTo' => $districtID,
                    'thanaIDMoveTo' => $thanaID,
                    'remarks' => $remarks,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($IdInfo, $migrationID, $this->config->item('migrationOutTable'));


                // update member household info

                $whereMigout = array('id' => $member_master_id);
                $member_household_id_last = $this->db->select('member_household_id_last')->from($this->config->item('memberMasterTable'))->where($whereMigout)->get()->row()->member_household_id_last;


                $memberHouseholdUpdate = array(
                    'exit_date' => $new_movement_date,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($memberHouseholdUpdate, $member_household_id_last, $this->config->item('memberHouseholdTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating movement/migration out.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Movement/Migration out updated successfully.');

            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/migration_out' . '?baseID=' . $baseID);
            }

            redirect($this->controller . '/edit_migration_out/' . $migrationID . '?baseID=' . $baseID);
        }
    }

    public function education() {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Education";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'education';
        $data['editMethod'] = 'edit_education';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';
        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/education', $data);
        $this->load->view('includes/footer');
    }

    public function show_education_view() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'is_last_education',
            2 => 'birth_date',
            3 => 'household_code',
            4 => 'member_code',
            5 => 'fk_religious_edu_code',
            6 => 'fk_secular_edu_code',
            7 => 'fk_education_type_code',
            8 => 'year_of_education_code',
            9 => 'insertedDate',
            10 => 'insertedTime',
            11 => 'insertedBy_name',
            12 => 'updatedDate',
            13 => 'updatedTime',
            14 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("education_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("education_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_education/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->is_last_education,
                    $rows->birth_date,
                    $rows->household_code,
                    $rows->member_code,
                    $rows->fk_religious_edu_code,
                    $rows->fk_secular_edu_code,
                    $rows->fk_education_type_code,
                    $rows->year_of_education_code,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_education_view();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_education_view() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("education_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("education_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_education($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Education";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_education';
        $data['shortName'] = "Education";
        $data['boxTitle'] = 'List';

        $data['education_info'] = $this->modelName->education_info($id, $this->config->item('memberEducationTable'));

//        echo "<pre/>";
//        print_r($data['conception_info']); exit();

        $data['educationtyp'] = $this->modelName->getLookUpList($this->config->item('educationtyp'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_education', $data);
        $this->load->view('includes/footer');
    }

    public function occupation() {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Occupation";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'occupation';
        $data['editMethod'] = 'edit_occupation';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/occupation', $data);
        $this->load->view('includes/footer');
    }

    public function show_occupation_view() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'main_occupation_oth',
            2 => 'is_last_occupation',
            3 => 'birth_date',
            4 => 'household_code',
            5 => 'member_code',
            6 => 'fk_main_occupation_code',
            7 => 'insertedDate',
            8 => 'insertedTime',
            9 => 'insertedBy_name',
            10 => 'updatedDate',
            11 => 'updatedTime',
            12 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("occupation_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("occupation_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_occupation/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->main_occupation_oth,
                    $rows->is_last_occupation,
                    $rows->birth_date,
                    $rows->household_code,
                    $rows->member_code,
                    $rows->fk_main_occupation_code,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_occupation_view();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_occupation_view() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("occupation_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("occupation_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_occupation($id) {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Occupation";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_occupation';
        $data['shortName'] = "Occupation";
        $data['boxTitle'] = 'List';

        $data['occupation_info'] = $this->modelName->occupation_info($id, $this->config->item('memberOccupationTable'));
        $data['occupationtyp'] = $this->modelName->getLookUpList($this->config->item('occupationtyp'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_occupation', $data);
        $this->load->view('includes/footer');
    }

    function update_occupation() {
        $occupationID = $this->input->post('occupationID', true);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('occupationType', 'Main Occupation', 'trim|required|numeric');


        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_occupation/' . $occupationID . '?baseID=' . $baseID);
        } else {

            if ($this->getCurrentRound()[0]->active == 0) {

                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_occupation/' . $occupationID . '?baseID=' . $baseID);
            }


            $occupationType = $this->input->post('occupationType', true);
            $main_occupation_oth = '';
            if ($occupationType == 166) {
                $main_occupation_oth = $this->input->post('main_occupation_oth', true);
            }


            $this->db->trans_start();

            try {

                $IdInfo = array(
                    'fk_main_occupation' => $occupationType,
                    'main_occupation_oth' => $main_occupation_oth,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );


                $this->modelName->UpdateInfo($IdInfo, $occupationID, $this->config->item('memberOccupationTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating occupation.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Member occupation updated successfully.');
            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/occupation' . '?baseID=' . $baseID);
            }
            redirect($this->controller . '/edit_occupation/' . $occupationID . '?baseID=' . $baseID);
        }
    }

    public function relation() {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Relation";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'relation';
        $data['editMethod'] = 'edit_relation';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/relation', $data);
        $this->load->view('includes/footer');
    }

    public function show_relation_view() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'household_master_id',
            2 => 'member_master_id',
            3 => 'round_master_id',
            4 => 'fk_relation',
            5 => 'is_last_relation',
            6 => 'birth_date',
            7 => 'household_code',
            8 => 'member_code',
            9 => 'fk_relation_code',
            10 => 'insertedDate',
            11 => 'insertedTime',
            12 => 'insertedBy_name',
            13 => 'updatedDate',
            14 => 'updatedTime',
            15 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("relation_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("relation_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_relation/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->household_master_id,
                    $rows->member_master_id,
                    $rows->round_master_id,
                    $rows->fk_relation,
                    $rows->is_last_relation,
                    $rows->birth_date,
                    $rows->household_code,
                    $rows->member_code,
                    $rows->fk_relation_code,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_relation_view();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_relation_view() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("relation_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("relation_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_relation($id) {

        $baseID = $this->input->get('baseID', TRUE);
        $household_master_id_current = $this->input->get('household_master_id', TRUE);
        $member_master_id_current = $this->input->get('member_master_id', TRUE);
        $round_master_id_current = $this->input->get('round_master_id', TRUE);
        $fk_relation_current = $this->input->get('fk_relation', TRUE);

//        echo $round_master_id_current;
//        exit();
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Relation";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_relation';
        $data['shortName'] = "Relation";
        $data['boxTitle'] = 'List';


        $data['relation_info'] = $this->modelName->relation_info($id, $household_master_id_current, $member_master_id_current, $round_master_id_current, $fk_relation_current, $this->config->item('memberRelationTable'));

//        echo "<pre/>";
//        print_r($data['relation_info']);
//        exit();
        //while Cause of head change,Effective date (If HHH) are empty instead of fk_relation==27
        if ($data['relation_info'] == false) {
            $data['relation_info'] = $this->modelName->relation_info($id, $household_master_id_current, $member_master_id_current, $round_master_id_current, 0, $this->config->item('memberRelationTable'));
        }

        if ($fk_relation_current != 27) {
            $data['relationhhh'] = $this->modelName->getLookUpListNotSpecific($this->config->item('relationhhh'), array('01'));
        }
//        echo "<pre/>";
//        print_r($data['relation_info']);
//        exit();


        $data['hh_change_reason'] = $this->modelName->getLookUpList($this->config->item('hh_change_reason'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_relation', $data);
        $this->load->view('includes/footer');
    }

    public function asset() {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Asset";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'asset';
        $data['editMethod'] = 'edit_asset';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }


        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/asset', $data);
        $this->load->view('includes/footer');
    }

    public function show_asset_view() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'HHNO',
            2 => 'DWELLING',
            3 => 'DHOUSE',
            4 => 'CHAIR',
            5 => 'DININGT',
            6 => 'KHAT',
            7 => 'CHOWKI',
            8 => 'ALMIRAH',
            9 => 'SOFASET',
            10 => 'RADIO',
            11 => 'TV',
            12 => 'FREEZE',
            13 => 'MOBILE',
            14 => 'ELCFAN',
            15 => 'WATCH',
            16 => 'RICKSHAW',
            17 => 'COMPUTER',
            18 => 'SEWINGMAC',
            19 => 'CYCLE',
            20 => 'MOTORCYCLE',
            21 => 'insertedDate',
            22 => 'insertedTime',
            23 => 'insertedBy_name',
            24 => 'updatedDate',
            25 => 'updatedTime',
            26 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("asset_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("asset_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_asset/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->HHNO,
                    $rows->DWELLING,
                    $rows->DHOUSE,
                    $rows->CHAIR,
                    $rows->DININGT,
                    $rows->KHAT,
                    $rows->CHOWKI,
                    $rows->ALMIRAH,
                    $rows->SOFASET,
                    $rows->RADIO,
                    $rows->TV,
                    $rows->FREEZE,
                    $rows->MOBILE,
                    $rows->ELCFAN,
                    $rows->WATCH,
                    $rows->RICKSHAW,
                    $rows->COMPUTER,
                    $rows->SEWINGMAC,
                    $rows->CYCLE,
                    $rows->MOTORCYCLE,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_asset_view();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_asset_view() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("asset_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("asset_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_asset($id) {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Asset";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_asset';
        $data['shortName'] = "Asset";
        $data['boxTitle'] = 'List';

        $data['asset_info'] = $this->modelName->asset_info($id, $this->config->item('householdAssetTable'));

        $data['assetYesNo'] = $this->modelName->getLookUpList($this->config->item('asset_yes_no'));
        $data['land_owner'] = $this->modelName->getLookUpList($this->config->item('land_owner'));
        $data['house_owner'] = $this->modelName->getLookUpList($this->config->item('house_owner'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_asset', $data);
        $this->load->view('includes/footer');
    }

    function update_asset() {

        $assetID = $this->input->post('assetID', true);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('fk_owner_land', 'Land Owner', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_owner_house', 'House Owner', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_chair', 'Chair', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_dining_table', 'Dining Table', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_khat', 'Khat', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_chowki', 'Chowki', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_almirah', 'Almirah', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_sofa', 'Sofa', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_radio', 'Radio', 'trim|required|numeric');

        $this->form_validation->set_rules('fk_tv', 'TV', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_freeze', 'Fridge', 'trim|required|numeric');

        $this->form_validation->set_rules('fk_mobile', 'Entry Date', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_electric_fan', 'Electric_Fan', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_hand_watch', 'Hand Watch', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_rickshow', 'Rikshaw', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_computer', 'Computer', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_sewing_machine', 'Sewing machine', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_cycle', 'By Cycle', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_motor_cycle', 'Motor Cycle', 'trim|required|numeric');


        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_asset/' . $assetID . '?baseID=' . $baseID);
        } else {

            if ($this->getCurrentRound()[0]->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_asset/' . $assetID . '?baseID=' . $baseID);
            }


            $fk_owner_land = $this->input->post('fk_owner_land', true);
            $fk_owner_house = $this->input->post('fk_owner_house', true);
            $fk_chair = $this->input->post('fk_chair', true);
            $fk_dining_table = $this->input->post('fk_dining_table', true);
            $fk_khat = $this->input->post('fk_khat', true);
            $fk_chowki = $this->input->post('fk_chowki', true);
            $fk_almirah = $this->input->post('fk_almirah', true);
            $fk_sofa = $this->input->post('fk_sofa', true);
            $fk_radio = $this->input->post('fk_radio', true);
            $fk_tv = $this->input->post('fk_tv', true);
            $fk_freeze = $this->input->post('fk_freeze', true);
            $fk_mobile = $this->input->post('fk_mobile', true);
            $fk_electric_fan = $this->input->post('fk_electric_fan', true);
            $fk_hand_watch = $this->input->post('fk_hand_watch', true);
            $fk_rickshow = $this->input->post('fk_rickshow', true);
            $fk_computer = $this->input->post('fk_computer', true);
            $fk_sewing_machine = $this->input->post('fk_sewing_machine', true);
            $fk_cycle = $this->input->post('fk_cycle', true);
            $fk_motor_cycle = $this->input->post('fk_motor_cycle', true);

            $this->db->trans_start();

            try {

                $IdInfo = array(
                    'fk_owner_land' => $fk_owner_land,
                    'fk_owner_house' => $fk_owner_house,
                    'fk_chair' => $fk_chair,
                    'fk_dining_table' => $fk_dining_table,
                    'fk_khat' => $fk_khat,
                    'fk_chowki' => $fk_chowki,
                    'fk_almirah' => $fk_almirah,
                    'fk_radio' => $fk_radio,
                    'fk_sofa' => $fk_sofa,
                    'fk_tv' => $fk_tv,
                    'fk_freeze' => $fk_freeze,
                    'fk_mobile' => $fk_mobile,
                    'fk_electric_fan' => $fk_electric_fan,
                    'fk_hand_watch' => $fk_hand_watch,
                    'fk_rickshow' => $fk_rickshow,
                    'fk_computer' => $fk_computer,
                    'fk_sewing_machine' => $fk_sewing_machine,
                    'fk_cycle' => $fk_cycle,
                    'fk_motor_cycle' => $fk_motor_cycle,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($IdInfo, $assetID, $this->config->item('householdAssetTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating household Asset.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Household Asset updated successfully.');
            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/asset' . '?baseID=' . $baseID);
            }
            redirect($this->controller . '/edit_asset/' . $assetID . '?baseID=' . $baseID);
        }
    }

    public function marriage_start() {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Marriage start";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'marriage_start';
        $data['editMethod'] = 'edit_marriage_start';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/marriage_start', $data);
        $this->load->view('includes/footer');
    }

    public function show_marriage_start() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'REMARKS',
            2 => 'MARDT',
            3 => 'birth_date',
            4 => 'HHNO',
            5 => 'member_code',
            6 => 'fk_bri_gem_premarital_status_code',
            7 => 'fk_bri_gem_marital_order_code',
            8 => 'fk_kazi_registered_code',
            9 => 'fk_member_premarital_status_code',
            10 => 'fk_member_marital_order_code',
            11 => 'member_code_bride_groom',
            12 => 'insertedDate',
            13 => 'insertedTime',
            14 => 'insertedBy_name',
            15 => 'updatedDate',
            16 => 'updatedTime',
            17 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("marriage_start_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("marriage_start_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "reports/edit_marriage_start/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->REMARKS,
                    $rows->MARDT,
                    $rows->birth_date,
                    $rows->HHNO,
                    $rows->member_code,
                    $rows->fk_bri_gem_premarital_status_code,
                    $rows->fk_bri_gem_marital_order_code,
                    $rows->fk_kazi_registered_code,
                    $rows->fk_member_premarital_status_code,
                    $rows->fk_member_marital_order_code,
                    $rows->member_code_bride_groom,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_marriage_start();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_marriage_start() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("marriage_start_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("marriage_start_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_marriage_start($id) {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Marriage start";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_marriage_start';
        $data['shortName'] = "Marriage start";
        $data['boxTitle'] = 'List';

        $data['marriage_start_info'] = $this->modelName->marriage_start_info($id, $this->config->item('marriageStartTable'));

//        echo "<pre/>";
//        print_r($data['conception_info']); exit();

        $data['maritalstatustyp'] = $this->modelName->getLookUpList($this->config->item('maritalstatustyp'));
        $data['marriage_order'] = $this->modelName->getLookUpList($this->config->item('marriage_order'));
        $data['marriage_registration'] = $this->modelName->getLookUpList($this->config->item('marriage_registration'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_marriage_start', $data);
        $this->load->view('includes/footer');
    }

    function update_marriage_start() {

        $marriageID = $this->input->post('marriageID', true);
        $member_master_id = $this->input->post('member_master_id', true);

        $this->load->library('form_validation');


        $this->form_validation->set_rules('fk_member_premarital_status', 'Member previous marital status', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_member_marital_order', 'Member marital order', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_bri_gem_premarital_status', 'Bride/Groom previous marital status', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_bri_gem_marital_order', 'Bride/Groom marital order', 'trim|required|numeric');
        $this->form_validation->set_rules('marriage_date', 'Marriage Date', 'trim|required');
        $this->form_validation->set_rules('fk_kazi_registered', 'Kazi registered', 'trim|required|numeric');


        $getCurrentRound = $this->modelName->getCurrentRound();

        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_marriage_start/' . $marriageID . '?baseID=' . $baseID);
        } else {

            if ($getCurrentRound->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_marriage_start/' . $marriageID . '?baseID=' . $baseID);
            }


            $fk_member_premarital_status = $this->input->post('fk_member_premarital_status', true);
            $fk_member_marital_order = $this->input->post('fk_member_marital_order', true);
            $fk_bri_gem_premarital_status = $this->input->post('fk_bri_gem_premarital_status', true);
            $fk_bri_gem_marital_order = $this->input->post('fk_bri_gem_marital_order', true);
            $marriage_date = $this->input->post('marriage_date', true);
            $fk_kazi_registered = $this->input->post('fk_kazi_registered', true);
            $remarks = $this->input->post('remarks', true);

            $prev_spause_id = $this->input->post('prev_spause_id', true);


            if (!empty($marriage_date)) {
                $parts5 = explode('/', $marriage_date);
                $new_marriage_date = $parts5[2] . '-' . $parts5[1] . '-' . $parts5[0];
            }

            $member_id = $this->input->post('member_id', true);

            $member_code = '';
            $member_code_spause = '';
            $bride_groom_id = 0;

            $member_master_id_bride_groom = $this->input->post('member_master_id_bride_groom', true);
            $full_code = $this->input->post('full_code', true);

            if (!empty($full_code)) {
                $member_code = $this->input->post('member_code', true);
                $bride_groom_id = $this->input->post('member_id', true);

                $wherememberCode = array('id' => $member_master_id);
                $member_code_spause = $this->db->select('member_code')->from($this->config->item('memberMasterTable'))->where($wherememberCode)->get()->row()->member_code;
            }

            $this->db->trans_start();

            try {

                $IdInfo = array(
                    'fk_member_premarital_status' => $fk_member_premarital_status,
                    'fk_member_marital_order' => $fk_member_marital_order,
                    'fk_bri_gem_premarital_status' => $fk_bri_gem_premarital_status,
                    'fk_bri_gem_marital_order' => $fk_bri_gem_marital_order,
                    'marriage_date' => $new_marriage_date,
                    'fk_kazi_registered' => $fk_kazi_registered,
                    'member_master_id_bride_groom' => $bride_groom_id,
                    'remarks' => $remarks,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($IdInfo, $marriageID, $this->config->item('marriageStartTable'));


                // update member info

                $memberUpdate = array(
                    'spouse_code' => $member_code,
                    'fk_spouse_id' => $bride_groom_id,
                    'fk_marital_status' => $this->config->item('maritalStatusMarried'),
                    'last_marriage_date' => $new_marriage_date,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($memberUpdate, $member_master_id, $this->config->item('memberMasterTable'));


                // update previous spause info

                if ($prev_spause_id > 0) {
                    $memberUpdateBrideGroomPrev = array(
                        'spouse_code' => '',
                        'fk_spouse_id' => 0,
                        'fk_marital_status' => $this->config->item('maritalStatusUnMarried'),
                        'last_marriage_date' => null,
                        'updateBy' => $this->vendorId,
                        'updatedOn' => date('Y-m-d H:i:s')
                    );

                    $this->modelName->UpdateInfo($memberUpdateBrideGroomPrev, $prev_spause_id, $this->config->item('memberMasterTable'));
                }

                // update spause info

                if ($bride_groom_id > 0) {
                    $memberUpdateBrideGroom = array(
                        'spouse_code' => $member_code_spause,
                        'fk_spouse_id' => $member_master_id,
                        'fk_marital_status' => $this->config->item('maritalStatusMarried'),
                        'last_marriage_date' => $new_marriage_date,
                        'updateBy' => $this->vendorId,
                        'updatedOn' => date('Y-m-d H:i:s')
                    );

                    $this->modelName->UpdateInfo($memberUpdateBrideGroom, $bride_groom_id, $this->config->item('memberMasterTable'));
                }
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating marriage start.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Member Marriage start info updated successfully.');

            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/marriage_start' . '?baseID=' . $baseID);
            }

            redirect($this->controller . '/edit_marriage_start/' . $marriageID . '?baseID=' . $baseID);
        }
    }

    public function edit_marriage_end($id) {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Marriage end";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_marriage_end';
        $data['shortName'] = "Marriage end";
        $data['boxTitle'] = 'List';

        $data['marriage_end_info'] = $this->modelName->marriage_end_info($id, $this->config->item('marriageEndTable'));

//        echo "<pre/>";
//        print_r($data['conception_info']); exit();

        $data['marriage_end_typ'] = $this->modelName->getLookUpListNotSpecific($this->config->item('maritalstatustyp'), array('unm', 'mar'));
        $data['marriage_end_cause'] = $this->modelName->getLookUpList($this->config->item('marriage_end_cause'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_marriage_end', $data);
        $this->load->view('includes/footer');
    }

    function update_marriage_end() {

        $marriageID = $this->input->post('marriageID', true);
        $member_master_id = $this->input->post('member_master_id', true);

        $this->load->library('form_validation');


        $this->form_validation->set_rules('fk_marriage_end_type', 'Member marriage end type', 'trim|required|numeric');
        $this->form_validation->set_rules('marriage_end_date', 'Marriage End Date', 'trim|required');

        $getCurrentRound = $this->modelName->getCurrentRound();
        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_marriage_end/' . $marriageID . '?baseID=' . $baseID);
        } else {

            if ($getCurrentRound->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_marriage_end/' . $marriageID . '?baseID=' . $baseID);
            }


            $fk_marriage_end_type = $this->input->post('fk_marriage_end_type', true);
            $fk_spouse_id = $this->input->post('fk_spouse_id', true);
            $fk_marriage_end_cause_one = $this->input->post('fk_marriage_end_cause_one', true);
            $fk_marriage_end_cause_two = $this->input->post('fk_marriage_end_cause_two', true);
            $fk_marriage_end_cause_three = $this->input->post('fk_marriage_end_cause_three', true);
            $marriage_end_date = $this->input->post('marriage_end_date', true);
            $remarks = $this->input->post('remarks', true);


            if (!empty($marriage_end_date)) {
                $parts5 = explode('/', $marriage_end_date);
                $new_marriage_end_date = $parts5[2] . '-' . $parts5[1] . '-' . $parts5[0];
            }


            $this->db->trans_start();

            try {

                $IdInfo = array(
                    'fk_marriage_end_type' => $fk_marriage_end_type,
                    'fk_marriage_end_cause_one' => $fk_marriage_end_cause_one,
                    'fk_marriage_end_cause_two' => $fk_marriage_end_cause_two,
                    'marriage_end_date' => $new_marriage_end_date,
                    'fk_marriage_end_cause_three' => $fk_marriage_end_cause_three,
                    'remarks' => $remarks,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($IdInfo, $marriageID, $this->config->item('marriageEndTable'));


                // update member info

                $memberUpdate = array('fk_marital_status' => $fk_marriage_end_type,
                    'last_marriage_end_date' => $new_marriage_end_date,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($memberUpdate, $member_master_id, $this->config->item('memberMasterTable'));


                // update previous spause info

                if ($fk_spouse_id > 0) {
                    $memberUpdateBrideGroomPrev = array(
                        'fk_marital_status' => $fk_marriage_end_type,
                        'last_marriage_end_date' => $new_marriage_end_date,
                        'updateBy' => $this->vendorId,
                        'updatedOn' => date('Y-m-d H:i:s')
                    );

                    $this->modelName->UpdateInfo($memberUpdateBrideGroomPrev, $fk_spouse_id, $this->config->item('memberMasterTable'));
                }
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating marriage end.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Member Marriage end updated successfully.');

            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/marriage_end' . '?baseID=' . $baseID);
            }

            redirect($this->controller . '/edit_marriage_end/' . $marriageID . '?baseID=' . $baseID);
        }
    }

    public function marriage_end() {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Marriage end";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'marriage_end';
        $data['editMethod'] = 'edit_marriage_end';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/marriage_end', $data);
        $this->load->view('includes/footer');
    }

    public function show_marriage_end() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'marriage_end_date',
            2 => 'REMARKS',
            3 => 'birth_date',
            4 => 'HHNO',
            5 => 'member_code',
            6 => 'fk_marriage_end_cause_one_code',
            7 => 'fk_marriage_end_cause_two_code',
            8 => 'fk_marriage_end_cause_three_code',
            9 => 'fk_marriage_end_type_code',
            10 => 'member_code_bride_groom',
            11 => 'insertedDate',
            12 => 'insertedTime',
            13 => 'insertedBy_name',
            14 => 'updatedDate',
            15 => 'updatedTime',
            16 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("marriage_end_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("marriage_end_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_marriage_end/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->marriage_end_date,
                    $rows->REMARKS,
                    $rows->birth_date,
                    $rows->HHNO,
                    $rows->member_code,
                    $rows->fk_marriage_end_cause_one_code,
                    $rows->fk_marriage_end_cause_two_code,
                    $rows->fk_marriage_end_cause_three_code,
                    $rows->fk_marriage_end_type_code,
                    $rows->member_code_bride_groom,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_marriage_end();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_marriage_end() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("marriage_end_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("marriage_end_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function interview() {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Interview";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'interview';
        $data['editMethod'] = 'edit_interview';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';
        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/interview', $data);
        $this->load->view('includes/footer');
    }

    public function show_interview() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'household_master_id',
            2 => 'interview_date',
            3 => 'respondent_code',
            4 => 'split_date',
            5 => 'no_of_new_household',
            6 => 'merge_date',
            7 => 'remarks',
            8 => 'household_code',
            9 => 'any_birth_code',
            10 => 'any_concepton_code',
            11 => 'any_pregnancy_code',
            12 => 'any_death_code',
            13 => 'any_hosp_code',
            14 => 'memberCheck_code',
            15 => 'any_vaccin_code',
            16 => 'any_marriage_start_code',
            17 => 'any_marriage_end_code',
            18 => 'any_migration_in_code',
            19 => 'any_migration_out_code',
            20 => 'fk_interview_status_code',
            21 => 'fk_interviewer_code',
            22 => 'fk_responded_type_code',
            23 => 'is_household_split_code',
            24 => 'is_household_merge_code',
            25 => 'any_asset_code',
            26 => 'any_education_code',
            27 => 'any_occupation_code',
            28 => 'any_relation_code',
            29 => 'insertedDate',
            30 => 'insertedTime',
            31 => 'insertedBy_name',
            32 => 'updatedDate',
            33 => 'updatedTime',
            34 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("interview_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("interview_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_interview/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->household_master_id,
                    $rows->interview_date,
                    $rows->respondent_code,
                    $rows->split_date,
                    $rows->no_of_new_household,
                    $rows->merge_date,
                    $rows->remarks,
                    $rows->household_code,
                    $rows->any_birth_code,
                    $rows->any_concepton_code,
                    $rows->any_pregnancy_code,
                    $rows->any_death_code,
                    $rows->any_hosp_code,
                    $rows->memberCheck_code,
                    $rows->any_vaccin_code,
                    $rows->any_marriage_start_code,
                    $rows->any_marriage_end_code,
                    $rows->any_migration_in_code,
                    $rows->any_migration_out_code,
                    $rows->fk_interview_status_code,
                    $rows->fk_interviewer_code,
                    $rows->fk_responded_type_code,
                    $rows->is_household_split_code,
                    $rows->is_household_merge_code,
                    $rows->any_asset_code,
                    $rows->any_education_code,
                    $rows->any_occupation_code,
                    $rows->any_relation_code,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_interview();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_interview() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("interview_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("interview_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_interview($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $household_master_id = $this->input->get('household_master_id', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Interview";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_interview';
        $data['shortName'] = "Interview";
        $data['boxTitle'] = 'List';

        $data['interview_info'] = $this->modelName->interview_info($id, $this->config->item('householdVisitTable'));

//        echo "<pre/>";
//        print_r($data['conception_info']); exit();

        $data['interview_status'] = $this->modelName->getLookUpList($this->config->item('interviewstatus'));
        $data['interview_code'] = $this->modelName->getLookUpList($this->config->item('interviewercode'));
        $data['respondent_typ'] = $this->modelName->getLookUpList($this->config->item('respondent_typ'));
        $data['presentMemberList'] = $this->modelName->getMemberMasterPresentList($household_master_id);
//                echo "<pre/>";
//        print_r($data['presentMemberList']); exit();

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_interview', $data);
        $this->load->view('includes/footer');
    }

    function update_interview() {

        $baseID = $this->input->get('baseID', TRUE);
        $household_master_id = $this->input->post('household_master_id', true);
        $householdVisitID = $this->input->post('householdVisitID', true);
        $contactNumber = $this->input->post('contactNumber', true);
        $remarks = $this->input->post('remarks', true);

        $fk_responded_type = $this->input->post('fk_responded_type', true);
        $respondent_code = $this->input->post('respondent_code', true);
        $fk_interviewer = $this->input->post('fk_interviewer', true);
        $fk_interview_status = $this->input->post('fk_interview_status', true);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('fk_interview_status', 'Interview status', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_interviewer', 'Interviewer Name', 'trim|required|numeric');
        $this->form_validation->set_rules('respondent_code', 'Respondent Code', 'trim|required');
        $this->form_validation->set_rules('fk_responded_type', 'Respondent Type', 'trim|required|numeric');
        $this->form_validation->set_rules('contactNumber', 'Contact Number', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $this->db->trans_start();

            try {

                $visitData = array(
                    'fk_responded_type' => $fk_responded_type,
                    'respondent_code' => $respondent_code,
                    'fk_interviewer' => $fk_interviewer,
                    'fk_interview_status' => $fk_interview_status,
                    'remarks' => $remarks,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($visitData, $householdVisitID, $this->config->item('householdVisitTable'));

                $masterData = array(
                    'contact_number' => $contactNumber,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($masterData, $household_master_id, $this->config->item('householdMasterTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while save.');
            }

            $this->db->trans_commit();

            $this->session->set_flashdata('success', 'interview Info updated successfully.');
        }

        if ($this->input->post('update_exit')) {
            redirect($this->controller . '/interview' . '?baseID=' . $baseID);
        }

        redirect($this->controller . '/edit_interview/' . $householdVisitID . '?baseID=' . $baseID . '&household_master_id=' . $household_master_id);
    }

    public function household_master() {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "household_master";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'household_master';
        $data['editMethod'] = 'edit_household_master';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['district'] = $this->modelName->getListType($this->config->item('districtTable'));

        $data['district_id'] = '';
        $data['thana_id'] = '';
        $data['slum_id'] = '';
        $data['slumarea_id'] = '';
        $data['round_no'] = '';

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('district_id');
            $this->session->unset_userdata('thana_id');
            $this->session->unset_userdata('slum_id');
            $this->session->unset_userdata('slumarea_id');
            $this->session->unset_userdata('round_no');
            $data['district_id'] = '';
            $data['thana_id'] = '';
            $data['slum_id'] = '';
            $data['slumarea_id'] = '';
            $data['round_no'] = '';
        }


        $district_id = $this->input->post('district_id');
        $data['district_id'] = $this->session->userdata('district_id');
        $thana_id = $this->input->post('thana_id');
        $data['thana_id'] = $this->session->userdata('thana_id');
        $slum_id = $this->input->post('slum_id');
        $data['slum_id'] = $this->session->userdata('slum_id');
        $slumarea_id = $this->input->post('slumarea_id');
        $data['slumarea_id'] = $this->session->userdata('slumarea_id');
        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('district_id', $district_id);
            $data['district_id'] = $this->session->userdata('district_id');
            $this->session->set_userdata('thana_id', $thana_id);
            $data['thana_id'] = $this->session->userdata('thana_id');
            $this->session->set_userdata('slum_id', $slum_id);
            $data['slum_id'] = $this->session->userdata('slum_id');
            $this->session->set_userdata('slumarea_id', $slumarea_id);
            $data['slumarea_id'] = $this->session->userdata('slumarea_id');
            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }


        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/household_master', $data);
        $this->load->view('includes/footer');
    }

    public function show_household_master() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'HHNO',
            2 => 'MOB1',
            3 => 'houseCount',
            4 => 'BARINO',
            5 => 'BARIWALA',
            6 => 'HEADNM',
            7 => 'LONGLIVY',
            8 => 'LONGLIVM',
            9 => 'LEFTPAD',
            10 => 'entry_date',
            11 => 'round_master_id_entry_round',
            12 => 'migration_reason_oth',
            13 => 'extinct_date',
            14 => 'round_master_id_extinct_round',
            15 => 'location_id',
            16 => 'location_split_id',
            17 => 'district_code',
            18 => 'thana_code',
            19 => 'slum_code',
            20 => 'slum_area_code',
            21 => 'fk_entry_type_code',
            22 => 'fk_migration_reason_code',
            23 => 'country_from_code',
            24 => 'district_from_code',
            25 => 'thana_from_code',
            26 => 'slum_from_code',
            27 => 'slum_area_from_code',
            28 => 'fk_extinct_type_code',
            29 => 'fk_contract_type_code',
            30 => 'fk_family_type_code',
            31 => 'fk_study_design_code',
            32 => 'member_code_last_head',
            33 => 'insertedDate',
            34 => 'insertedTime',
            35 => 'insertedBy_name',
            36 => 'updatedDate',
            37 => 'updatedTime',
            38 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $district_id = $this->session->userdata('district_id') ? $this->session->userdata('district_id') : 0;
        $thana_id = $this->session->userdata('thana_id') ? $this->session->userdata('thana_id') : 0;
        $slum_id = $this->session->userdata('slum_id') ? $this->session->userdata('slum_id') : 0;
        $slumarea_id = $this->session->userdata('slumarea_id') ? $this->session->userdata('slumarea_id') : 0;
        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        $this->db->select('*');
        $this->db->from("household_master_view");


        if ($district_id > 0) {
            $this->db->where(array('fk_district_id' => $district_id));
        }
        if ($thana_id > 0) {
            $this->db->where(array('fk_thana_id' => $thana_id));
        }
        if ($slum_id > 0) {
            $this->db->where(array('fk_slum_id' => $slum_id));
        }
        if ($slumarea_id > 0) {
            $this->db->where(array('fk_slum_area_id' => $slumarea_id));
        }
        if ($round_no > 0) {
            $this->db->where(array('round_master_id_entry_round' => $round_no));
        }

        $all_data_list = $this->db->get();

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_household_master/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->HHNO,
                    $rows->MOB1,
                    $rows->houseCount,
                    $rows->BARINO,
                    $rows->BARIWALA,
                    $rows->HEADNM,
                    $rows->LONGLIVY,
                    $rows->LONGLIVM,
                    $rows->LEFTPAD,
                    $rows->entry_date,
                    $rows->round_master_id_entry_round,
                    $rows->migration_reason_oth,
                    $rows->extinct_date,
                    $rows->round_master_id_extinct_round,
                    $rows->location_id,
                    $rows->location_split_id,
                    $rows->district_code,
                    $rows->thana_code,
                    $rows->slum_code,
                    $rows->slum_area_code,
                    $rows->fk_entry_type_code,
                    $rows->fk_migration_reason_code,
                    $rows->country_from_code,
                    $rows->district_from_code,
                    $rows->thana_from_code,
                    $rows->slum_from_code,
                    $rows->slum_area_from_code,
                    $rows->fk_extinct_type_code,
                    $rows->fk_contract_type_code,
                    $rows->fk_family_type_code,
                    $rows->fk_study_design_code,
                    $rows->member_code_last_head,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_household_master();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_household_master() {

        $district_id = $this->session->userdata('district_id') ? $this->session->userdata('district_id') : 0;
        $thana_id = $this->session->userdata('thana_id') ? $this->session->userdata('thana_id') : 0;
        $slum_id = $this->session->userdata('slum_id') ? $this->session->userdata('slum_id') : 0;
        $slumarea_id = $this->session->userdata('slumarea_id') ? $this->session->userdata('slumarea_id') : 0;
        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        $this->db->select("COUNT(*) as num");
        $this->db->from("household_master_view");


        if ($district_id > 0) {
            $this->db->where(array('fk_district_id' => $district_id));
        }
        if ($thana_id > 0) {
            $this->db->where(array('fk_thana_id' => $thana_id));
        }
        if ($slum_id > 0) {
            $this->db->where(array('fk_slum_id' => $slum_id));
        }
        if ($slumarea_id > 0) {
            $this->db->where(array('fk_slum_area_id' => $slumarea_id));
        }
        if ($round_no > 0) {
            $this->db->where(array('round_master_id_entry_round' => $round_no));
        }

        $query = $this->db->get();

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_household_master($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $household_master_id = $this->input->get('household_master_id', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Household master";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_household_master';
        $data['shortName'] = "Household master";
        $data['boxTitle'] = 'List';

        $data['household_master_info'] = $this->modelName->household_master_info($id, $this->config->item('householdMasterTable'));

        //$data['division'] = $this->modelName->getListType($this->config->item('divTable'));
        $data['district'] = $this->modelName->getListType($this->config->item('districtTable'));
        $data['district2'] = $this->modelName->getListType($this->config->item('districtTable'));
        $data['country'] = $this->modelName->getListType($this->config->item('countryTable'));
        // $data['thana']    = $this->modelName->getListType($this->config->item('upazilaTable'));
        //$data['slum']     = $this->modelName->getListType($this->config->item('slumTable'));
        // $data['slumarea'] = $this->modelName->getListType($this->config->item('slumAreaTable'));

        $data['entryType'] = $this->modelName->getLookUpListSpecific($this->config->item('hhentrytype'), array('bls', 'min', 'intin'));
        $data['migrationReason'] = $this->modelName->getLookUpList($this->config->item('migReason'));
        $data['hhcontacttyp'] = $this->modelName->getLookUpList($this->config->item('hhcontacttyp'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_household_master', $data);
        $this->load->view('includes/footer');
    }

    function update_household_master() {
        $this->load->library('form_validation');

        $id = $this->input->post('id');
        $baseID = $this->input->get('baseID', TRUE);

        //$this->form_validation->set_rules('districtID','District Name','trim|required|numeric');
        // $this->form_validation->set_rules('thanaID','Upazila Name','trim|required|numeric');
        // $this->form_validation->set_rules('slumID','Slum Name','trim|required|numeric');
        // $this->form_validation->set_rules('slumAreaID','Slum Area Name','trim|required|numeric');
        $this->form_validation->set_rules('bariwallaName', 'Bariwalla Name', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('bariNumber', 'Bari Number', 'trim|required|max_length[10]|xss_clean');
        $this->form_validation->set_rules('headName', 'Head Name', 'trim|required|max_length[255]|xss_clean');
        $this->form_validation->set_rules('livingYear', 'Living Year', 'trim|required|max_length[2]|xss_clean');
        $this->form_validation->set_rules('livingMonth', 'Living Month', 'trim|required|max_length[2]|xss_clean');

        $this->form_validation->set_rules('leftSlum', 'Left Slum', 'trim|required|numeric');
        $this->form_validation->set_rules('entryType', 'Entry Type', 'trim|required|numeric');

        $this->form_validation->set_rules('entryDate', 'Entry Date', 'trim|required');
        $this->form_validation->set_rules('contactNumber', 'Contact Number', 'trim|required|max_length[100]|xss_clean');
        $this->form_validation->set_rules('contactSource', 'Contact Source', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {

            // $districtID = $this->input->post('districtID',true);
            // $thanaID = $this->input->post('thanaID',true);
            // $slumID = $this->input->post('slumID',true);
            //$slumAreaID = $this->input->post('slumAreaID',true);
            $bariwallaName = $this->input->post('bariwallaName', true);
            $bariNumber = $this->input->post('bariNumber', true);
            $headName = $this->input->post('headName', true);
            $livingYear = $this->input->post('livingYear', true);
            $livingMonth = $this->input->post('livingMonth', true);
            $leftSlum = $this->input->post('leftSlum', true);
            $entryType = $this->input->post('entryType', true);
            $entryDate = $this->input->post('entryDate', true);
            $contactNumber = $this->input->post('contactNumber', true);
            $contactSource = $this->input->post('contactSource', true);
            $household_code = $this->input->post('household_code', true);


            if (!empty($entryDate)) {
                $parts1 = explode('/', $entryDate);
                $new_entryDate = $parts1[2] . '-' . $parts1[1] . '-' . $parts1[0];
            }


            $migrationReason = 0;
            $countryID = 0;
            $migDistrictID = 0;
            $migThanaID = 0;
            $slumIDFrom = 0;
            $slumAreaIDFrom = 0;

            $migreasonOth = '';



            if ($entryType == $this->config->item('HHEntyMigIn')) { // migration in
                $migrationReason = $this->input->post('migrationReason', true);
                $countryID = $this->input->post('countryID', true);
                $migDistrictID = $this->input->post('migDistrictID', true);
                $migThanaID = $this->input->post('migThanaID', true);

                if ($migrationReason == 12) {
                    $migreasonOth = $this->input->post('migreasonOth', true);
                }
            }


            if ($entryType == $this->config->item('HHEntyIntIn')) { // int in
                $migrationReason = $this->input->post('migrationReason', true);
                $countryID = $this->input->post('countryID', true);
                $migDistrictID = $this->input->post('migDistrictID', true);
                $migThanaID = $this->input->post('migThanaID', true);

                $slumIDFrom = $this->input->post('slumIDFrom', true);
                $slumAreaIDFrom = $this->input->post('slumAreaIDFrom', true);

                if ($migrationReason == 12) {
                    $migreasonOth = $this->input->post('migreasonOth', true);
                }
            }


            $this->db->trans_start();

            try {


                $IdInfo = array(
                    'contact_number' => $contactNumber,
                    // 'fk_district_id'=>$districtID, 
                    // 'fk_thana_id'=>$thanaID, 
                    // 'fk_slum_id'=>$slumID, 
                    // 'fk_slum_area_id'=>$slumAreaID, 
                    'barino' => $bariNumber,
                    'bariwalla_name' => $bariwallaName,
                    'household_head_name' => $headName,
                    'longlivy' => $livingYear,
                    'longlivm' => $livingMonth,
                    'leftpad' => $leftSlum,
                    'fk_entry_type' => $entryType,
                    'entry_date' => $new_entryDate,
                    'fk_migration_reason' => $migrationReason,
                    'migration_reason_oth' => $migreasonOth,
                    'fk_country_id_from' => $countryID,
                    'fk_district_id_from' => $migDistrictID,
                    'fk_thana_id_from' => $migThanaID,
                    'fk_slum_id_from' => $slumIDFrom,
                    'fk_slumArea_id_from' => $slumAreaIDFrom,
                    'fk_contract_type' => $contactSource,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $result = $this->modelName->UpdateInfo($IdInfo, $id, $this->config->item('householdMasterTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating household.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Household master info updated successfully');

            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/household_master' . '?baseID=' . $baseID);
            }

            redirect($this->controller . '/edit_household_master/' . $id . '?baseID=' . $baseID);
        }
    }

    public function household_head() {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Household head";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'household_head';
        $data['editMethod'] = 'edit_household_head';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';
        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/household_head', $data);
        $this->load->view('includes/footer');
    }

    public function show_household_head_view() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'change_date',
            2 => 'is_last_head',
            3 => 'birth_date',
            4 => 'household_code',
            5 => 'member_code',
            6 => 'fk_hhh_cause_code',
            7 => 'insertedDate',
            8 => 'insertedTime',
            9 => 'insertedBy_name',
            10 => 'updatedDate',
            11 => 'updatedTime',
            12 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("household_head_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("household_head_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_household_head/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->change_date,
                    $rows->is_last_head,
                    $rows->birth_date,
                    $rows->household_code,
                    $rows->member_code,
                    $rows->fk_hhh_cause_code,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_household_head_view();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_household_head_view() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("household_head_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("household_head_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_household_head($id) {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Household head";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_household_head';
        $data['shortName'] = "Household head";
        $data['boxTitle'] = 'List';

        $data['household_head_info'] = $this->modelName->household_head_info($id, $this->config->item('memberHeadTable'));
        $data['hh_change_reason'] = $this->modelName->getLookUpList($this->config->item('hh_change_reason'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_household_head', $data);
        $this->load->view('includes/footer');
    }

    function update_household_head() {

        $household_head_id = $this->input->post('household_head_id', true);

//        $this->load->library('form_validation');

        $getCurrentRound = $this->modelName->getCurrentRound();

        if ($this->input->post('relationType', true) != NULL) {
            $relationType = $this->input->post('relationType', true);
        } else {
            $relationType = 27;
        }

        $baseID = $this->input->get('baseID', TRUE);

        if ($getCurrentRound->active == 0) {
            $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
            redirect($this->controller . '/edit_household_head/' . $household_head_id . '?baseID=' . $baseID);
        }



        $fk_hhh_cause = $this->input->post('fk_hhh_cause', true);
        $hhdate = $this->input->post('hhdate', true);

        $new_hhdate = null;

        if (!empty($hhdate)) {
            $parts3 = explode('/', $hhdate);
            $new_hhdate = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
        }

        $this->db->trans_start();

        try {
            $householdHeadInfo = array(
                'change_date' => $new_hhdate,
                'fk_hhh_cause' => $fk_hhh_cause,
                'updateBy' => $this->vendorId,
                'updatedOn' => date('Y-m-d H:i:s')
            );
            $this->modelName->UpdateInfo($householdHeadInfo, $household_head_id, $this->config->item('memberHeadTable'));
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Error occurred while updating Relation.');
        }

        $this->db->trans_commit();

        $this->session->set_flashdata('success', 'Household head info updated successfully.');

        if ($this->input->post('update_exit')) {
            redirect($this->controller . '/household_head' . '?baseID=' . $baseID);
        }

        redirect($this->controller . '/edit_household_head/' . $household_head_id . '?baseID=' . $baseID);
    }

    function update_conception() {
        $conceptionID = $this->input->post('conceptionID', true);
        $getCurrentRound = $this->modelName->getCurrentRound();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('conception_date', 'Conception Date', 'trim|required');
        $this->form_validation->set_rules('fk_conception_plan', 'Conception plan', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_conception_order', 'Conception order', 'trim|required|numeric');

        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_conception/' . $conceptionID . '?baseID=' . $baseID);
        } else {
            if ($getCurrentRound->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_conception/' . $conceptionID . '?baseID=' . $baseID);
            }

            if (!empty($this->input->post('conception_date'))) {
                $parts3 = explode('/', $this->input->post('conception_date'));
                $data['conception_date'] = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }
            $data['fk_conception_plan'] = $this->input->post('fk_conception_plan', true);
            $data['fk_conception_order'] = $this->input->post('fk_conception_order', true);
            $data['updateBy'] = $this->vendorId;
            $data['updatedOn'] = date('Y-m-d H:i:s');


            try {

                $this->modelName->UpdateInfo($data, $conceptionID, $this->config->item('conceptionTable'));
            } catch (Exception $e) {
                $this->session->set_flashdata('error', 'Error occurred while updating Conception.');
                redirect($this->controller . '/edit_conception/' . $conceptionID . '?baseID=' . $baseID);
            }

            $this->session->set_flashdata('success', 'Member Conception Info updated successfully.');
            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/conception' . '?baseID=' . $baseID);
            }
            redirect($this->controller . '/edit_conception/' . $conceptionID . '?baseID=' . $baseID);
        }
    }

    function update_education() {

        $educationID = $this->input->post('educationID', true);

        $getCurrentRound = $this->modelName->getCurrentRound();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('educationType', 'Education Type', 'trim|required|numeric');

        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_education/' . $educationID . '?baseID=' . $baseID);
        } else {
            if ($getCurrentRound->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_education/' . $educationID . '?baseID=' . $baseID);
            }

            $data['fk_education_type'] = $this->input->post('educationType', true);
            $data['year_of_education'] = ($this->input->post('yearOfEdu') == NULL) ? 0 : $this->input->post('yearOfEdu');
            $data['updateBy'] = $this->vendorId;
            $data['updatedOn'] = date('Y-m-d H:i:s');


            try {

                $this->modelName->UpdateInfo($data, $educationID, $this->config->item('memberEducationTable'));
            } catch (Exception $e) {
                $this->session->set_flashdata('error', 'Error occurred while updating Conception.');
                redirect($this->controller . '/edit_education/' . $educationID . '?baseID=' . $baseID);
            }

            $this->session->set_flashdata('success', 'Member Education Info updated successfully.');
            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/education' . '?baseID=' . $baseID);
            }
            redirect($this->controller . '/edit_education/' . $educationID . '?baseID=' . $baseID);
        }
    }

    function update_relation() {

        $household_master_id = $this->input->post('household_master_id', true);
        $round_master_id = $this->input->post('round_master_id', true);
        $relationID = $this->input->post('relationID', true);
        $member_master_id = $this->input->post('member_master_id', true);

//        $this->load->library('form_validation');

        $getCurrentRound = $this->modelName->getCurrentRound();

        if ($this->input->post('relationType', true) != NULL) {
            $relationType = $this->input->post('relationType', true);
        } else {
            $relationType = 27;
        }

        $baseID = $this->input->get('baseID', TRUE);

        if ($getCurrentRound->active == 0) {
            $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
            redirect($this->controller . '/edit_relation/' . $relationID . '?baseID=' . $baseID . '&household_master_id=' . $household_master_id . '&member_master_id=' . $member_master_id . '&round_master_id=' . $round_master_id . '&fk_relation=' . $relationType);
        }



        $fk_hhh_cause = $this->input->post('fk_hhh_cause', true);
        $hhdate = $this->input->post('hhdate', true);

        $new_hhdate = null;

        if (!empty($hhdate)) {
            $parts3 = explode('/', $hhdate);
            $new_hhdate = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
        }

        $this->db->trans_start();

        try {

            if ($relationType != 27) {
                $IdInfo = array(
                    'fk_relation' => $relationType,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($IdInfo, $relationID, $this->config->item('memberRelationTable'));
            } else if ($relationType == 27) {
                $householdHeadInfo = array(
                    'change_date' => $new_hhdate,
                    'fk_hhh_cause' => $fk_hhh_cause,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo_HeadTable($householdHeadInfo, $household_master_id, $member_master_id, $round_master_id, $this->config->item('memberHeadTable'));
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Error occurred while updating Relation.');
        }

        $this->db->trans_commit();

        $this->session->set_flashdata('success', 'Member Relation updated successfully.');

        if ($this->input->post('update_exit')) {
            redirect($this->controller . '/relation' . '?baseID=' . $baseID);
        }

        redirect($this->controller . '/edit_relation/' . $relationID . '?baseID=' . $baseID . '&household_master_id=' . $household_master_id . '&member_master_id=' . $member_master_id . '&round_master_id=' . $round_master_id . '&fk_relation=' . $relationType);
    }

    public function death() {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = $this->pageTitle;
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'death';
        $data['editMethod'] = 'edit_death';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

//        $data['list_fields'] = $this->modelName->all_internal_in_info(1, $this->config->item('migrationInTable'), 1);
//        
//        foreach ($data['list_fields'] as $list_field) {
//                                        if ($list_field != 'id')
//                                            echo '<th>' . $list_field . '</th>';
//                                    }
//        echo "<pre/>";
//        print_r($data['list_fields']); exit();
//                                    exit();

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/death', $data);
        $this->load->view('includes/footer');
    }

    public function show_death_view() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'DTHDT',
            2 => 'death_time',
            3 => 'DOB',
            4 => 'HHNO',
            5 => 'member_code',
            6 => 'DTHCAUSE',
            7 => 'fk_death_place_code',
            8 => 'DTH_TYP',
            9 => 'DTH_CAUS',
            10 => 'insertedDate',
            11 => 'insertedTime',
            12 => 'insertedBy_name',
            13 => 'updatedDate',
            14 => 'updatedTime',
            15 => 'updateBy_name'
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("death_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("death_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "Reports/edit_death/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->DTHDT,
                    $rows->death_time,
                    $rows->DOB,
                    $rows->HHNO,
                    $rows->member_code,
                    $rows->DTHCAUSE,
                    $rows->fk_death_place_code,
                    $rows->DTH_TYP,
                    $rows->DTH_CAUS,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_death_view();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_death_view() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("death_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("death_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_death($id) {

        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Death";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_death';
        $data['shortName'] = "Death";
        $data['boxTitle'] = 'List';

        $data['death_info'] = $this->modelName->death_info($id, $this->config->item('deathTable'));

        $data['member_death_cause'] = $this->modelName->getLookUpList($this->config->item('member_death_cause'));
        $data['member_death_place'] = $this->modelName->getLookUpList($this->config->item('member_death_place'));
        $data['type_of_death'] = $this->modelName->getLookUpList($this->config->item('type_of_death'));
        $data['death_confirm_by'] = $this->modelName->getLookUpList($this->config->item('death_confirm_by'));

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_death', $data);
        $this->load->view('includes/footer');
    }

    function update_death() {

        $deathID = $this->input->post('deathID', true);
        $member_master_id = $this->input->post('member_master_id', true);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('deathDate', 'Death Date', 'trim|required');
        $this->form_validation->set_rules('fk_death_place', 'Relation', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_death_cause', 'Relation', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_death_type', 'Relation', 'trim|required|numeric');
        $this->form_validation->set_rules('fk_death_confirmby', 'Relation', 'trim|required|numeric');



        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_death/' . $deathID . '?baseID=' . $baseID);
        } else {

            if ($this->getCurrentRound()[0]->active == 0) {

                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_death/' . $deathID . '?baseID=' . $baseID);
            }


            $fk_death_place = $this->input->post('fk_death_place', true);
            $fk_death_cause = $this->input->post('fk_death_cause', true);
            $fk_death_type = $this->input->post('fk_death_type', true);
            $fk_death_confirmby = $this->input->post('fk_death_confirmby', true);
            $deathtime = $this->input->post('deathtime', true);
            $deathDate = $this->input->post('deathDate', true);

            $new_deathDate = null;

            if (!empty($deathDate)) {
                $parts3 = explode('/', $deathDate);
                $new_deathDate = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $this->db->trans_start();

            try {

                $whereMemember = array('id' => $member_master_id);
                $member_household_id_last = $this->db->select('member_household_id_last')->from($this->config->item('memberMasterTable'))->where($whereMemember)->get()->row()->member_household_id_last;

                //member household 

                $dethUpdate = array(
                    'exit_date' => $new_deathDate,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->db->where('id', $member_household_id_last);
                $this->db->where('member_master_id', $member_master_id);
                $this->db->update($this->config->item('memberHouseholdTable'), $dethUpdate);


                $IdInfo = array(
                    'death_date' => $new_deathDate,
                    'fk_death_place' => $fk_death_place,
                    'fk_death_cause' => $fk_death_cause,
                    'fk_death_type' => $fk_death_type,
                    'fk_death_confirmby' => $fk_death_confirmby,
                    'transfer_complete' => 'No',
                    'death_time' => $deathtime,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($IdInfo, $deathID, $this->config->item('deathTable'));


                $memberUpdate = array(
                    'is_died' => 1,
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($memberUpdate, $member_master_id, $this->config->item('memberMasterTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating Death.');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Member Death updated successfully.');
            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/death' . '?baseID=' . $baseID);
            }
            redirect($this->controller . '/edit_death/' . $deathID . '?baseID=' . $baseID);
        }
    }

    public function immunization() {
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = $this->pageTitle;
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'immunization';
        $data['editMethod'] = 'edit_immunization';
        $data['shortName'] = $this->pageShortName;
        $data['boxTitle'] = 'List';

        $data['all_round_info'] = $this->modelName->all_round_info($this->config->item('roundTable'));
        $data['round_no'] = '';

        $round_no = '';

        if ($this->input->post('Clear')) {
            $this->session->unset_userdata('round_no');
            $data['round_no'] = '';
        }


        $round_no = $this->input->post('round_no');
        $data['round_no'] = $this->session->userdata('round_no');

        if ($this->input->post('search')) {

            $this->session->set_userdata('round_no', $round_no);
            $data['round_no'] = $this->session->userdata('round_no');
        }

        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/immunization', $data);
        $this->load->view('includes/footer');
    }

    public function show_immunization() {

        $baseID = $this->input->get('baseID', TRUE);

        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = $this->input->post("length");
        $order = $this->input->post("order");
        $search = $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc") {
            $dir = "desc";
        }
        $valid_columns = array(
            0 => 'id',
            1 => 'DOB',
            2 => 'HHNO',
            3 => 'member_code',
            4 => 'CH1_code',
            5 => 'BCG',
            6 => 'BCGFROM_code',
            7 => 'BCGOTH',
            8 => 'PENTA1',
            9 => 'PENTA1FROM_code',
            10 => 'PENTA1OTH',
            11 => 'PENTA2',
            12 => 'PENTA2FROM_code',
            13 => 'PENTA2OTH',
            14 => 'PENTA3',
            15 => 'PENTA3FROM_code',
            16 => 'PENTA3OTH',
            17 => 'PCV1',
            18 => 'PCV1FROM_code',
            19 => 'PCV1OTH',
            20 => 'PCV2',
            21 => 'PCV2FROM_code',
            22 => 'PCV2OTH',
            23 => 'PPV3',
            24 => 'PPV3FROM_code',
            25 => 'PPV3OTH',
            26 => 'OPV1',
            27 => 'OPV1FROM_code',
            28 => 'OPV1OTH',
            29 => 'OPV2',
            30 => 'OPV2FROM_code',
            31 => 'OPV2OTH',
            32 => 'OPV3',
            33 => 'OPV3FROM_code',
            34 => 'OPV3OTH',
            35 => 'MR1',
            36 => 'MR1FROM_code',
            37 => 'MR1OTH',
            38 => 'MR2',
            39 => 'MR2FROM_code',
            40 => 'MR2OTH',
            41 => 'FIPV1',
            42 => 'FIPV1FROM_code',
            43 => 'FIPV1OTH',
            44 => 'FIPV2',
            45 => 'FIPV2FROM_code',
            46 => 'FIPV2OTH',
            47 => 'FIPV3',
            48 => 'FIPV3FROM_code',
            49 => 'FIPV3OTH',
            50 => 'VITA1',
            51 => 'VITA1FROM_code',
            52 => 'VITA1OTH',
            53 => 'VITA2',
            54 => 'VITA2FROM_code',
            55 => 'VITA2OTH',
            56 => 'interview_status_code',
            57 => 'followup_exit_date',
            58 => 'folowup_exit_round',
            59 => 'Q20_code',
            60 => 'Q21_code',
            61 => 'Q22_code',
            62 => 'Q22OTH',
            63 => 'CODER',
            64 => 'REMARKS',
            65 => 'insertedDate',
            66 => 'insertedTime',
            67 => 'insertedBy_name',
            68 => 'updatedDate',
            69 => 'updatedTime',
            70 => 'updateBy_name');

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }
        if ($order != null) {
            $this->db->order_by($order, $dir);
        }

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $this->db->like($sterm, $search);
                } else {
                    $this->db->or_like($sterm, $search);
                }
                $x++;
            }
        }
        $this->db->limit($length, $start);

//$this->db->limit(($length != '' && $length != '-1')? $length : 0, ($start)? $start : 0);


        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {
            $all_data_list = $this->db->get_where("immunization_view", array('round_master_id' => $round_no));
        } else {
            $all_data_list = $this->db->get("immunization_view");
        }

        $data = array();

        if (!empty($all_data_list)) {

            foreach ($all_data_list->result() as $rows) {
                $edit_link = "<a href='" . base_url() . "reports/edit_immunization/" . $rows->id . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
                $data[] = array(
                    $edit_link,
                    $rows->DOB,
                    $rows->HHNO,
                    $rows->member_code,
                    $rows->CH1_code,
                    $rows->BCG,
                    $rows->BCGFROM_code,
                    $rows->BCGOTH,
                    $rows->PENTA1,
                    $rows->PENTA1FROM_code,
                    $rows->PENTA1OTH,
                    $rows->PENTA2,
                    $rows->PENTA2FROM_code,
                    $rows->PENTA2OTH,
                    $rows->PENTA3,
                    $rows->PENTA3FROM_code,
                    $rows->PENTA3OTH,
                    $rows->PCV1,
                    $rows->PCV1FROM_code,
                    $rows->PCV1OTH,
                    $rows->PCV2,
                    $rows->PCV2FROM_code,
                    $rows->PCV2OTH,
                    $rows->PPV3,
                    $rows->PPV3FROM_code,
                    $rows->PPV3OTH,
                    $rows->OPV1,
                    $rows->OPV1FROM_code,
                    $rows->OPV1OTH,
                    $rows->OPV2,
                    $rows->OPV2FROM_code,
                    $rows->OPV2OTH,
                    $rows->OPV3,
                    $rows->OPV3FROM_code,
                    $rows->OPV3OTH,
                    $rows->MR1,
                    $rows->MR1FROM_code,
                    $rows->MR1OTH,
                    $rows->MR2,
                    $rows->MR2FROM_code,
                    $rows->MR2OTH,
                    $rows->FIPV1,
                    $rows->FIPV1FROM_code,
                    $rows->FIPV1OTH,
                    $rows->FIPV2,
                    $rows->FIPV2FROM_code,
                    $rows->FIPV2OTH,
                    $rows->FIPV3,
                    $rows->FIPV3FROM_code,
                    $rows->FIPV3OTH,
                    $rows->VITA1,
                    $rows->VITA1FROM_code,
                    $rows->VITA1OTH,
                    $rows->VITA2,
                    $rows->VITA2FROM_code,
                    $rows->VITA2OTH,
                    $rows->interview_status_code,
                    $rows->followup_exit_date,
                    $rows->folowup_exit_round,
                    $rows->Q20_code,
                    $rows->Q21_code,
                    $rows->Q22_code,
                    $rows->Q22OTH,
                    $rows->CODER,
                    $rows->REMARKS,
                    $rows->insertedDate,
                    $rows->insertedTime,
                    $rows->insertedBy_name,
                    $rows->updatedDate,
                    $rows->updatedTime,
                    $rows->updateBy_name
                );
            }
        }
        $total_all_data_list = $this->totalMembers_immunization();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_all_data_list,
            "recordsFiltered" => $total_all_data_list,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalMembers_immunization() {

        $round_no = $this->session->userdata('round_no') ? $this->session->userdata('round_no') : 0;

        if ($round_no > 0) {

            $query = $this->db->select("COUNT(*) as num")->get_where("immunization_view", array('round_master_id' => $round_no));
        } else {
            $query = $this->db->select("COUNT(*) as num")->get("immunization_view");
        }

        $result = $query->row();
        if (isset($result))
            return $result->num;
        return 0;
    }

    public function edit_immunization($id) {
//        echo $id; exit();
        $baseID = $this->input->get('baseID', TRUE);
        $this->global['menu'] = $this->menuModel->getMenu($this->role);

        $this->global['pageTitle'] = $this->config->item('prefix') . ' : ' . $this->pageTitle;
        $data['pageTitle'] = "Immunization";
        $data['controller'] = $this->controller;
        $data['actionMethod'] = 'update_immunization';
        $data['shortName'] = "Immunization";
        $data['boxTitle'] = 'List';

        $data['immunization_info'] = $this->modelName->immunization_info($id, $this->config->item('memberImmunizationTable'));

//        echo "<pre/>";
//        print_r($data['conception_info']); exit();

        $data['why_not_seen_card'] = $this->modelName->getLookUpList($this->config->item('why_not_seen_card'));
        $data['interview_status_immunization'] = $this->modelName->getLookUpList($this->config->item('interview_status_immunization'));
        $data['information_recorded_from'] = $this->modelName->getLookUpList($this->config->item('information_recorded_from'));
        $data['yes_no'] = $this->modelName->getLookUpList($this->config->item('yes_no'));


        $data['editPerm'] = $this->getPermission($baseID, $this->role, 'edit');

        $this->load->view('includes/header', $this->global);
        $this->load->view('includes/script');
        $this->load->view($this->controller . '/edit_immunization', $data);
        $this->load->view('includes/footer');
    }

    function update_immunization() {
        $immunizationID = $this->input->post('immunizationID', true);
        $getCurrentRound = $this->modelName->getCurrentRound();
        $member_master_id = $this->input->post('member_master_id', true);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('CH1', 'Did the child get any vaccine?', 'trim|required|numeric');
        $this->form_validation->set_rules('interview_status', 'Interview status', 'trim|required|numeric');
        $this->form_validation->set_rules('followup_exit_date', 'Child follow up exit date', 'trim|required');
        $this->form_validation->set_rules('folowup_exit_round', 'Child follow up exit round', 'trim|required|numeric');
        $this->form_validation->set_rules('Q20', 'Have the vaccination card?', 'trim|required|numeric');

        $baseID = $this->input->get('baseID', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->controller . '/edit_immunization/' . $immunizationID . '?baseID=' . $baseID);
        } else {
            if ($getCurrentRound->active == 0) {
                $this->session->set_flashdata('error', 'Currenty round is closed. Please wait until new round is open.');
                redirect($this->controller . '/edit_immunization/' . $immunizationID . '?baseID=' . $baseID);
            }

            $BCG = $this->input->post('BCG', true);
            $new_BCG = null;
            if (!empty($BCG)) {
                $parts3 = explode('/', $BCG);
                $new_BCG = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $PENTA1 = $this->input->post('PENTA1', true);
            $new_PENTA1 = null;
            if (!empty($PENTA1)) {
                $parts3 = explode('/', $PENTA1);
                $new_PENTA1 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $PENTA2 = $this->input->post('PENTA2', true);
            $new_PENTA2 = null;
            if (!empty($PENTA2)) {
                $parts3 = explode('/', $PENTA2);
                $new_PENTA2 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $PENTA3 = $this->input->post('PENTA3', true);
            $new_PENTA3 = null;
            if (!empty($PENTA3)) {
                $parts3 = explode('/', $PENTA3);
                $new_PENTA3 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $PCV1 = $this->input->post('PCV1', true);
            $new_PCV1 = null;
            if (!empty($PCV1)) {
                $parts3 = explode('/', $PCV1);
                $new_PCV1 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }
            $PCV2 = $this->input->post('PCV2', true);
            $new_PCV2 = null;
            if (!empty($PCV2)) {
                $parts3 = explode('/', $PCV2);
                $new_PCV2 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $PPV3 = $this->input->post('PPV3', true);
            $new_PPV3 = null;
            if (!empty($PPV3)) {
                $parts3 = explode('/', $PPV3);
                $new_PPV3 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $OPV1 = $this->input->post('OPV1', true);
            $new_OPV1 = null;
            if (!empty($OPV1)) {
                $parts3 = explode('/', $OPV1);
                $new_OPV1 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $OPV2 = $this->input->post('OPV2', true);
            $new_OPV2 = null;
            if (!empty($OPV2)) {
                $parts3 = explode('/', $OPV2);
                $new_OPV2 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $OPV3 = $this->input->post('OPV3', true);
            $new_OPV3 = null;
            if (!empty($OPV3)) {
                $parts3 = explode('/', $OPV3);
                $new_OPV3 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $MR1 = $this->input->post('MR1', true);
            $new_MR1 = null;
            if (!empty($MR1)) {
                $parts3 = explode('/', $MR1);
                $new_MR1 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $MR2 = $this->input->post('MR2', true);
            $new_MR2 = null;
            if (!empty($MR2)) {
                $parts3 = explode('/', $MR2);
                $new_MR2 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $FIPV1 = $this->input->post('FIPV1', true);
            $new_FIPV1 = null;
            if (!empty($FIPV1)) {
                $parts3 = explode('/', $FIPV1);
                $new_FIPV1 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $FIPV2 = $this->input->post('FIPV2', true);
            $new_FIPV2 = null;
            if (!empty($FIPV2)) {
                $parts3 = explode('/', $FIPV2);
                $new_FIPV2 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }
            $FIPV3 = $this->input->post('FIPV3', true);
            $new_FIPV3 = null;
            if (!empty($FIPV3)) {
                $parts3 = explode('/', $FIPV3);
                $new_FIPV3 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $VITA1 = $this->input->post('VITA1', true);
            $new_VITA1 = null;
            if (!empty($VITA1)) {
                $parts3 = explode('/', $VITA1);
                $new_VITA1 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }
            $VITA2 = $this->input->post('VITA2', true);
            $new_VITA2 = null;
            if (!empty($VITA2)) {
                $parts3 = explode('/', $VITA2);
                $new_VITA2 = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $followup_exit_date = $this->input->post('followup_exit_date', true);

            $new_followup_exit_date = null;
            if (!empty($followup_exit_date)) {
                $parts3 = explode('/', $followup_exit_date);
                $new_followup_exit_date = $parts3[2] . '-' . $parts3[1] . '-' . $parts3[0];
            }

            $Q20 = $this->input->post('Q20', true);
            $Q21 = 0;
            $Q22 = 0;
            if ($Q20 == 1) {
                $Q21 = $this->input->post('Q21', true);
            }
            $Q21 = $this->input->post('Q21', true);
            if ($Q21 == 1) {
                $Q22 = $this->input->post('Q22', true);
            }

            $Q22OTH = NULL;

            if ($Q22 == 436) {
                $Q22OTH = $this->input->post('Q22OTH', true);
            }
            $CH1 = $this->input->post('CH1', true);


            if ($CH1 == 1) {
                $info_first_part = array(
                    'BCG' => $new_BCG,
                    'BCGFROM' => $this->input->post('BCGFROM', true),
                    'BCGFROM' => $this->input->post('BCGFROM', true),
                    'BCGOTH' => $this->input->post('BCGOTH', true),
                    'PENTA1' => $new_PENTA1,
                    'PENTA1FROM' => $this->input->post('PENTA1FROM', true),
                    'PENTA1OTH' => $this->input->post('PENTA1OTH', true),
                    'PENTA2' => $new_PENTA2,
                    'PENTA2FROM' => $this->input->post('PENTA2FROM', true),
                    'PENTA2OTH' => $this->input->post('PENTA2OTH', true),
                    'PENTA3' => $new_PENTA3,
                    'PENTA3FROM' => $this->input->post('PENTA3FROM', true),
                    'PENTA3OTH' => $this->input->post('PENTA3OTH', true),
                    'PCV1' => $new_PCV1,
                    'PCV1FROM' => $this->input->post('PCV1FROM', true),
                    'PCV1OTH' => $this->input->post('PCV1OTH', true),
                    'PCV2' => $new_PCV2,
                    'PCV2FROM' => $this->input->post('PCV2FROM', true),
                    'PCV2OTH' => $this->input->post('PCV2OTH', true),
                    'PPV3' => $new_PPV3,
                    'PPV3FROM' => $this->input->post('PPV3FROM', true),
                    'PPV3OTH' => $this->input->post('PPV3OTH', true),
                    'OPV1' => $new_OPV1,
                    'OPV1FROM' => $this->input->post('OPV1FROM', true),
                    'OPV1OTH' => $this->input->post('OPV1OTH', true),
                    'OPV2' => $new_OPV2,
                    'OPV2FROM' => $this->input->post('OPV2FROM', true),
                    'OPV2OTH' => $this->input->post('OPV2OTH', true),
                    'OPV3' => $new_OPV3,
                    'OPV3FROM' => $this->input->post('OPV3FROM', true),
                    'OPV3OTH' => $this->input->post('OPV3OTH', true),
                    'MR1' => $new_MR1,
                    'MR1FROM' => $this->input->post('MR1FROM', true),
                    'MR1OTH' => $this->input->post('MR1OTH', true),
                    'MR2' => $new_MR2,
                    'MR2FROM' => $this->input->post('MR2FROM', true),
                    'MR2OTH' => $this->input->post('MR2OTH', true),
                    'FIPV1' => $new_FIPV1,
                    'FIPV1FROM' => $this->input->post('FIPV1FROM', true),
                    'FIPV1OTH' => $this->input->post('FIPV1OTH', true),
                    'FIPV2' => $new_FIPV2,
                    'FIPV2FROM' => $this->input->post('FIPV2FROM', true),
                    'FIPV2OTH' => $this->input->post('FIPV2OTH', true),
                    'FIPV3' => $new_FIPV3,
                    'FIPV3FROM' => $this->input->post('FIPV3FROM', true),
                    'FIPV3OTH' => $this->input->post('FIPV3OTH', true),
                    'VITA1' => $new_VITA1,
                    'VITA1FROM' => $this->input->post('VITA1FROM', true),
                    'VITA1OTH' => $this->input->post('VITA1OTH', true),
                    'VITA2' => $new_VITA2,
                    'VITA2FROM' => $this->input->post('VITA2FROM', true),
                    'VITA2OTH' => $this->input->post('VITA2OTH', true)
                );
            } else {
                $info_first_part = array(
                    'BCG' => NULL,
                    'BCGFROM' => NULL,
                    'BCGFROM' => NULL,
                    'BCGOTH' => NULL,
                    'PENTA1' => NULL,
                    'PENTA1FROM' => NULL,
                    'PENTA1OTH' => NULL,
                    'PENTA2' => NULL,
                    'PENTA2FROM' => NULL,
                    'PENTA2OTH' => NULL,
                    'PENTA3' => NULL,
                    'PENTA3FROM' => NULL,
                    'PENTA3OTH' => NULL,
                    'PCV1' => NULL,
                    'PCV1FROM' => NULL,
                    'PCV1OTH' => NULL,
                    'PCV2' => NULL,
                    'PCV2FROM' => NULL,
                    'PCV2OTH' => NULL,
                    'PPV3' => NULL,
                    'PPV3FROM' => NULL,
                    'PPV3OTH' => NULL,
                    'OPV1' => NULL,
                    'OPV1FROM' => NULL,
                    'OPV1OTH' => NULL,
                    'OPV2' => NULL,
                    'OPV2FROM' => NULL,
                    'OPV2OTH' => NULL,
                    'OPV3' => NULL,
                    'OPV3FROM' => NULL,
                    'OPV3OTH' => NULL,
                    'MR1' => NULL,
                    'MR1FROM' => NULL,
                    'MR1OTH' => NULL,
                    'MR2' => NULL,
                    'MR2FROM' => NULL,
                    'MR2OTH' => NULL,
                    'FIPV1' => NULL,
                    'FIPV1FROM' => NULL,
                    'FIPV1OTH' => NULL,
                    'FIPV2' => NULL,
                    'FIPV2FROM' => NULL,
                    'FIPV2OTH' => NULL,
                    'FIPV3' => NULL,
                    'FIPV3FROM' => NULL,
                    'FIPV3OTH' => NULL,
                    'VITA1' => NULL,
                    'VITA1FROM' => NULL,
                    'VITA1OTH' => NULL,
                    'VITA2' => NULL,
                    'VITA2FROM' => NULL,
                    'VITA2OTH' => NULL
                );
            }

            $this->db->trans_start();

            try {
                $info_second_part = array(
                    'CH1' => $this->input->post('CH1', true),
                    'Q20' => $Q20,
                    'Q21' => $Q21,
                    'Q22' => $Q22,
                    'Q22OTH' => $Q22OTH,
                    'interview_status' => $this->input->post('interview_status', true),
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $IdInfo = array_merge($info_first_part, $info_second_part);

                $this->modelName->UpdateInfo($IdInfo, $immunizationID, $this->config->item('memberImmunizationTable'));

                $fk_followup_exit_type = 0;

                if ($this->input->post('interview_status', true) == 437 || $this->input->post('interview_status', true) == 438) {
                    $fk_followup_exit_type = $this->input->post('interview_status', true);
                }

                $memberUpdate = array(
                    'fk_followup_exit_type' => $fk_followup_exit_type,
                    'followup_exit_date' => $new_followup_exit_date,
                    'folowup_exit_round' => $this->input->post('folowup_exit_round', true),
                    'updateBy' => $this->vendorId,
                    'updatedOn' => date('Y-m-d H:i:s')
                );

                $this->modelName->UpdateInfo($memberUpdate, $member_master_id, $this->config->item('memberMasterTable'));
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error occurred while updating Immunization.');
                redirect($this->controller . '/edit_immunization/' . $immunizationID . '?baseID=' . $baseID);
            }

            $this->db->trans_commit();

            $this->session->set_flashdata('success', 'Member Immunization Info updated successfully.');
            if ($this->input->post('update_exit')) {
                redirect($this->controller . '/immunization' . '?baseID=' . $baseID);
            }
            redirect($this->controller . '/edit_immunization/' . $immunizationID . '?baseID=' . $baseID);
        }
    }

    public function getUpaZila() {
        if ($this->input->post('districtID')) {
            echo $this->modelName->getUpaZila($this->input->post('districtID'));
        }
    }

    public function getSlum() {
        if ($this->input->post('thanaID')) {
            echo $this->modelName->getSlum($this->input->post('thanaID'));
        }
    }

    public function getSlumArea() {
        if ($this->input->post('slumID')) {
            echo $this->modelName->getSlumArea($this->input->post('slumID'));
        }
    }

    //.sav and .dta file exporting system

    public function sav_format($file_name) {

        $command = escapeshellcmd("sav_format.py $file_name");
        $output = shell_exec($command);

        $file = $file_name . '.sav';
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
        }
    }

    public function dta_format($file_name) {

        $command = escapeshellcmd("dta_format.py $file_name");
        $output = shell_exec($command);

        $file = $file_name . '.dta';
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
        }
    }

    public function excel_format($file_name) {

        $command = escapeshellcmd("excel_format.py $file_name");
        $output = shell_exec($command);

        $file = $file_name . '.xlsx';
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
        }
    }
    
    public function do(){
        
       $result = $this->modelName->all_marriage_end_info(0, $this->config->item('marriageEndTable'), 0);
       echo "<pre/>";
       print_r($result);
    }

}

?>