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


    function setQuickTime(hours) {
        let startInput = document.getElementById('ngayMuon');
        let endInput = document.getElementById('ngayTraDuKien');
        
        let startTime = startInput.value ? new Date(startInput.value) : new Date();
        
        if (!startInput.value) {
            let localStart = new Date(startTime.getTime() - (startTime.getTimezoneOffset() * 60000));
            startInput.value = localStart.toISOString().slice(0, 16);
        } else {
             startTime = new Date(startInput.value); 
        }

        let endTime = new Date(startTime.getTime() + hours * 60 * 60 * 1000);
        let localEndTime = new Date(endTime.getTime() - (endTime.getTimezoneOffset() * 60000));
        endInput.value = localEndTime.toISOString().slice(0, 16);
    }

    // HÀM GỌI API LỊCH CHUNG (Dùng cho cả 2 modal)
    function fetchSchedule(maTB, listElementId) {
        let listContainer = document.getElementById(listElementId);
        listContainer.innerHTML = '<li class="list-group-item bg-transparent text-center"><div class="spinner-border spinner-border-sm text-secondary"></div></li>';

        fetch(`/api/get-schedule/${maTB}`)
            .then(response => response.json())
            .then(data => {
                listContainer.innerHTML = '';
                let pendingList = data.filter(item => item.trangThai === 'Pending');

                if (pendingList.length === 0) {
                    listContainer.innerHTML = '<li class="list-group-item bg-transparent text-success text-center small fst-italic">✅ Không có lịch đặt trước nào.</li>';
                } else {
                    pendingList.forEach(item => {
                        let start = new Date(item.ngayMuon).toLocaleString('vi-VN', {hour: '2-digit', minute:'2-digit', day:'2-digit', month:'2-digit'});
                        let end = new Date(item.ngayTraDuKien).toLocaleString('vi-VN', {hour: '2-digit', minute:'2-digit', day:'2-digit', month:'2-digit'});
                        let ten = item.hoTen ? item.hoTen : 'Người dùng';
                        
                        let li = document.createElement('li');
                        li.className = 'list-group-item bg-transparent px-0 py-1 border-bottom';
                        li.innerHTML = `<div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold small text-dark">${ten}</span>
                                            <span class="small text-muted">${start} ➝ ${end}</span>
                                        </div>`;
                        listContainer.appendChild(li);
                    });
                }
            })
            .catch(err => { console.error(err); listContainer.innerHTML = '<li class="text-danger small">Lỗi tải dữ liệu</li>'; });
    }

    document.addEventListener('DOMContentLoaded', function() {
        let now = new Date();
        let localNow = new Date(now.getTime() - (now.getTimezoneOffset() * 60000));
        if(document.getElementById('ngayMuon')) {
            document.getElementById('ngayMuon').value = localNow.toISOString().slice(0, 16);
        }
        
        const borrowModal = document.getElementById('borrowModal');
        const infoModal = document.getElementById('infoModal');

        // --- 1. XỬ LÝ NÚT XANH (MƯỢN NGAY) ---
        document.querySelectorAll('.borrow-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let maTB = this.dataset.matb;
                document.getElementById('modalMaTB').value = maTB;
                document.getElementById('modalTenTB').innerText = this.dataset.tentb;
                document.getElementById('ngayTraDuKien').value = ''; 

                // GỌI API LỊCH CHO NÚT XANH LUÔN
                fetchSchedule(maTB, 'borrowScheduleList');

                borrowModal.style.display = 'flex';
            });
        });

        // --- 2. XỬ LÝ NÚT VÀNG (XEM & ĐẶT LỊCH) ---
        document.querySelectorAll('.info-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let maTB = this.dataset.matb;
                document.getElementById('infoMaTB').value = maTB;
                document.getElementById('infoTenTB').innerText = this.dataset.tentb;
                document.getElementById('infoNguoiMuon').innerText = this.dataset.nguoimuon;
                document.getElementById('infoNgayTra').innerText = this.dataset.ngaytra;

                // GỌI API LỊCH CHO NÚT VÀNG
                fetchSchedule(maTB, 'infoScheduleList');

                // Auto-fill Logic (như cũ)
                try {
                    let rawDate = this.dataset.ngaytraIso; 
                    if (rawDate) {
                        let returnDate = new Date(rawDate);
                        if (!isNaN(returnDate.getTime())) {
                            returnDate.setMinutes(returnDate.getMinutes() + 15);
                            let localDate = new Date(returnDate.getTime() - (returnDate.getTimezoneOffset() * 60000));
                            let startInput = document.querySelector('#infoModal input[name="ngayMuon"]');
                            if(startInput) startInput.value = localDate.toISOString().slice(0, 16);
                        }
                    }
                } catch(e) {}

                infoModal.style.display = 'flex';
            });
        });

        document.querySelectorAll('.close, .btn-secondary').forEach(el => {
            el.addEventListener('click', () => {
                borrowModal.style.display = 'none';
                infoModal.style.display = 'none';
            });
        });
        
        window.onclick = function(event) {
            if (event.target == borrowModal) borrowModal.style.display = "none";
            if (event.target == infoModal) infoModal.style.display = "none";
        }
    });
