$(document).ready(function () {
    let currentSection = "";

    loadStudents(currentSection);

    /* Filter by section */
    $("#sectionFilter").change(function () {
        currentSection = $(this).val();
        loadStudents(currentSection);
    });

    /* export button */
    $("#exportBtn").click(function () {
        window.location.href = "export_excel.php?section=" + currentSection;
    });

    /* Auto refresh every 5 seconds */
    setInterval(function () {
        loadStudents(currentSection);
    }, 5000);


    /* Function to load students */
    function loadStudents(section) {
        $.ajax({
            url: "fetch_students.php",
            type: "POST",
            data: { section: section },

            success: function (data) {
                $("#studentTable tbody").html(data);
            }
        });
    }
});