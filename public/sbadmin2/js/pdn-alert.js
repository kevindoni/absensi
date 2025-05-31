// SWEETALERT
// NOTIFIKASI
function pdnAlert(icon, message)
{
	if(icon === 'success'){
		Swal.fire({
            position: "top-end",
            icon: "success",
            title: message,
            showConfirmButton: false,
            timer: 1500
        });
	}else{
		Swal.fire({
            position: "top-end",
            icon: "error",
            title: message,
            showConfirmButton: false,
            timer: 1500
        });
	}
}
