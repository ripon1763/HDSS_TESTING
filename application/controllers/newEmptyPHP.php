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
0=>'id',
1=>'DTHDT',
2=>'death_time',
3=>'DOB',
4=>'HHNO',
5=>'member_code',
6=>'DTHCAUSE',
7=>'fk_death_place_code',
8=>'DTH_TYP',
9=>'DTH_CAUS',
10=>'insertedDate',
11=>'insertedTime',
12=>'insertedBy_name',
13=>'updatedDate',
14=>'updatedTime',
15=>'updateBy_name'

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