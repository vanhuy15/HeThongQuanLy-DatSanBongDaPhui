/**
 * ADMIN TOAST MODULE
 * Handles auto-dismissing notifications and floating toast alerts
 */

function showAdminMessage(message, type = "success", duration = 4000) {
  let container = document.getElementById("adminToastContainer");
  if (!container) {
    container = document.createElement("div");
    container.id = "adminToastContainer";
    container.style.cssText = `
      position: fixed; top: 20px; right: 20px;
      display: flex; flex-direction: column; gap: 10px;
      z-index: 999999; pointer-events: none;
    `;
    document.body.appendChild(container);
  }

  const div = document.createElement("div");
  div.className = `admin-message ${type}`;
  div.style.cssText = `
    pointer-events: auto;
    padding: 12px 18px;
    background: #ffffff;
    color: #0f172a;
    border: 1px solid #e2e8f0;
    border-left: 4px solid ${type === "success" ? "#0d9488" : "#ef4444"};
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
    display: flex; align-items: center; gap: 10px;
    opacity: 0; transform: translateX(30px);
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  `;
  const icon = type === "success" ? "fa-bell" : "fa-exclamation-circle";
  const iconColor = type === "success" ? "#0d9488" : "#ef4444";
  div.innerHTML = `<i class="fas ${icon}" style="color: ${iconColor}; font-size: 1rem;"></i> <span style="flex: 1;">${message}</span> <button type="button" style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:1.1rem;line-height:1;padding:0 0 0 8px;" onclick="this.parentElement.remove()">&times;</button>`;

  container.appendChild(div);
  setTimeout(() => {
    div.style.opacity = "1";
    div.style.transform = "translateX(0)";
  }, 10);
  setTimeout(() => {
    div.style.opacity = "0";
    div.style.transform = "translateX(30px)";
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
let lastAdminPendingConfirm = null;
let lastAdminPendingRefund = null;

function initAdminRealtimeNotification() {
  const bellBtn = document.getElementById('notifBellBtn');
  if (!bellBtn) return;

  function checkAdminPending() {
    fetch('/quanlysanbong/index.php?controller=Notification&action=getAdminPendingCount')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const currentCount = parseInt(data.count, 10);
          const currentConfirm = parseInt(data.pending_confirm ?? 0, 10);
          const currentRefund = parseInt(data.pending_refund ?? 0, 10);

          if (lastAdminPendingCount !== null) {
            // Có yêu cầu hoàn tiền mới từ khách
            if (lastAdminPendingRefund !== null && currentRefund > lastAdminPendingRefund) {
              playAdminAudioChime();
              const diffRf = currentRefund - lastAdminPendingRefund;
              showAdminMessage('Bạn có ' + (diffRf > 1 ? diffRf + ' yêu cầu hoàn tiền mới!' : '1 yêu cầu hoàn tiền mới cần xử lý!'), 'success', 5000);
            } 
            // Có đơn đặt sân mới (tiền mặt hoặc chuyển khoản) cần duyệt
            else if (lastAdminPendingConfirm !== null && currentConfirm > lastAdminPendingConfirm) {
              playAdminAudioChime();
              const diffCf = currentConfirm - lastAdminPendingConfirm;
              showAdminMessage('Bạn có ' + (diffCf > 1 ? diffCf + ' đơn đặt sân mới cần duyệt!' : '1 đơn đặt sân mới cần duyệt!'), 'success', 5000);
            }
          }
          lastAdminPendingCount = currentCount;
          lastAdminPendingConfirm = currentConfirm;
          lastAdminPendingRefund = currentRefund;

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

  // Khởi động kiểm tra và lặp lại mỗi 15 giây
  checkAdminPending();
  setInterval(checkAdminPending, 15000);
}

document.addEventListener('DOMContentLoaded', initAdminRealtimeNotification);
