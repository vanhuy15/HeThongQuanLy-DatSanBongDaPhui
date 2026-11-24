/**
 * PAYMENT MODULE JAVASCRIPT (assets/js/payment.js)
 * Manages Payment Method Toggles, VietQR Copy text, and Hold Countdown
 */

document.addEventListener("DOMContentLoaded", function () {
  initPaymentView();
});

function initPaymentView() {
  const timerEl = document.getElementById("countdownTimer");
  if (timerEl) {
    const rawRemaining = timerEl.dataset.remainingSeconds;
    let remaining = rawRemaining ? parseInt(rawRemaining, 10) : 0;
    const isCashLocked = timerEl.dataset.cashLocked === "1";

    function formatTime(sec) {
      const m = Math.floor(sec / 60);
      const s = sec % 60;
      return (m < 10 ? "0" : "") + m + ":" + (s < 10 ? "0" : "") + s;
    }

    timerEl.textContent = formatTime(remaining);

    const countdownInterval = setInterval(() => {
      remaining--;
      if (remaining <= 0) {
        clearInterval(countdownInterval);
        timerEl.textContent = "00:00 (Hết hạn)";
        alert("Thời gian giữ chỗ 10 phút đã hết hạn! Sân đã được tự động mở lại cho người khác.");
        window.location.reload();
      } else {
        timerEl.textContent = formatTime(remaining);
      }
    }, 1000);

    }

  // Bind change on radios
  document.querySelectorAll('input[name="phuong_thuc"]').forEach((radio) => {
    radio.addEventListener("change", toggleQR);
  });
}

function selectPaymentMethod(type) {
  if (type === 'tien_mat') {
    const cashCard = document.getElementById('cardTienMat');
    if (cashCard && cashCard.classList.contains('locked')) {
      alert('⚠️ Điểm uy tín của bạn dưới 60đ nên phương thức Tiền mặt đã bị khóa. Vui lòng chọn VietQR!');
      return;
    }
    const rTienMat = document.getElementById('radioTienMat');
    if (rTienMat) rTienMat.checked = true;
  } else if (type === 'chuyen_khoan') {
    const rQR = document.getElementById('radioChuyenKhoan');
    if (rQR) rQR.checked = true;
  }
  toggleQR();
}

function toggleQR() {
  const checkedRadio = document.querySelector('input[name="phuong_thuc"]:checked');
  const pt = checkedRadio ? checkedRadio.value : "";
  const qrBox = document.getElementById("chuyenKhoanBox");
  const cashGuideBox = document.getElementById("cashGuideBox");
  const emptyGuideBox = document.getElementById("emptyGuideBox");
  const btnWrapper = document.getElementById("submitBtnWrapper");
  const btnText = document.getElementById("submitBtnText");
  const submitBtn = document.getElementById("btnSubmitPayment");

  // Highlight active payment card
  document.querySelectorAll('.payment-card').forEach(card => card.classList.remove('selected'));
  if (pt === "chuyen_khoan") {
    const cardQR = document.getElementById("cardChuyenKhoan");
    if (cardQR) cardQR.classList.add("selected");
  } else if (pt === "tien_mat") {
    const cardTM = document.getElementById("cardTienMat");
    if (cardTM) cardTM.classList.add("selected");
  }

  if (!pt) {
    if (emptyGuideBox) emptyGuideBox.style.display = "flex";
    if (qrBox) qrBox.style.display = "none";
    if (cashGuideBox) cashGuideBox.style.display = "none";
    if (btnWrapper) btnWrapper.style.display = "none";
    return;
  }

  if (emptyGuideBox) emptyGuideBox.style.display = "none";

  if (pt === "chuyen_khoan") {
    if (qrBox) qrBox.style.display = "block";
    if (cashGuideBox) cashGuideBox.style.display = "none";
    // Ẩn hoàn toàn nút xác nhận chuyển khoản thủ công, vì QR tự động xác nhận qua SePAY Webhook
    if (btnWrapper) btnWrapper.style.display = "none";
  } else if (pt === "tien_mat") {
    if (qrBox) qrBox.style.display = "none";
    if (cashGuideBox) cashGuideBox.style.display = "flex";
    // Chỉ hiển thị nút xác nhận đặt sân khi người dùng chọn Tiền mặt
    if (btnWrapper) {
      btnWrapper.style.display = "block";
      btnWrapper.style.textAlign = "center";
    }
    if (btnText) btnText.textContent = "Xác nhận đặt sân";
    if (submitBtn) {
      submitBtn.style.background = "#0d9488";
      submitBtn.style.boxShadow = "0 4px 12px rgba(13, 148, 136, 0.25)";
    }
  }
}

