function toggledMenu() {
    if ($('#sidebar').hasClass('toggled')) {
        $('#sidebar').removeClass('toggled')
        $('#sidebar nav').removeClass('toggled')
    } else {
        $('#sidebar').addClass('toggled')
        $('#sidebar nav').addClass('toggled')
    }
}

