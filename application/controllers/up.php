<?php

$this->db->trans_start();

$this->db->select('id,new_id');
$this->db->from('tbl_lookup_details');
$this->db->where('new_id>', 0);
$query = $this->db->get();
$table_one_data = $query->result();


foreach ($table_one_data as $data_one) {
    $this->db->from('tbl_member_master')->where('fk_why_not_birth_registration', $data_one->id);
    $query = $this->db->get();
    $exists = $query->row();
    if ($exists) {
        $update_data = array(
            'fk_why_not_birth_registration' => $data_one->new_id
        );

        $this->db->where('fk_why_not_birth_registration', $data_one->id);
        $this->db->update('tbl_member_master', $update_data);
    }
}
foreach ($table_one_data as $data_one) {
    $this->db->from('household_master')->where('inv_status', $data_one->id);
    $query = $this->db->get();
    $exists = $query->row();
    if ($exists) {
        $update_data = array(
            'inv_status' => $data_one->new_id
        );

        $this->db->where('inv_status', $data_one->id);
        $this->db->update('household_master', $update_data);
    }
}
foreach ($table_one_data as $data_one) {
    $this->db->from('tbl_member_master')->where('marriage_order', $data_one->id);
    $query = $this->db->get();
    $exists = $query->row();
    if ($exists) {
        $update_data = array(
            'marriage_order' => $data_one->new_id
        );

        $this->db->where('marriage_order', $data_one->id);
        $this->db->update('tbl_member_master', $update_data);
    }
}
foreach ($table_one_data as $data_one) {
    $this->db->from('tbl_member_education')->where('year_of_education', $data_one->id);
    $query = $this->db->get();
    $exists = $query->row();
    if ($exists) {
        $update_data = array(
            'year_of_education' => $data_one->new_id
        );

        $this->db->where('year_of_education', $data_one->id);
        $this->db->update('tbl_member_education', $update_data);
    }
}

$this->db->trans_complete();

echo "OKK done!!!!";

exit();

?>