function copyText(elementId, btnEl) {
  const textEl = document.getElementById(elementId);
  if (!textEl) return;
  const text = textEl.textContent.trim();

  function setSuccessState() {
    btnEl.innerHTML = '<i class="fas fa-check" style="color: #10b981;"></i>';
    btnEl.classList.add("copied");
    setTimeout(() => {
      btnEl.innerHTML = '<i class="far fa-copy"></i>';
      btnEl.classList.remove("copied");
    }, 1500);
  }

  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard
      .writeText(text)
      .then(setSuccessState)
      .catch(() => fallbackCopy(text, setSuccessState));
  } else {
    fallbackCopy(text, setSuccessState);
  }
}

function fallbackCopy(text, callback) {
  const temp = document.createElement("textarea");
  temp.value = text;
  temp.style.position = "fixed";
  temp.style.left = "-9999px";
  document.body.appendChild(temp);
  temp.select();
  try {
    document.execCommand("copy");
    if (callback) callback();
  } catch (e) {
    alert("Đã sao chép: " + text);
  }
  document.body.removeChild(temp);
}

/**
 * Âm thanh báo thành công bằng Web Audio API (Ting-Ting thanh thoát, 0 cần file mp3)
 */
function playPaymentSuccessChime() {
  try {
    const AudioCtx = window.AudioContext || window.webkitAudioContext;
    if (!AudioCtx) return;
    const ctx = new AudioCtx();
    if (ctx.state === 'suspended') ctx.resume();
    const now = ctx.currentTime;

    // Tone 1: E5 (659Hz)
    const osc1 = ctx.createOscillator();
    const g1 = ctx.createGain();
    osc1.type = 'sine';
    osc1.frequency.setValueAtTime(659, now);
    g1.gain.setValueAtTime(0, now);
    g1.gain.linearRampToValueAtTime(0.2, now + 0.05);
    g1.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
    osc1.connect(g1);
    g1.connect(ctx.destination);
    osc1.start(now);
    osc1.stop(now + 0.4);

    // Tone 2: A5 (880Hz)
    const osc2 = ctx.createOscillator();
    const g2 = ctx.createGain();
    osc2.type = 'sine';
    osc2.frequency.setValueAtTime(880, now + 0.12);
    g2.gain.setValueAtTime(0, now + 0.12);
    g2.gain.linearRampToValueAtTime(0.25, now + 0.17);
    g2.gain.exponentialRampToValueAtTime(0.001, now + 0.7);
    osc2.connect(g2);
    g2.connect(ctx.destination);
    osc2.start(now + 0.12);
    osc2.stop(now + 0.7);
  } catch (e) {
    // Browser muted
  }
}

/**
 * Polling kiểm tra trạng thái thanh toán theo thời gian thực (Webhook Auto-confirm)
 */
let paymentPollInterval = null;

