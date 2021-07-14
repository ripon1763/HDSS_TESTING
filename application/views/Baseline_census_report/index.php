

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
                    <div class="form-group margin5pxBot">
                        <a class="btn btn-primary" href="<?php echo base_url() . $controller . '/sav_format/baseline_census_view' . '?baseID=' . $baseID ?>">sav</a>
                        <a class="btn btn-primary" href="<?php echo base_url() . $controller . '/dta_format/baseline_census_view' . '?baseID=' . $baseID ?>">dta</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content margin_need">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Baseline Census report</h3>

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

                    <form action="<?php echo base_url() . $controller . '/'. '?baseID=' . $baseID ?>" method="post">
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
                                        <th>household_code</th>
                                        <th>upazilla_name</th>
                                        <th>division_code</th>
                                        <th>looking_for_work</th>
                                        <th>for_earning_more_money</th>
                                        <th>river_erosion</th>
                                        <th>for_family</th>
                                        <th>for_children_education</th>
                                        <th>for_own_education</th>
                                        <th>for_marriage</th>
                                        <th>na_as_birth_here</th>
                                        <th>coming_reason_other</th>
                                        <th>coming_reason_other_specify</th>
                                        <th>pregnancy_status_code</th>
                                        <th>pregnancy_status_since_when</th>
                                        <th>roof_code</th>
                                        <th>roof_other</th>
                                        <th>wall_code</th>
                                        <th>wall_other</th>
                                        <th>floor_code</th>
                                        <th>room</th>
                                        <th>room1l</th>
                                        <th>room1b</th>
                                        <th>room2l</th>
                                        <th>room2b</th>
                                        <th>room3l</th>
                                        <th>room3b</th>
                                        <th>Q42A</th>
                                        <th>Q42B</th>
                                        <th>water_code</th>
                                        <th>winside_code</th>
                                        <th>wcol_time</th>
                                        <th>wait_time</th>
                                        <th>wat_coll_code</th>
                                        <th>watcoloth</th>
                                        <th>wshare_code</th>
                                        <th>wsharef</th>
                                        <th>wat_supp_code</th>
                                        <th>w_suppoth</th>
                                        <th>w_safe_code</th>
                                        <th>w_suff_code</th>
                                        <th>toilet_code</th>
                                        <th>toilet_ct_code</th>
                                        <th>toilet_ct_ot</th>
                                        <th>toilte_mf_code</th>
                                        <th>tmf_usep_code</th>
                                        <th>toilet_cl_code</th>
                                        <th>toilet_coth</th>
                                        <th>toilet_dis_code</th>
                                        <th>toilet_dot</th>
                                        <th>tinside_code</th>
                                        <th>tshare_code</th>
                                        <th>tsharef</th>
                                        <th>light_code</th>
                                        <th>light_oth</th>
                                        <th>Q61_code</th>
                                        <th>Q62_code</th>
                                        <th>Q62oth</th>
                                        <th>Q63_code</th>
                                        <th>Q63oth</th>
                                        <th>Q65A_code</th>
                                        <th>Q65B_code</th>
                                        <th>Q65C_code</th>
                                        <th>Q65D_code</th>
                                        <th>Q65E_code</th>
                                        <th>Q65F_code</th>
                                        <th>cook_code</th>
                                        <th>cookoth</th>
                                        <th>cinside_code</th>
                                        <th>cshare_code</th>
                                        <th>csharef</th>
                                        <th>garbage_code</th>
                                        <th>garbageoth</th>
                                        <th>gcollect_code</th>
                                        <th>voterid_code</th>
                                        <th>resp_ind</th>
                                        <th>imobile</th>
                                        <th>remarks</th>
                                        <th>fk_owner_land_code</th>
                                        <th>fk_owner_house_code</th>
                                        <th>fk_chair_code</th>
                                        <th>fk_dining_table_code</th>
                                        <th>fk_khat_code</th>
                                        <th>fk_chowki_code</th>
                                        <th>fk_almirah_code</th>
                                        <th>fk_sofa_code</th>
                                        <th>fk_radio_code</th>
                                        <th>fk_tv_code</th>
                                        <th>fk_freeze_code</th>
                                        <th>fk_mobile_code</th>
                                        <th>fk_electric_fan_code</th>
                                        <th>fk_hand_watch_code</th>
                                        <th>fk_rickshow_code</th>
                                        <th>fk_computer_code</th>
                                        <th>fk_sewing_machine_code</th>
                                        <th>fk_cycle_code</th>
                                        <th>fk_motor_cycle_code</th>
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
                url: '<?php echo base_url() ?>Baseline_census_report/show_baseline_census?baseID=<?php echo $baseID ?>',
                                type: 'POST'
                            },

                            "dom": 'lBfrtip',

                            buttons: [{
                                    extend: 'excel',
                                    title: 'member baseline census report',
                                    exportOptions: {
                                        columns: "thead th:not(.noExport)"
                                    }
                                }, {
                                    extend: 'csv',
                                    title: 'member baseline census report',
                                    exportOptions: {
                                        columns: "thead th:not(.noExport)"
                                    }
                                }, {
                                    extend: 'print',
                                    title: 'member baseline census report',
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

