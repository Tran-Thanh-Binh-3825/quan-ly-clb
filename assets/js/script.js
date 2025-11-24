/* --- assets/js/script.js --- */

// Hàm cài đặt Modal: Truyền vào ID của Modal và ID của Nút mở
function setupModal(modalId, btnId) {
    var modal = document.getElementById(modalId);
    var btn = document.getElementById(btnId);

    // Tìm nút đóng (class .close-btn) bên trong modal này
    var span = modal ? modal.getElementsByClassName("close-btn")[0] : null;

    // Nếu không tìm thấy phần tử thì dừng, tránh lỗi
    if (!modal || !btn || !span) return;

    // Mở khi bấm nút
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // Đóng khi bấm X
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Đóng khi bấm ra ngoài vùng trắng
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
}