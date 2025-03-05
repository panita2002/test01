$(document).ready(function () {
    // จัดการฟอร์ม Security Question
    $("#security-form").submit(function (event) {
        event.preventDefault();
        var question = $("#security-question").val();
        var answer = $("#security-answer").val();

        $.post("../php/check_security.php", { question: question, answer: answer }, function (response) {
            if (response === "success") {
                $(".tab-content").removeClass("active"); 
                $("#reset-tab").addClass("active"); 
            } else {
                $("#answer-error").show();
            }
        });
    });

    // จัดการฟอร์ม Reset Password
    $("#reset-form").submit(function (event) {
        event.preventDefault();
        var newPassword = $("#new-password").val();
        var confirmPassword = $("#confirm-password").val();

        if (newPassword.length < 8) {
            $("#password-error").show();
            return;
        } else {
            $("#password-error").hide();
        }

        if (newPassword !== confirmPassword) {
            $("#confirm-error").show();
            return;
        } else {
            $("#confirm-error").hide();
        }

        $.post("../php/reset_password.php", { password: newPassword }, function (response) {
            if (response === "success") {
                alert("Your password has been successfully changed!");
                window.location.href = "login.html";
            } else {
                alert("An error occurred. Please try again.");
            }
        });
    });
});
