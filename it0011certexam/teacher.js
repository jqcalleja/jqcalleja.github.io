$(document).ready(function () {
    let currentSection = "";
    let currentExam = "";

    loadStudents();

    /* SECTION FILTER */
    $("#sectionFilter").change(function () {
        currentSection = $(this).val();
        loadStudents();
    });

    /* EXAM FILTER */
    $("#examFilter").change(function () {
        currentExam = $(this).val();
        loadStudents();
    });

    /* AUTO REFRESH */
    setInterval(function () {
        loadStudents();
    }, 5000);

    /* EXPORT */
    $("#exportBtn").click(function () {
        window.location.href =
            "export_excel.php?section=" + currentSection +
            "&exam=" + currentExam;
    });

    function loadStudents() {
        $.ajax({
            url: "fetch_students.php",
            type: "POST",

            data: {
                section: currentSection,
                exam: currentExam
            },

            success: function (data) {
                $("#studentTable tbody").html(data);
            }
        });
    }
});