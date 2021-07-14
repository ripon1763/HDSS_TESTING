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
0=>'id',
1=>'DOB',
2=>'HHNO',
3=>'member_code',
4=>'CH1_code',
5=>'BCG',
6=>'BCGFROM_code',
7=>'BCGOTH',
8=>'PENTA1',
9=>'PENTA1FROM_code',
10=>'PENTA1OTH',
11=>'PENTA2',
12=>'PENTA2FROM_code',
13=>'PENTA2OTH',
14=>'PENTA3',
15=>'PENTA3FROM_code',
16=>'PENTA3OTH',
17=>'PCV1',
18=>'PCV1FROM_code',
19=>'PCV1OTH',
20=>'PCV2',
21=>'PCV2FROM_code',
22=>'PCV2OTH',
23=>'PPV3',
24=>'PPV3FROM_code',
25=>'PPV3OTH',
26=>'OPV1',
27=>'OPV1FROM_code',
28=>'OPV1OTH',
29=>'OPV2',
30=>'OPV2FROM_code',
31=>'OPV2OTH',
32=>'OPV3',
33=>'OPV3FROM_code',
34=>'OPV3OTH',
35=>'MR1',
36=>'MR1FROM_code',
37=>'MR1OTH',
38=>'MR2',
39=>'MR2FROM_code',
40=>'MR2OTH',
41=>'FIPV1',
42=>'FIPV1FROM_code',
43=>'FIPV1OTH',
44=>'FIPV2',
45=>'FIPV2FROM_code',
46=>'FIPV2OTH',
47=>'FIPV3',
48=>'FIPV3FROM_code',
49=>'FIPV3OTH',
50=>'VITA1',
51=>'VITA1FROM_code',
52=>'VITA1OTH',
53=>'VITA2',
54=>'VITA2FROM_code',
55=>'VITA2OTH',
56=>'fk_followup_exit_type_code',
57=>'followup_exit_date',
58=>'folowup_exit_round',
59=>'Q20_code',
60=>'Q21_code',
61=>'Q22_code',
62=>'Q22OTH',
63=>'CODER',
64=>'REMARKS',
65=>'insertedDate',
66=>'insertedTime',
67=>'insertedBy_name',
68=>'updatedDate',
69=>'updatedTime',
70=>'updateBy_name');

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
$edit_link = "<a href='" . base_url() . "Report/edit_immunization/" . $rows->ID . "?baseID=" . $baseID . "' class='btn btn-sm btn-primary'>Edit</a>";
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
$rows->fk_followup_exit_type_code,
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