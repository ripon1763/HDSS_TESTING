<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-xs-6 text-left header-margin ">
                <h3>
                    <?php echo $pageTitle; ?>
                    <small>(Edit)</small>
                    <?php $baseID = $this->input->get('baseID', TRUE); ?>
                </h3>

            </div>
            <div class="col-xs-6 text-right">
                <div class="form-group margin5pxBot">
                    <a class="btn btn-primary" href="<?php echo base_url() . $controller . '/immunization?baseID=' . $baseID ?>"><?php echo $shortName ?> List</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content content-margin">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><?php echo $shortName ?> Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
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
                    <form role="form" action="<?php echo base_url() . $controller . '/' . $actionMethod . '?baseID=' . $baseID ?>" method="post" id="editUser" role="form">
                        <input type="hidden" name="immunizationID" value="<?php echo $immunization_info->id; ?>">
                        <input type="hidden" name="member_master_id" value="<?php echo $immunization_info->member_master_id ?>">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <b>Household Code:</b> <?php echo $immunization_info->household_code; ?>
                                </div>
                                <div class="col-md-3 form-group">
                                    <b>Round No:</b> <?php echo $immunization_info->round_master_id; ?>
                                </div>
                                <div class="col-md-3 form-group">
                                    <b>Member Code:</b> <?php echo $immunization_info->member_code; ?>
                                </div>
                                <div class="col-md-3 form-group">
                                    <b>Member Name:</b> <?php echo $immunization_info->member_name; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <b>Gender:</b> <?php echo $immunization_info->gender_code . '-' . $immunization_info->gender_name; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="CH1">Did the child get any vaccine?  <span style="color:red">*</span></label>
                                        <select class="form-control" id="CH1" name="CH1" required>
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($assetYesNo)) {
                                                foreach ($assetYesNo as $assetYesNo_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->CH1 == $assetYesNo_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $assetYesNo_single->id ?>"><?php echo $assetYesNo_single->code . '-' . $assetYesNo_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $BCG = null;
                                        if ($immunization_info->BCG != "") {
                                            $partsRequire = explode('-', $immunization_info->BCG);
                                            $BCG = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="BCG">BCG </label>
                                        <input value="<?php echo $BCG; ?>" autocomplete="off" type="text" class="form-control date_format"  name="BCG">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="BCGFROM">Information recorded from </label>
                                        <select class="form-control" id="BCGFROM" name="BCGFROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->BCGFROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="BCGOTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->BCGOTH; ?>" type="text" class="form-control" id="BCGOTH"  name="BCGOTH">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $PENTA1 = null;
                                        if ($immunization_info->PENTA1 != "") {
                                            $partsRequire = explode('-', $immunization_info->PENTA1);
                                            $PENTA1 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="PENTA1">Penta1 </label>
                                        <input value="<?php echo $PENTA1; ?>" autocomplete="off" type="text" class="form-control date_format"  name="PENTA1">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="PENTA1FROM">Information recorded from </label>
                                        <select class="form-control" id="PENTA1FROM" name="PENTA1FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->PENTA1FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="PENTA1OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->PENTA1OTH; ?>" type="text" class="form-control" id="PENTA1OTH"  name="PENTA1OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $PENTA2 = null;
                                        if ($immunization_info->PENTA2 != "") {
                                            $partsRequire = explode('-', $immunization_info->PENTA2);
                                            $PENTA2 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="PENTA2">Penta2 </label>
                                        <input value="<?php echo $PENTA2; ?>" autocomplete="off" type="text" class="form-control date_format"  name="PENTA2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="PENTA2FROM">Information recorded from </label>
                                        <select class="form-control" id="PENTA2FROM" name="PENTA2FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->PENTA2FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="PENTA2OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->PENTA2OTH; ?>" type="text" class="form-control" id="PENTA2OTH"  name="PENTA2OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $PENTA3 = null;
                                        if ($immunization_info->PENTA3 != "") {
                                            $partsRequire = explode('-', $immunization_info->PENTA3);
                                            $PENTA3 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="PENTA3">Penta3 </label>
                                        <input value="<?php echo $PENTA3; ?>" autocomplete="off" type="text" class="form-control date_format"  name="PENTA3">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="PENTA3FROM">Information recorded from </label>
                                        <select class="form-control" id="PENTA3FROM" name="PENTA3FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->PENTA3FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="PENTA3OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->PENTA3OTH; ?>" type="text" class="form-control" id="PENTA3OTH"  name="PENTA3OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $PCV1 = null;
                                        if ($immunization_info->PCV1 != "") {
                                            $partsRequire = explode('-', $immunization_info->PCV1);
                                            $PCV1 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="PCV1">Pcv1 </label>
                                        <input value="<?php echo $PCV1; ?>" autocomplete="off" type="text" class="form-control date_format"  name="PCV1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="PCV1FROM">Information recorded from </label>
                                        <select class="form-control" id="PCV1FROM" name="PCV1FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->PCV1FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="PCV1OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->PCV1OTH; ?>" type="text" class="form-control" id="PCV1OTH"  name="PCV1OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $PCV2 = null;
                                        if ($immunization_info->PCV2 != "") {
                                            $partsRequire = explode('-', $immunization_info->PCV2);
                                            $PCV2 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="PCV2">Pcv2 </label>
                                        <input value="<?php echo $PCV2; ?>" autocomplete="off" type="text" class="form-control date_format"  name="PCV2" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="PCV2FROM">Information recorded from </label>
                                        <select class="form-control" id="PCV2FROM" name="PCV2FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->PCV2FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="PCV2OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->PCV2OTH; ?>" type="text" class="form-control" id="PCV2OTH"  name="PCV2OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $PPV3 = null;
                                        if ($immunization_info->PPV3 != "") {
                                            $partsRequire = explode('-', $immunization_info->PPV3);
                                            $PPV3 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="PPV3">Pcv3 </label>
                                        <input value="<?php echo $PPV3; ?>" autocomplete="off" type="text" class="form-control date_format"  name="PPV3" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="PPV3FROM">Information recorded from </label>
                                        <select class="form-control" id="PPV3FROM" name="PPV3FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->PPV3FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="PPV3OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->PPV3OTH; ?>" type="text" class="form-control" id="PPV3OTH"  name="PPV3OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $OPV1 = null;
                                        if ($immunization_info->OPV1 != "") {
                                            $partsRequire = explode('-', $immunization_info->OPV1);
                                            $OPV1 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="OPV1">Opv1 </label>
                                        <input value="<?php echo $OPV1; ?>" autocomplete="off" type="text" class="form-control date_format"  name="OPV1" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="OPV1FROM">Information recorded from </label>
                                        <select class="form-control" id="OPV1FROM" name="OPV1FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->OPV1FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="OPV1OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->OPV1OTH; ?>" type="text" class="form-control" id="OPV1OTH"  name="OPV1OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $OPV2 = null;
                                        if ($immunization_info->OPV2 != "") {
                                            $partsRequire = explode('-', $immunization_info->OPV2);
                                            $OPV2 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="OPV2">Opv2 </label>
                                        <input value="<?php echo $OPV2; ?>" autocomplete="off" type="text" class="form-control date_format"  name="OPV2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="OPV2FROM">Information recorded from </label>
                                        <select class="form-control" id="OPV2FROM" name="OPV2FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->OPV2FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="OPV2OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->OPV2OTH; ?>" type="text" class="form-control" id="OPV2OTH"  name="OPV2OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $OPV3 = null;
                                        if ($immunization_info->OPV3 != "") {
                                            $partsRequire = explode('-', $immunization_info->OPV3);
                                            $OPV3 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="OPV3">Opv3 </label>
                                        <input value="<?php echo $OPV3; ?>" autocomplete="off" type="text" class="form-control date_format"  name="OPV3" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="OPV3FROM">Information recorded from </label>
                                        <select class="form-control" id="OPV3FROM" name="OPV3FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->OPV3FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="OPV3OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->OPV3OTH; ?>" type="text" class="form-control" id="OPV3OTH"  name="OPV3OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $MR1 = null;
                                        if ($immunization_info->MR1 != "") {
                                            $partsRequire = explode('-', $immunization_info->MR1);
                                            $MR1 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="MR1">Mr1 </label>
                                        <input value="<?php echo $MR1; ?>" autocomplete="off" type="text" class="form-control date_format"  name="MR1" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="MR1FROM">Information recorded from </label>
                                        <select class="form-control" id="MR1FROM" name="MR1FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->MR1FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="MR1OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->MR1OTH; ?>" type="text" class="form-control" id="MR1OTH"  name="MR1OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $MR2 = null;
                                        if ($immunization_info->MR2 != "") {
                                            $partsRequire = explode('-', $immunization_info->MR2);
                                            $MR2 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="MR2">Mr2 </label>
                                        <input value="<?php echo $MR2; ?>" autocomplete="off" type="text" class="form-control date_format"  name="MR2" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="MR2FROM">Information recorded from </label>
                                        <select class="form-control" id="MR2FROM" name="MR2FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->MR2FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="MR2OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->MR2OTH; ?>" type="text" class="form-control" id="MR2OTH"  name="MR2OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $FIPV1 = null;
                                        if ($immunization_info->FIPV1 != "") {
                                            $partsRequire = explode('-', $immunization_info->FIPV1);
                                            $FIPV1 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="FIPV1">Fipv1 </label>
                                        <input value="<?php echo $FIPV1; ?>" autocomplete="off" type="text" class="form-control date_format"  name="FIPV1" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FIPV1FROM">Information recorded from </label>
                                        <select class="form-control" id="FIPV1FROM" name="FIPV1FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->FIPV1FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="FIPV1OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->FIPV1OTH; ?>" type="text" class="form-control" id="FIPV1OTH"  name="FIPV1OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $FIPV2 = null;
                                        if ($immunization_info->FIPV2 != "") {
                                            $partsRequire = explode('-', $immunization_info->FIPV2);
                                            $FIPV2 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="FIPV2">Fipv2 </label>
                                        <input value="<?php echo $FIPV2; ?>" autocomplete="off" type="text" class="form-control date_format"  name="FIPV2" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FIPV2FROM">Information recorded from </label>
                                        <select class="form-control" id="FIPV2FROM" name="FIPV2FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->FIPV2FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="FIPV2OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->FIPV2OTH; ?>" type="text" class="form-control" id="FIPV2OTH"  name="FIPV2OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $FIPV3 = null;
                                        if ($immunization_info->FIPV3 != "") {
                                            $partsRequire = explode('-', $immunization_info->FIPV3);
                                            $FIPV3 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="FIPV3">Fipv3 </label>
                                        <input value="<?php echo $FIPV3; ?>" autocomplete="off" type="text" class="form-control date_format"  name="FIPV3" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FIPV3FROM">Information recorded from </label>
                                        <select class="form-control" id="FIPV3FROM" name="FIPV3FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->FIPV3FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FIPV3OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->FIPV3OTH; ?>" type="text" class="form-control" id="FIPV3OTH"  name="FIPV3OTH">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $VITA1 = null;
                                        if ($immunization_info->VITA1 != "") {
                                            $partsRequire = explode('-', $immunization_info->VITA1);
                                            $VITA1 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="VITA1">Vita1 </label>
                                        <input value="<?php echo $VITA1; ?>" autocomplete="off" type="text" class="form-control date_format"  name="VITA1" >
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="VITA1FROM">Information recorded from </label>
                                        <select class="form-control" id="VITA1FROM" name="VITA1FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->VITA1FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="VITA1OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->VITA1OTH; ?>" type="text" class="form-control" id="VITA1OTH"  name="VITA1OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $VITA2 = null;
                                        if ($immunization_info->VITA2 != "") {
                                            $partsRequire = explode('-', $immunization_info->VITA2);
                                            $VITA2 = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="VITA2">Vita2 </label>
                                        <input value="<?php echo $VITA2; ?>" autocomplete="off" type="text" class="form-control date_format"  name="VITA2" >
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="VITA2FROM">Information recorded from </label>
                                        <select class="form-control" id="VITA2FROM" name="VITA2FROM">
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($information_recorded_from)) {
                                                foreach ($information_recorded_from as $information_recorded_from_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->VITA2FROM == $information_recorded_from_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $information_recorded_from_single->id ?>"><?php echo $information_recorded_from_single->code . '-' . $information_recorded_from_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="VITA2OTH">If not taken vaccine please specify</label>
                                        <input value="<?php echo $immunization_info->VITA2OTH; ?>" type="text" class="form-control" id="VITA2OTH"  name="VITA2OTH">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fk_followup_exit_type">Child follow up exit type <span style="color:red">*</span></label>
                                        <select class="form-control" id="fk_followup_exit_type" name="fk_followup_exit_type" required>
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($child_follow_up_exit_type)) {
                                                foreach ($child_follow_up_exit_type as $child_follow_up_exit_type_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->fk_followup_exit_type == $child_follow_up_exit_type_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $child_follow_up_exit_type_single->id ?>"><?php echo $child_follow_up_exit_type_single->code . '-' . $child_follow_up_exit_type_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php
                                        $followup_exit_date = null;
                                        if ($immunization_info->followup_exit_date != "") {
                                            $partsRequire = explode('-', $immunization_info->followup_exit_date);
                                            $followup_exit_date = $partsRequire[2] . '/' . $partsRequire[1] . '/' . $partsRequire[0];
                                        }
                                        ?>
                                        <label for="followup_exit_date">Child follow up exit date <span style="color:red">*</span></label>
                                        <input value="<?php echo $followup_exit_date; ?>" autocomplete="off" type="text" class="form-control date_format"  name="followup_exit_date" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="folowup_exit_round">Child follow up exit round <span style="color:red">*</span></label>
                                        <input value="<?php echo $immunization_info->folowup_exit_round; ?>" type="text" class="form-control" name="folowup_exit_round" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Q20">Have the vaccination card? <span style="color:red">*</span></label>
                                        <select class="form-control" id="Q20" name="Q20" required>
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($assetYesNo)) {
                                                foreach ($assetYesNo as $assetYesNo_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->Q20 == $assetYesNo_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $assetYesNo_single->id ?>"><?php echo $assetYesNo_single->code . '-' . $assetYesNo_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Q21">Seen the vaccination card? <span style="color:red">*</span></label>
                                        <select class="form-control" id="Q21" name="Q21" required>
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($assetYesNo)) {
                                                foreach ($assetYesNo as $assetYesNo_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->Q21 == $assetYesNo_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $assetYesNo_single->id ?>"><?php echo $assetYesNo_single->code . '-' . $assetYesNo_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Q22">Why haven’t seen card? <span style="color:red">*</span></label>
                                        <select class="form-control" id="Q22" name="Q22" required>
                                            <option value="">Please Select</option>
                                            <?php
                                            if (!empty($why_not_seen_card)) {
                                                foreach ($why_not_seen_card as $why_not_seen_card_single) {
                                                    ?>
                                                    <option <?php
                                                    if ($immunization_info->Q22 == $why_not_seen_card_single->id) {
                                                        echo " selected";
                                                    }
                                                    ?> value="<?php echo $why_not_seen_card_single->id ?>"><?php echo $why_not_seen_card_single->code . '-' . $why_not_seen_card_single->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 Q22OTH_part">
                                    <div class="form-group">
                                        <label for="Q22OTH">Specify here <span style="color:red">*</span></label>
                                        <input value="<?php echo $immunization_info->Q22OTH; ?>" type="text" class="form-control" id="Q22OTH" name="Q22OTH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="CODER">Interviewer code <span style="color:red">*</span></label>
                                        <input value="<?php echo $immunization_info->CODER; ?>" type="text" class="form-control"  name="CODER" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="REMARKS">Remarks</label>
                                        <input value="<?php echo $immunization_info->REMARKS; ?>" type="text" class="form-control"  name="REMARKS">
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Update"> <input name="update_exit" type="submit" class="btn btn-primary" value="Update & Exit">
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
</div>


<script type="text/javascript">

    $(document).ready(function () {
        $('.date_format').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        });

    });

    $("#Q22").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");

            if (optionValue == 436)
            {
                $("#Q22OTH").prop('required', true);
                $(".Q22OTH_part").show();
            } else {
                $("#Q22OTH").prop('required', false);
                $(".Q22OTH_part").hide();
            }


        });
    }).change();

</script>

