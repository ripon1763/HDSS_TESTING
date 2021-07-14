
<?php
$member_master_id = 0;
$member_code = '';
$member_name = '';

if (!empty($memberInfo)) {

    $member_master_id = $memberInfo[0]->id;
    $member_name = $memberInfo[0]->member_name;
    $member_code = $memberInfo[0]->member_code;
}
?>


<?php $baseID = $this->input->get('baseID', TRUE); ?>


<form action="<?php echo base_url() . 'memberChildIllness/addChildIllnessDetails?baseID=' . $baseID ?>" id="myForm" role="form" data-toggle="validator" method="post" accept-charset="utf-8">

    <!-- SmartWizard html -->



    <div id="immunization" style="padding-left: 20px; padding-right: 20px">
        <h4>Child Illness Details</h4>
        <div class="row">
            <div class="col-md-4">
                <p>Household Code : <?php echo $householdcode ?></p>
            </div>
            <div class="col-md-4">
                <p>Round Number :  <?php echo $roundNo ?></p>
            </div>

        </div>

        <div id="form-step-0" role="form" data-toggle="validator">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="Active">Member Information : </label>
                        <?php echo $member_code . '-' . $member_name ?>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="Q20">12. বাচ্চাটিকে কখনো বুকের দুধ খাওয়ানো হয়েছে কি?<span style="color:red">*</span></label>
                        <select class="form-control" id="Q20" name="Q20" required>
                            <option value="">Please Select</option>
                            <?php
                            if (!empty($yes_no)) {
                                foreach ($yes_no as $yes_no_single) {
                                    ?>
                                    <option value="<?php echo $yes_no_single->id ?>"><?php echo $yes_no_single->code . '-' . $yes_no_single->name ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="Q21">13. বাচ্চাটি জন্মের কতক্ষণ পর বুকের দুধ দেওয়া হয়েছিল?
                            (যদি এক ঘন্টার কম হয় তাহলে ঘন্টার ঘরে “০০” লিখুন, যদি ২৪ ঘন্টার কম হয় তাহলে ঘন্টায় লিখুন এবং ২৪ ঘন্টার বেশি হলে দিনে লিখুন)<span style="color:red">*</span></label>
                        <select class="form-control" id="Q21" name="Q21">
                            <option value="">Please Select</option>
                            <?php
                            if (!empty($instantly_hour_day)) {
                                foreach ($instantly_hour_day as $instantly_hour_day_single) {
                                    ?>
                                    <option value="<?php echo $instantly_hour_day_single->id ?>"><?php echo $instantly_hour_day_single->code . '-' . $instantly_hour_day_single->name ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="Q22">14. জন্মের পর প্রথম তিন দিনের মধ্যে (নামকে) বুকের দুধ ছাড়া অন্য কোন কিছু পান করতে দেওয়া হয়েছিল কি? 
                            <span style="color:red">*</span></label>
                        <select class="form-control" id="Q20" name="Q20" required>
                            <option value="">Please Select</option>
                            <?php
                            if (!empty($yes_no)) {
                                foreach ($yes_no as $yes_no_single) {
                                    ?>
                                    <option value="<?php echo $yes_no_single->id ?>"><?php echo $yes_no_single->code . '-' . $yes_no_single->name ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="Q22">15. (নামকে) কি কি পান করতে দেওয়া হয়েছিল ?

                            (প্রত্যেকটি পড়ে শোনাান)

                            (উল্লেখিত সকল তরল জাতীয় খাবার রেকর্ড করুন)
                            <span style="color:red">*</span></label>
                        <select class="form-control" id="Q20" name="Q20" required>
                            <option value="">Please Select</option>
                            <?php
                            if (!empty($drink_type)) {
                                foreach ($drink_type as $drink_type_single) {
                                    ?>
                                    <option value="<?php echo $drink_type_single->id ?>"><?php echo $drink_type_single->code . '-' . $drink_type_single->name ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="Q22">16. (নামকে) আপনি কি এখনো বুকের দুধ খাওয়াচ্ছেন ?
                            <span style="color:red">*</span></label>
                        <select class="form-control" id="Q20" name="Q20" required>
                            <option value="">Please Select</option>
                            <?php
                            if (!empty($yes_no)) {
                                foreach ($yes_no as $yes_no_single) {
                                    ?>
                                    <option value="<?php echo $yes_no_single->id ?>"><?php echo $yes_no_single->code . '-' . $yes_no_single->name ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="Q22">17. (নামকে) আপনি কতমাস পর্যন্ত বুকের দুধ খাইয়েছেন ?
                            <span style="color:red">*</span></label>
                        <select class="form-control" id="Q20" name="Q20" required>
                            <option value="">Please Select</option>
                            <?php
                            if (!empty($month_dont_know)) {
                                foreach ($month_dont_know as $month_dont_know_single) {
                                    ?>
                                    <option value="<?php echo $month_dont_know_single->id ?>"><?php echo $month_dont_know_single->code . '-' . $month_dont_know_single->name ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <fieldset class="scheduler-border CH1_yes_part">
                    <legend class="scheduler-border">18. আমি এখন আপনাকে জিজ্ঞেস করবো (নামকে) গতকাল দিনে এবং রাতে কি কি তরল খাবার খাওয়ানো হয়েছে ? </legend>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">A) শুধু পানি
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($yes_no_dont_know)) {
                                    foreach ($yes_no_dont_know as $yes_no_dont_know_single) {
                                        ?>
                                        <option value="<?php echo $yes_no_dont_know_single->id ?>"><?php echo $yes_no_dont_know_single->code . '-' . $yes_no_dont_know_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">B) জুস
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($yes_no_dont_know)) {
                                    foreach ($yes_no_dont_know as $yes_no_dont_know_single) {
                                        ?>
                                        <option value="<?php echo $yes_no_dont_know_single->id ?>"><?php echo $yes_no_dont_know_single->code . '-' . $yes_no_dont_know_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">C) সুপ / ফলের রস
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($yes_no_dont_know)) {
                                    foreach ($yes_no_dont_know as $yes_no_dont_know_single) {
                                        ?>
                                        <option value="<?php echo $yes_no_dont_know_single->id ?>"><?php echo $yes_no_dont_know_single->code . '-' . $yes_no_dont_know_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">D) টিনজাত দুধ, পাউডার দুধ বা গরুর দুধ
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($yes_no_dont_know)) {
                                    foreach ($yes_no_dont_know as $yes_no_dont_know_single) {
                                        ?>
                                        <option value="<?php echo $yes_no_dont_know_single->id ?>"><?php echo $yes_no_dont_know_single->code . '-' . $yes_no_dont_know_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">E) শিশু খাদ্য
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($yes_no_dont_know)) {
                                    foreach ($yes_no_dont_know as $yes_no_dont_know_single) {
                                        ?>
                                        <option value="<?php echo $yes_no_dont_know_single->id ?>"><?php echo $yes_no_dont_know_single->code . '-' . $yes_no_dont_know_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">F) এছাড়া অন্য কোন তরল খাবার
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($yes_no_dont_know)) {
                                    foreach ($yes_no_dont_know as $yes_no_dont_know_single) {
                                        ?>
                                        <option value="<?php echo $yes_no_dont_know_single->id ?>"><?php echo $yes_no_dont_know_single->code . '-' . $yes_no_dont_know_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="Q22">19. (নামকে) গতকাল দিনে এবং রাতে কোন শক্ত খাবার, আধা শক্ত খাবার বা নরম খাবার খাওয়ানো হয়েছে কি ?
                            যদি হ্যাঁ হয় তাহলে যাচাই করুন কি ধরণের খাবার (শক্ত/আধা শক্ত/নরম) খাওয়ানো হয়েছে ?
                            <span style="color:red">*</span></label>
                        <select class="form-control" id="Q20" name="Q20" required>
                            <option value="">Please Select</option>
                            <?php
                            if (!empty($yes_no)) {
                                foreach ($yes_no as $yes_no_single) {
                                    ?>
                                    <option value="<?php echo $yes_no_single->id ?>"><?php echo $yes_no_single->code . '-' . $yes_no_single->name ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="Q22">20. আপনি আপনার (নামকে) কতমাস বয়স থেকে শক্ত/আধা শক্ত/নরম খাবার দেওয়া শুরু করেছেন ?
                            <span style="color:red">*</span></label>
                        <select class="form-control" id="Q20" name="Q20" required>
                            <option value="">Please Select</option>
                            <?php
                            if (!empty($month_dont_know)) {
                                foreach ($month_dont_know as $month_dont_know_single) {
                                    ?>
                                    <option value="<?php echo $month_dont_know_single->id ?>"><?php echo $month_dont_know_single->code . '-' . $month_dont_know_single->name ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <fieldset class="scheduler-border CH1_yes_part">
                    <legend class="scheduler-border">Diarrhoea (Last 14 days)</legend>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">21. (নামের) গত ১৪ দিনের মধ্যে কোন ডায়রিয়া হয়েছিল কি?
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($diarrhea_happened)) {
                                    foreach ($diarrhea_happened as $diarrhea_happened_single) {
                                        ?>
                                        <option value="<?php echo $diarrhea_happened_single->id ?>"><?php echo $diarrhea_happened_single->code . '-' . $diarrhea_happened_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">22. ডায়রিয়া কি ধরণের ছিল ?
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($diarrhea_type)) {
                                    foreach ($diarrhea_type as $diarrhea_type_single) {
                                        ?>
                                        <option value="<?php echo $diarrhea_type_single->id ?>"><?php echo $diarrhea_type_single->code . '-' . $diarrhea_type_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">23. ডায়রিয়ার জন্য কি ধরণের চিকিৎসা দেওয়া হয়েছিল ?
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($diarrhea_treatment_type)) {
                                    foreach ($diarrhea_treatment_type as $diarrhea_treatment_type_single) {
                                        ?>
                                        <option value="<?php echo $diarrhea_treatment_type_single->id ?>"><?php echo $diarrhea_treatment_type_single->code . '-' . $diarrhea_treatment_type_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">24. ডায়রিয়ার জন্য কার কাছ থেকে বা কোথা থেকে চিকিৎসা নিয়েছিলেন ?
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($treatment_taken_from)) {
                                    foreach ($treatment_taken_from as $treatment_taken_from_single) {
                                        ?>
                                        <option value="<?php echo $treatment_taken_from_single->id ?>"><?php echo $treatment_taken_from_single->code . '-' . $treatment_taken_from_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">25. (নামের) কোন তারিখে ডায়রিয়া হয়েছিল ?
                                <span style="color:red">*</span></label>
                            <input type="text" class="form-control date_format" id="" name="" required>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="row">
                <fieldset class="scheduler-border CH1_yes_part">
                    <legend class="scheduler-border">Pneumonia / ARI</legend>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">26. (নামের) গত ১৪ দিনের মধে নিউমোনিয়ার কি কি লক্ষণ ছিল ? 
                                <span style="color:red">*</span></label>
                            <div class="checkbox">
                                <label><input name="available_govt_hospital" type="checkbox" value="1">কোন লক্ষল ছিল না</label>
                            </div>
                            <div class="checkbox">
                                <label><input name="available_central_dist_hospital" type="checkbox" value="2">জ্বর</label>
                            </div>
                            <div class="checkbox">
                                <label><input name="available_matri_sonod" type="checkbox" value="3">স্বর্দি এবং কাঁশি</label>
                            </div>
                            <div class="checkbox">
                                <label><input name="available_ngo_facility" type="checkbox" value="4">শ্বাসকষ্ট / ঘন ঘন শ্বাস নেওয়া</label>
                            </div>
                            <div class="checkbox">
                                <label><input name="available_upazilla_sasthokendro" type="checkbox" value="5">বুকের খাঁচা ডেবে যাওয়া</label>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">27.নিউমোনিয়ার জন্য কোন এন্টিবায়োটিক দেওয়া হয়েছিল কি ? 
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($antibiotic_for_pneumonia)) {
                                    foreach ($antibiotic_for_pneumonia as $antibiotic_for_pneumonia_single) {
                                        ?>
                                        <option value="<?php echo $antibiotic_for_pneumonia_single->id ?>"><?php echo $antibiotic_for_pneumonia_single->code . '-' . $antibiotic_for_pneumonia_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="Q22">28. নিউমোনিয়ার জন্য কার কাছ থেকে বা কোথা থেকে চিকিৎসা নিয়েছিলেন? 
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($treatment_taken_from)) {
                                    foreach ($treatment_taken_from as $treatment_taken_from_single) {
                                        ?>
                                        <option value="<?php echo $treatment_taken_from_single->id ?>"><?php echo $treatment_taken_from_single->code . '-' . $treatment_taken_from_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">29. (নামের) কোন তারিখে নিউমোনিয়া হয়েছিল ?
                                <span style="color:red">*</span></label>
                            <input type="text" class="form-control date_format" id="Q20" name="Q20" required>
                                
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Q22">30.ইন্টারভিউ স্ট্যাটাস
                                <span style="color:red">*</span></label>
                            <select class="form-control" id="Q20" name="Q20" required>
                                <option value="">Please Select</option>
                                <?php
                                if (!empty($interview_status_child_illness)) {
                                    foreach ($interview_status_child_illness as $interview_status_child_illness_single) {
                                        ?>
                                        <option value="<?php echo $interview_status_child_illness_single->id ?>"><?php echo $interview_status_child_illness_single->code . '-' . $interview_status_child_illness_single->name ?></option>
                                        <?php
                                    }
                                }
                                ?>

                            </select>        
                        </div>
                    </div>
                </fieldset>
            </div>

        </div>
    </div>




    <div class="box-footer" style="margin-left: 10px">
        <input type="hidden" name="household_master_id_sub" value="<?php echo $household_master_id_sub ?>">
        <input type="hidden" name="round_master_id" value="<?php echo $round_master_id ?>">
        <input type="hidden" name="member_master_id" value="<?php echo $member_master_id ?>">
        <input type="submit" class="btn btn-primary btnVisile" name="submit" value="Save" />
        <a class="btn btn-primary" href="<?php echo base_url() . 'householdvisit/immunization?baseID=' . $baseID . '#immunization' ?>" class="">Back </a>

    </div>




</form>




</div><!-- /.box-body -->
</div><!-- /.box -->
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

    $("#Q20").change(function () {

        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");

            if (optionValue == 1)
            {
                $("#Q21").prop('required', true);
                $(".Q20_yes_part").show();
            } else {
                $("#Q21").prop('required', false);
                $(".Q20_yes_part").hide();
                $('#Q21').val('').trigger('change');
            }




        });
    }).change();
    $("#Q21").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");

            if (optionValue == 1)
            {
                $("#Q22").prop('required', true);
                $(".Q21_yes_part").show();
            } else {
                $("#Q22").prop('required', false);
                $(".Q21_yes_part").hide();
                $('#Q22').val('').trigger('change');
            }



        });
    }).change();

    $("#CH1").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");

            if (optionValue == 1)
            {
                $(".CH1_yes_part").show();
            } else {
                $(".CH1_yes_part").hide();
            }

        });
    }).change();

    $('.allowInteger').keypress(function (event) {
        return isNumber(event, this)
    });

// THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
    function isNumber(evt, element) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode < 48 || charCode > 57)
            return false;
        return true;
    }

</script>