function startPaymentStatusPolling(datSanId) {
  if (!datSanId || paymentPollInterval) return;

  paymentPollInterval = setInterval(() => {
    fetch('index.php?controller=Payment&action=checkStatus&dat_san_id=' + encodeURIComponent(datSanId))
      .then(res => res.json())
      .then(data => {
        if (data.success && (data.is_confirmed || data.is_paid)) {
          clearInterval(paymentPollInterval);
          paymentPollInterval = null;

          playPaymentSuccessChime();

          const form = document.getElementById('formThanhToan');
          if (form) {
            form.className = '';
            form.style.display = 'block';
            form.innerHTML = `
              <div style="background: #ecfdf5; border: 2px solid #10b981; border-radius: 20px; padding: 45px 25px; text-align: center; max-width: 520px; margin: 0 auto; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.1); animation: modalFadeIn 0.3s ease;">
                <div style="width: 72px; height: 72px; background: #10b981; color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2.2rem; margin-bottom: 18px; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);">
                  <i class="fas fa-check"></i>
                </div>
                <h3 style="color: #065f46; font-size: 1.5rem; font-weight: 800; margin-bottom: 8px;">
                  THANH TOÁN THÀNH CÔNG!
                </h3>
                <p style="color: #047857; font-size: 0.95rem; margin-bottom: 18px;">
                  Hệ thống VietQR Webhook đã tự động xác nhận đơn <strong>#${datSanId}</strong>.
                </p>
                <div style="color: #059669; font-size: 0.88rem; font-weight: 600;" id="redirectCountdownText">
                  <i class="fas fa-spinner fa-spin"></i> Tự động chuyển về lịch sử đặt sân sau <span id="redirectSec">4</span> giây...
                </div>
              </div>
            `;
            let sec = 4;
            const redirectSecEl = document.getElementById('redirectSec');
            const countdownRedirect = setInterval(() => {
              sec--;
              if (redirectSecEl) redirectSecEl.textContent = sec;
              if (sec <= 0) {
                clearInterval(countdownRedirect);
                window.location.href = 'index.php?controller=User&action=history';
              }
            }, 1000);
          }
        } else if (data.success && data.is_cancelled) {
          clearInterval(paymentPollInterval);
          paymentPollInterval = null;
          alert("Thời gian giữ chỗ 10 phút đã hết hạn! Sân đã được tự động mở lại cho người khác.");
          window.location.href = 'index.php?controller=User&action=history';
        }
      })
      .catch(err => console.error("Poll error:", err));
  }, 2500);
}

/**
 * Bắn Webhook giả lập (Cách 1: SePAY Simulator 0đ)
 */
function triggerWebhookSimulation(datSanId) {
  const btn = document.getElementById('btnSimulateWebhook') || document.getElementById('btnConfirmSepayPayment');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang bắn Webhook giả lập...';
  }

  fetch('index.php?controller=Payment&action=simulateWebhook&dat_san_id=' + encodeURIComponent(datSanId))
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        if (btn) {
          btn.innerHTML = '<i class="fas fa-check"></i> Đã kích hoạt giả lập thành công!';
          btn.style.background = '#059669';
        }
      } else {
        alert(data.message || 'Lỗi giả lập Webhook');
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-check"></i> Xác nhận thanh toán qua SePAY';
        }
      }
    })
    .catch(err => {
      alert('Lỗi kết nối: ' + err);
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Xác nhận thanh toán qua SePAY';
      }
    });
}

/**
 * Các hàm hỗ trợ Kéo - Thả mã QR vào SePAY Simulator
 */
function handleQrDragOver(e) {
  e.preventDefault();
  e.stopPropagation();
  const dropArea = document.getElementById("sepayDropArea");
  if (dropArea) {
    dropArea.style.borderColor = "#10b981";
    dropArea.style.background = "#ecfdf5";
  }
}

function handleQrDragLeave(e) {
  e.preventDefault();
  e.stopPropagation();
  const dropArea = document.getElementById("sepayDropArea");
  if (dropArea) {
    dropArea.style.borderColor = "#cbd5e1";
    dropArea.style.background = "#f8fafc";
  }
}

function handleQrDrop(e) {
  e.preventDefault();
  e.stopPropagation();
  triggerSimulateDrop();
}

function triggerSimulateDrop() {
  const dropArea = document.getElementById("sepayDropArea");
  const scannedInfo = document.getElementById("sepayScannedInfo");
  if (dropArea && scannedInfo) {
    dropArea.style.display = "none";
    scannedInfo.style.display = "block";
  }
}

function executeSepayAutoConfirm(datSanId, soTien) {
  const btn = document.getElementById("btnConfirmSepayPayment");
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SePAY đang gửi Webhook duyệt tự động...';
  }

  const payload = {
    id: Date.now(),
    gateway: "MBBank",
    transactionDate: new Date().toISOString(),
    accountNumber: "1111000050174",
    content: "DATSAN " + datSanId,
    transferAmount: soTien,
    transferType: "in"
  };

  fetch("index.php?controller=Payment&action=webhook", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(payload)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success && data.processed > 0) {
        if (btn) {
          btn.innerHTML = '<i class="fas fa-check"></i> SePAY đã xác nhận giao dịch!';
          btn.style.background = "#059669";
        }
      } else {
        triggerWebhookSimulation(datSanId);
      }
    })
    .catch(() => {
      triggerWebhookSimulation(datSanId);
    });
}

