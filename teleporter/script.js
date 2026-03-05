$(document).ready(function () {

    let roomsData = [];

    // =============================
    // Load JSON via AJAX
    // =============================
    $.getJSON("rooms.json", function (data) {
        roomsData = data.rooms;
        renderRooms(roomsData);
    }).fail(function () {
        alert("Error loading JSON file.");
    });

    // =============================
    // Render Rooms Function
    // =============================
    function renderRooms(rooms) {
        $("#roomContainer").empty();

        $.each(rooms, function (index, room) {
            let button = $("<button>")
                .addClass("room-button")
                .text(room.name)
                .click(function () {
                    window.open(room.url, "_blank");
                });

            $("#roomContainer").append(button);
        });
    }

    // =============================
    // Search Filter
    // =============================
    $("#searchInput").on("input", function () {
        let searchValue = $(this).val().toLowerCase();

        let filtered = roomsData.filter(function (room) {
            return room.name.toLowerCase().includes(searchValue);
        });

        renderRooms(filtered);
    });

});