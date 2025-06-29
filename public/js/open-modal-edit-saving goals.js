function toggleEditMode(button, isEditing) {
    const modalContent = button.closest('.modal-content');
    if (!modalContent) return;
    const viewMode = modalContent.querySelector('.view-mode');
    const editMode = modalContent.querySelector('.edit-mode');
    if (isEditing) {
        viewMode.style.display = 'none';
        editMode.style.display = 'block';
    } else {
        viewMode.style.display = 'block';
        editMode.style.display = 'none';
    }
}

function toggleWithdrawMode(button, isWithdrawing) {
    const modalContent = button.closest('.modal-content');
    if (!modalContent) return;
    const viewMode = modalContent.querySelector('.view-mode');
    const withdrawMode = modalContent.querySelector('.withdraw-mode');
    if (isWithdrawing) {
        viewMode.style.display = 'none';
        withdrawMode.style.display = 'block';
    } else {
        viewMode.style.display = 'block';
        withdrawMode.style.display = 'none';
    }
}

function formatRupiah(inputElement, hiddenInputId) {
    let number_string = inputElement.value.replace(/[^,\d]/g, '').toString();
    document.getElementById(hiddenInputId).value = number_string;
    let split = number_string.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    inputElement.value = rupiah;
}