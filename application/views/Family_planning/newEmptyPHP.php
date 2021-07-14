$(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            
            if (optionValue == 4) {
                alert("NOOOOOOOOOO");
                $(".birth_control_method_usage_status_yes_part").hide();
                $(".birth_control_method_other_part").hide();
                $(".birth_control_method_suggestion_from_where_other_part").hide();
                $(".whose_decision_other_part").hide();
                

            } else {
                   alert("TESTTTT"); 
                $(".birth_control_method_usage_status_yes_part").show();
            }
        });