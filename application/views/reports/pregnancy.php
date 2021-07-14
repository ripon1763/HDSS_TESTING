<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-xs-7 text-left header-margin ">
                <h3>
                    <?php echo $pageTitle; ?>

                    <?php $baseID = $this->input->get('baseID', TRUE); ?>
                </h3>

            </div>
            <div class="col-xs-5 text-right">
                <div class="form-group margin5pxBot">
                    <a class="btn btn-primary" href="<?php echo base_url() . $controller . '/sav_format/pregnancy_view' . '?baseID=' . $baseID ?>">sav</a>
                    <a class="btn btn-primary" href="<?php echo base_url() . $controller . '/dta_format/pregnancy_view' . '?baseID=' . $baseID ?>">dta</a>
                </div>
            </div>
        </div>
    </section>
    <section class="content margin_need">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Pregnancy report</h3>

                        <?php
                        $this->load->helper('form');
                        $error = $this->session->flashdata('error');
                        if ($error) {
                            ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?php echo $this->session->flashdata('error'); ?>
                            </div>
                        <?php } ?>
                        <?php
                        $success = $this->session->flashdata('success');
                        if ($success) {
                            ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?php echo $this->session->flashdata('success'); ?>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-md-12">
                                <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                            </div>
                        </div>
                    </div><!-- /.box-header -->

                    <form action="<?php echo base_url() . $controller . '/' . $actionMethod . '?baseID=' . $baseID ?>" method="post">
                        <div class="row">
                            <div class="col-md-6">

                            </div>

                            <div class="col-md-4 no-print">
                                <div class="input-group pull-right">
                                    <label class="control-label" for="round_no">Round No </label>
                                    <select class="form-control" id="round_no" name="round_no">
                                        <option value="0">Select Round</option>
                                        <?php
                                        foreach ($all_round_info as $all_round_info_single) {
                                            ?>
                                            <option <?php if ($round_no == $all_round_info_single->id) echo ' selected'; ?> value="<?php echo $all_round_info_single->id; ?>"><?php echo $all_round_info_single->id; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 no-print">
                                <button title="Search" type="submit" class="btn btn-sm btn-default pull-left" name="search" value="search" style="margin-top:25px"><i class="fa fa-search"> </i></button>
                                &nbsp;<button title="Clear" type="submit" class="btn btn-sm btn-default pull-left" id="clear" name="Clear" value="Clear" style="margin-top:25px; margin-left:5px"><i class="fa fa-eraser"> </i></button>

                            </div>

                        </div>
                    </form>

                    <div class="box-body">
                        <div class="table-responsive">


                            <table style="white-space: nowrap;" class="table" id="memberData">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>pregnancy_outcome_date</th>
                                        <th>breast_milk_day</th>
                                        <th>induced_abortion</th>
                                        <th>spontaneous_abortion</th>
                                        <th>live_birth</th>
                                        <th>still_birth</th>
                                        <th>milk_hours</th>
                                        <th>milk_day</th>
                                        <th>keep_follow_up</th>
                                        <th>routine_anc_chkup_mother_times</th>
                                        <th>anc_first_visit_months</th>
                                        <th>anc_second_visit_months</th>
                                        <th>anc_third_visit_months</th>
                                        <th>anc_fourth_visit_months</th>
                                        <th>anc_fifth_visit_months</th>
                                        <th>totalnumbertab</th>
                                        <th>pnc_chkup_mother_times</th>
                                        <th>pnc_first_visit_days</th>
                                        <th>pnc_second_visit_days</th>
                                        <th>remarks</th>
                                        <th>birth_date</th>
                                        <th>household_code</th>
                                        <th>member_code</th>
                                        <th>fk_litter_size_code</th>
                                        <th>fk_delivery_methodology_code</th>
                                        <th>fk_delivery_assist_type_code</th>
                                        <th>fk_delivery_term_place_code</th>
                                        <th>fk_colostrum_code</th>
                                        <th>fk_first_milk_code</th>
                                        <th>fk_facility_delivery_code</th>
                                        <th>fk_preg_complication_code</th>
                                        <th>fk_delivery_complication_code</th>
                                        <th>fk_preg_violence_code</th>
                                        <th>fk_health_problem_code</th>
                                        <th>fk_high_pressure_code</th>
                                        <th>fk_diabetis_code</th>
                                        <th>fk_preaklampshia_code</th>
                                        <th>fk_lebar_birth_code</th>
                                        <th>fk_vomiting_code</th>
                                        <th>fk_amliotic_code</th>
                                        <th>fk_membrane_code</th>
                                        <th>fk_malposition_code</th>
                                        <th>fk_headache_code</th>
                                        <th>fk_routine_anc_chkup_mother_code</th>
                                        <th>fk_anc_first_assist_code</th>
                                        <th>fk_anc_second_assist_code</th>
                                        <th>fk_anc_second_visit_code</th>
                                        <th>fk_anc_third_assist_code</th>
                                        <th>fk_anc_third_visit_code</th>
                                        <th>fk_anc_fourth_assist_code</th>
                                        <th>fk_anc_fourth_visit_code</th>
                                        <th>fk_anc_fifth_assist_code</th>
                                        <th>fk_anc_fifth_visit_code</th>
                                        <th>fk_anc_supliment_code</th>
                                        <th>fk_supliment_received_way_code</th>
                                        <th>fk_how_many_tab_code</th>
                                        <th>fk_anc_weight_taken_code</th>
                                        <th>fk_anc_blood_pressure_code</th>
                                        <th>fk_anc_urine_code</th>
                                        <th>fk_anc_blood_code</th>
                                        <th>fk_anc_denger_sign_code</th>
                                        <th>fk_anc_nutrition_code</th>
                                        <th>fk_anc_birth_prepare_code</th>
                                        <th>fk_anc_delivery_kit_code</th>
                                        <th>fk_anc_soap_code</th>
                                        <th>fk_anc_care_chix_code</th>
                                        <th>fk_anc_dried_code</th>
                                        <th>fk_anc_bathing_code</th>
                                        <th>fk_anc_breast_feed_code</th>
                                        <th>fk_anc_skin_contact_code</th>
                                        <th>fk_anc_enc_code</th>
                                        <th>fk_suspecred_infection_code</th>
                                        <th>fk_baby_antibiotics_code</th>
                                        <th>fk_prescribe_antibiotics_code</th>
                                        <th>fk_seek_treatment_code</th>
                                        <th>fk_anc_vaginal_bleeding_code</th>
                                        <th>fk_anc_convulsions_code</th>
                                        <th>fk_anc_severe_headache_code</th>
                                        <th>fk_anc_fever_code</th>
                                        <th>fk_anc_abdominal_pain_code</th>
                                        <th>fk_anc_diff_breath_code</th>
                                        <th>fk_anc_water_break_code</th>
                                        <th>fk_anc_vaginal_bleed_aph_code</th>
                                        <th>fk_anc_obstructed_labour_code</th>
                                        <th>fk_anc_convulsion_code</th>
                                        <th>fk_anc_sepsis_code</th>
                                        <th>fk_anc_severe_headache_delivery_code</th>
                                        <th>fk_anc_consciousness_code</th>
                                        <th>fk_anc_vaginal_bleeding_post_code</th>
                                        <th>fk_anc_convulsion_eclampsia_post_code</th>
                                        <th>fk_anc_high_feaver_post_code</th>
                                        <th>fk_anc_smelling_discharge_post_code</th>
                                        <th>fk_anc_severe_headache_post_code</th>
                                        <th>fk_anc_consciousness_post_code</th>
                                        <th>fk_anc_inability_baby_code</th>
                                        <th>fk_anc_baby_small_baby_code</th>
                                        <th>fk_anc_fast_breathing_baby_code</th>
                                        <th>fk_anc_convulsions_baby_code</th>
                                        <th>fk_anc_drowsy_baby_code</th>
                                        <th>fk_anc_movement_baby_code</th>
                                        <th>fk_anc_grunting_baby_code</th>
                                        <th>fk_anc_indrawing_baby_code</th>
                                        <th>fk_anc_temperature_baby_code</th>
                                        <th>fk_anc_hypothermia_baby_code</th>
                                        <th>fk_anc_central_cyanosis_baby_code</th>
                                        <th>fk_anc_umbilicus_baby_code</th>
                                        <th>fk_anc_labour_preg_code</th>
                                        <th>fk_anc_excessive_bld_pre_code</th>
                                        <th>fk_anc_severe_headache_preg_code</th>
                                        <th>fk_anc_obstructed_preg_code</th>
                                        <th>fk_anc_convulsion_preg_code</th>
                                        <th>fk_anc_placenta_preg_code</th>
                                        <th>fk_anc_breath_child_code</th>
                                        <th>fk_anc_suck_baby_code</th>
                                        <th>fk_anc_hot_cold_child_code</th>
                                        <th>fk_anc_blue_child_code</th>
                                        <th>fk_anc_convulsion_child_code</th>
                                        <th>fk_anc_indrawing_child_code</th>
                                        <th>fk_supliment_post_code</th>
                                        <th>fk_post_natal_visit_code</th>
                                        <th>fk_pnc_chkup_mother_code</th>
                                        <th>fk_pnc_first_visit_assist_code</th>
                                        <th>fk_pnc_first_visit_code</th>
                                        <th>fk_pnc_second_visit_assist_code</th>
                                        <th>fk_pnc_second_visit_code</th>
                                        <th>insertedDate</th>
                                        <th>insertedTime</th>
                                        <th>insertedBy_name</th>
                                        <th>updatedDate</th>
                                        <th>updatedTime</th>
                                        <th>updateBy_name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>  



                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </section>
</div>
<script>
    $(document).ready(function (e) {

        var base_url = "<?php echo base_url(); ?>"; // You can use full url here but I prefer like this
        $('#memberData').DataTable({
            // "lengthMenu": [ [10, 25, 50,100, -1], [10, 25, 50,100, "All"] ],
            "lengthMenu": [[10, 25, 50, 100, 500, 1000, 100000, 200000, 500000], [10, 25, 50, 100, 500, 1000, 100000, 200000, 500000]],
            "processing": true,
            // "stateSave": true,
            "paging": true,

            "pagingType": "full_numbers",
            "pageLength": 10,
            "serverSide": true,
            "order": [[0, "asc"]],

            "ajax": {
                url: '<?php echo base_url() ?>Reports/show_pregnancy?baseID=<?php echo $baseID ?>',
                                type: 'POST'
                            },

                            "dom": 'lBfrtip',

                            buttons: [{
                                    extend: 'excel',
                                    title: 'member pregnancy report',
                                    exportOptions: {
                                        columns: "thead th:not(.noExport)"
                                    }
                                }, {
                                    extend: 'csv',
                                    title: 'member pregnancy report',
                                    exportOptions: {
                                        columns: "thead th:not(.noExport)"
                                    }
                                }, {
                                    extend: 'print',
                                    title: 'member pregnancy report',
                                    exportOptions: {
                                        columns: "thead th:not(.noExport)"
                                    }
                                }
                            ]
                                    // "buttons": [
                                    // {
                                    // extend: 'collection',
                                    // text: 'Export',
                                    // buttons: [
                                    // 'copy',
                                    // 'excel',
                                    // 'csv',
                                    // 'pdf',
                                    // 'print'
                                    // ]
                                    // }
                                    // ]

                        }); // End of DataTable
                    });
</script>  

<script type="text/javascript" src="<?php echo base_url() ?>assets/js/datatables.min.js"></script> 

