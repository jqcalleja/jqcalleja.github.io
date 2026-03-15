$(document).ready(function () {

    /* AUTO CAPITALIZE FIELDS */

    $("#last_name, #first_name").on("input", function () {
        this.value = this.value.toUpperCase();
    });

    $("#middle_initial").on("input", function () {
        this.value = this.value.toUpperCase().slice(0, 1);
    });


    /* FORM SUBMISSION */

    $("#studentForm").submit(function (e) {

        e.preventDefault();

        let id = $("#id_number").val().trim();
        let last = $("#last_name").val().trim();
        let first = $("#first_name").val().trim();
        let mi = $("#middle_initial").val().trim();
        let email = $("#email").val().trim();
        let section = $("#section").val();
        let certification = $("#certification_exam").val();

        let nameRegex = /^[A-Za-z]+$/;
        let idRegex = /^[0-9\-]+$/;
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!idRegex.test(id)) {
            $("#message").text("Invalid ID Number");
            return;
        }

        if (!nameRegex.test(last)) {
            $("#message").text("Last name must contain letters only");
            return;
        }

        if (!nameRegex.test(first)) {
            $("#message").text("First name must contain letters only");
            return;
        }

        if (mi !== "" && !nameRegex.test(mi)) {
            $("#message").text("Middle initial must be a letter");
            return;
        }

        if (!emailRegex.test(email)) {
            $("#message").text("Invalid email address");
            return;
        }

        if (section === "") {
            $("#message").text("Please select a section");
            return;
        }

        if(certification === ""){
            $("#message").text("Please select a certification exam");
            return;
        }

        $.ajax({

            url: "save_student.php",
            type: "POST",
            data: $("#studentForm").serialize(),

            success: function (response) {

                $("#message").text(response);
                $("#studentForm")[0].reset();

            }

        });

    });

});