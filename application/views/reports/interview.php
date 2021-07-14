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
                    <a class="btn btn-primary" href="<?php echo base_url() . $controller . '/sav_format/interview_view' . '?baseID=' . $baseID ?>">sav</a>
                    <a class="btn btn-primary" href="<?php echo base_url() . $controller . '/dta_format/interview_view' . '?baseID=' . $baseID ?>">dta</a>
                </div>
            </div>
        </div>
    </section>
    <section class="content margin_need">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Interview report</h3>

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
                                        <th>household_master_id</th>
                                        <th>interview_date</th>
                                        <th>respondent_code</th>
                                        <th>split_date</th>
                                        <th>no_of_new_household</th>
                                        <th>merge_date</th>
                                        <th>remarks</th>
                                        <th>household_code</th>
                                        <th>any_birth_code</th>
                                        <th>any_concepton_code</th>
                                        <th>any_pregnancy_code</th>
                                        <th>any_death_code</th>
                                        <th>any_hosp_code</th>
                                        <th>memberCheck_code</th>
                                        <th>any_vaccin_code</th>
                                        <th>any_marriage_start_code</th>
                                        <th>any_marriage_end_code</th>
                                        <th>any_migration_in_code</th>
                                        <th>any_migration_out_code</th>
                                        <th>fk_interview_status_code</th>
                                        <th>fk_interviewer_code</th>
                                        <th>fk_responded_type_code</th>
                                        <th>is_household_split_code</th>
                                        <th>is_household_merge_code</th>
                                        <th>any_asset_code</th>
                                        <th>any_education_code</th>
                                        <th>any_occupation_code</th>
                                        <th>any_relation_code</th>
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
                url: '<?php echo base_url() ?>Reports/show_interview?baseID=<?php echo $baseID ?>',
                                type: 'POST'
                            },

                            "dom": 'lBfrtip',

                            buttons: [{
                                    extend: 'excel',
                                    title: 'member interview report',
                                    exportOptions: {
                                        columns: "thead th:not(.noExport)"
                                    }
                                }, {
                                    extend: 'csv',
                                    title: 'member interview report',
                                    exportOptions: {
                                        columns: "thead th:not(.noExport)"
                                    }
                                }, {
                                    extend: 'print',
                                    title: 'member interview report',
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

