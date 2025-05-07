function navbarmanager() {
    var x = document.getElementById("OTMTopnav");
    if (x.className === "topnav") {
      x.className += " responsive";
    } else {
      x.className = "topnav";
    }
}

function copyEmailToClipboard(email) {
    if (!email) {
        // Thử lấy từ thuộc tính data-email của nút copy
        var btn = document.getElementById('copyemailbtn');
        if (btn && btn.dataset.email) {
            email = btn.dataset.email;
        }
    }
    if (email) {
        navigator.clipboard.writeText(email);
        var btn = document.getElementById('copyemailbtn');
        if (btn) btn.innerHTML = '<i class="fas fa-check-circle" style="color: green;"></i> Copied!';
    }
}