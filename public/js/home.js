document.addEventListener('DOMContentLoaded', function() {
    // Toast
    const toast = document.getElementById('toast-success');
    if (toast) {
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 6000);
    }
    
    // Borrow Modal
    document.querySelectorAll('.borrow-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modalMaTB').value = this.dataset.matb;
            document.getElementById('modalTenTB').innerText = this.dataset.tentb;
            document.getElementById('borrowModal').style.display = 'flex';
        });
    });

    // Info Modal
    document.querySelectorAll('.info-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('infoTenTB').innerText = this.dataset.tentb;
            document.getElementById('infoNguoiMuon').innerText = this.dataset.nguoimuon;
            document.getElementById('infoNgayTra').innerText = this.dataset.ngaytra;
            document.getElementById('infoModal').style.display = 'flex';
        });
    });

    // Close modal
    document.querySelectorAll('.modal .close, .modal .btn-secondary').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });

    // Click ngoài modal để đóng
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
});
