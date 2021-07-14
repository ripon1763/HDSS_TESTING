

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
                        <a class="btn btn-primary" href="<?php echo base_url() . $controller . '/sav_format/immunization_view' . '?baseID=' . $baseID ?>">sav</a>
                        <a class="btn btn-primary" href="<?php echo base_url() . $controller . '/dta_format/immunization_view' . '?baseID=' . $baseID ?>">dta</a>
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
                        <h3 class="box-title">Immunization report</h3>

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
                                        <th>DOB</th>
                                        <th>HHNO</th>
                                        <th>member_code</th>
                                        <th>CH1_code</th>
                                        <th>BCG</th>
                                        <th>BCGFROM_code</th>
                                        <th>BCGOTH</th>
                                        <th>PENTA1</th>
                                        <th>PENTA1FROM_code</th>
                                        <th>PENTA1OTH</th>
                                        <th>PENTA2</th>
                                        <th>PENTA2FROM_code</th>
                                        <th>PENTA2OTH</th>
                                        <th>PENTA3</th>
                                        <th>PENTA3FROM_code</th>
                                        <th>PENTA3OTH</th>
                                        <th>PCV1</th>
                                        <th>PCV1FROM_code</th>
                                        <th>PCV1OTH</th>
                                        <th>PCV2</th>
                                        <th>PCV2FROM_code</th>
                                        <th>PCV2OTH</th>
                                        <th>PPV3</th>
                                        <th>PPV3FROM_code</th>
                                        <th>PPV3OTH</th>
                                        <th>OPV1</th>
                                        <th>OPV1FROM_code</th>
                                        <th>OPV1OTH</th>
                                        <th>OPV2</th>
                                        <th>OPV2FROM_code</th>
                                        <th>OPV2OTH</th>
                                        <th>OPV3</th>
                                        <th>OPV3FROM_code</th>
                                        <th>OPV3OTH</th>
                                        <th>MR1</th>
                                        <th>MR1FROM_code</th>
                                        <th>MR1OTH</th>
                                        <th>MR2</th>
                                        <th>MR2FROM_code</th>
                                        <th>MR2OTH</th>
                                        <th>FIPV1</th>
                                        <th>FIPV1FROM_code</th>
                                        <th>FIPV1OTH</th>
                                        <th>FIPV2</th>
                                        <th>FIPV2FROM_code</th>
                                        <th>FIPV2OTH</th>
                                        <th>FIPV3</th>
                                        <th>FIPV3FROM_code</th>
                                        <th>FIPV3OTH</th>
                                        <th>VITA1</th>
                                        <th>VITA1FROM_code</th>
                                        <th>VITA1OTH</th>
                                        <th>VITA2</th>
                                        <th>VITA2FROM_code</th>
                                        <th>VITA2OTH</th>
                                        <th>interview_status_code</th>
                                        <th>followup_exit_date</th>
                                        <th>folowup_exit_round</th>
                                        <th>Q20_code</th>
                                        <th>Q21_code</th>
                                        <th>Q22_code</th>
                                        <th>Q22OTH</th>
                                        <th>CODER</th>
                                        <th>REMARKS</th>
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
                url: '<?php echo base_url() ?>Reports/show_immunization?baseID=<?php echo $baseID ?>',
                                type: 'POST'
                            },

                            "dom": 'lBfrtip',

                            buttons: [{
                                    extend: 'excel',
                                    title: 'member immunization report',
                                    exportOptions: {
                                        columns: "thead th:not(.noExport)"
                                    }
                                }, {
                                    extend: 'csv',
                                    title: 'member immunization report',
                                    exportOptions: {
                                        columns: "thead th:not(.noExport)"
                                    }
                                }, {
                                    extend: 'print',
                                    title: 'member immunization report',
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

