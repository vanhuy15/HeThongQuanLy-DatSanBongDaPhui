/**
 * ADMIN TOAST MODULE
 * Handles auto-dismissing notifications and floating toast alerts
 */

function showAdminMessage(message, type = "success", duration = 3000) {
  const div = document.createElement("div");
  div.className = `admin-message ${type}`;
  div.textContent = message;
  div.style.cssText = `
        position: fixed; top: 20px; right: 20px; padding: 12px 22px;
        background: ${type === "success" ? "linear-gradient(135deg, #10b981, #059669)" : "linear-gradient(135deg, #ef4444, #dc2626)"};
        color: white; border-radius: 10px; font-weight: 600;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        opacity: 0; transform: translateY(-20px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); z-index: 99999;
    `;
  document.body.appendChild(div);
  setTimeout(() => {
    div.style.opacity = "1";
    div.style.transform = "translateY(0)";
  }, 10);
  setTimeout(() => {
    div.style.opacity = "0";
    div.style.transform = "translateY(-20px)";
    setTimeout(() => div.remove(), 400);
  }, duration);
}

function autoHideAdminMessages() {
  const msgs = document.querySelectorAll(".admin-message");
  msgs.forEach((msg) => {
    setTimeout(() => {
      msg.style.opacity = "0";
      msg.style.transform = "translateY(-20px)";
      setTimeout(() => msg.remove(), 400);
    }, 3000);
  });
}

/**
 * Âm thanh thông báo cho Admin (Web Audio API Ting-Ting chuyên nghiệp, 0 cần mp3)
 */
function playAdminAudioChime() {
  try {
    const AudioCtx = window.AudioContext || window.webkitAudioContext;
    if (!AudioCtx) return;
    const ctx = new AudioCtx();
    if (ctx.state === 'suspended') ctx.resume();
    const now = ctx.currentTime;

    const osc = ctx.createOscillator();
    const g = ctx.createGain();
    osc.type = 'sine';
    osc.frequency.setValueAtTime(784, now);
    osc.frequency.exponentialRampToValueAtTime(1046, now + 0.15);
    g.gain.setValueAtTime(0, now);
    g.gain.linearRampToValueAtTime(0.25, now + 0.05);
    g.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
    osc.connect(g);
    g.connect(ctx.destination);
    osc.start(now);
    osc.stop(now + 0.6);
  } catch(e) {}
}

/**
 * Polling kiểm tra số lượng đơn chờ duyệt mới cho Admin theo thời gian thực
 */
let lastAdminPendingCount = null;

function initAdminRealtimeNotification() {
  const bellBtn = document.getElementById('notifBellBtn');
  if (!bellBtn) return;

  function checkAdminPending() {
    fetch('/quanlysanbong/index.php?controller=Notification&action=getAdminPendingCount')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const currentCount = parseInt(data.count, 10);
          if (lastAdminPendingCount !== null && currentCount > lastAdminPendingCount) {
            playAdminAudioChime();
            showAdminMessage('🔔 Có đơn đặt sân mới cần duyệt! (Tổng: ' + currentCount + ')', 'success', 5000);
          }
          lastAdminPendingCount = currentCount;

          let badge = bellBtn.querySelector('#adminNotifBadge') || bellBtn.querySelector('.notification-badge');
          if (badge) {
            if (currentCount > 0) {
              badge.textContent = currentCount > 99 ? '99+' : currentCount;
              badge.style.display = 'flex';
            } else {
              badge.style.display = 'none';
            }
          }
        }
      })
      .catch(err => console.error(err));
  }

  // Khởi động kiểm tra và lặp lại mỗi 20 giây
  checkAdminPending();
  setInterval(checkAdminPending, 20000);
}

document.addEventListener('DOMContentLoaded', initAdminRealtimeNotification);
