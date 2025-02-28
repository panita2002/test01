// forgot_password.js - เชื่อมต่อ API PHP สำหรับลืมรหัสผ่าน
function setLoading(buttonId, isLoading, text) {
    const button = document.getElementById(buttonId);
    if (button) {
        button.textContent = text;
        button.disabled = isLoading;
    }
}

function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

// ส่งอีเมลเพื่อรับ Token
function sendEmailOTP(e) {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
        showError('email-error', 'รูปแบบอีเมลไม่ถูกต้อง');
        return false;
    }

    setLoading('email-submit', true, 'กำลังส่ง...');

    $.ajax({
        url: '../php/send_otp.php',
        type: 'POST',
        data: { email: email },
        dataType: 'json',
        timeout: 10000,
        success: function (response) {
            if (response.status === 'success') {
                document.getElementById('sent-to').textContent = email;
                startCountdown();
                switchTab('otp-tab');
                setupOTPInputs();
            } else {
                showError('email-error', response.message);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                showError('email-error', 'การเชื่อมต่อหมดเวลา กรุณาลองใหม่');
            } else {
                showError('email-error', `เกิดข้อผิดพลาด: ${jqXHR.status} ${errorThrown}`);
            }
        },
        complete: function () {
            setLoading('email-submit', false, 'ส่งรหัส OTP');
        }
    });

    return false;
}

// ตรวจสอบ Token
function verifyToken(e) {
    e.preventDefault();
    const token = document.getElementById('otp-token').value.trim();
    const tokenPattern = /^[A-Fa-f0-9]{64}$/;

    if (!tokenPattern.test(token)) {
        showError('otp-error', 'Token ไม่ถูกต้อง');
        return false;
    }

    setLoading('otp-submit', true, 'กำลังตรวจสอบ...');

    $.ajax({
        url: '../php/verify_otp.php',
        type: 'POST',
        data: { token: token },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                switchTab('reset-tab');
            } else {
                showError('otp-error', response.message);
            }
        },
        error: function () {
            showError('otp-error', 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
        },
        complete: function () {
            setLoading('otp-submit', false, 'ยืนยันรหัส');
        }
    });

    return false;
}

// รีเซ็ตรหัสผ่าน
function resetPassword(e) {
    e.preventDefault();
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const token = document.getElementById('otp-token').value.trim();
    const csrfToken = document.getElementById('csrf-token').value;

    if (newPassword.length < 8 || !/[A-Za-z]/.test(newPassword) || !/[0-9]/.test(newPassword)) {
        showError('password-error', 'รหัสผ่านต้องมีตัวอักษรและตัวเลข และมีอย่างน้อย 8 ตัวอักษร');
        return false;
    }

    if (newPassword !== confirmPassword) {
        showError('confirm-error', 'รหัสผ่านไม่ตรงกัน');
        return false;
    }

    setLoading('reset-submit', true, 'กำลังบันทึก...');

    $.ajax({
        url: '../php/reset_password.php',
        type: 'POST',
        data: { password: newPassword, token: token, csrf_token: csrfToken },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                switchTab('success-tab');
            } else {
                alert(response.message);
            }
        },
        error: function () {
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
        },
        complete: function () {
            setLoading('reset-submit', false, 'บันทึกรหัสผ่านใหม่');
        }
    });

    return false;
}

// ส่ง Token ใหม่
function resendToken() {
    const email = document.getElementById('sent-to').textContent;
    const resendLink = document.getElementById('resend-link');
    if (resendLink.classList.contains('disabled')) {
        alert('กรุณารอให้ครบ 60 วินาทีก่อนส่งรหัสใหม่');
        return;
    }

    resendLink.textContent = 'กำลังส่ง...';
    resendLink.classList.add('disabled');

    $.ajax({
        url: '../php/send_otp.php',
        type: 'POST',
        data: { email: email },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                startCountdown();
                alert('ส่ง Token ใหม่แล้ว กรุณาตรวจสอบอีเมลของคุณ');
            } else {
                alert(response.message);
                resendLink.textContent = 'ส่งรหัสใหม่';
                resendLink.classList.remove('disabled');
            }
        },
        error: function () {
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
            resendLink.textContent = 'ส่งรหัสใหม่';
            resendLink.classList.remove('disabled');
        }
    });
}

// ตั้งค่าการพิมพ์ในช่อง OTP
function setupOTPInputs() {
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach((input, index) => {
        input.addEventListener('input', function () {
            if (this.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Backspace' && this.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });
}
