<script>
    $("#current_pregnancy_status").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue == 331) {
                $("#husband_living_place").prop('required', false);
                $("#birth_control_method_usage_status").prop('required', false);
                $("#birth_control_method").prop('required', false);
                $("#birth_control_method_other_value").prop('required', false);
                $("#continuously_using_how_many_year").prop('required', false);
                $("#continuously_using_how_many_month").prop('required', false);
                $("#birth_control_method_suggestion_from_where").prop('required', false);
                $("#birth_control_method_suggestion_from_where_other_value").prop('required', false);
                $("#whose_decision").prop('required', false);
                $("#whose_decision_other_value").prop('required', false);
                $("#reason_behind_not_using").prop('required', false);
                $("#reason_behind_not_using_other_value").prop('required', false);
                $(".current_pregnancy_status_non_yes_part").hide();

            } else {
                $("#husband_living_place").prop('required', true);
                $("#birth_control_method_usage_status").prop('required', true);
                $("#birth_control_method").prop('required', true);
                $("#birth_control_method_other_value").prop('required', true);
                $("#continuously_using_how_many_year").prop('required', true);
                $("#continuously_using_how_many_month").prop('required', true);
                $("#birth_control_method_suggestion_from_where").prop('required', true);
                $("#birth_control_method_suggestion_from_where_other_value").prop('required', true);
                $("#whose_decision").prop('required', true);
                $("#whose_decision_other_value").prop('required', true);
                $("#reason_behind_not_using").prop('required', true);
                $("#reason_behind_not_using_other_value").prop('required', true);
                $(".current_pregnancy_status_non_yes_part").show();

                $('#birth_control_method_usage_status').val('').trigger('change');
            }
        });
    }).change();

    $("#birth_control_method_usage_status").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue == 4) {
                $("#birth_control_method").prop('required', false);
                $("#birth_control_method_other_value").prop('required', false);
                $("#continuously_using_how_many_year").prop('required', false);
                $("#continuously_using_how_many_month").prop('required', false);
                $("#birth_control_method_suggestion_from_where").prop('required', false);
                $("#birth_control_method_suggestion_from_where_other_value").prop('required', false);
                $("#whose_decision").prop('required', false);
                $("#whose_decision_other_value").prop('required', false);
                $("#reason_behind_not_using").prop('required', false);
                $("#reason_behind_not_using_other_value").prop('required', false);
                $(".birth_control_method_usage_status_yes_part").hide();

            } else {
                $("#birth_control_method").prop('required', true);
                $("#birth_control_method_other_value").prop('required', true);
                $("#continuously_using_how_many_year").prop('required', true);
                $("#continuously_using_how_many_month").prop('required', true);
                $("#birth_control_method_suggestion_from_where").prop('required', true);
                $("#birth_control_method_suggestion_from_where_other_value").prop('required', true);
                $("#whose_decision").prop('required', true);
                $("#whose_decision_other_value").prop('required', true);
                $("#reason_behind_not_using").prop('required', true);
                $("#reason_behind_not_using_other_value").prop('required', true);
                $(".birth_control_method_usage_status_yes_part").show();
            }
        });
    }).change();

    $("#whose_decision").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue > 0) {
                $("#reason_behind_not_using").prop('required', false);
                $("#reason_behind_not_using_other_value").prop('required', false);
                $("#future_desire").prop('required', false);
                $("#reason_behind_not_having_future_desire").prop('required', false);
                $("#reason_behind_not_having_future_desire_other_value").prop('required', false);
                $("#do_you_know_from_where").prop('required', false);

                $(".whose_decision_part").hide();

            } else {
                $("#reason_behind_not_using").prop('required', true);
                $("#reason_behind_not_using_other_value").prop('required', true);
                $("#future_desire").prop('required', true);
                $("#reason_behind_not_having_future_desire").prop('required', true);
                $("#reason_behind_not_having_future_desire_other_value").prop('required', true);
                $("#do_you_know_from_where").prop('required', true);
                $(".whose_decision_part").show();
            }
        });
    }).change();

    $("#birth_control_method").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue == 455) {
                $("#birth_control_method_other_value").prop('required', true);
                $(".birth_control_method_other_part").show();
            } else {
                $("#birth_control_method_other_value").prop('required', false);
                $(".birth_control_method_other_part").hide();
            }
        });
    }).change();
    $("#birth_control_method_suggestion_from_where").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue == 470) {
                $("#birth_control_method_suggestion_from_where_other_value").prop('required', true);
                $(".birth_control_method_suggestion_from_where_other_part").show();
            } else {
                $("#birth_control_method_suggestion_from_where_other_value").prop('required', false);
                $(".birth_control_method_suggestion_from_where_other_part").hide();
            }
        });
    }).change();
    
    $("#alive_children").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue == 504) {
                $("#alive_children_yes_availability").prop('required', false);
                $(".alive_children_yes_availability_part").hide();
            } else {
                $("#alive_children_yes_availability").prop('required', true);
                $(".alive_children_yes_availability_part").show();
            }
        });
    }).change();
    $("#birth_control_method_suggestion_from_where").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue == 470) {
                $("#birth_control_method_suggestion_from_where_other_value").prop('required', true);
                $(".birth_control_method_suggestion_from_where_other_part").show();
            } else {
                $("#birth_control_method_suggestion_from_where_other_value").prop('required', false);
                $(".birth_control_method_suggestion_from_where_other_part").hide();
            }
        });
    }).change();
    $("#whose_decision").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue == 474) {
                $("#whose_decision_other_value").prop('required', true);
                $(".whose_decision_other_part").show();
            } else {
                $("#whose_decision_other_value").prop('required', false);
                $(".whose_decision_other_part").hide();
            }
        });
    }).change();
    $("#alive_children_yes_availability").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue == 505 || optionValue == 507) {
                $(".alive_children_yes_availability_parts").hide();


                if (optionValue == 507) {
                    $("#alive_children_yes_availability_other_value").prop('required', true);
                    $(".alive_children_yes_availability_other_part").show();
                } else {
                    $("#alive_children_yes_availability_other_value").prop('required', false);
                    $(".alive_children_yes_availability_other_part").hide();
                }
            }
            else if (optionValue == 506) {
                $(".alive_children_no_availability_related_part").hide();
                $(".alive_children_no_availability_non_related_part").show();
            } else {
                $(".alive_children_yes_availability_parts").show();
                $("#alive_children_yes_availability_other_value").prop('required', false);
                $(".alive_children_yes_availability_other_part").hide();
            }
        });
    }).change();
    $("#alive_children_no_availability").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");

            if (optionValue == 505 || optionValue == 507) {
                $(".alive_children_no_availability_parts").hide();
                if (optionValue == 507) {
                    $("#alive_children_no_availability_other_value").prop('required', true);
                    $(".alive_children_no_availability_other_part").show();
                } else {
                    $("#alive_children_no_availability_other_value").prop('required', false);
                    $(".alive_children_no_availability_other_part").hide();
                }
            } else {
                $(".alive_children_no_availability_parts").show();
                $("#alive_children_no_availability_other_value").prop('required', false);
                $(".alive_children_no_availability_other_part").hide();
            }
        });
    }).change();
    
    $("#reason_behind_not_having_future_desire").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue == 491) {
                $("#reason_behind_not_having_future_desire_other_value").prop('required', true);
                $(".reason_behind_not_having_future_desire_other_part").show();
            } else {
                $("#reason_behind_not_having_future_desire_other_value").prop('required', false);
                $(".reason_behind_not_having_future_desire_other_part").hide();
            }
        });
    }).change();
    
    $("#how_many_male_female_children").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue == 511) {
                $("#how_many_male_female_children_other_value").prop('required', true);
                $(".how_many_male_female_children_other_part").show();
            } else {
                $("#how_many_male_female_children_other_value").prop('required', false);
                $(".how_many_male_female_children_other_part").hide();
            }
        });
    }).change();

</script>